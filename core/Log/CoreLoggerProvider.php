<?php

namespace Archi\Log;

use Archi\Container\ArchiContainer;
use Archi\Container\Wireable;

class CoreLoggerProvider implements Wireable
{

    public function getId(): string
    {
        return 'CoreLogger';
    }

    public function wire(ArchiContainer $container): object
    {
        return new Logger(['location' => 'core']);
    }
}
