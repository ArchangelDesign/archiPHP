<?php

namespace Archi\Routing;

class Builder extends Router
{
    public function any(string $uri): Route
    {
        return (new Route())
            ->setUri($uri);
    }

    public function get(): Route
    {
    }
}
