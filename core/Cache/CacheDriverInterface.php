<?php

namespace Archi\Cache;

use Psr\Cache\CacheItemInterface;

interface CacheDriverInterface
{

    public function fetch(string $key): CacheItemInterface;

    public function has(string $key): bool;

    public function clear(): bool;

    public function delete(string $key): bool;

    public function save(CacheItem $item): bool;
}
