<?php

namespace Archi\Helper;

class File
{
    public static function getContents(string $path, int $bufferSize = 1024): string
    {
        $buffer = "";
        $currentBufferSize = 0;
        $f = self::openForReading($path);

        while ($currentBufferSize < $bufferSize && !self::isEof($f)) {
            $buffer .= self::read($f, 1);
            $currentBufferSize++;
        }

        self::close($f);

        return $buffer;
    }

    public static function getContentsWithoutPhpComments(string $path, int $bufferSize = 1024): string
    {
        $STATE_ZERO = 0;
        $STATE_SLASH = 1;
        $STATE_ONE_LINE_COMMENT = 2;
        $STATE_LONG_COMMENT = 3;
        $STATE_LONG_COMMENT_ASTRIX = 4;
        $buffer = "";
        $state = $STATE_ZERO;
        $currentBufferSize = 0;

        $f = self::openForReading($path);
        while ($currentBufferSize < $bufferSize && !self::isEof($f)) {
            $char = self::read($f, 1);
            switch ($state) {
                case $STATE_ZERO:
                    if ($char == '/') {
                        $state = $STATE_SLASH;
                        break;
                    }
                    $buffer .= $char;
                    $currentBufferSize++;
                    break;
                case $STATE_SLASH:
                    if ($char == '/') {
                        $state = $STATE_ONE_LINE_COMMENT;
                        break;
                    }
                    if ($char == '*') {
                        $state = $STATE_LONG_COMMENT;
                        break;
                    }
                    break;
                case $STATE_LONG_COMMENT:
                    if ($char == '*') {
                        $state = $STATE_LONG_COMMENT_ASTRIX;
                        break;
                    }
                    break;
                case $STATE_ONE_LINE_COMMENT:
                    if ($char == "\n") {
                        $state = $STATE_ZERO;
                        break;
                    }
                    break;
                case $STATE_LONG_COMMENT_ASTRIX:
                    if ($char == '/') {
                        $state = $STATE_ZERO;
                        break;
                    }
                    break;
            }
        }
        self::close($f);

        return $buffer;
    }

    /**
     * @param string $path
     * @return resource
     */
    public static function openForReading(string $path)
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('Cannot open file ' . $path . ' for reading.');
        }

        return $handle;
    }

    public static function close($handle, bool $orThrow = true)
    {
        if (!is_resource($handle)) {
            if (!$orThrow) {
                return;
            }
            throw new \RuntimeException('Invalid file handle. Cannot close the file.');
        }
        fclose($handle);
    }

    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    public static function read($handle, int $length)
    {
        if (!is_resource($handle)) {
            throw new \RuntimeException('Invalid file handle provided to `read` method.');
        }
        return fread($handle, $length);
    }

    public static function isEof($handle): bool
    {
        return feof($handle);
    }

    public static function getExtension(string $subject): string
    {
        return substr($subject, strrpos($subject, '.') + 1);
    }

    public static function noExtension($fileName): string
    {
        $ext = self::getExtension($fileName);
        return str_replace('.' . $ext, '', $fileName);
    }
}
