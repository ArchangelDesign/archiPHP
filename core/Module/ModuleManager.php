<?php

namespace Archi\Module;

use Archi\Container\ArchiContainer;
use Archi\Environment\Env;
use Archi\Helper\Directory;
use Archi\Helper\Nomenclature;
use Archi\Module\Exception\InvalidModule;
use Archi\Module\Exception\ModuleNotFound;

class ModuleManager
{
    private $autloadModules = false;

    private static ?ModuleManager $instance = null;

    private static bool $modulesPreLoaded = false;

    /** @var ModuleDescriptor[] */
    private array $preLoadedModules = [];

    /** @var ModuleInterface[] */
    private array $modules = [];

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
        if (!Directory::isValid($path)) {
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

    private function preloadModule(string $directoryName): ?ModuleDescriptor
    {
        $directory = $this->getModuleDirectoryPath() . '/' . $directoryName;

        if (!Directory::isValid($directory)) {
            return null;
        }

        if (!file_exists($this->getModuleJsonFilePath($directory))) {
            return null;
        }

        $moduleJson = json_decode(file_get_contents($this->getModuleJsonFilePath($directory)));
        $module = $this->getModuleDescriptorFromJson($directory, $moduleJson);
        $module->preLoad();
        $this->preLoadedModules[$module->getNameInPascalCase()] = $module;
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

    private function getModuleDescriptorFromJson(string $directory, \stdClass $moduleJson): ModuleDescriptor
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

        return new ModuleDescriptor(
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

    private function loadModule(ModuleDescriptor $module)
    {
        require_once $module->getLoadFile();
        $class = $module->getClassName();
        if (!class_exists($class)) {
            throw new InvalidModule('Error loading module ' . $module->getName());
        }
        $instance = ArchiContainer::getInstance()->get($class);
        if (!$instance instanceof ModuleInterface) {
            throw new InvalidModule('Class ' . $class . ' is not a valid Archi Module.');
        }
        $this->pushModuleInstance($module, $instance);
    }

    public function getPreloadedModules(): array
    {
        return $this->preLoadedModules;
    }

    public function isLoaded(string $moduleName): bool
    {
        return isset($this->modules[$this->normalizeName($moduleName)]);
    }

    public function hasModule(string $moduleName): bool
    {
        return isset($this->preLoadedModules[$this->normalizeName($moduleName)]);
    }

    /**
     * Returns the instance of the module. If the module
     * hasn't been loaded yet, it will be loaded now.
     * For that reason this method might throw validation errors.
     *
     * @param string $moduleName
     * @return ModuleInterface
     * @throws InvalidModule
     * @throws ModuleNotFound
     */
    public function getModuleInstance(string $moduleName): ModuleInterface
    {
        if (!$this->hasModule($moduleName)) {
            throw new ModuleNotFound('Cannot locate module ' . $moduleName);
        }

        if (!$this->isLoaded($moduleName)) {
            $this->loadModule($this->getModuleDescriptor($moduleName));
        }

        return $this->modules[$this->normalizeName($moduleName)];
    }

    private function pushModuleInstance(ModuleDescriptor $module, ModuleInterface $instance)
    {
        $this->modules[$module->getPascalName()] = $instance;
    }

    private function normalizeName(string $moduleName)
    {
        return Nomenclature::toPascalCase($moduleName);
    }

    public function getModuleDescriptor(string $moduleName)
    {
        return $this->preLoadedModules[$this->normalizeName($moduleName)];
    }
}
