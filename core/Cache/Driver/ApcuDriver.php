<?php

namespace Archi\Cache\Driver;

use Archi\Cache\CacheDriverInterface;
use Archi\Cache\CacheItem;
use Psr\Cache\CacheItemInterface;

class ApcuDriver implements CacheDriverInterface
{

    /**
     * ApcuDriver constructor.
     */
    public function __construct()
    {
        if (!function_exists('apcu_fetch')) {
            throw new \RuntimeException('ext-apcu is not installed. Cannot use this driver.');
        }
    }

    public function fetch(string $key): CacheItemInterface
    {
        return new CacheItem($key, apcu_fetch($key));
    }

    public function has(string $key): bool
    {
        return apcu_exists($key);
    }

    public function clear(): bool
    {
        return apcu_clear_cache();
    }

    public function delete(string $key): bool
    {
        return apcu_delete($key);
    }

    public function save(CacheItem $item): bool
    {
        return apcu_store($item->getKey(), $item->get(), $item->getExpiration());
    }
}
