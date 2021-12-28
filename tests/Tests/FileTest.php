<?php

namespace Tests;

use Archi\Helper\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testFileCOntentsWithoutComments()
    {
        $dir = dirname(__DIR__, 2);
        $file = $dir . '/modules/DebugBar/DebugBar.php';
        $contents = File::getContentsWithoutPhpComments($file, 30);
        $this->assertTrue(strlen($contents) == 30);
        $this->assertStringContainsString('namespace Archi\Debug;', $contents);
    }
}
