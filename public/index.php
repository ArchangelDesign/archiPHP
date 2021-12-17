<?php

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\Event\RequestEvent;
use Archi\Environment\Env;
use Archi\Log\Logger;

require __DIR__ . '/../vendor/autoload.php';
$logger = new Logger(['location' => 'bootstrap']);
$logger->debug('Staring application');
Env::initialize(dirname(__DIR__));
$dispatcher = ArchiContainer::getInstance()->get('Dispatcher');
/** @var \Archi\Http\Request\ArchiRequest $request */
$request = ArchiContainer::getInstance()->get('Request');
$dispatcher->dispatch(new RequestEvent($request));

var_dump(Env::getAll());