<?php

namespace Archi\Config;

class ConfigProvider
{
    private $logListeners = [];

    public function hasLoggerConfig(): bool
    {
        return !empty($this->logListeners);
    }

    /**
     * @return array
     */
    public function getLogListeners(): array
    {
        return $this->logListeners;
    }
}
