<?php

namespace Phoenix\Di\Interfaces;

use Phoenix\Di\Container;

interface CanSetContainer
{
    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container);
}