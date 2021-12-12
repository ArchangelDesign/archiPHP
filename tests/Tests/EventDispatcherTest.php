<?php

namespace Tests;

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\ArchiDispatcher;
use Archi\Dispatcher\Event;
use Archi\Dispatcher\EventPayload;
use Archi\Dispatcher\TestObjects\TestEvent;
use Archi\Dispatcher\TestObjects\TestEventListener;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testRegistrationAndDispatching()
    {
        /** @var ArchiDispatcher $dispatcher */
        $dispatcher = ArchiContainer::getInstance()->get('Dispatcher');
        $this->assertInstanceOf(ArchiDispatcher::class, $dispatcher);
        $e = new TestEvent();
        $this->assertEquals('Archi\Dispatcher\Event\GenericEvent', $e->getName());
        $e->setPayload(EventPayload::buildFromArray(['processed' => false]));
        $dispatcher->register(new TestEventListener($e));
        $processedEvent = $dispatcher->dispatch($e);
        $this->assertInstanceOf(Event::class, $processedEvent);
        $payload = $processedEvent->getPayload()->getPayload();
        $this->assertArrayHasKey('processed', $payload);
        $this->assertTrue($payload['processed']);
    }
}
