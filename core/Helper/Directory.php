<?php

namespace Archi\Helper;

class Directory
{
    public static function getDirectoryList(string $directory): array
    {
        $dir = new \DirectoryIterator($directory);
        $result = [];
        foreach ($dir as $subject) {
            if (!$subject->isDir()) {
                continue;
            }
            if (in_array($subject->getFilename(), ['.', '..'])) {
                continue;
            }
            $result[] = $subject->getFilename();
        }

        return $result;
    }

    public static function isValid(string $path): bool
    {
        return is_dir($path);
    }
}
