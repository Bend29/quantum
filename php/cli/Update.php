<?php declare(strict_types = 1);

namespace quantum\cli;

class Update extends Base
{
    protected function ensureLocal(string $path, string $contents, int $chmod): self
    {
        $local = $this->resource->getPath('.local');

        if (!is_dir($local)) {
            mkdir($local, 0777, true);
        }

        if (!is_file("$local/$path")) {
            file_put_contents("$local/$path", $contents);
            chmod("$local/$path", $chmod);
            echo "[+] $local/$path\n";
        }

        return $this;
    }

    public function handle(Request $request): void
    {
        $this
            ->ensureLocal('resource.ini', implode("\n", [
                'debug = true',
            ]), 0644)
        ;

        echo "done\n";
    }
}
