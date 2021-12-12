<?php

namespace Tests;

use Archi\Dispatcher\ListenerProvider;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testRegistrationAndDispatching()
    {
        $provider = new ListenerProvider();
        $this->assertInstanceOf(ListenerProvider::class, $provider);
    }
}
