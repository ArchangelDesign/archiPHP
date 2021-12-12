<?php

namespace Tests;

use Archi\Container\ArchiContainer;
use Archi\Container\Binding;
use Archi\Container\TestObjects\TestClass1;
use Archi\Container\TestObjects\TestClass2;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testAutowire()
    {
        ArchiContainer::reset();
        $container = ArchiContainer::getInstance();
        $autowired = $container->get(TestClass1::class);
        $this->assertInstanceOf(TestClass1::class, $autowired);
    }

    public function testWiringSingleton()
    {
        ArchiContainer::reset();
        $c = ArchiContainer::getInstance();
        $binding = new Binding(TestClass1::class, TestClass1::class, true);
        $c->register($binding);
        $class = $c->get(TestClass2::class);
        $this->assertInstanceOf(TestClass2::class, $class);
    }
}
