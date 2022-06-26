<?php

namespace Archi\Dispatcher\Provider;

use Archi\Dispatcher\Event;
use Archi\Dispatcher\ListenerException;
use Archi\Dispatcher\ListenerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    private array $listenerCallbacks = [];

    private array $listeners = [];

    /**
     * @param object $event
     * @return Event[]
     * @throws ListenerException
     */
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

    /**
     * Registers a listener that will be invoked for given event
     *
     * @param Event|string $event
     * @param ListenerInterface $listener
     */
    public function register($event, ListenerInterface $listener)
    {
        if (!$event instanceof Event && !is_string($event)) {
            throw new \RuntimeException(
                'Event must be identified as either string or Archi\Dispatcher\Event instance.'
            );
        }

        $key = $this->getEventKey($event);
        $this->prepareForNewListener($key);
        $this->pushListener($listener, $key);
    }

    /**
     * @param string $key
     */
    private function prepareForNewListener(string $key): void
    {
        if (!isset($this->listeners[$key])) {
            $this->listeners[$key] = [];
        }
        if (!isset($this->listenerCallbacks[$key])) {
            $this->listenerCallbacks[$key] = [];
        }
    }

    /**
     * @param ListenerInterface $listener
     * @param string $key
     */
    private function pushListener(ListenerInterface $listener, string $key): void
    {
        $this->listeners[$key][] = $listener;
        $this->listenerCallbacks[$key][] = function (Event $event) use ($listener) {
            return $listener->dispatch($event);
        };
    }

    /**
     * Returns the identifier of the event (name)
     *
     * @param $event
     * @return string
     */
    private function getEventKey($event): string
    {
        if ($event instanceof Event) {
            return $event->getName();
        }

        return $event;
    }
}
