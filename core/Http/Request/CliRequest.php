<?php

namespace Archi\Http\Request;

use Archi\Http\ProtocolVersion;

class CliRequest extends ArchiRequest
{
    public function __construct()
    {
        parent::__construct(
            new RequestMethod(RequestMethod::CLI),
            new ProtocolVersion('CLI'),
            new Uri('cli:request'),
            [],
            null
        );
    }
}
