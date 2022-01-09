<?php

namespace Tests;

use Archi\Helper\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testFileContentsWithoutComments()
    {
        $dir = dirname(__DIR__, 2);
        $file = $dir . '/modules/DebugBar/DebugBar.php';
        $contents = File::getContentsWithoutPhpComments($file, 30);
        $this->assertTrue(strlen($contents) == 30);
        $this->assertStringContainsString('namespace Archi\Debug;', $contents);
    }

    public function testFileContentsWithoutCommentsAndUses()
    {
        $dir = dirname(__DIR__, 2);
        $file = $dir . '/core/Container/ArchiContainer.php';
        $contents = File::getContentsWithoutPhpComments($file, 86);
        $this->assertStringContainsString(
            'class ArchiContainer implements ContainerInterface',
            $contents,
            '`Use` statements have not been properly omitted. (probably)'
        );
    }

    public function testNoExtension()
    {
        $this->assertEquals('file', File::noExtension('file.ext'));
        $this->assertEquals('file.ext', File::noExtension('file.ext.tmp'));
        $this->assertEquals('file', File::noExtension('file.Dockerfile'));
    }
}
