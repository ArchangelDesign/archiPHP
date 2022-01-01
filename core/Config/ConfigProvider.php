<?php

namespace Archi\Config;

use Archi\Log\LogHandler;

class ConfigProvider
{
    /** @var LogHandler[] */
    private array $handlers = [];
    private ?CacheConfig $cacheConfig;

    public function hasLoggerConfig(): bool
    {
        return !empty($this->handlers);
    }

    /**
     * @return LogHandler[]
     */
    public function getLogHandlers(): array
    {
        return $this->handlers;
    }

    public function registerLogHandler(LogHandler $handler)
    {
        $this->handlers[] = $handler;
    }

    public function hasCacheConfig(): bool
    {
        return !is_null($this->cacheConfig);
    }
}
