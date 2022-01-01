<?php

namespace Archi\Cache;

use Archi\Container\ArchiContainer;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class ArchiCache implements CacheItemPoolInterface
{
    private CacheDriverInterface $driver;

    /**
     * ArchiCache constructor.
     */
    public function __construct()
    {
        ArchiContainer::getConfig()->hasCacheConfig();
    }

    public function getItem($key)
    {
        // TODO: Implement getItem() method.
    }

    public function getItems(array $keys = array())
    {
        // TODO: Implement getItems() method.
    }

    public function hasItem($key)
    {
        // TODO: Implement hasItem() method.
    }

    public function clear()
    {
        // TODO: Implement clear() method.
    }

    public function deleteItem($key)
    {
        // TODO: Implement deleteItem() method.
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
        // TODO: Implement save() method.
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        // TODO: Implement saveDeferred() method.
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }
}
