base_path = '/home/runz.org/quantum'
audio_path = base_path .. '/.local/audio'
sound_path = '/etc/asterisk/sounds'

function table.indexOf(table, elem)
    for k, v in ipairs(table) do
        if v == elem then
            return k
        end
    end

    return nil
end;

-- обновляет список мемберов очереди
-- мемберы выживают после полного останова и старта астериска, когда в конфиге persistentmembers = yes
function updateQueueMembers(queue_name, members)
    -- Список текущих мемберов, разделённых запятой
    local current = channel["QUEUE_MEMBER_LIST(" .. queue_name .. ")"]:get()

    -- Разбиваем строку по ,
    for ext in current:gmatch('[^,]+') do
        ext = ext:gsub('^Local/(%d+)@queue%-member$', '%1') -- выкусываем номер

        local index = table.indexOf(members, ext)

        if index ~= nil then
            -- исключаем те, которые уже есть, чтобы оставшиеся добавить
            table.remove(members, index)
        else
            -- удаляем из очереди, те которых нет в members
            app.removeQueueMember(queue_name, "Local/" .. ext .. "@queue-member")
        end
    end

    for index, ext in ipairs(members) do
        -- добавляем в очередь, те которых ещё нет в очереди
		app.addQueueMember(queue_name, "Local/" .. ext .. "@queue-member", nil, nil, nil, "SIP/" .. ext)
    end
end;

function dial(exten, timeout, opts)
    if "400" == exten then
		updateQueueMembers("400", {'103'})
        app.Queue("400" , "nt", nil , nil, timeout)

	elseif #exten == 3 then
        app.Dial("SIP/" .. exten, timeout, opts)

    else
        -- исходящая линия отрублена
        app.Verbose('Cannot dial ' .. exten)
    end
end

extensions = {
    -- звонки из офиса (из jitsi)
    ["office-out"] = {
        -- здесь можно писать всякие тесты. Вызывать - набрав 991 в jitsi
        ["991"] = function()
            app.Verbose('тест')
            app.Hangup()
        end;

        -- эмуляция вызова с внешней линии
        ["911"] = function()
            app.Goto("zadarma-in", "911", 1)
        end;

        ["_."] = function(context, exten)
            dial(exten)
            app.Hangup()
        end;

        failed = function(context, exten)
        end;

        h = function(context, exten)
        end;
    };

    ["zadarma-in"] = {
        ["_."] = function(context, exten)
            --app.EAGI(base_path .. '/eagi')

            dial('400');
            app.Hangup()
        end;

        failed = function(context, exten)
        end;

        h = function(context, exten)
        end;
    };

    ["queue-member"] = {
        ["_."] = function(context, exten)
            local uid = channel.UNIQUEID:get();
            local file = audio_path .. '/' .. uid;
            local phone = channel['CALLERID(num)']:get();

            local command = 'tail -z -c +1 -F ' .. file .. '.caller.wav | ' .. base_path .. '/monitor ' .. uid .. ' ' .. phone .. ' ' .. exten;
            local monitor_caller = assert(io.popen(command .. '&', 'r'))
            monitor_caller:close()

            app.MixMonitor(file .. '.mixed.wav', 'br(' .. file .. '.caller.wav)t(' .. file .. '.calee.wav)');
            dial(exten)
            app.Hangup()
        end;

        failed = function(context, exten)
        end;

        h = function(context, exten)
            local uid = channel.UNIQUEID:get();
            local pid_file = assert(io.open(base_path .. '/.local/pid/' .. uid, 'r'))
            local pid = pid_file:read()
            pid_file:close()
            os.execute('kill -9 ' .. pid)
        end;
    };

    default = {
        ["_."] = function(context, exten)
            app.Verbose('unauthorized')
            --print("UNATHORIZED ACCESS")
            --app.DumpChan()
            app.Hangup()
        end;

        failed = function(context, exten)
        end;

        h = function(context, exten)
        end;
    };
}
