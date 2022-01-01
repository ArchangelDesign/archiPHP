<?php

namespace Archi\Cache;

use Archi\Cache\Driver\ApcuDriver;
use Archi\Container\ArchiContainer;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class ArchiCache implements CacheItemPoolInterface
{
    private CacheDriverInterface $driver;

    /** @var CacheItemInterface[] */
    private array $saveQueue = [];

    /**
     * ArchiCache constructor.
     */
    public function __construct()
    {
        if (!ArchiContainer::getConfig()->hasCacheConfig()) {
            $this->driver = new ApcuDriver();
        }
        // @TODO: Load cache config
    }

    public function getItem($key)
    {
        return $this->driver->fetch($key);
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
        return $this->driver->has($key);
    }

    public function clear()
    {
        return $this->driver->clear();
    }

    public function deleteItem($key)
    {
        return $this->driver->delete($key);
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
        return $this->driver->save($item);
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
}
