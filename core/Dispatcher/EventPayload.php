<?php

namespace Archi\Dispatcher;

use Archi\Http\Request\ArchiRequest;

class EventPayload
{
    private $payload = [];

    private $request;

    public static function buildFromJson(string $jsonString): EventPayload
    {
        return new static(json_decode($jsonString));
    }

    public static function buildFromArray(array $payload)
    {
        return new static($payload);
    }

    private function __construct(array $payload, ?ArchiRequest $request = null)
    {
    }

    public static function buildFromRequest(\Archi\Http\Request\ArchiRequest $request)
    {
        return new static([], $request);
    }

    public function getRequest(): ?ArchiRequest
    {
        return $this->request;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
