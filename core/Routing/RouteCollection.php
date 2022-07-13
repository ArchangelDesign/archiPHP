<?php

namespace Archi\Routing;

use Archi\Http\Request\ArchiRequest;

class RouteCollection
{
    public function registerRoute(Route $route): void
    {
    }

    public function matchRoute(ArchiRequest $request): Route
    {
    }
}
