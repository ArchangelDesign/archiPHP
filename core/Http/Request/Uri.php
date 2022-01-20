<?php

namespace Archi\Http\Request;

use Archi\Http\Request\Exception\InvalidUrl;
use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 * @package Archi\Http\Request
 *
 * The following are two example URIs and their component parts:

   foo://example.com:8042/over/there?name=ferret#nose
   \_/   \______________/\_________/ \_________/ \__/
    |           |            |            |        |
 scheme     authority       path        query   fragment
    |   _____________________|__
   / \ /                        \
   urn:example:animal:ferret:nose

 */
class Uri implements UriInterface
{
    private string $rawUrl;

    private string $scheme;

    private string $authority;
    private string $userInfo;
    private string $host;
    private ?string $port;
    private string $path;
    private string $query;
    private string $fragment;

    /**
     * Uri constructor.
     * @param string $rawUrl
     * @throws InvalidUrl
     */
    public function __construct(string $rawUrl)
    {
        $this->rawUrl = $rawUrl;
        $this->scheme = $this->extractScheme();
        $this->authority = $this->extractAuthority();
        $this->userInfo = $this->extractUserInfo();
        $this->host = $this->extractHost();
        $this->port = $this->extractPort();
        $this->path = $this->extractPath();
        $this->fragment = $this->extractFragment();
        $this->query = $this->extractQuery();
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
        return $this->authority;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function withScheme($scheme)
    {
        // TODO: Implement withScheme() method.
    }

    public function withUserInfo($user, $password = null)
    {
        // TODO: Implement withUserInfo() method.
    }

    public function withHost($host)
    {
        // TODO: Implement withHost() method.
    }

    public function withPort($port)
    {
        // TODO: Implement withPort() method.
    }

    public function withPath($path)
    {
        // TODO: Implement withPath() method.
    }

    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
    }

    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    /**
     * @return string
     * @throws InvalidUrl
     */
    private function extractScheme(): string
    {
        if (strpos($this->rawUrl, ':') === false) {
            throw new InvalidUrl($this->rawUrl);
        }

        return strtolower(explode(':', $this->rawUrl)[0]);
    }

    private function extractAuthority(): string
    {
        $withoutScheme = $this->getWithoutScheme();

        if (strpos($withoutScheme, '//') === false) {
            return '';
        }

        $withoutScheme = str_replace('//', '', $withoutScheme);

        if (
            strpos($withoutScheme, '/') === false &&
            strpos($withoutScheme, '?') === false &&
            strpos($withoutScheme, '#') === false
        ) {
            return $withoutScheme;
        }
        if (strpos($withoutScheme, '?') !== false) {
            $withoutScheme = explode('?', $withoutScheme)[0];
        }
        if (strpos($withoutScheme, '#') !== false) {
            $withoutScheme = explode('#', $withoutScheme)[0];
        }
        if (strpos($withoutScheme, '/') !== false) {
            $withoutScheme = explode('/', $withoutScheme)[0];
        }

        return $withoutScheme;
    }

    /**
     * @return false|string
     */
    private function getWithoutScheme(): string
    {
        return substr($this->rawUrl, strpos($this->rawUrl, ':') + 1);
    }

    private function getWithoutAuthority(): string
    {
        return str_replace([$this->getAuthority(), '//'], '', $this->getWithoutScheme());
    }

    private function extractUserInfo(): string
    {
        if (strpos($this->rawUrl, '@') === false) {
            return '';
        }
    }

    private function extractHost()
    {
        $authority = $this->getAuthority();

        if (strpos($authority, ':') === false) {
            return $authority;
        }

        return explode(':', $authority)[0];
    }

    private function extractPort()
    {
        $authority = $this->getAuthority();
        if (strpos($authority, ':') === false) {
            return null;
        }

        return explode(':', $authority)[1];
    }

    private function extractPath()
    {
        $withoutScheme = $this->getWithoutScheme();

        if (strpos($withoutScheme, '//') === false) {
            return $withoutScheme;
        }

        $withoutScheme = str_replace('//', '', $withoutScheme);
        $withoutAuthority = str_replace($this->getAuthority(), '', $withoutScheme);

        if (strpos($withoutAuthority, '?') === false) {
            return $withoutAuthority;
        }

        $s = substr($withoutAuthority, strpos($withoutAuthority, '?'));

        return str_replace($s, '', $withoutAuthority);
    }

    private function extractQuery()
    {
        if (strpos($this->rawUrl, '?') === false) {
            return '';
        }
        $withoutPath = str_replace([$this->getPath(), '?'], '', $this->getWithoutAuthority());

        if (strpos($withoutPath, '#') === false) {
            return $withoutPath;
        }

        return str_replace([$this->getFragment(), '#'], '', $withoutPath);
    }

    private function extractFragment()
    {
        if (strpos($this->rawUrl, '#') === false) {
            return '';
        }
        return explode('#', $this->rawUrl)[1];
    }
}
