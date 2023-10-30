<?php

namespace PHPNomad\Di\Interfaces;

use PHPNomad\Di\Container;

interface CanSetContainer
{
    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container);
}