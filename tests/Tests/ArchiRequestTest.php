<?php

namespace Tests;

use Archi\Http\ProtocolVersion;
use Archi\Http\Request\ArchiRequest;
use Archi\Http\Request\ArchiRequestMethod;
use Archi\Http\Request\RequestBuilder;
use PHPUnit\Framework\TestCase;

class ArchiRequestTest extends TestCase
{
    public function testRequestIsCreated()
    {
        $m = new ArchiRequestMethod('POST');
        $r = new ArchiRequest($m, new ProtocolVersion('1.1'));
        $this->assertInstanceOf(ArchiRequest::class, $r);
    }

    public function testCliRequestCreated()
    {
        $request = RequestBuilder::createFromGlobals();
        $this->assertInstanceOf(ArchiRequest::class, $request);
        $this->assertEquals(ArchiRequestMethod::CLI, $request->getMethod());
    }

    public function testProtocolVersion()
    {
        $request = new ArchiRequest(new ArchiRequestMethod('GET'), new ProtocolVersion('HTTP/1.1'));
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $plainNumber = $request->withProtocolVersion('1.1');
        $this->assertEquals('1.1', $plainNumber->getProtocolVersion());
        $withInstance = $request->withProtocolVersion(new ProtocolVersion('2.0'));
        $this->assertEquals('2.0', $withInstance->getProtocolVersion());
    }
}