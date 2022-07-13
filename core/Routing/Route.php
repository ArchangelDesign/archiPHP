<?php

namespace Archi\Routing;

use Archi\Http\Request\RequestMethod;

class Route
{
    public function setName(string $name): Route
    {
    }

    public function hasName(): bool
    {

    }

    public function addMiddleware(string $class): Route
    {
    }

    public function setUri(string $uri): Route
    {

    }

    public function setMethod(RequestMethod $method): Route
    {

    }

    public function hasMethod(): bool
    {

    }

    public function setTarget(): Route
    {

    }
}
