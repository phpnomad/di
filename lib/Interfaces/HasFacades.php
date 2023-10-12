<?php

namespace Phoenix\Di\Interfaces;

use Phoenix\Di\Abstracts\Facade;

interface HasFacades
{
    /**
     * @return Facade[]
     */
    public function getFacades(): array;
}