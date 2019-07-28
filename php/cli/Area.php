<?php declare(strict_types = 1);

namespace quantum\cli;

class Area extends Base
{
    protected $handlers = [
        'update' => Update::class,
        'test_api' => ApiTest::class,
        'call' => Call::class,
    ];

    public function handle(Request $request): void
    {
        $next = $request->args[1] ?? null;
        $handler = $this->handlers[$next] ?? null;

        if (null === $handler) {
            echo 'Usage: ' . $request->args[0] . " <action>\n";
            echo "Actions:\n";
            echo '  ' . implode("\n  ", array_keys($this->handlers)) . "\n";
            return;
        }

        (new $handler($this->resource))->handle($request->next());
    }
}
