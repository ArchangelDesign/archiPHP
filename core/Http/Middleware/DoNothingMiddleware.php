<?php

namespace Archi\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;

class DoNothingMiddleware implements MiddlewareInterface
{
    public function process(
        \Psr\Http\Message\ServerRequestInterface $request,
        \Psr\Http\Server\RequestHandlerInterface $handler
    ): \Psr\Http\Message\ResponseInterface {
        return $handler->handle($request);
    }
}
