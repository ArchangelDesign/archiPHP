<?php

namespace Tests;

use Archi\Cache\ArchiCache;
use Archi\Container\ArchiContainer;
use PHPUnit\Framework\TestCase;

class ArchiCacheTest extends TestCase
{
    public function testCacheSanity()
    {
        $this->markTestSkipped('WIP');
        return;
        $cache = ArchiContainer::getCache();
        $this->assertInstanceOf(ArchiCache::class, $cache);
    }
}
