<?php

namespace Archi\Helper;

class Nomenclature
{
    public static function toCamelCase(string $input): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $input)));
    }

    public static function smartToCamelCase(string $input): string
    {
        if (strpos($input, ' ') === false && strpos($input, '-') === false && strpos($input, '_') === false) {
            return $input;
        }

        return self::toCamelCase($input);
    }
}
