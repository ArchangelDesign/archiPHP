<?php

namespace Tests;

use Archi\Helper\ArchiString;
use PHPUnit\Framework\TestCase;

class ArchiStringTest extends TestCase
{
    public function testEndsWith()
    {
        $this->assertTrue(ArchiString::endsWith('my-text', 'text'));
        $this->assertTrue(ArchiString::endsWith('/some/url', 'url'));
        $this->assertFalse(ArchiString::endsWith('/some/url', 'Url'));
        $this->assertFalse(ArchiString::endsWith('string-this-is', 'long-string-this-is'));
    }
}