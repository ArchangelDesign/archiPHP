<?php

namespace Archi\Dispatcher\TestObjects;

use Archi\Dispatcher\Event;
use Archi\Dispatcher\EventPayload;
use Archi\Dispatcher\ListenerInterface;

class TestEventListener implements ListenerInterface
{

    public function dispatch(Event $event): Event
    {
        $payload = EventPayload::buildFromExisting($event->getPayload(), ['processed' => true]);
        $event->setPayload($payload);

        return $event;
    }
}
