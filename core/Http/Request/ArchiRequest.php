<?php

namespace Archi\Http\Request;

use Archi\Helper\Arr;
use Archi\Http\ProtocolVersion;
use Archi\Http\RequestStream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
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
    private array $serverParams;
    private array $cookieParams;
    private array $queryParams;
    /** @var UploadedFileInterface[] */
    private ?array $uploadedFiles;
    private $parsedBody;

    public function __construct(
        RequestMethod $method,
        ProtocolVersion $protocolVersion,
        Uri $uri,
        array $headers,
        ?RequestStream $body,
        ?array $serverParams = null,
        ?array $cookieParams = null,
        ?array $uploadedFiles = null
    ) {
        $this->method = $method;
        $this->protocolVersion = $protocolVersion;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->requestTarget = $uri->getPath();
        $this->body = $body;
        if (is_null($serverParams)) {
            $serverParams = $_SERVER;
        }
        $this->serverParams = $serverParams;
        if (is_null($cookieParams)) {
            $cookieParams = $_COOKIE;
        }
        $this->cookieParams = $cookieParams;
        $this->queryParams = $this->extractQueryParams($uri->getQuery());
        $this->uploadedFiles = $uploadedFiles;
        $this->parsedBody = $this->extractParsedBody();
    }

    public function getProtocolVersion()
    {
        return $this->protocolVersion->get();
    }

    /**
     * @param string|ProtocolVersion $version
     * @return ArchiRequest
     */
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
        $clone = clone $this;
        $clone->method = $method instanceof RequestMethod ? $method : new RequestMethod($method);

        return $clone;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        if ($uri->getHost() != $clone->getUri()->getHost() && $preserveHost) {
            $uri = $uri->withHost($clone->getUri()->getHost());
        }
        $clone->uri = $uri;

        return $clone;
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

    /**
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    private function extractQueryParams(string $query): array
    {
        if (empty($query)) {
            return [];
        }
        $result = [];
        foreach (explode('&', $query) as $param) {
            [$key, $value] = explode('=', $param);
            $result[$key] = $value;
        }

        return $result;
    }

    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    public function withAttribute($name, $value)
    {
        // TODO: Implement withAttribute() method.
    }

    public function withoutAttribute($name)
    {
        // TODO: Implement withoutAttribute() method.
    }

    private function extractParsedBody()
    {
        if (!$this->method->shouldHaveBody()) {
            return [];
        }

        return $_POST;
    }
}
