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
        $this->register(new Binding('Config', 'Archi\Config\ConfigProvider', true));
        $this->register(new Binding('Dispatcher', 'Archi\Dispatcher\ArchiDispatcher', true));
    }

    private function __clone()
    {
    }

    /**
     * @return ArchiContainer
     */
    public static function getInstance(): ArchiContainer
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Finds an entry of the container by its identifier and returns it. (PSR)
     * Archi Framework will try to first find existing instance (in case of singletons)
     * or use autowiring or factory to create it. If there are no entries registered
     * under given ID, it will be treated as full class path.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface|\ReflectionException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id)
    {
        if ($this->hasFactory($id)) {
            return $this->factories[$id]($this);
        }

        if (!$this->has($id) && !$this->isWireable($id)) {
            throw new NotFoundException("Container cannot find instance for {$id}");
        }

        if (!$this->isRegistered($id)) {
            return $this->registerAndAutowire($id, false);
        }

        return $this->autowire($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->hasBinding($id) || $this->hasFactory($id) || $this->isWireable($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function isRegistered(string $id): bool
    {
        return $this->hasBinding($id) || $this->hasFactory($id);
    }

    /**
     * Returns true if object can be wired in any way
     * regardless of whether it's registered
     *
     * @param string $id
     * @return bool
     */
    public function isWireable(string $id): bool
    {
        try {
            $i = new \ReflectionClass($id);
            return $i->isInstantiable();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    /**
     * Wires and instance of the object identified
     * by given ID. Binding needs to be registered
     *
     * @param string $id
     * @return object
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    private function autowire(string $id): object
    {
        if (!isset($this->bindings[$id])) {
            throw new NotFoundException('Trying to wire ' . $id . ' but no binding has been registered.');
        }
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

    /**
     * @param Binding $binding
     * @return mixed|object
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
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
            $constructorParams[] = $this->get($param->getType()->getName());
        }

        return $reflection->newInstanceArgs($constructorParams);
    }

    /**
     * Class is not registered but can be located.
     * Register it for further use and return wired instance
     *
     * @param string $class
     * @param bool $isSingleton
     * @return mixed|object
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

    /**
     * Registers Wireable which will provide object instance
     *
     * @param Wireable $wireable
     */
    public function registerFactory(Wireable $wireable)
    {
        $this->factories[$wireable->getId()] = function () use ($wireable) {
            return $wireable->wire($this);
        };
    }

    public static function reset()
    {
        // @TODO: throw if not in testing
        self::$instance = new static();
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
