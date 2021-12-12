<?php

namespace Archi\Log;

use Archi\Log\Exception\InvalidStream;

class LogListener
{
    private $stream;

    private $level;

    /**
     * LogListener constructor.
     *
     * @param string|resource $stream
     * @param $level
     * @throws InvalidStream
     */
    public function __construct($stream, $level = Logger::INFO)
    {
        if (!is_resource($stream) && !is_string($stream)) {
            throw new InvalidStream('Log stream must be a string or a resource.');
        }
        $this->level = $level;

        if (is_resource($stream)) {
            $this->stream = $stream;
            return;
        }

        $this->stream = self::canonicalizePath($stream);
    }

    /**
     * @param string $streamUrl
     * @return string
     * @throws InvalidStream
     */
    public static function canonicalizePath(string $streamUrl): string
    {
        $prefix = '';
        if ('file://' === substr($streamUrl, 0, 7)) {
            $streamUrl = substr($streamUrl, 7);
            $prefix = 'file://';
        }

        // other type of stream, not supported
        if (false !== strpos($streamUrl, '://')) {
            throw new InvalidStream('Invalid log stream provided: ' . $streamUrl);
        }

        // already absolute
        if (
            substr($streamUrl, 0, 1) === '/'
            || substr($streamUrl, 1, 1) === ':'
            || substr($streamUrl, 0, 2) === '\\\\'
        ) {
            return $prefix . $streamUrl;
        }

        $streamUrl = getcwd() . '/' . $streamUrl;

        return $prefix . $streamUrl;
    }

    public function log($level, $message, $context)
    {
        if ($this->level > $level) {
            return;
        }

        $this->write(Formatter::formatAsString($level, $message, $context));
    }

    private function write(string $formatted)
    {
        if (!is_resource($this->stream)) {
            $this->stream = fopen($this->stream, 'w');
        }

        fwrite($this->stream, $formatted);
    }
}
