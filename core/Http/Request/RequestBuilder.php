<?php

namespace Archi\Http\Request;

class RequestBuilder
{
    public static function createFromGlobals(): ArchiRequest
    {
        if (self::isCli()) {
            return new CliRequest();
        }
        $method = new ArchiRequestMethod(self::getRequestMethod());

        return new ArchiRequest($method);
    }

    public static function getRequestMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public static function isCli(): bool
    {
        return php_sapi_name() === "cli";
    }
}
