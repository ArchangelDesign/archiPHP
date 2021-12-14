<?php

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\Event\RequestEvent;
use Archi\Log\Logger;

require __DIR__ . '/../vendor/autoload.php';
$logger = new Logger();
$logger->debug('Staring application');
$dispatcher = ArchiContainer::getInstance()->get('Dispatcher');
$request = ArchiContainer::getInstance()->get('Request');
$dispatcher->dispatch(new RequestEvent($request));
