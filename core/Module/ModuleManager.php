<?php

namespace Archi\Module;

use Archi\Environment\Env;
use Archi\Helper\Directory;

class ModuleManager
{
    private $autloadModules = false;

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

    private function preloadModules()
    {
        $path = $this->getModuleDirectoryPath();
        if (!is_dir($path)) {
            throw new \RuntimeException('Cannot load modules. Directory ' . $path . ' is not valid.');
        }
        $modules = Directory::getDirectoryList($path);
        foreach ($modules as $moduleName) {
            $this->preloadModule($moduleName);
        }
    }

    /**
     * @return string
     */
    private function getModuleDirectoryPath(): string
    {
        return Env::cwd() . '/' . $this->getModuleDirectoryName();
    }

    private function preloadModule(string $moduleName)
    {
        $directory = $this->getModuleDirectoryPath() . '/' . $moduleName;

        if (!is_dir($directory)) {
            return null;
        }

        $moduleFileName = $moduleName . '.php';
        if (file_exists($this->getModuleJsonFilePath($directory))) {
            $moduleJson = json_decode(file_get_contents($this->getModuleJsonFilePath($directory)));
            $module = $this->getModuleFromJson($directory, $moduleJson);
            if ($this->isAutoloadEnabled()) {
                $this->loadModule($module);
            }
        }
        $moduleFilePath = $directory . '/' . $moduleFileName;
        if (!file_exists($moduleFilePath)) {
            return null;
        }
    }

    private function getModuleJsonFilePath(string $directory): string
    {
        return $directory . '/module.json';
    }

    private function isAutoloadEnabled(): bool
    {
        return $this->autloadModules;
    }

    private function getModuleFromJson(string $directory, \stdClass $moduleJson): Module
    {
        if (!property_exists($moduleJson, 'description')) {
            $moduleJson->description = null;
        }
        if (!property_exists($moduleJson, 'minimumCoreVersion')) {
            $moduleJson->minimumCoreVersion = "0";
        }
        if (!property_exists($moduleJson, 'author')) {
            $moduleJson->author = null;
        }

        return new Module(
            $moduleJson->fileName,
            $moduleJson->name,
            $moduleJson->description,
            $moduleJson->minimumCoreVersion,
            $moduleJson->title,
            $moduleJson->version,
            $moduleJson->author,
            $directory
        );
    }

    private function loadModule(Module $module)
    {
        require_once $module->getLoadFile();
    }
}
