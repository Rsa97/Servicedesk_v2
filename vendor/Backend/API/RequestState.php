<?php
namespace Backend\API;

class RequestState
{
    public const PRERECEIVED = 'preReceived';
    public const RECEIVED = 'received';
    public const ACCEPTED = 'accepted';
    public const FIXED = 'fixed';
    public const REPAIRED = 'repaired';
    public const CLOSED = 'closed';
    public const CANCELED ='canceled';
    public const PLANNED = 'planned';
    public const LIST = [
        self::PRERECEIVED, self::ACCEPTED, self::CANCELED, self::CLOSED, self::FIXED,
        self::PLANNED, self::RECEIVED, self::REPAIRED
    ];
}
