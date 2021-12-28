<?php

namespace Archi\Module;

use Archi\Helper\File;
use Archi\Helper\Nomenclature;

abstract class ClassMap implements \Iterator, \Countable
{
    private array $classMap = [];
    private int $size = 0;
    private ?int $position = null;

    public function current()
    {
        return $this->classMap[$this->position]['location'];
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        return $this->classMap[$this->position]['className'];
    }

    public function valid()
    {
        if (is_null($this->position)) {
            return false;
        }
        if ($this->position > $this->count()) {
            return false;
        }

        return true;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count()
    {
        return count($this->classMap);
    }

    protected function push(string $className, string $location)
    {
        if ($className != Nomenclature::toPascalCase($className)) {
            throw new \RuntimeException('Invalid class name ' . $className);
        }
        if (!File::exists($location)) {
            throw new \RuntimeException('Invalid file specified for class ' . $className . ' | ' . $location);
        }

        $this->classMap[] = ['className' => $className, 'location' => $location];
        $this->size++;
    }
}
