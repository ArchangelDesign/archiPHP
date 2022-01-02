<?php

namespace Tests;

use Archi\Cache\ArchiCache;
use Archi\Cache\CacheItem;
use Archi\Config\CacheConfig;
use Archi\Container\ArchiContainer;
use Archi\Environment\Env;
use Archi\Helper\Directory;
use PHPUnit\Framework\TestCase;

class ArchiCacheTest extends TestCase
{
    private $cache = null;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Directory::exists($this->getCacheDir())) {
            Directory::create($this->getCacheDir());
        }
    }

    public function testCacheSanity()
    {
        $cache = $this->getArchiCache();
        $this->assertInstanceOf(ArchiCache::class, $cache);
    }

    public function testCacheIsWorking()
    {
        $cache = $this->getArchiCache();
        $cache->save(new CacheItem('test-key', 'test-string-value'));
        $this->assertEquals('test-string-value', $cache->getItem('test-key')->get());
    }


    private function getCacheDir(): string
    {
        return Env::getTempDir() . Env::ds() . 'archi-cache';
    }

    /**
     * @return CacheConfig
     */
    private function getCacheConfig(): CacheConfig
    {
        return new CacheConfig('\Archi\Cache\Driver\FilesystemDriver', $this->getCacheDir());
    }

    /**
     * @return ArchiCache
     */
    private function getArchiCache(): ArchiCache
    {
        if (ArchiContainer::getInstance()->hasInstance('Cache')) {
            return ArchiContainer::getCache();
        }
        $this->cache = new ArchiCache($this->getCacheConfig());
        ArchiContainer::getInstance()->injectInstance('Cache', $this->cache);

        return $this->cache;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Directory::removeAllFiles($this->getCacheDir());
        Directory::remove($this->getCacheDir());
    }
}
