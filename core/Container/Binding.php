<?php

namespace Archi\Container;

class Binding
{
    private bool $isSingleton;

    private string $id;

    private string $classPath;

    /**
     * Binding constructor.
     *
     * @param string $id
     * @param string $classPath
     * @param bool $isSingleton
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
