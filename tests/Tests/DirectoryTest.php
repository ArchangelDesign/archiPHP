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
}
