<?php

namespace Archi\Module\ClassMap;

use Archi\Helper\Directory;
use Archi\Helper\File;
use Archi\Helper\Nomenclature;
use Archi\Module\ModuleDescriptor;

class PsrClassMap extends SimpleClassMap
{
    public function __construct(ModuleDescriptor $descriptor, string $directory)
    {
        $dir = $descriptor->getDirectory() . DIRECTORY_SEPARATOR . $directory;
        $map = $this->loadClassFiles($dir, $descriptor->getNamespace());
        parent::__construct($map);
    }

    private function loadClassFiles(string $directory, string $namespace): array
    {
        $result = [];
        $directories = Directory::getDirectoryList($directory);
        foreach ($directories as $dir) {
            $ns = $namespace . '\\' . $dir;
            $d = $directory . DIRECTORY_SEPARATOR . $dir;
            $result = array_merge($result, $this->loadClassFiles($d, $ns));
        }
        $phpFiles = Directory::getPhpFiles($directory);
        foreach ($phpFiles as $fileName) {
            $className = File::noExtension($fileName);
            if (!Nomenclature::isValidClassName($className)) {
                continue;
            }
            $fullClassName = $namespace . '\\' . $className;
            $result[$fullClassName] = $directory . DIRECTORY_SEPARATOR . $fileName;
        }

        return $result;
    }
}
