<?php

namespace Archi\Helper;

class ArchiString
{
    public static function endsWith(string $haystack, string $needle): bool
    {
        if (function_exists('str_ends_with')) {
            return str_ends_with($haystack, $needle);
        }
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
