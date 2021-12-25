<?php

namespace Tests;

use Archi\Helper\Nomenclature;
use PHPUnit\Framework\TestCase;

class NomenclatureTest extends TestCase
{
    public function testCamelCase()
    {
        $input = 'The text to camel case';
        $output = Nomenclature::toCamelCase($input);
        $this->assertEquals('TheTextToCamelCase', $output);
    }

    public function testSmartCamelCase()
    {
        $output = Nomenclature::smartToCamelCase('text-to camel-case');
        $this->assertEquals('TextToCamelCase', $output);
        $output = Nomenclature::smartToCamelCase('AlreadyInCamelCase');
        $this->assertEquals('AlreadyInCamelCase', $output);
        $output = Nomenclature::smartToCamelCase('snake_case_text');
        $this->assertEquals('SnakeCaseText', $output);
    }
}
