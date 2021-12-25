<?php

namespace Tests;

use Archi\Container\ArchiContainer;
use Archi\Environment\Env;
use Archi\Module\Module;
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
        $module = $this->callPrivateMethod($mm, 'getModuleFromJson', [$directory, $json]);
        $this->assertInstanceOf(Module::class, $module);
    }

    public function testModuleLoads()
    {
        /** @var ModuleManager $mm */
        $mm = ArchiContainer::getInstance()->get('ModuleManager');
        $mm->preloadModules();
        $this->assertTrue($mm->hasModule('DebugBar'));
    }
}
