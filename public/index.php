<?php

use Archi\Dispatcher\ArchiDispatcher;
use Archi\Dispatcher\Event\RequestEvent;
use Archi\Dispatcher\ListenerProvider;
use Archi\Http\Request\RequestBuilder;
use Archi\Log\Logger;

require __DIR__ . '/../vendor/autoload.php';
$logger = new Logger();
$logger->debug('Staring application');
$dispatcher = new ArchiDispatcher(new ListenerProvider());
$request = RequestBuilder::createFromGlobals();
$dispatcher->dispatch(new RequestEvent($request));
