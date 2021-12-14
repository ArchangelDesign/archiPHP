<?php

namespace Archi\Http\Request;

use Archi\Http\ProtocolVersion;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ArchiRequest implements RequestInterface
{
    /** @var ProtocolVersion */
    private $protocolVersion;
    private $headers;
    private $body;
    private $requestTarget;
    private $method;

    /**
     * @var UriInterface
     */
    private $uri;

    public function __construct(ArchiRequestMethod $method, ProtocolVersion $protocolVersion)
    {
        $this->method = $method;
        $this->protocolVersion = $protocolVersion;
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion->get();
    }

    public function withProtocolVersion($version)
    {
        $protocolVersion = $version instanceof ProtocolVersion ? $version : new ProtocolVersion($version);
        $cloned = clone $this;
        $cloned->protocolVersion = $protocolVersion;

        return $cloned;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * @param  string $name
     * @return string[]
     */
    public function getHeader($name)
    {
        return $this->hasHeader($name) ? explode(',', $this->headers[$name]) : [];
    }

    public function getHeaderLine($name)
    {
        return $this->hasHeader($name) ? $this->headers[$name] : '';
    }

    public function withHeader($name, $value)
    {
        throw new \RuntimeException('implement me');
    }

    public function withAddedHeader($name, $value)
    {
        throw new \RuntimeException('implement me');
    }

    public function withoutHeader($name)
    {
        throw new \RuntimeException('implement me');
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        throw new \RuntimeException('implement me');
    }

    public function getRequestTarget()
    {
        return $this->requestTarget ? $this->requestTarget : '/';
    }

    public function withRequestTarget($requestTarget)
    {
        throw new \RuntimeException('implement me');
    }

    public function getMethod()
    {
        return $this->method->getMethod();
    }

    public function withMethod($method)
    {
        throw new \RuntimeException('implement me');
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        throw new \RuntimeException('implement me');
    }
}
