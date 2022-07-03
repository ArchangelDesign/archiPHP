<?php

namespace Tests;

use Archi\Helper\ArchiArray;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function testArrayTrim()
    {
        $input = [' one ', '   two '];
        $output = ArchiArray::trim($input);
        $this->assertCount(2, $output);
        $this->assertEquals('one', $output[0]);
        $this->assertEquals('two', $output[1]);
    }

    public function testArrayKtrim()
    {
        $input = [
            'one' => '  one ',
            'two' => '  two ',
            'three' => ['three ']
        ];
        $output = ArchiArray::ktrim($input);
        $this->assertEquals('one', $output['one']);
        $this->assertEquals('two', $output['two']);
        $this->assertIsArray($output['three']);
        $this->assertEquals('three ', $output['three'][0]);
    }

    public function testArrayKrtrim()
    {
        $input = [
            'one' => '  one ',
            'two' => '  two ',
            'three' => ['three '],
            'four' => ['four' => ' four ']
        ];
        $output = ArchiArray::krtrim($input);
        $this->assertEquals('one', $output['one']);
        $this->assertEquals('two', $output['two']);
        $this->assertIsArray($output['three']);
        $this->assertEquals('three', $output['three'][0]);
        $this->assertEquals('four', $output['four']['four']);
    }

    public function testDotNotationAccess()
    {
        $array = new ArchiArray(['one' => ['two' => ['three' => 'The Value']]]);
        $this->assertEquals('The Value', $array->fetch('one.two.three'));
        $this->assertNull($array->fetch('one.two.six'));
        $this->assertEquals('1', $array->fetch('one.two.ten', '1'));
    }
}
