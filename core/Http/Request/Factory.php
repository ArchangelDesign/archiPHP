<?php

namespace Archi\Http\Request;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Factory
 */
class Factory implements RequestFactoryInterface
{
    /**
     * Returns an instance of ArchiRequest
     *
     * @param string              $method Request method.
     * @param UriInterface|string $uri    Uri.
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        $m = new RequestMethod($method);
        return new ArchiRequest($m);
    }
}
