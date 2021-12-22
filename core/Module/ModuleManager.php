<?php

namespace Archi\Module;

use Archi\Environment\Env;
use Archi\Helper\Directory;

class ModuleManager
{
    public function __construct()
    {
        if (!Env::isInitialized()) {
            throw new \RuntimeException('Environment has not been initialized.');
        }
    }

    public function getModuleDirectoryName()
    {
        return Env::get('ARCHI_MODULE_DIR', 'modules');
    }

    private function loadModules()
    {
        $path = $this->getModuleDirectoryPath();
        if (!is_dir($path)) {
            throw new \RuntimeException('Cannot load modules. Directory ' . $path . ' is not valid.');
        }
        $modules = Directory::getDirectoryList($path);
    }

    /**
     * @return string
     */
    private function getModuleDirectoryPath(): string
    {
        return Env::cwd() . '/' . $this->getModuleDirectoryName();
    }
}
