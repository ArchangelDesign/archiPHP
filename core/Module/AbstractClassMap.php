<?php

namespace Archi\Module;

use Archi\Helper\File;
use Archi\Helper\Nomenclature;

abstract class AbstractClassMap implements ClassMapInterface
{
    private array $classStack = [];
    private int $size = 0;
    private ?int $position = null;
    private array $classMap = [];

    public function current()
    {
        if (is_null($this->position)) {
            $this->rewind();
        }
        return $this->classStack[$this->position]['location'];
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        return $this->classStack[$this->position]['className'];
    }

    public function valid()
    {
        if (is_null($this->position)) {
            return false;
        }
        if ($this->position > $this->count()) {
            return false;
        }

        return isset($this->classStack[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count()
    {
        return $this->size;
    }

    protected function push(string $className, string $location)
    {
        if ($this->has($className)) {
            return;
        }
        if (!Nomenclature::isValidClassName($className)) {
            throw new \RuntimeException('Invalid class name ' . $className);
        }
        if (!File::exists($location)) {
            throw new \RuntimeException('Invalid file specified for class ' . $className . ' | ' . $location);
        }

        $this->classStack[] = ['className' => $className, 'location' => $location];
        $this->classMap[$className] = $location;
        $this->size++;
    }

    public function has(string $className): bool
    {
        return isset($this->classMap[$className]);
    }

    public function getLocation(string $className): string
    {
        return $this->classMap[$className];
    }
}
