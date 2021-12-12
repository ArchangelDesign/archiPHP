<?php


namespace Archi\Http\Request;


use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Factory implements RequestFactoryInterface
{

    public function createRequest(string $method, $uri): RequestInterface
    {
        return new ArchiRequest();
    }
}
