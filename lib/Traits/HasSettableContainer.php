<?php

namespace Phoenix\Di\Traits;

use Phoenix\Di\Container;

trait HasSettableContainer
{
    protected Container $container;

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