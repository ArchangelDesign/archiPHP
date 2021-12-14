<?php

namespace Archi\Log;

use Archi\Container\ArchiContainer;
use Archi\Container\Wireable;

class Provider implements Wireable
{

    public function getId(): string
    {
        return 'Logger';
    }

    public function wire(ArchiContainer $container): object
    {
        return new Logger(['location' => 'core']);
    }
}
