<?php

namespace Tests;

use Archi\Helper\ArchiString;
use PHPUnit\Framework\TestCase;

class ArchiStringTest extends TestCase
{
    public function testEndsWith()
    {
        $this->assertTrue(ArchiString::staticEndsWith('my-text', 'text'));
        $this->assertTrue(ArchiString::staticEndsWith('/some/url', 'url'));
        $this->assertFalse(ArchiString::staticEndsWith('/some/url', 'Url'));
        $this->assertFalse(ArchiString::staticEndsWith('string-this-is', 'long-string-this-is'));
        $this->assertFalse(ArchiString::staticEndsWith('string-this-is', '_this-is'));
        $this->assertFalse(ArchiString::staticEndsWith('string-this-is', 'this-is-'));
    }

    public function testArchiStringEndsWithInstance()
    {
        $this->assertTrue((new ArchiString('something'))->endsWith('ing'));
        $this->assertTrue((new ArchiString('*(&%^#%@^&^*(&()&'))->endsWith('&'));
        $this->assertFalse((new ArchiString('*(&%^#%@^&^*(&()&'))->endsWith('.&'));
    }

    public function testPascalCase()
    {
        $output = new ArchiString('The text to camel case');
        $this->assertEquals('TheTextToCamelCase', $output->toPascal());
        $output = new ArchiString('camelCase');
        $this->assertEquals('CamelCase', $output->toPascal());
        $output = new ArchiString('camelCase_and_snake');
        $this->assertEquals('CamelCaseAndSnake', $output->toPascal());
        $output = new ArchiString('camelCase and spaces');
        $this->assertEquals('CamelCaseAndSpaces', $output->toPascal());
        $output = new ArchiString('AlreadyPascalCase');
        $this->assertEquals('AlreadyPascalCase', $output->toPascal());
    }

    public function testCamelCase()
    {
        $output = new ArchiString('The text to camel case');
        $this->assertEquals('theTextToCamelCase', $output->toCamel());
        $output = new ArchiString('PascalCase');
        $this->assertEquals('pascalCase', $output->toCamel());
        $output = new ArchiString('PascalCase_and_snake');
        $this->assertEquals('pascalCaseAndSnake', $output->toCamel());
        $output = new ArchiString('PascalCase and spaces');
        $this->assertEquals('pascalCaseAndSpaces', $output->toCamel());
    }
}