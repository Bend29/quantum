<?php

namespace quantum\curl;

class EasyException extends \Exception
{
    public function __construct(int $code, string $url, \Exception $prev = null)
    {
        $error = curl_strerror($code);
        parent::__construct("$error, url: $url", $code, $prev);
    }
}
