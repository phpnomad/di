<?php

namespace PHPNomad\Di;

use Mockery\Exception;
use PHPNomad\Di\Exceptions\DiException;
use PHPNomad\Di\Interfaces\CanProvideConcreteInstance;
use PHPNomad\Utils\Helpers\Arr;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use Throwable;

class Container
{
    /**
     * @var array<string, string>
     */
    private array $bindings = [];

    /**
     * @var array<null|object>
     */
    private array $instances = [];

    /**
     * Associate an abstract class or interface with a concrete class
     *
     * @param class-string $concrete
     * @param class-string $abstract
     * @param class-string ...$abstracts Additional abstract classes to bind to this instance.
     * @return Container
     */
    public function bind(string $concrete, string $abstract, string ...$abstracts): Container
    {
        $instance = null;
        /** @var array<string, class-string> $abstracts */
        $abstracts = Arr::merge([$abstract], $abstracts);

        foreach ($abstracts as $abstractClass) {
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
            /** @var T $result */
            $result = Arr::get($this->instances, $abstract);

            if(is_null($result)){
                $result = $this->instantiate($abstract);
            }

            return $result;
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

        //TODO: OPTIMIZE THIS BY MAKING IT POSSIBLE TO CACHE THE INSTANCES BETWEEN REQUESTS.

        try {
            $object = $this->resolve($concrete);

            if (!$object instanceof $abstract) {
                throw new DiException('The provided instance for ' . $abstract . ' Is not an instance of the abstraction', 0);
            }
        } catch (ReflectionException $e) {
            throw new DiException('Could not instantiate the provided class ' . $abstract . ' Using ' . $concrete, 0, $e);
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
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            try {
                return new $concrete();
            } catch (Throwable $e) {
                throw new DiException('Failed creating instance ' . $concrete . ':' . $e->getMessage(), 0, $e);
            }
        }

        $constructorParams = $constructor->getParameters();

        $dependencies = [];
        foreach ($constructorParams as $param) {
            $paramType = $param->getType();
            if (!$paramType->isBuiltin() && $paramType instanceof ReflectionNamedType) {
                $dependencies[] = $this->get($paramType->getName());
            } else {
                $dependencies[] = $param;
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
