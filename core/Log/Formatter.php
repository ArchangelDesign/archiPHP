<?php

namespace Archi\Log;

class Formatter
{
    public static function formatAsArray($level, $message, $context): array
    {
        $result = [
            'level' => $level,
            'context' => $context
        ];
        if (!is_null($jsonDecoded = json_decode($message))) {
            $result['message'] = $jsonDecoded;
            return $result;
        }
        $result['message'] = $message;

        return $result;
    }

    public static function formatAsString($level, $message, $context): string
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        return sprintf('[%s] %s %s', strtoupper($level), $message, json_encode($context));
    }
}
