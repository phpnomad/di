<?php

namespace Phoenix\Di\Interfaces;

use Phoenix\Di\Exceptions\DiException;

interface CanProvideConcreteInstance
{
    /**
     * @return object
     * @throws DiException
     */
    public function provideConcreteInstance(): object;
}