<?php

namespace Archi\Helper;

class ArchiArray
{

    private array $subject;

    /**
     * @param array $subject
     */
    public function __construct(array $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Expects an array of strings and returns the same array
     * with trimmed strings. Keys are not preserved.
     *
     * @param string[] $input
     * @return string[]
     */
    public static function trim(array $input): array
    {
        $result = [];
        array_walk($input, function ($str) use (&$result) {
            $result[] = trim($str);
        });

        return $result;
    }

    /**
     * Trims the strings in given array preserving keys.
     * Recursive arrays will not be trimmed.
     *
     * @param array $input
     * @return array
     */
    public static function ktrim(array $input): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            $result[$key] = is_string($value) ? trim($value) : $value;
        }

        return $result;
    }

    /**
     * Trims the array recursively.
     *
     * @param array $input
     * @return array
     */
    public static function krtrim(array $input): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            $result[$key] = is_string($value) ? trim($value) : self::ktrim($value);
        }

        return $result;
    }

    public static function staticFetch(array $subject, string $path, $default = null)
    {
        if (strpos($path, '.') === false) {
            return $subject[$path] ?? $default;
        }
        $parts = explode('.', $path);
        $buffer = $subject;
        foreach ($parts as $part) {
            $buffer = $buffer[$part] ?? null;
            if (is_null($buffer)) {
                return $default;
            }
        }

        return $buffer;
    }

    /**
     * Returns the value of the array using dot notation.
     * If the value is not found, the default is returned.
     *
     * @param string $path
     * @param $default
     * @return array|mixed|null
     */
    public function fetch(string $path, $default = null)
    {
        return self::staticFetch($this->subject, $path, $default);
    }
}
