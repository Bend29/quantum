<?php declare(strict_types = 1);

namespace quantum;

class Resource
{
    private $path;
    protected $options;

    public function __construct(string $path, array $options = [])
    {
        $ini = "$path/.local/resource.ini";

        if (is_file($ini)) {
            $options = array_replace_recursive(
                parse_ini_file($ini, true, INI_SCANNER_TYPED),
                $options
            );
        }

        $this->path = $path;
        $this->options = $options;
    }

    public function getPath(string $path = null): string
    {
        return null === $path ? $this->path : "$this->path/$path";
    }

    public function isDebug(): bool
    {
        return (bool)($this->options['debug'] ?? false);
    }
}
