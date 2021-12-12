<?php

namespace Tests;

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\ArchiDispatcher;
use Archi\Dispatcher\ListenerProvider;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testRegistrationAndDispatching()
    {
        $dispatcher = ArchiContainer::getInstance()->get('Dispatcher');
        $this->assertInstanceOf(ArchiDispatcher::class, $dispatcher);
    }
}
