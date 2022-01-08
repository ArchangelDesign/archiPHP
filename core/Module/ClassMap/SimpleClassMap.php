<?php

namespace Archi\Module\ClassMap;

use Archi\Module\AbstractClassMap;

class SimpleClassMap extends AbstractClassMap
{
    public function __construct(array $map = [])
    {
        if (empty($map)) {
            return;
        }

        foreach ($map as $className => $location) {
            $this->push($className, $location);
        }
    }
}
