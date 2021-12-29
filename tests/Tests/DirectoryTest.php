<?php

namespace Tests;

use Archi\Helper\Directory;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    public function testDirectoryList()
    {
        $dir = dirname(__DIR__);
        $directories = Directory::getDirectoryList($dir);
        $this->assertCount(1, $directories);
        $this->assertEquals('Tests', $directories[0]);
    }

    public function testSubjectList()
    {
        $files = Directory::getPhpFiles(__DIR__);
        $thisTestFound = false;
        foreach ($files as $f) {
            $this->assertStringContainsString('.php', $f);
            if ($f == 'ClassMapTest.php') {
                $thisTestFound = true;
            }
        }
        $this->assertTrue($thisTestFound);
    }
}
