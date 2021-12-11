<?php

namespace Tests;

use Archi\Http\Request\ArchiRequest;
use Archi\Http\Request\ArchiRequestMethod;
use PHPUnit\Framework\TestCase;

class ArchiRequestTest extends TestCase
{
    public function testRequestIsCreated()
    {
        $m = new ArchiRequestMethod('POST');
        $r = new ArchiRequest($m);
        $this->assertInstanceOf(ArchiRequest::class, $r);
    }
}