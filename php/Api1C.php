<?php declare(strict_types = 1);

namespace quantum;

class Api1C
{
    public function upload($data = [])
    {
        //ssh -R 9001:10.30.20.155:80 s01.runz.org
        $easy = new \quantum\curl\Easy([
            'url' => "http://127.0.0.1:9001/InfoBase3/hs/Upload/UploadHello",
            'returnTransfer' => true,
            'post' => true,
            'postFields' => json_encode($data),
            'httpHeader' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        return $easy();

        /*
        $path = '/home/runz.org/quantum';
        $f = fopen("$path/.local/speech.log", 'a');
        fwrite($f, print_r($data, true));
        fclose($f);
        return 'Ok';
        */
    }
}
