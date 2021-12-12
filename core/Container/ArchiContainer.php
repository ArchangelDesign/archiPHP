<?php

namespace Archi\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ArchiContainer implements ContainerInterface
{
    private static $instance;

    private $instances = [];

    /**
     * @var Binding[]
     */
    private $bindings = [];

    private $factories = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance(): ArchiContainer
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function get(string $id)
    {
        if ($this->hasFactory($id)) {
            return $this->factories[$id]($this);
        }

        if (!$this->has($id) && !$this->isWireable($id)) {
            throw new NotFoundException("Container cannot find instance for {$id}");
        }

        if (!$this->has($id)) {
            return $this->registerAndAutowire($id, false);
        }

        return $this->autowire($id);
    }

    public function has(string $id): bool
    {
        return $this->hasBinding($id) || $this->hasFactory($id);
    }

    public function isWireable(string $id): bool
    {
        try {
            $i = new \ReflectionClass($id);
            return $i->isInstantiable();
        } catch (\ReflectionException $e) {
            return false;
        }
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
     * @param  string $id
     * @return bool
     */
    private function hasInstance(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    private function wire(Binding $binding)
    {
        $c = $binding->getClassPath();
        try {
            $reflection = new \ReflectionClass($c);
        } catch (\ReflectionException $e) {
            throw new ContainerException('Cannot instantiate object of ' . $binding->getClassPath(), 0, $e);
        }

        if (!$reflection->isInstantiable()) {
            throw new ContainerException('Class ' . $binding->getClassPath() . ' cannot be instantiated');
        }

        if (is_null($reflection->getConstructor())) {
            return new $c();
        }

        if ($reflection->getConstructor()->getNumberOfParameters() == 0) {
            return new $c();
        }

        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        $constructorParams = [];
        foreach ($parameters as $param) {
            if (!$param->hasType() && !$param->isOptional()) {
                throw new ContainerException(
                    'Cannot wire parameter '
                    . $param->getName() . ' for class '
                    . $binding->getClassPath() . '. It has no type and is not optional.'
                );
            }
            if ($param->isVariadic()) {
                // @TODO: merge arrays
                throw new ContainerException('Variadic parameters are not supported.');
            }

            if ($param->getType() == null || $param->getType()->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $constructorParams[] = $param->getDefaultValue();
                    continue;
                }
                throw new ContainerException(
                    'Cannot wire parameter '
                    . $param->getName() . ' for class '
                    . $binding->getClassPath() . '. Builtin types must have default value to be autowired.'
                );
            }

            if (!$param->getType() instanceof \ReflectionNamedType) {
                throw new ContainerException(
                    'Cannot wire parameter '
                    . $param->getName() . ' for class '
                    . $binding->getClassPath() . '.'
                );
            }
            $constructorParams[] = $this->autowire($this->getParameterClassName($param));
        }

        return $reflection->newInstanceArgs($constructorParams);
    }

    /**
     * Class is not registered but can be located.
     * Register it for further use and return wired instance
     *
     * @param string $class
     * @param bool   $isSingleton
     */
    private function registerAndAutowire(string $class, bool $isSingleton)
    {
        $b = new Binding($class, $class, $isSingleton);
        $this->register($b);

        return $this->autowire($class);
    }

    public function register(Binding $binding)
    {
        $this->bindings[$binding->getId()] = $binding;
    }

    public function registerFactory(string $id, callable $callable)
    {
        $this->factories[$id] = $callable;
    }

    public static function reset()
    {
        // @TODO: throw if not in testing
        self::$instance = new static();
    }

    public function getParameterClassName(\ReflectionParameter $parameter)
    {
        $type = $parameter->getType();
        $typeName = $type->getName();
        if (is_null($parameter->getDeclaringClass())) {
            return $typeName;
        }

        $class = $parameter->getDeclaringClass();
        if ($typeName === 'self') {
            return $class->getName();
        }

        if ($typeName === 'parent' && $parent = $class->getParentClass()) {
            return $parent->getName();
        }
    }

    private function hasFactory(string $id)
    {
        return isset($this->factories[$id]);
    }

    /**
     * @param string $id
     * @return bool
     */
    private function hasBinding(string $id): bool
    {
        return isset($this->bindings[$id]);
    }
}
