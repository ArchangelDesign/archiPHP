<?php

namespace Archi\Dispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

abstract class Event implements StoppableEventInterface
{

    private $stopPropagation = false;

    private $name;

    public function __construct(string $name, bool $stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
        $this->name = $name;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function stopPropagation(bool $stop = true)
    {
        $this->stopPropagation = $stop;
    }
}
