<?php declare(strict_types = 1);

namespace quantum\cli;

use quantum\Resource;

abstract class Base
{
    protected $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    abstract public function handle(Request $request): void;
}
