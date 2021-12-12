<?php

use Archi\Dispatcher\ArchiDispatcher;
use Archi\Dispatcher\Event\RequestEvent;
use Archi\Dispatcher\ListenerProvider;
use Archi\Http\Request\RequestBuilder;

require __DIR__ . '/../vendor/autoload.php';

$dispatcher = new ArchiDispatcher(new ListenerProvider());
$request = RequestBuilder::createFromGlobals();
$dispatcher->dispatch(new RequestEvent($request));
