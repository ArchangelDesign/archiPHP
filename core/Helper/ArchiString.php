<?php

namespace Archi\Helper;

class ArchiString
{
    private string $content;

    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }


    public static function staticEndsWith(string $haystack, string $needle): bool
    {
        if (function_exists('str_ends_with')) {
            return str_ends_with($haystack, $needle);
        }
        if (strlen($haystack) < $needle) {
            return false;
        }
        return substr($haystack, strlen($haystack) - strlen($needle), strlen($needle)) === $needle;
    }

    public function endsWith(string $needle): bool
    {
        return self::staticEndsWith($this->content, $needle);
    }

    public function str(): string
    {
        return $this->content;
    }

    public function toPascal(): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $this->content)));
    }

    public function toCamel(): string
    {
        return lcfirst($this->toPascal());
    }
}
