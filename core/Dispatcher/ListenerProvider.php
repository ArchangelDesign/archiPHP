<?php

namespace Archi\Dispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    private $listenerCallbacks = [];

    private $listeners = [];

    public function getListenersForEvent(object $event): iterable
    {
        if (!$event instanceof Event) {
            throw new ListenerException('Trying to dispatch event that is not an instance of Archi\Dispatcher\Event');
        }
        if (isset($this->listenerCallbacks[$event->getName()])) {
            return $this->listenerCallbacks[$event->getName()];
        }

        return [];
    }

    public function register(Event $event, Listener $listener)
    {
        $this->prepareForEvent($event);
        $this->listeners[$event->getName()][] = $listener;
        $this->listenerCallbacks[$event->getName()][] = $listener->getCallable();
    }

    /**
     * @param Event $event
     */
    private function prepareForEvent(Event $event): void
    {
        if (!isset($this->listeners[$event->getName()])) {
            $this->listeners[$event->getName()] = [];
        }
        if (!isset($this->listenerCallbacks[$event->getName()])) {
            $this->listenerCallbacks[$event->getName()] = [];
        }
    }

}
