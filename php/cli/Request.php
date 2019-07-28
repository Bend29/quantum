<?php declare(strict_types = 1);

namespace quantum\cli;

class Request
{
    private $args;
    private $options;

    public function __construct(array $args, array $options = [])
    {
        $this->args = [];
        $ini = '';

        foreach ($args as $arg) {
            if (strncmp('--', $arg, 2) === 0) {
                $ini .= substr($arg, 2) . "\n";
            } else {
                $this->args[] = $arg;
            }
        }

        if ('' !== $ini) {
            $options = array_replace_recursive(
                parse_ini_string($ini, true, INI_SCANNER_TYPED),
                $options
            );
        }

        $this->options = $options;
    }

    public function next(): self
    {
        $out = clone $this;
        array_unshift($out->args, implode(' ', array_splice($out->args, 0, 2)));

        return $out;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
