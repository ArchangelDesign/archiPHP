<?php

namespace Archi\Dispatcher\Event;

use Archi\Dispatcher\Event;
use Archi\Dispatcher\EventPayload;

abstract class GenericEvent extends Event
{
    public function __construct()
    {
        parent::__construct(__CLASS__, null, true);
    }
}
