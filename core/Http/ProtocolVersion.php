<?php

namespace Archi\Http;

class ProtocolVersion
{
    private string $protocolVersion;

    public function __construct(string $protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }

    public function get(): string
    {
        return str_replace('HTTP/', '', $this->protocolVersion);
    }
}
