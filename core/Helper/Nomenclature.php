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

    public static function isValidClassName(string $className): bool
    {
        $parts = explode('\\', $className);

        if (count($parts) == 1) {
            return self::isValidPascalCase($className);
        }

        foreach ($parts as $part) {
            if (!self::isValidPascalCase($part)) {
                return false;
            }
        }

        return true;
    }

    public static function isValidPascalCase(string $className): bool
    {
        return $className == self::toPascalCase($className);
    }
}
