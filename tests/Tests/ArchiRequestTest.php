<?php

namespace Tests;

use Archi\Http\Request\ArchiRequest;
use Archi\Http\Request\ArchiRequestMethod;
use Archi\Http\Request\RequestBuilder;
use PHPUnit\Framework\TestCase;

class ArchiRequestTest extends TestCase
{
    public function testRequestIsCreated()
    {
        $m = new ArchiRequestMethod('POST');
        $r = new ArchiRequest($m);
        $this->assertInstanceOf(ArchiRequest::class, $r);
    }

    public function testCliRequestCreated()
    {
        $request = RequestBuilder::createFromGlobals();
        $this->assertInstanceOf(ArchiRequest::class, $request);
        $this->assertEquals(ArchiRequestMethod::CLI, $request->getMethod());
    }
}