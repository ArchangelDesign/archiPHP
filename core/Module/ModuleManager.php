<?php

namespace Archi\Module;

use Archi\Environment\Env;

class ModuleManager
{
    public function __construct()
    {
        if (!Env::isInitialized()) {
            throw new \RuntimeException('Environment has not been initialized.');
        }
    }
}
