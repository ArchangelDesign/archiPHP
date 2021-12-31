<?php

namespace Archi\Helper;

class Arr
{
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
}
