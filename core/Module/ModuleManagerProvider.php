<?php

namespace Archi\Module;

use Archi\Container\ArchiContainer;
use Archi\Container\Wireable;

class ModuleManagerProvider implements Wireable
{

    public function getId(): string
    {
        return 'ModuleManager';
    }

    public function wire(ArchiContainer $container): object
    {
        return ModuleManager::getInstance();
    }
}
