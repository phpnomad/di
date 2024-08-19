<?php

namespace PHPNomad\Di\Traits;

use PHPNomad\Di\Container;
use PHPNomad\Di\Interfaces\InstanceProvider;

trait HasSettableContainer
{
    protected InstanceProvider $container;

    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}