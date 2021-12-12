<?php

namespace Archi\Container\TestObjects;

use Archi\Container\ArchiContainer;
use Archi\Container\Wireable;

class SimpleProvider implements Wireable
{

    public function getId(): string
    {
        return CannotBeAutowired::class;
    }

    public function wire(ArchiContainer $container): object
    {
        return new CannotBeAutowired('test');
    }
}
