<?php

namespace Archi\Routing;

use Archi\Http\Request\ArchiRequest;

class Router
{
    protected array $routes;
    /**
     * @param ArchiRequest $request
     * @return array
     */
    public function getCollectionOfCode(ArchiRequest $request): array
    {
        return [];
    }
}
