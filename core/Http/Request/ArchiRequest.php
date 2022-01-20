<?php

namespace Archi\Http\Request;

use Archi\Helper\Arr;
use Archi\Http\ProtocolVersion;
use Archi\Http\RequestStream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ArchiRequest implements ServerRequestInterface
{
    private ProtocolVersion $protocolVersion;
    private $headers;
    private $body;
    private $requestTarget;
    private RequestMethod $method;
    private Uri $uri;
    private array $attributes = [];

    public function __construct(
        RequestMethod $method,
        ProtocolVersion $protocolVersion,
        Uri $uri,
        array $headers,
        ?RequestStream $body
    ) {
        $this->method = $method;
        $this->protocolVersion = $protocolVersion;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->requestTarget = $uri->getPath();
        $this->body = $body;
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
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    public function getRequestTarget()
    {
        return $this->requestTarget ? $this->requestTarget : '/';
    }

    public function withRequestTarget($requestTarget)
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
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

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttribute($name, $default = null)
    {
        if (!$this->hasAttribute($name)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getServerParams()
    {
        // TODO: Implement getServerParams() method.
    }

    public function getCookieParams()
    {
        // TODO: Implement getCookieParams() method.
    }

    public function withCookieParams(array $cookies)
    {
        // TODO: Implement withCookieParams() method.
    }

    public function getQueryParams()
    {
        // TODO: Implement getQueryParams() method.
    }

    public function withQueryParams(array $query)
    {
        // TODO: Implement withQueryParams() method.
    }

    public function getUploadedFiles()
    {
        // TODO: Implement getUploadedFiles() method.
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        // TODO: Implement withUploadedFiles() method.
    }

    public function getParsedBody()
    {
        // TODO: Implement getParsedBody() method.
    }

    public function withParsedBody($data)
    {
        // TODO: Implement withParsedBody() method.
    }

    public function withAttribute($name, $value)
    {
        // TODO: Implement withAttribute() method.
    }

    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
    }
}
