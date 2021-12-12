<?php

namespace Archi\Http\Request;

class CliRequest extends ArchiRequest
{
    public function __construct()
    {
        parent::__construct(new ArchiRequestMethod(ArchiRequestMethod::CLI));
    }
}
