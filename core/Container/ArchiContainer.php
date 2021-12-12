<?php

namespace Archi\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ArchiContainer implements ContainerInterface
{
    private $instances = [];

    /** @var Binding[] */
    private $bindings = [];

    public function get(string $id)
    {
        if (!$this->has($id) && !$this->isWireable($id)) {
            throw new NotFoundException("Container cannot find instance for {$id}");
        }

        if (!$this->has($id)) {
            return $this->autowireAndRegister($id);
        }
        return $this->autowire($id);
    }

    public function has(string $id): bool
    {
        if (class_exists($id)) {
            return true;
        }

        if (isset($this->instances[$id])) {
            return true;
        }

        if (isset($this->bindings[$id])) {
            return true;
        }

        return false;
    }

    private function isWireable(string $id)
    {
        return $this->has($id) || class_exists($id);
    }

    private function autowire(string $id)
    {
        if (!$this->bindings[$id]->isSingleton()) {
            return $this->wire($this->bindings[$id]);
        }

        if ($this->hasInstance($id)) {
            return $this->instances[$id];
        }

        $this->instances[$id] = $this->wire($this->bindings[$id]);

        return $this->instances[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    private function hasInstance(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    private function wire(Binding $binding)
    {
        // @TODO: before and after triggers
        $c = $binding->getClassPath();
        try {
            $reflection = new \ReflectionClass($c);
        } catch (\ReflectionException $e) {
            throw new ContainerException('Cannot instantiate object of ' . $binding->getClassPath(), 0, $e);
        }

        if (!$reflection->isInstantiable()) {
            throw new ContainerException('Class ' . $binding->getClassPath() . ' cannot be instantiated');
        }

        if ($reflection->getConstructor()->getNumberOfParameters() == 0) {
            return new $c();
        }

        // @TODO: resolve dependencies for constructor argument

        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        $constructorParams = [];
        foreach ($parameters as $param) {
            if (!$param->hasType() && !$param->isOptional()) {
                throw new ContainerException('Cannot wire parameter ' . $param->getName() . ' for class ' . $binding->getClassPath() . '. It has no type and is not optional.');
            }
            if ($param->isVariadic()) {
                // @TODO: merge arrays
                throw new ContainerException('Variadic parameters are not supported.');
            }
            $constructorParams[] = $this->autowire($param->getClass());
        }

        return $reflection->newInstanceArgs($constructorParams);
    }

    /**
     * Class is not registered but can be located.
     * Register it for further use and return wired instance
     *
     * @param string $class
     */
    private function autowireAndRegister(string $class)
    {

    }
}