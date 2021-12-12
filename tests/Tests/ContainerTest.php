<?php

namespace Tests;

use Archi\Container\ArchiContainer;
use Archi\Container\Binding;
use Archi\Container\ContainerException;
use Archi\Container\TestObjects\CannotBeAutowired;
use Archi\Container\TestObjects\NoDependencyClass;
use Archi\Container\TestObjects\OneDependencyAndNoTypeArgumentWithDefaultValue;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testAutowire()
    {
        ArchiContainer::reset();
        $container = ArchiContainer::getInstance();
        $autowired = $container->get(NoDependencyClass::class);
        $this->assertInstanceOf(NoDependencyClass::class, $autowired);
    }

    public function testWiringSingleton()
    {
        ArchiContainer::reset();
        $c = ArchiContainer::getInstance();
        $binding = new Binding(NoDependencyClass::class, NoDependencyClass::class, true);
        $c->register($binding);
        $class = $c->get(OneDependencyAndNoTypeArgumentWithDefaultValue::class);
        $this->assertInstanceOf(OneDependencyAndNoTypeArgumentWithDefaultValue::class, $class);
    }

    public function testCannotBeAutowired()
    {
        $this->expectException(ContainerException::class);
        ArchiContainer::reset();
        ArchiContainer::getInstance()->get(CannotBeAutowired::class);
    }
}
