<?php

namespace Archi\Http\Request;

use Archi\Http\ProtocolVersion;

class CliRequest extends ArchiRequest
{
    public function __construct()
    {
        parent::__construct(new ArchiRequestMethod(ArchiRequestMethod::CLI), new ProtocolVersion('CLI'));
    }
}
