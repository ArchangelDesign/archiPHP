<?php

namespace Archi\Log;

use Archi\Config\ConfigProvider;
use Archi\Container\ArchiContainer;
use Archi\Container\ContainerException;
use Archi\Container\NotFoundException;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * Detailed debug information
     */
    public const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    public const INFO = 200;

    /**
     * Uncommon events
     */
    public const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    public const WARNING = 300;

    /**
     * Runtime errors
     */
    public const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    public const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    public const ALERT = 550;

    /**
     * Urgent alert.
     */
    public const EMERGENCY = 600;

    private array $context;

    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    public function emergency($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->dispatch(__FUNCTION__, $message, $context);
    }

    private function dispatch($level, $message, array $context = [])
    {
        $config = ArchiContainer::getConfig();

        if (!$config->hasLoggerConfig()) {
            $this->fallback($level, $message, array_merge($this->context, $context));
            return;
        }

        $listeners = $config->getLogHandlers();

        foreach ($listeners as $l) {
            $l->log($level, $message, array_merge($this->context, $context));
        }
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     */
    private function fallback($level, $message, array $context): void
    {
        error_log(Formatter::formatAsString($level, $message, $context));
    }
}
