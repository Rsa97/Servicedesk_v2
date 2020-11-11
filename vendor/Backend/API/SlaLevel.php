<?php
namespace Backend\API;

class SlaLevel
{
    public const CRITICAL = 'critical';
    public const HIGH = 'high';
    public const MEDIUM = 'medium';
    public const LOW = 'low';
    public const USER = 'user';

    public const LIST = [
        self::CRITICAL, self::HIGH, self::MEDIUM, self::LOW, self::USER
    ];

    public const NAMES = [
        self::CRITICAL => 'Критический',
        self::HIGH => 'Высокий',
        self::MEDIUM => 'Средний',
        self::LOW => 'Низкий',
        self::USER => 'На время'
    ];
}
