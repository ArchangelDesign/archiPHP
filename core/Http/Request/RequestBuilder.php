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
            new ProtocolVersion($_SERVER['SERVER_PROTOCOL']),
            new Uri(self::getFullUrl()),
            self::getRequestHeaders()
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

    private static function getUrl($useForwardedHost = false): string
    {
        $ssl = self::isHttps();
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . ((self::isHttps()) ? 's' : '');
        $port = $_SERVER['SERVER_PORT'];
        $port = ((!self::isHttps() && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = ($useForwardedHost && isset($_SERVER['HTTP_X_FORWARDED_HOST']) ) ?
            $_SERVER['HTTP_X_FORWARDED_HOST'] : ( isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null );
        $host = isset($host) ? $host : $_SERVER['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    public static function getFullUrl($useForwardedHost = false)
    {
        return self::getUrl($useForwardedHost) . $_SERVER['REQUEST_URI'];
    }

    /**
     * @return bool
     */
    private static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
    }

    /**
     * @return string[][]
     */
    public static function getRequestHeaders(): array
    {
        $server = $_SERVER;
        $headers = [];
        foreach ($server as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headerName = self::getHeaderName($name);
                $headers[$headerName] = self::getHeaderValues($value);
            }
        }

        return $headers;
    }

    public static function getHeaderName(string $name)
    {
        $name = str_replace('HTTP_', '', $name);
        $name = ucwords(strtolower(str_replace('_', ' ', $name)));

        return str_replace(' ', '-', $name);
    }

    private static function getHeaderValues($value): array
    {
        if (strpos($value, ',') === false) {
            return [$value];
        }
        $result = [];
        $values = explode(',', $value);
        array_walk($values, function ($v) use (&$result) {
            $result[] = trim($v);
        });

        return $result;
    }
}
