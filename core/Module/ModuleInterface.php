<?php

namespace Archi\Module;

use Archi\Container\ArchiContainer;

interface ModuleInterface
{
    public function getClassMap(): ClassMapInterface;

    public function bootstrap(ArchiContainer $container);
}
