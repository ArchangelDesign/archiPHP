<?php

namespace Archi\Cache;

use Archi\Cache\Driver\ApcuDriver;
use Archi\Config\CacheConfig;
use Archi\Container\ArchiContainer;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class ArchiCache implements CacheItemPoolInterface
{
    private ?CacheDriverInterface $driver = null;

    /** @var CacheItemInterface[] */
    private array $saveQueue = [];
    /**
     * @var CacheConfig
     */
    private CacheConfig $config;

    /**
     * ArchiCache constructor.
     * @param CacheConfig $config
     */
    public function __construct(CacheConfig $config)
    {
        $this->config = $config;
    }

    public function getDriver(): CacheDriverInterface
    {
        if (is_null($this->driver)) {
            $this->driver = ArchiContainer::getInstance()->get($this->config->getDriver());
        }

        return $this->driver;
    }

    public function getItem($key)
    {
        return $this->getDriver()->fetch($key);
    }

    public function getItems(array $keys = array())
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->getItem($key);
        }

        return $result;
    }

    public function hasItem($key)
    {
        return $this->getDriver()->has($key);
    }

    public function clear()
    {
        return $this->getDriver()->clear();
    }

    public function deleteItem($key)
    {
        return $this->getDriver()->delete($key);
    }

    public function deleteItems(array $keys)
    {
        $result = true;
        foreach ($keys as $key) {
            if (!$this->deleteItem($key)) {
                $result = false;
            }
        }

        return $result;
    }

    public function save(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            $item = new CacheItem($item->getKey(), $item->get());
        }
        return $this->getDriver()->save($item);
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        $this->saveQueue[] = $item;
    }

    public function commit()
    {
        foreach ($this->saveQueue as $i) {
            $this->save($i);
        }
        $this->saveQueue = [];
    }

    public function getConfig(): CacheConfig
    {
        return $this->config;
    }
}
