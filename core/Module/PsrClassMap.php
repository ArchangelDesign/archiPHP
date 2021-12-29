<?php

namespace Archi\Module;

use Archi\Helper\Directory;
use Archi\Helper\File;
use Archi\Helper\Nomenclature;

class PsrClassMap extends SimpleClassMap
{
    public function __construct(ModuleDescriptor $descriptor, string $directory)
    {
        // @TODO: shallow load for now, only files are processed...
        $map = $this->loadClassFiles($directory, $descriptor->getNamespace());
        parent::__construct($map);
    }

    private function loadClassFiles(string $directory, string $namespace): array
    {
        $phpFiles = Directory::getPhpFiles($directory);
        $result = [];
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
