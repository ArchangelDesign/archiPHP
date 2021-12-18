<?php

namespace Tests;

use Archi\Http\RequestStream;
use PHPUnit\Framework\TestCase;

class RequestStreamTest extends TestCase
{
    public function testStreamContents()
    {
        $content = '<h1>Archi</h1>';
        $memory = fopen('php://memory', 'r+');
        fwrite($memory, $content);
        fseek($memory, 0);
        $s = new RequestStream($memory);
        $this->assertEquals($content, $s->getContents(), 'Stream was not loaded properly.');
        fseek($memory, 2);
        $this->assertEquals($content, $s->__toString(), '__toString() did not rewind the stream.');
    }
}
