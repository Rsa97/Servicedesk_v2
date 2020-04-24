<?php
namespace Backend\API;

class Test
{
    public static function check(array $payload, array $params) : array
    {
        sleep(5);
        return [
            'payload' => $payload,
            'params' => $params
        ];
    }
}
