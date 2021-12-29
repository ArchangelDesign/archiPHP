<?php

namespace Tests;

use Archi\Module\SimpleClassMap;
use PHPUnit\Framework\TestCase;

class ClassMapTest extends TestCase
{
    public function testSimpleClassMap()
    {
        $cm = new SimpleClassMap([self::class => __FILE__]);
        $this->assertTrue($cm->has(self::class));
        $this->assertEquals(__FILE__, $cm->getLocation(self::class));
    }
}
