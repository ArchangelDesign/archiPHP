<?php

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\Event\RequestEvent;
use Archi\Environment\Env;
use Archi\Log\Logger;

// Autoload
require __DIR__ . '/../vendor/autoload.php';

// Initialize
Env::initialize(dirname(__DIR__));
ArchiContainer::getModuleManager()->bootstrap();

// Logger
$logger = new Logger(['location' => 'bootstrap']);
$logger->debug('Staring application');

// Dispatch request
$dispatcher = ArchiContainer::getDispatcher();
$dispatcher->dispatch(new RequestEvent(ArchiContainer::getRequest()));

// Done.
var_dump(ArchiContainer::getModuleManager()->getDescriptors());
