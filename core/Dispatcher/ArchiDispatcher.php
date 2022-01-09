<?php

namespace Archi\Dispatcher;

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\Event\RequestEvent;
use Archi\Dispatcher\Listener\CoreRequestListener;
use Archi\Dispatcher\Provider\ListenerProvider;
use Psr\EventDispatcher\EventDispatcherInterface;

class ArchiDispatcher implements EventDispatcherInterface
{
    private $provider;

    /**
     * ArchiDispatcher constructor.
     * @param ListenerProvider $provider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function __construct(ListenerProvider $provider)
    {
        $this->provider = $provider;
        $request = ArchiContainer::getInstance()->get('Request');
        $event = new RequestEvent($request);
        $this->provider->register($event, ArchiContainer::getInstance()->get(CoreRequestListener::class));
    }

    /**
     * @param object $event
     * @return Event
     * @throws ListenerException
     */
    public function dispatch(object $event)
    {
        if (!$event instanceof Event) {
            throw new ListenerException('Trying to dispatch event that is not an instance of Archi\Dispatcher\Event');
        }

        $listeners = $this->provider->getListenersForEvent($event);
        $result = null;

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

        return $result;
    }

    public function register($subject, ListenerInterface $listener)
    {
        $this->provider->register($subject, $listener);
    }
}
