<?php

namespace Archi\Dispatcher;

interface ListenerInterface
{
    public function dispatch(Event $event): Event;
}
