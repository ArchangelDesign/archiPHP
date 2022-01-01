<?php

namespace Archi\Config;

use Archi\Environment\Env;

class CacheConfig
{
    private static ?string $driver;
    private static ?string $host;
    private static ?string $username;
    private static ?string $password;

    public static function getDriver(): string
    {
        if (!is_null(self::$driver)) {
            return self::$driver;
        }

        self::$driver = Env::get('CACHE_DRIVER', 'FileSystemCache');
        return self::$driver;
    }
}
