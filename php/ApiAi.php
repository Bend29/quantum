<?php declare(strict_types = 1);

namespace quantum;

class ApiAi
{
    public function getPredict($question)
    {
        //ssh -R 9001:10.30.20.155:80 s01.runz.org
        //curl -v 10.30.22.208:9000/getPredict -d '{"question": "123"}' -H 'Content-Type: application/json'
        $easy = new \quantum\curl\Easy([
            'url' => "http://127.0.0.1:9002/getPredict",
            'returnTransfer' => true,
            'post' => true,
            'postFields' => json_encode(['question' => $question]),
            'httpHeader' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $data = json_decode($easy(), true);
        $data = $data['data']['category'] ?? null;

        if (null === $data) {
            return null;
        }

        $normal_data = [];

        foreach ($data as $elem) {
            $normal_data[] = [
                'weight' => $elem[0],
                'id' => $elem[1],
                'text' => $elem[2],
            ];
        }

        usort($normal_data, function ($a, $b) {
            if ($a['weight'] == $b['weight']) {
                return 0;
            }

            return ($a['weight'] < $b['weight']) ? 1 : -1;
        });

        return $normal_data;
    }
}
