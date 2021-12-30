<?php
/*
 * Debug Bar
 * Archi PHP Framework Module
 */

namespace Archi\Debug;

use Archi\Container\ArchiContainer;
use Archi\Module\AbstractClassMap;
use Archi\Module\ClassMapInterface;
use Archi\Module\ModuleInterface;
use Archi\Module\PsrClassMap;
use Archi\Module\SimpleClassMap;

class DebugBar implements ModuleInterface
{

    public function getClassMap(): ClassMapInterface
    {
        return new PsrClassMap(
            ArchiContainer::getModuleManager()->getModuleDescriptor('DebugBar'),
            'src'
        );
    }
}
