<?php

namespace Archi\Dispatcher;

abstract class Listener
{
    private $callable;
    private string $eventName;

    public function __construct(string $eventName, callable $callable)
    {
        $this->callable = $callable;
        $this->eventName = $eventName;
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }
}
