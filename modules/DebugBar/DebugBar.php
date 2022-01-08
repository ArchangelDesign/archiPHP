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
use DebugBar\RequestListener;

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
        // @FIXME: make this work
//        ArchiContainer::getDispatcher()->register(ArchiRequest::class, new RequestListener());
    }
}
