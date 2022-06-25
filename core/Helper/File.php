<?php

namespace Archi\Helper;

use Archi\Environment\Env;

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

    /**
     * Returns file contents no larger than the buffer.
     * Comments and `use` instructions will be omitted.
     * If the `use` statement ends with new line character,
     * it will also be omitted.
     *
     * @param string $path
     * @param int $bufferSize
     * @return string
     */
    public static function getContentsWithoutPhpComments(string $path, int $bufferSize = 1024): string
    {
        $STATE_ZERO = 0;
        $STATE_SLASH = 1;
        $STATE_ONE_LINE_COMMENT = 2;
        $STATE_LONG_COMMENT = 3;
        $STATE_LONG_COMMENT_ASTRIX = 4;
        $STATE_USE = 5;
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
                    if ($char == 'u') {
                        $tempChar = $char . self::read($f, 2);
                        if ($tempChar == 'use') {
                            $state = $STATE_USE;
                            break;
                        }
                        self::seekFromCurrentPosition($f, -2);
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
                case $STATE_USE:
                    if ($char == ';') {
                        $tempChar = self::read($f, 1);
                        if ($tempChar != "\n") {
                            self::seekFromCurrentPosition($f, -1);
                        }
                        $state = $STATE_ZERO;
                        break;
                    }
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

    public static function close($handle, bool $orThrow = true): bool
    {
        if (!is_resource($handle)) {
            if (!$orThrow) {
                return false;
            }
            throw new \RuntimeException('Invalid file handle. Cannot close the file.');
        }
        $result = fclose($handle);
        if (!$result && $orThrow) {
            throw new \RuntimeException('Cannot close file.');
        }

        return $result;
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

    public static function tell($handle): int
    {
        return ftell($handle);
    }

    public static function seekFromCurrentPosition($handle, int $offset): int
    {
        return fseek($handle, $offset, SEEK_CUR);
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

    public static function buildPath(string $directory, string $fileName)
    {
        if (!ArchiString::staticEndsWith($directory, Env::ds())) {
            $directory .= Env::ds();
        }

        return $directory . $fileName;
    }

    public static function writeContents(string $path, string $contents): bool
    {
        $f = self::openForWriting($path);
        self::write($f, $contents, strlen($contents));
        return self::close($f);
    }

    /**
     * @param string $filePath
     * @return resource
     */
    public static function openForWriting(string $filePath)
    {
        $handle = fopen($filePath, 'w');
        if (!$handle) {
            throw new \RuntimeException('Cannot open file ' . $filePath . ' for writing.');
        }

        return $handle;
    }

    /**
     * @param string $filePath
     * @return resource
     */
    public static function openForReadingAndWriting(string $filePath)
    {
        $handle = fopen($filePath, 'r+');
        if (!$handle) {
            throw new \RuntimeException('Cannot open file ' . $filePath . ' for r/w.');
        }

        return $handle;
    }

    /**
     * @param resource $handle
     * @param string $contents
     * @param int $length
     */
    public static function write($handle, string $contents, int $length)
    {
        fwrite($handle, $contents, $length);
    }

    public static function remove($fileName): bool
    {
        return unlink($fileName);
    }
}
