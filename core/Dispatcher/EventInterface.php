<?php

namespace Archi\Dispatcher;

use Psr\EventDispatcher\StoppableEventInterface;

interface EventInterface extends StoppableEventInterface
{
    public function getName(): string;
    public function stopPropagation();
    public function getPayload(): EventPayload;
    public function setPayload(EventPayload $payload);
}
