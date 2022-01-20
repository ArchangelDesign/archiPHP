<?php

namespace Tests;

use Archi\Environment\Env;
use Archi\Http\ProtocolVersion;
use Archi\Http\Request\ArchiRequest;
use Archi\Http\Request\RequestMethod;
use Archi\Http\Request\RequestBuilder;
use Archi\Http\Request\Uri;
use Archi\Http\RequestStream;
use PHPUnit\Framework\TestCase;

class ArchiRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!Env::isInitialized()) {
            Env::initialize(dirname(__DIR__, 2), true);
        }
    }

    public function testRequestIsCreated()
    {
        $m = new RequestMethod('POST');
        $r = new ArchiRequest($m, new ProtocolVersion('1.1'), new Uri('http://google.com'), [], null);
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
            [],
            null
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
        $uri = new Uri('https://google.com#fragment');
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
        $uri = new Uri('https://google.com?a=abc#fragment');
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

    public function testRequestBuilderWorksForCli()
    {
        $r = RequestBuilder::createFromGlobals();
        $this->assertInstanceOf(ArchiRequest::class, $r);
    }

    public function testRequestHeaderNameConversionFromServerVars()
    {
        $name = 'HTTP_USER_AGENT';
        $this->assertEquals('User-Agent', RequestBuilder::getHeaderName($name));
    }

    public function testRequestWithHeaders()
    {
        $r = RequestBuilder::createFromGlobals();
        $cloned = $r->withHeader('test-header', 'test-value');
        $this->assertEquals('test-value', $cloned->getHeaderLine('test-header'));
        $this->assertCount(1, $cloned->getHeader('test-header'));
        $cloned = $r->withHeader('test-header', '1,2');
        $this->assertEquals('1, 2', $cloned->getHeaderLine('test-header'));
        $this->assertCount(2, $cloned->getHeader('test-header'));
        $cloned = $r->withHeader('test-header', ['3', '5']);
        $this->assertEquals('3, 5', $cloned->getHeaderLine('test-header'));
        $this->assertCount(2, $cloned->getHeader('test-header'));
        $cloned = $r->withHeader('test-header', '1, 2');
        $this->assertEquals('1, 2', $cloned->getHeaderLine('test-header'));
        $this->assertCount(2, $cloned->getHeader('test-header'));
    }

    public function testRequestWithAddedHeader()
    {
        $r = RequestBuilder::createFromGlobals();
        $r = $r->withHeader('existing', 'one, two');
        $this->assertCount(2, $r->getHeader('existing'));
        $r = $r->withAddedHeader('existing', 'three');
        $this->assertCount(3, $r->getHeader('existing'));
        $this->assertEquals('three', $r->getHeader('existing')[2]);
    }

    public function testRequestWithoutHeader()
    {
        $r = RequestBuilder::createFromGlobals();
        $r = $r->withHeader('existing', 'one, two');
        $r = $r->withoutHeader('existing');
        $this->assertCount(0, $r->getHeaders());
    }

    public function testWithRequestMethod()
    {
        $r = RequestBuilder::createFromGlobals();
        $w = $r->withMethod('get');
        $this->assertIsString($w->getMethod());
        $this->assertEquals('GET', $w->getMethod());
    }

    public function testQueryParams()
    {
        $uri = new Uri('https://local.com?key=value');
        $request = new ArchiRequest(new RequestMethod('get'), new ProtocolVersion('1.1'), $uri, [], null);
        $this->assertNotEmpty($request->getQueryParams());
        $this->assertArrayHasKey('key', $request->getQueryParams());
        $this->assertEquals('value', $request->getQueryParams()['key']);
    }

    public function testRequestBody()
    {
        $body = new RequestStream('php://memory');
        $body->write(json_encode(['request' => ['status' => true]]));
        $r = new ArchiRequest(
            new RequestMethod('GET'),
            new ProtocolVersion('1.1'),
            new Uri('https://google.com'),
            [],
            null
        );
        $withBody = $r->withBody($body);
        $bodyContents = (string)$withBody->getBody();
        $this->assertNotEmpty($bodyContents);
        $decodedBody = json_decode($bodyContents, true);
        $this->assertIsArray($decodedBody);
        $this->assertNotEmpty($decodedBody);
        $this->assertArrayHasKey('request', $decodedBody);
        $this->assertArrayHasKey('status', $decodedBody['request']);
        $this->assertTrue($decodedBody['request']['status']);
    }
}
