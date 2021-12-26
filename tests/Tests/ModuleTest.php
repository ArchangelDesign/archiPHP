<?php

namespace Tests;

use Archi\Container\ArchiContainer;
use Archi\Environment\Env;
use Archi\Module\ModuleDescriptor;
use Archi\Module\ModuleInterface;
use Archi\Module\ModuleManager;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!Env::isInitialized()) {
            Env::initialize(dirname(__DIR__, 2));
        }
    }

    private function callPrivateMethod($object, string $method, array $args = [])
    {
        $ref = new \ReflectionClass($object);
        $method = $ref->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    public function testModuleFromJson()
    {
        $directory = '/var/www/archi/modules';

        $json = json_decode(json_encode([
            'fileName' => 'MyModule.php',
            'name' => 'My Module',
            'title' => 'My Module',
            'version' => '1.0',
        ]));
        $mm = ArchiContainer::getInstance()->get('ModuleManager');
        /** @var ModuleDescriptor $module */
        $module = $this->callPrivateMethod($mm, 'getModuleDescriptorFromJson', [$directory, $json]);
        $this->assertInstanceOf(ModuleDescriptor::class, $module);
        $this->assertEquals('MyModule.php', $module->getFileName());
        $this->assertEquals('My Module', $module->getName());
        $this->assertEquals('MyModule', $module->getNameInPascalCase());
        $this->assertEquals('My Module', $module->getTitle());
        $this->assertEquals('1.0', $module->getVersion());
        $this->assertNull($module->getDescription());
        $this->assertEquals('0', $module->getMinimumCoreVersion());
    }

    public function testDebugBarModuleLoads()
    {
        /** @var ModuleManager $mm */
        $mm = ArchiContainer::getInstance()->get('ModuleManager');
        $mm->preloadModules();
        $this->assertTrue($mm->hasModule('DebugBar'));
        $namespace = $mm->getModuleDescriptor('DebugBar')->getNamespace();
        $this->assertEquals('Archi\Debug', $namespace);
        $class = $mm->getModuleDescriptor('DebugBar')->getClassName();
        $this->assertEquals('\Archi\Debug\DebugBar', $class);
        $instance = $mm->getModuleInstance('DebugBar');
        $this->assertInstanceOf(ModuleInterface::class, $instance);
        $this->assertInstanceOf(\Archi\Debug\DebugBar::class, $instance);
    }
}
