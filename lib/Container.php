<?php

namespace Phoenix\Di;

use Phoenix\Di\Exceptions\DiException;
use Phoenix\Utils\Helpers\Arr;
use ReflectionClass;
use ReflectionException;

final class Container
{
    private array $bindings = [];
    private array $instances = [];

    /**
     * Associate an abstract class or interface with a concrete class
     *
     * @param class-string $concrete
     * @param class-string $abstract
     * @param class-string ...$abstracts Additional abstract classes to bind to this instance.
     * @return void
     */
    public function bind(string $concrete, string $abstract, string ...$abstracts): Container
    {
        $instance = null;

        foreach (Arr::merge([$abstract], $abstracts) as $abstractClass) {
            $this->bindings[$abstractClass] = $concrete;

            // This ensures all bound abstracts will return the same instance
            $this->instances[$abstractClass] = &$instance;
        }

        return $this;
    }

    /**
     * Get an instance of the class, with dependencies autowired
     *
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     * @throws DiException
     */
    public function get(string $abstract): object
    {
        try {
            return Arr::get($this->instances, $abstract, $this->instantiate($abstract));
        } catch (ReflectionException $e) {
            throw new DiException('Failed to get instance from the provided abstract.', 0, $e);
        }
    }

    /**
     * Create instance of the class, with dependencies autowired
     *
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     * @throws DiException
     */
    protected function instantiate(string $abstract)
    {
        $concrete = $this->bindings[$abstract] ?? $abstract;

        // If we already have an instance, return that
        if ($this->instances[$abstract]) {
            return $this->instances[$abstract];
        }

        try {
            $object = $this->resolve($concrete);
        } catch (ReflectionException $e) {
            throw new DiException('Could not instantiate the provided class', 0, $e);
        }

        $this->instances[$abstract] = $object;

        return $object;
    }

    /**
     * Resolves the instance, instantiating constructor arguments.
     * @template T of object
     * @param class-string<T> $concrete
     * @return T
     * @throws DiException
     * @throws ReflectionException
     */
    protected function resolve(string $concrete)
    {
        $reflectionClass = new ReflectionClass($concrete);

        if (!$reflectionClass->getConstructor()) {
            return new $concrete();
        }

        $constructorParams = $reflectionClass->getConstructor()->getParameters();

        $dependencies = [];
        foreach ($constructorParams as $param) {
            $paramClass = $param->getClass();
            if ($paramClass) {
                $dependencies[] = $this->get($paramClass->getName());
            } else {
                $dependencies[] = $param;
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * Gets instanced classes from of an array of abstract classes.
     *
     * @param string ...$abstracts
     * @return array
     */
    public function hydrate(string ...$abstracts): array
    {
        return Arr::map($abstracts, [$this, 'get']);
    }
}
