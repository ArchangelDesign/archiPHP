<?php

namespace Tests;

use Archi\Http\ProtocolVersion;
use Archi\Http\Request\ArchiRequest;
use Archi\Http\Request\RequestMethod;
use Archi\Http\Request\RequestBuilder;
use Archi\Http\Request\Uri;
use PHPUnit\Framework\TestCase;

class ArchiRequestTest extends TestCase
{
    public function testRequestIsCreated()
    {
        $m = new RequestMethod('POST');
        $r = new ArchiRequest($m, new ProtocolVersion('1.1'), new Uri('http://google.com'), []);
        $this->assertInstanceOf(ArchiRequest::class, $r);
    }

    public function testCliRequestCreated()
    {
        $request = RequestBuilder::createFromGlobals();
        $this->assertInstanceOf(ArchiRequest::class, $request);
        $this->assertEquals(RequestMethod::CLI, $request->getMethod());
    }

    public function testProtocolVersion()
    {
        $request = new ArchiRequest(
            new RequestMethod('GET'),
            new ProtocolVersion('HTTP/1.1'),
            new Uri('http://google.com'),
            []
        );
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $plainNumber = $request->withProtocolVersion('1.1');
        $this->assertEquals('1.1', $plainNumber->getProtocolVersion());
        $withInstance = $request->withProtocolVersion(new ProtocolVersion('2.0'));
        $this->assertEquals('2.0', $withInstance->getProtocolVersion());
    }

    public function testUriScheme()
    {
        $uri = new Uri('hTTps://google.com/search?a=abc#fragment');
        $this->assertEquals('https', $uri->getScheme());
        $uri = new Uri('urn:google:search');
        $this->assertEquals('urn', $uri->getScheme());
    }

    public function testUriAuthority()
    {
        $uri = new Uri('https://google.com/search?a=abc#fragment');
        $this->assertEquals('google.com', $uri->getAuthority());
        $uri = new Uri('https://google.com:8080/search?a=abc#fragment');
        $this->assertEquals('google.com:8080', $uri->getAuthority());
        $uri = new Uri('urn:example:animal:ferret:nose');
        $this->assertEquals('', $uri->getAuthority());
        $uri = new Uri('https://google.com:8080');
        $this->assertEquals('google.com:8080', $uri->getAuthority());
    }

    public function testUriHost()
    {
        $uri = new Uri('https://google.com/search?a=abc#fragment');
        $this->assertEquals('google.com', $uri->getHost());
        $uri = new Uri('https://google.com:8080/search?a=abc#fragment');
        $this->assertEquals('google.com', $uri->getHost());
        $uri = new Uri('https://123.123.123.123:8080/search?a=abc#fragment');
        $this->assertEquals('123.123.123.123', $uri->getHost());
    }

    public function testUriPort()
    {
        $uri = new Uri('https://google.com/search?a=abc#fragment');
        $this->assertNull($uri->getPort());
        $uri = new Uri('https://google.com:8080/search?a=abc#fragment');
        $this->assertEquals(8080, $uri->getPort());
        $uri = new Uri('https://123.123.123.123:8080/search?a=abc#fragment');
        $this->assertEquals(8080, $uri->getPort());
    }

    public function testUriPath()
    {
        $uri = new Uri('https://google.com/search?a=abc#fragment');
        $this->assertEquals('/search', $uri->getPath());
        $uri = new Uri('https://google.com:8080/search/something?a=abc#fragment');
        $this->assertEquals('/search/something', $uri->getPath());
        $uri = new Uri('https://123.123.123.123:8080/search?a=abc#fragment');
        $this->assertEquals('/search', $uri->getPath());
        $uri = new Uri('urn:example:animal:ferret:nose');
        $this->assertEquals('example:animal:ferret:nose', $uri->getPath());
    }

    public function testUriQuery()
    {
        $uri = new Uri('https://google.com/search?a=abc#fragment');
        $this->assertEquals('a=abc', $uri->getQuery());
        $uri = new Uri('https://google.com');
        $this->assertEquals('', $uri->getQuery());
        $uri = new Uri('urn:example:animal:ferret:nose');
        $this->assertEquals('', $uri->getQuery());
    }

    public function testUriFragment()
    {
        $uri = new Uri('https://google.com/search?a=abc#fragment');
        $this->assertEquals('fragment', $uri->getFragment());
        $uri = new Uri('https://google.com');
        $this->assertEquals('', $uri->getFragment());
        $uri = new Uri('urn:example:animal:ferret:nose');
        $this->assertEquals('', $uri->getFragment());
    }

    public function testRequestBuilder()
    {
        $r = RequestBuilder::createFromGlobals();
        $this->assertInstanceOf(ArchiRequest::class, $r);
    }

    public function testRequestHeaderName()
    {
        $name = 'HTTP_USER_AGENT';
        $this->assertEquals('User-Agent', RequestBuilder::getHeaderName($name));
    }
}
