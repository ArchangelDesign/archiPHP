<?php

namespace Archi\Helper;

class Directory
{
    public static function getDirectoryList(string $directory): array
    {
        return self::getSubjects($directory, true, false);
    }

    public static function exists(string $path): bool
    {
        return is_dir($path);
    }

    private static function getSubjects(
        string $directory,
        bool $includeDirectories = true,
        bool $includeFiles = true,
        array $extFilter = []
    ): array {
        $dir = new \DirectoryIterator($directory);
        $result = [];
        foreach ($dir as $subject) {
            if ($subject->isDir() && !$includeDirectories) {
                continue;
            }
            if ($subject->isFile() && !$includeFiles) {
                continue;
            }
            if (in_array($subject->getFilename(), ['.', '..'])) {
                continue;
            }
            if (empty($filter)) {
                $result[] = $subject->getFilename();
                continue;
            }
            if (!in_array(File::getExtension($subject->getFilename()), $extFilter)) {
                continue;
            }
            $result[] = $subject->getFilename();
        }

        return $result;
    }

    public static function getFiles(string $directory): array
    {
        return self::getSubjects($directory, false, true);
    }

    public static function getPhpFiles(string $directory)
    {
        return self::getSubjects($directory, false, true, ['php']);
    }

    public static function isWritable(string $directory): bool
    {
        return is_writable($directory);
    }

    public static function create(string $directory): bool
    {
        return mkdir($directory);
    }
}
