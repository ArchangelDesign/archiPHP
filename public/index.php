<?php

use Archi\Container\ArchiContainer;
use Archi\Dispatcher\Event\RequestEvent;
use Archi\Environment\Env;
use Archi\Log\Logger;

// Autoload
require __DIR__ . '/../vendor/autoload.php';

// Initialize
Env::initialize(dirname(__DIR__));
ArchiContainer::getInstance()->get('ModuleManager')->preloadModules();

// Logger
$logger = new Logger(['location' => 'bootstrap']);
$logger->debug('Staring application');

// Dispatch request
$dispatcher = ArchiContainer::getInstance()->get('Dispatcher');
/** @var \Archi\Http\Request\ArchiRequest $request */
$request = ArchiContainer::getInstance()->get('Request');
$dispatcher->dispatch(new RequestEvent($request));

// Done.
var_dump(ArchiContainer::getInstance()->get('ModuleManager')->getPreloadedModules());