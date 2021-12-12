<?php

namespace Archi\Dispatcher\Event;

use Archi\Dispatcher\Event;
use Archi\Dispatcher\EventPayload;
use Archi\Http\Request\ArchiRequest;

class RequestEvent extends Event
{
    public function __construct(ArchiRequest $request)
    {
        parent::__construct('request', EventPayload::buildFromRequest($request), true);
    }
}
