<?php

namespace Archi\Debug;

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\Event;
use Archi\Dispatcher\EventPayload;
use Archi\Dispatcher\ListenerInterface;

class RequestListener implements ListenerInterface
{

    public function dispatch(Event $event): Event
    {
        $vm = ArchiContainer::getViewManager();
        $currentPayload = $event->getPayload()->getPayload();
        $currentPayload['DebugBar'] = [
            'initialized' => true
        ];

        $event->setPayload(EventPayload::buildFromArray($currentPayload));

        return $event;
    }
}