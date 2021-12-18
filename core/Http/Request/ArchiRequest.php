<?php

namespace Archi\Http\Request;

use Archi\Arr;
use Archi\Http\ProtocolVersion;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ArchiRequest implements RequestInterface
{
    private ProtocolVersion $protocolVersion;
    private $headers;
    private $body;
    private $requestTarget;
    private RequestMethod $method;
    private Uri $uri;

    public function __construct(
        RequestMethod $method,
        ProtocolVersion $protocolVersion,
        Uri $uri,
        array $headers
    ) {
        $this->method = $method;
        $this->protocolVersion = $protocolVersion;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->requestTarget = $uri->getPath();
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
        return $this->hasHeader($name) ? $this->headers[$name] : [];
    }

    public function getHeaderLine($name)
    {
        return $this->hasHeader($name) ? implode(', ', $this->headers[$name]) : '';
    }

    public function withHeader($name, $value)
    {
        $cloned = clone $this;
        if (is_string($value)) {
            $cloned->headers[$name] = $this->headerLineToArray($value);
            return $cloned;
        }
        if (!is_array($value)) {
            throw new \RuntimeException('header value must be either string or array');
        }
        $cloned->headers[$name] = $value;
        return $cloned;
    }

    public function withAddedHeader($name, $value)
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }
        $cloned = clone $this;
        if (is_string($value)) {
            $value = $this->headerLineToArray($value);
        }
        $cloned->headers[$name] = array_merge($cloned->headers[$name], $value);

        return $cloned;
    }

    public function withoutHeader($name)
    {
        $cloned = clone $this;
        if ($cloned->hasHeader($name)) {
            unset($cloned->headers[$name]);
        }

        return $cloned;
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

    /**
     * @param string $value
     * @return string[]
     */
    private function headerLineToArray(string $value): array
    {
        return Arr::trim(explode(',', $value));
    }
}
