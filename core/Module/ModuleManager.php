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
    public const DEFAULT_MODULE_DIR = 'modules';
    public const MODULE_DESCRIPTOR_FILE_NAME = 'module.json';
    private bool $autloadModules = false;

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

    /**
     * @return ModuleManager
     */
    public static function getInstance(): ModuleManager
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Returns the name of the directory in which modules reside.
     *
     * @return string
     */
    public function getModuleDirectoryName(): string
    {
        return Env::get(Env::ENV_MODULE_DIR, self::DEFAULT_MODULE_DIR);
    }

    /**
     * Expected to run at bootstrap. Required for the rest of the framework.
     *
     * @return void
     * @throws InvalidModule
     * @throws ModuleNotFound
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function bootstrap()
    {
        if (self::$modulesPreLoaded) {
            return;
        }
        $path = $this->getModuleDirectoryPath();
        if (!Directory::exists($path)) {
            throw new \RuntimeException('Cannot load modules. Directory ' . $path . ' is not valid.');
        }
        $moduleDirectories = Directory::getDirectoryList($path);

        foreach ($moduleDirectories as $moduleName) {
            $moduleDescriptor = $this->getModuleDescriptorFromDirectoryName($moduleName);
            if (is_null($moduleDescriptor)) {
                continue;
            }
            try {
                $this->preloadModule($moduleDescriptor);
            } catch (Exception\InvalidLocalModule $e) {
                ArchiContainer::logger()->error('Cannot preload module ' . $moduleDescriptor->getName());
            }
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

    /**
     * @param ModuleDescriptor $module
     * @return void
     * @throws Exception\InvalidLocalModule
     * @throws InvalidModule
     * @throws ModuleNotFound
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    private function preloadModule(ModuleDescriptor $module)
    {
        $module->preLoad();
        $this->loadModule($module);
        $this->descriptors[$module->getNameInPascalCase()] = $module;
        $cm = $this->getModuleInstance($module->getPascalName()->str())->getClassMap()->toArray();
        $this->classMap = array_merge(
            $this->classMap,
            $cm
        );
    }

    /**
     * @param string $directory
     * @return string
     */
    private function getModuleJsonFilePath(string $directory): string
    {
        return $directory . Env::DS . self::MODULE_DESCRIPTOR_FILE_NAME;
    }

    private function isAutoloadEnabled(): bool
    {
        return $this->autloadModules;
    }

    /**
     * @param string $directory
     * @param \stdClass $moduleJson
     * @return ModuleDescriptor
     */
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

    /**
     * @param ModuleDescriptor $module
     * @return void
     * @throws InvalidModule
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
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

    /**
     * @return ModuleDescriptor[]
     */
    public function getDescriptors(): array
    {
        return $this->descriptors;
    }

    /**
     * @param string $moduleName
     * @return bool
     */
    public function isLoaded(string $moduleName): bool
    {
        return isset($this->modules[$this->normalizeName($moduleName)]);
    }

    /**
     * @param string $moduleName
     * @return bool
     */
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
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

    /**
     * @param ModuleDescriptor $module
     * @param ModuleInterface $instance
     * @return void
     */
    private function pushModuleInstance(ModuleDescriptor $module, ModuleInterface $instance)
    {
        $this->modules[$module->getPascalName()->str()] = $instance;
    }

    /**
     * @param string $moduleName
     * @return string
     */
    private function normalizeName(string $moduleName): string
    {
        return Nomenclature::toPascalCase($moduleName);
    }

    /**
     * @param string $moduleName
     * @return ModuleDescriptor
     */
    public function getModuleDescriptor(string $moduleName): ModuleDescriptor
    {
        return $this->descriptors[$this->normalizeName($moduleName)];
    }

    /**
     * @return void
     */
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

    /**
     * @param string $moduleName
     * @return ModuleDescriptor|null
     */
    private function getModuleDescriptorFromDirectoryName(string $moduleName): ?ModuleDescriptor
    {
        $directory = $this->getModuleDirectoryPath() . DIRECTORY_SEPARATOR . $moduleName;

        if (!Directory::exists($directory)) {
            return null;
        }

        if (!file_exists($this->getModuleJsonFilePath($directory))) {
            return null;
        }

        $moduleJson = json_decode(file_get_contents($this->getModuleJsonFilePath($directory)));
        return $this->getModuleDescriptorFromJson($directory, $moduleJson);
    }
}
