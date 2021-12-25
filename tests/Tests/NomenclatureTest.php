<?php

namespace Tests;

use Archi\Helper\Nomenclature;
use PHPUnit\Framework\TestCase;

class NomenclatureTest extends TestCase
{
    public function testPascalCase()
    {
        $output = Nomenclature::toPascalCase('The text to camel case');
        $this->assertEquals('TheTextToCamelCase', $output);
        $output = Nomenclature::toPascalCase('camelCase');
        $this->assertEquals('CamelCase', $output);
        $output = Nomenclature::toPascalCase('camelCase_and_snake');
        $this->assertEquals('CamelCaseAndSnake', $output);
        $output = Nomenclature::toPascalCase('camelCase and spaces');
        $this->assertEquals('CamelCaseAndSpaces', $output);
    }

    public function testCamelCase()
    {
        $output = Nomenclature::toCamelCase('The text to camel case');
        $this->assertEquals('theTextToCamelCase', $output);
        $output = Nomenclature::toCamelCase('PascalCase');
        $this->assertEquals('pascalCase', $output);
        $output = Nomenclature::toCamelCase('PascalCase_and_snake');
        $this->assertEquals('pascalCaseAndSnake', $output);
        $output = Nomenclature::toCamelCase('PascalCase and spaces');
        $this->assertEquals('pascalCaseAndSpaces', $output);
    }
}
