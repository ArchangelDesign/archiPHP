<?php

namespace Archi\Module;

use Archi\Environment\Env;
use Archi\Helper\Directory;
use Archi\Helper\Nomenclature;

class ModuleManager
{
    private $autloadModules = false;

    private static ?ModuleManager $instance = null;

    private static bool $modulesPreLoaded = false;

    private $preLoadedModules = [];

    private function __construct()
    {
        if (!Env::isInitialized()) {
            throw new \RuntimeException('Environment has not been initialized.');
        }
    }

    public static function getInstance(): ModuleManager
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getModuleDirectoryName()
    {
        return Env::get('ARCHI_MODULE_DIR', 'modules');
    }

    public function preloadModules()
    {
        if (self::$modulesPreLoaded) {
            return;
        }
        $path = $this->getModuleDirectoryPath();
        if (!is_dir($path)) {
            throw new \RuntimeException('Cannot load modules. Directory ' . $path . ' is not valid.');
        }
        $modules = Directory::getDirectoryList($path);

        foreach ($modules as $moduleName) {
            $this->preloadModule($moduleName);
        }

        self::$modulesPreLoaded = true;
    }

    /**
     * @return string
     */
    private function getModuleDirectoryPath(): string
    {
        return Env::cwd() . '/' . $this->getModuleDirectoryName();
    }

    private function preloadModule(string $directoryName): ?Module
    {
        $directory = $this->getModuleDirectoryPath() . '/' . $directoryName;

        if (!is_dir($directory)) {
            return null;
        }

        if (!file_exists($this->getModuleJsonFilePath($directory))) {
            return null;
        }

        $moduleJson = json_decode(file_get_contents($this->getModuleJsonFilePath($directory)));
        $module = $this->getModuleFromJson($directory, $moduleJson);
        $this->preLoadedModules[$module->getNameInCamelCase()] = $module;
        if ($this->isAutoloadEnabled()) {
            $this->loadModule($module);
        }

        return $module;
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

    public function getModules(): array
    {
        return $this->preLoadedModules;
    }

    public function hasModule(string $moduleName): bool
    {
        return isset($this->preLoadedModules[Nomenclature::smartToCamelCase($moduleName)]);
    }
}
