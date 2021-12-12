<?php

namespace Tests;

use Archi\Container\ArchiContainer;
use Archi\Container\Binding;
use Archi\Container\ContainerException;
use Archi\Container\TestObjects\CannotBeAutowired;
use Archi\Container\TestObjects\NoDependencyClass;
use Archi\Container\TestObjects\OneDependencyAndNoTypeArgumentWithDefaultValue;
use Archi\Container\TestObjects\SingletonTest;
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

    public function testSingleton()
    {
        ArchiContainer::reset();
        $b = new Binding(SingletonTest::class, SingletonTest::class, true);
        ArchiContainer::getInstance()->register($b);
        /** @var SingletonTest $i */
        $i = ArchiContainer::getInstance()->get(SingletonTest::class);
        $i->inc();
        /** @var SingletonTest $i2 */
        $i2 = ArchiContainer::getInstance()->get(SingletonTest::class);
        $i2->inc();
        $i2->inc();
        $i2->inc();
        $this->assertEquals($i->getCounter(), $i2->getCounter());
        $this->assertEquals(4, $i2->getCounter());
    }
}
