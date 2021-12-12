<?php

namespace Archi\Dispatcher;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    private $listeners;

    public function getListenersForEvent(object $event): iterable
    {
        if (!$event instanceof Event) {
            throw new ListenerException('Trying to dispatch event that is not an instance of Archi\Dispatcher\Event');
        }
        if (isset($this->listeners[$event->getName()])) {
            return $this->listeners[$event->getName()];
        }

        return [];
    }
}
