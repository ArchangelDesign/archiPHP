<?php

namespace Archi\Helper;

class Nomenclature
{
    /**
     * Returns the string converted to pascal case.
     * input: snake_case  output: SnakeCase
     *
     * @param string $input
     * @return string
     */
    public static function toPascalCase(string $input): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $input)));
    }

    public static function toCamelCase(string $input): string
    {
        return lcfirst(self::toPascalCase($input));
    }
}
