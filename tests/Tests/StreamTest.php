<?php

namespace Tests;

use Archi\Http\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function testStreamContents()
    {
        $content = '<h1>Archi</h1>';
        $memory = fopen('php://memory', 'r+');
        fwrite($memory, $content);
        fseek($memory, 0);
        $s = new Stream($memory);
        $this->assertEquals($content, $s->getContents(), 'Stream was not loaded properly.');
        fseek($memory, 2);
        $this->assertEquals($content, $s->__toString(), '__toString() did not rewind the stream.');
    }
}
