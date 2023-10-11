<?php

namespace Phoenix\Di\Abstracts;

use Phoenix\Di\Container;

abstract class Facade
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}