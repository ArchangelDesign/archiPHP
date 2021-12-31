<?php

namespace Archi\Dispatcher;

abstract class Event implements EventInterface
{

    private $stopPropagation = false;

    private $name;

    private $payload;

    private $handlersProcessed = 0;

    public function __construct(string $name, ?EventPayload $payload, bool $stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
        $this->name = get_called_class();
        $this->payload = $payload;
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

    public function stopPropagation()
    {
        $this->stopPropagation = true;
    }

    public function getPayload(): EventPayload
    {
        return $this->payload;
    }

    public function setPayload(EventPayload $payload)
    {
        $this->payload = $payload;
        $this->handlersProcessed++;
    }
}
