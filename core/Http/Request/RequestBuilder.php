<?php

namespace Archi\Http\Request;

use Archi\Http\ProtocolVersion;

class RequestBuilder
{
    public static function createFromGlobals(): ArchiRequest
    {
        if (self::isCli()) {
            return new CliRequest();
        }

        return new ArchiRequest(
            new RequestMethod(self::getRequestMethod()),
            new ProtocolVersion($_SERVER['SERVER_PROTOCOL'])
        );
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
