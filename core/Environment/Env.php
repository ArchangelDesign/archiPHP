<?php

namespace Archi\Environment;

use Archi\Helper\Directory;

class Env
{
    public const DS = '/';
    private static bool $initialized = false;

    private static ?string $workingDirectory;
    private static $variables = [];
    private static bool $isInTesting = false;

    public static function initialize(string $workingDirectory, bool $isInTesting = false)
    {
        if (!Directory::exists($workingDirectory)) {
            throw new \RuntimeException('Invalid working directory');
        }
        if (self::$initialized) {
            throw new \RuntimeException('Already initialized');
        }
        self::$workingDirectory = $workingDirectory;
        self::$initialized = true;
        self::$isInTesting = $isInTesting;
        self::loadEnvFile();
    }

    public static function cwd(): string
    {
        if (!self::$initialized) {
            throw new \RuntimeException('Environment not initialized');
        }
        return self::$workingDirectory;
    }

    private static function loadEnvFile()
    {
        if (!self::envFileExists()) {
            throw new \RuntimeException('No environment file provided');
        }
        $lines = self::getLines(self::getEnvFilePath());
        foreach ($lines as $line) {
            self::processLine($line);
        }
    }

    private static function envFileExists(): bool
    {
        return file_exists(self::getEnvFilePath());
    }

    private static function getEnvFilePath()
    {
        return self::$workingDirectory . '/.env';
    }

    private static function getLines(string $envFilePath)
    {
        $contents = file_get_contents($envFilePath);

        return explode("\n", $contents);
    }

    private static function processLine(string $line)
    {
        if (strpos(trim($line), '#') === 0) {
            return;
        }
        if (strpos($line, '=') === false) {
            return;
        }
        list($name, $value) = explode('=', $line);
        self::push(trim($name), trim($value));
    }

    private static function push(string $name, string $value)
    {
        self::$variables[$name] = $value;
    }

    public static function has(string $name): bool
    {
        return array_key_exists($name, self::$variables);
    }

    public static function get(string $name, ?string $default = null): ?string
    {
        if (!self::has($name)) {
            return $default;
        }

        return self::$variables[$name];
    }

    public static function getAll(): array
    {
        return self::$variables;
    }

    public static function getBool(string $name): bool
    {
        if (!self::has($name)) {
            return false;
        }

        if (self::get($name) === 'true') {
            return true;
        }

        if (self::get($name) === 'false') {
            return false;
        }

        return boolval(self::get($name));
    }

    public static function isInitialized()
    {
        return self::$initialized;
    }

    public static function ds()
    {
        return DIRECTORY_SEPARATOR;
    }

    public static function isInTesting(): bool
    {
        return self::$isInTesting;
    }

    public static function getTempDir(): string
    {
        return sys_get_temp_dir();
    }
}
