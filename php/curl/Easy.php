<?php

namespace quantum\curl;

/**
 * usage:
 *     $easy = new \quantum\curl\Easy([
 *         'url' => 'http://runz.org/',
 *         'returnTransfer' => true,
 *         'headerFunction' => function ($easy, $header) {
 *             echo $header;
 *             return strlen($header);
 *         },
 *     ]);
 *     
 *     $content = $easy();
 *     
 *     echo "$content\n";
 *     print_r($easy->info);
 */
class Easy
{
    protected $resource;

    public function __construct(array $options = [])
    {
        $this->resource = curl_init();

        if (false === $this->resource) {
            throw new EasyException(CURLE_FAILED_INIT, $options['url'] ?? '');
        }

        foreach ($options as $name => $value) {
            $this->__set($name, $value);
        }
    }

    public function __clone()
    {
        $this->resource = curl_copy_handle($this->resource);
    }

    public function __set(string $name, $value)
    {
        $name = strtoupper($name);

        if ($value && substr($name, -8) === 'FUNCTION') {
            $value = function ($resource, ...$args) use ($value) {
                return $value($this, ...$args);
            };
        }

        if ('HTTPHEADER' === $name) {
            $headers = $value;
            $value = [];
            foreach ($headers as $k => $v) {
                $value[] = "$k: $v";
            }
        }

        curl_setopt($this->resource, constant("CURLOPT_$name"), $value);
    }

    public function __invoke()
    {
        $content = curl_exec($this->resource);
        $code = curl_errno($this->resource);

        if (CURLE_OK !== $code) {
            throw new EasyException($code, $this->effective_url);
        }

        return $content;
    }

    public function __get($name)
    {
        if ('resource' === $name) {
            return $this->resource;

        } elseif ('content' === $name) {
            return curl_multi_getcontent($this->resource);

        } elseif ('info' === $name) {
            return curl_getinfo($this->resource);
        }

        $name = strtoupper($name);

        return curl_getinfo($this->resource, constant("CURLINFO_$name"));
    }

    public function __destruct()
    {
        curl_close($this->resource);
    }

    public function reset(): void
    {
        curl_reset($this->resource);
    }

    public function pause(bool $receive, bool $send): void
    {
        if ($receive && $send) {
            $bitmask = CURLPAUSE_ALL;
        } elseif($receive) {
            $bitmask = CURLPAUSE_RECV;
        } elseif($send) {
            $bitmask = CURLPAUSE_SEND;
        } else {
            $bitmask = CURLPAUSE_CONT;
        }

        $code = curl_pause($this->resource, $bitmask);

        if (CURLE_OK !== $code) {
            throw new EasyException($code, $this->effective_url);
        }
    }
}
