<?php

namespace Archi\Cache\Driver;

use Archi\Cache\CacheDriverInterface;
use Archi\Cache\CacheItem;
use Archi\Container\ArchiContainer;
use Archi\Helper\Directory;
use Archi\Helper\File;
use Psr\Cache\CacheItemInterface;

class FilesystemDriver implements CacheDriverInterface
{
    public const MAX_BUFFER_SIZE = 99999;
    private string $directory;

    /**
     * FilesystemDriver constructor.
     * @param string $directory
     */
    public function __construct(?string $directory = null)
    {
        if (is_null($directory)) {
            $directory = ArchiContainer::getCache()->getConfig()->getHost();
        }
        if (empty($directory) || !Directory::exists($directory) || !Directory::isWritable($directory)) {
            throw new \RuntimeException('Invalid cache directory provided. ' . $directory);
        }
        $this->directory = $directory;
    }

    public function fetch(string $key): CacheItemInterface
    {
        return unserialize(File::getContents(File::buildPath($this->directory, $key), self::MAX_BUFFER_SIZE));
    }

    public function has(string $key): bool
    {
        return File::exists(File::buildPath($this->directory, $key));
    }

    public function clear(): bool
    {
        $allFiles = Directory::getFiles($this->directory);
        foreach ($allFiles as $f) {
            File::remove(File::buildPath($this->directory, $f));
        }
        return true;
    }

    public function delete(string $key): bool
    {
        File::remove(File::buildPath($this->directory, $key));
    }

    public function save(CacheItem $item): bool
    {
        return File::writeContents(File::buildPath($this->directory, $item->getKey()), serialize($item));
    }
}
