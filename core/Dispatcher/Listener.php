<?php

namespace Archi\Dispatcher;

abstract class Listener implements ListenerInterface
{
    private $callable;
    /** @var Event */
    private $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function getCallable(): callable
    {
        return function () {
            return $this->dispatch($this->event);
        };
    }

    public function getEventName(): string
    {
        return $this->event->getName();
    }

    public function getEvent(): Event
    {
        return $this->event;
    }
}
