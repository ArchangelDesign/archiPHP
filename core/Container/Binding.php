<?php

namespace Archi\Container;

class Binding
{
    private $isSingleton;

    private $id;

    private $classPath;

    /**
     * Binding constructor.
     * @param $isSingleton
     * @param $id
     * @param $classPath
     */
    public function __construct(string $id, string $classPath, bool $isSingleton)
    {
        $this->isSingleton = $isSingleton;
        $this->id = $id;
        $this->classPath = $classPath;
    }


    public function isSingleton(): bool
    {
        return $this->isSingleton;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClassPath(): string
    {
        return $this->classPath;
    }

}