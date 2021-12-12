<?php

namespace Archi\Container\TestObjects;

class SingletonTest
{
    private $counter = 0;

    public function inc()
    {
        $this->counter++;
    }

    public function getCounter()
    {
        return $this->counter;
    }
}
