<?php
namespace Backend\ORM;

class UserRights
{
    public const CLIENT = 'client';
    public const ENGINEER = 'engineer';
    public const OPERATOR = 'operator';
    public const PARTNER = 'partner';
    public const ADMIN = 'admin';
    public const LIST = [
        self::ADMIN, self::CLIENT, self::ENGINEER, self::OPERATOR, self::PARTNER
    ];
}
