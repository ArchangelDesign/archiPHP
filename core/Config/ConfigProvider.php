<?php

namespace Archi\Config;

use Archi\Log\LogHandler;

class ConfigProvider
{
    /** @var LogHandler[] */
    private $handlers = [];

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
}
