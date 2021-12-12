<?php

namespace Archi\Dispatcher\TestObjects;

use Archi\Dispatcher\Event;
use Archi\Dispatcher\EventPayload;
use Archi\Dispatcher\Listener;

class TestEventListener extends Listener
{

    public function dispatch(Event $event): Event
    {
        $payload = EventPayload::buildFromExisting($event->getPayload(), ['processed' => true]);
        $event->setPayload($payload);

        return $event;
    }
}
