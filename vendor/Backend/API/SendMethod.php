<?php
namespace Backend\API;

class SendMethod
{
    public const EMAIL = 'email';
    public const SMS = 'sms';
    public const TEAMS = 'teams';
    public const LIST = [
        self::EMAIL, self::SMS, self::TEAMS
    ];
}
