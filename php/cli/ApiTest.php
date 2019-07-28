<?php declare(strict_types = 1);

namespace quantum\cli;

class ApiTest extends Base
{
    public function handle(Request $request): void
    {
        /*
        $data_string = stream_get_contents(STDIN);
        $data = json_decode($data_string, true);

        $api = new \quantum\Api1C;
        $response = $api->upload($data);

        echo "$response\n";
        */

        //echo "done\n";

        /*
        $path = '/home/runz.org/quantum';
        $f = fopen("$path/.local/speech.log", 'a');
        fwrite($f, $data);
        fclose($f);
        */

        //$api = new \quantum\ApiAi;
        //$response = $api->getPredict("123");

        //print_r($response);
    }
}
