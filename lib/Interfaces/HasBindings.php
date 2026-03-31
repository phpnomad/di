<?php

namespace PHPNomad\Di\Interfaces;

interface HasBindings
{
    /**
     * Bind a concrete class to one or more abstracts.
     *
     * @param class-string $concrete
     * @param class-string $abstract
     * @param class-string ...$abstracts Additional abstracts to bind to this concrete.
     * @return $this
     */
    public function bind(string $concrete, string $abstract, string ...$abstracts);

    /**
     * Bind an abstract to a factory callable.
     *
     * @param class-string $abstract
     * @param callable $factory
     * @return $this
     */
    public function bindFactory(string $abstract, callable $factory);
}
