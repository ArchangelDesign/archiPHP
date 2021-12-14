<?php

namespace Archi\Dispatcher\Provider;

use Archi\Container\ArchiContainer;
use Archi\Container\Wireable;
use Archi\Http\Request\ArchiRequest;
use Archi\Http\Request\RequestBuilder;
use Psr\EventDispatcher\ListenerProviderInterface;

class RequestProvider implements Wireable
{
    public function getId(): string
    {
        return 'Request';
    }

    public function wire(ArchiContainer $container): object
    {
        return RequestBuilder::createFromGlobals();
    }
}
