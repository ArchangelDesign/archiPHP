<?php

namespace Tests;

use Archi\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function testArrayTrim()
    {
        $input = [' one ', '   two '];
        $output = Arr::trim($input);
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
        $output = Arr::ktrim($input);
        $this->assertEquals('one', $output['one']);
        $this->assertEquals('two', $output['two']);
        $this->assertIsArray($output['three']);
    }

    public function testArrayKrtrim()
    {
        $input = [
            'one' => '  one ',
            'two' => '  two ',
            'three' => ['three '],
            'four' => ['four' => ' four ']
        ];
        $output = Arr::krtrim($input);
        $this->assertEquals('one', $output['one']);
        $this->assertEquals('two', $output['two']);
        $this->assertIsArray($output['three']);
        $this->assertEquals('three', $output['three'][0]);
        $this->assertEquals('four', $output['four']['four']);
    }
}
