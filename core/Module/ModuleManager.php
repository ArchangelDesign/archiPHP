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
    private array $descriptors = [];

    /** @var ModuleInterface[] */
    private array $modules = [];

    /** @var string[] */
    private array $classMap = [];

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

    public function bootstrap()
    {
        if (self::$modulesPreLoaded) {
            return;
        }
        $path = $this->getModuleDirectoryPath();
        if (!Directory::exists($path)) {
            throw new \RuntimeException('Cannot load modules. Directory ' . $path . ' is not valid.');
        }
        $modules = Directory::getDirectoryList($path);

        foreach ($modules as $moduleName) {
            $this->preloadModule($moduleName);
        }

        self::$modulesPreLoaded = true;
        $this->bootstrapModules();
    }

    /**
     * @return string
     */
    private function getModuleDirectoryPath(): string
    {
        return Env::cwd() . DIRECTORY_SEPARATOR . $this->getModuleDirectoryName();
    }

    private function preloadModule(string $directoryName): ?ModuleDescriptor
    {
        $directory = $this->getModuleDirectoryPath() . DIRECTORY_SEPARATOR . $directoryName;

        if (!Directory::exists($directory)) {
            return null;
        }

        if (!file_exists($this->getModuleJsonFilePath($directory))) {
            return null;
        }

        $moduleJson = json_decode(file_get_contents($this->getModuleJsonFilePath($directory)));
        $module = $this->getModuleDescriptorFromJson($directory, $moduleJson);
        $module->preLoad();
        try {
            $this->loadModule($module);
        } catch (InvalidModule $e) {
            return null;
        }
        $this->descriptors[$module->getNameInPascalCase()] = $module;
        $cm = $this->getModuleInstance($module->getPascalName())->getClassMap()->toArray();
        $this->classMap = array_merge(
            $this->classMap,
            $cm
        );

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

    public function getDescriptors(): array
    {
        return $this->descriptors;
    }

    public function isLoaded(string $moduleName): bool
    {
        return isset($this->modules[$this->normalizeName($moduleName)]);
    }

    public function hasModule(string $moduleName): bool
    {
        return isset($this->descriptors[$this->normalizeName($moduleName)]);
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
        return $this->descriptors[$this->normalizeName($moduleName)];
    }

    private function bootstrapModules()
    {
        foreach ($this->modules as $moduleName => $moduleInstance) {
            $moduleInstance->bootstrap(ArchiContainer::getInstance());
        }
    }

    /**
     * @return string[]
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }
}
