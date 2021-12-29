<?php

namespace Archi\Module;

interface ClassMapInterface extends \Iterator, \Countable
{
    public function has(string $className): bool;

    public function getLocation(string $className): string;
}
