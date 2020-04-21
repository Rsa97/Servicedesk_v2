<?php
namespace Backend\ORM;

class DayType
{
    public const WORKDAY = 1;
    public const WEEKDAY = 2;
    public const LIST = [
        self::WORKDAY => 'work',
        self::WEEKDAY => 'weekday'
    ];
}
