<?php

namespace Archi\Dispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;

class ArchiDispatcher implements EventDispatcherInterface
{
    private $provider;

    /**
     * ArchiDispatcher constructor.
     * @param ListenerProvider $provider
     */
    public function __construct(ListenerProvider $provider)
    {
        $this->provider = $provider;
    }

    public function dispatch(object $event)
    {
        if (!$event instanceof Event) {
            throw new ListenerException('Trying to dispatch event that is not an instance of Archi\Dispatcher\Event');
        }

        $listeners = $this->provider->getListenersForEvent($event);

        foreach ($listeners as $listener) {
            /** @var Event $result */
            try {
                $result = $listener($event);
            } catch (\Exception $e) {
                throw new ListenerException('One of listeners threw an exception for event ' . $event);
            }
            if ($result->isPropagationStopped()) {
                break;
            }
        }
    }
}