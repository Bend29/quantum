<?php declare(strict_types = 1);

namespace quantum\cli;

class Call extends Base
{
    public function handle(Request $request): void
    {
        $api_ai = new \quantum\ApiAi;
        $api_1c = new \quantum\Api1C;

        $data_string = stream_get_contents(STDIN);
        $data = json_decode($data_string, true);
        $action = $data['action'] ?? null;
        $transcription = $data['transcription'] ?? null;

        if ('recommend' === $action && null !== $transcription) {
            $prediction = $api_ai->getPredict($transcription);

            if (null !== $prediction) {
                $data['data'] = $prediction;
            }
        }

        $res = $api_1c->upload($data);
    }
}
