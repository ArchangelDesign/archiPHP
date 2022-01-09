<?php
/*
 * Debug Bar
 * Archi PHP Framework Module
 */

namespace Archi\Debug;

use Archi\Container\ArchiContainer;
use Archi\Http\Request\ArchiRequest;
use Archi\Module\ClassMap\PsrClassMap;
use Archi\Module\ClassMapInterface;
use Archi\Module\ModuleInterface;

class DebugBar implements ModuleInterface
{

    public function getClassMap(): ClassMapInterface
    {
        return new PsrClassMap(
            ArchiContainer::getModuleManager()->getModuleDescriptor('DebugBar'),
            'src'
        );
    }

    public function bootstrap(ArchiContainer $container)
    {
        ArchiContainer::getDispatcher()->register(ArchiRequest::class, new RequestListener());
    }
}
