<?php

namespace Archi\Dispatcher\Listener;

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\Event;
use Archi\Dispatcher\Listener;
use Archi\Dispatcher\ListenerInterface;

class CoreRequestListener implements ListenerInterface
{

    public function dispatch(Event $event): Event
    {
        $logger = ArchiContainer::getInstance()->get('Logger');
        $logger->debug('Dispatching ' . $event->getPayload()->getRequest()->getMethod() . ' request');
        return $event;
    }
}
