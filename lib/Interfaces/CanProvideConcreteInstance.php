<?php

namespace Phoenix\Di\Interfaces;

use Phoenix\Di\Exceptions\DiException;

interface CanProvideConcreteInstance extends CanSetContainer
{
    /**
     * @return object
     * @throws DiException
     */
    public function provideConcreteInstance(): object;
}