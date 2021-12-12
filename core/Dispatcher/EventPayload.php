<?php

namespace Archi\Dispatcher;

class EventPayload
{
    private $payload = [];

    public static function buildFromJson(string $jsonString): EventPayload
    {
        return new static(json_decode($jsonString));
    }

    public static function buildFromArray(array $payload)
    {
        return new static($payload);
    }

    private function __construct(array $payload)
    {
    }
}
