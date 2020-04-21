<?php
namespace Backend\ORM;

class EventType
{
    public const NEW_REQUEST = 'open';
    public const CHANGE_STATE = 'changeState';
    public const CHANGE_DATE = 'changeDate';
    public const ADD_COMMENT = 'comment';
    public const ADD_DOCUMENT = 'addDocument';
    public const ON_WAIT = 'onWait';
    public const OFF_WAIT = 'offWait';
    public const UNCLOSE = 'unClose';
    public const UNCANCEL = 'unCancel';
    public const CHANGE_EQUIPMENT = 'eqChange';
    public const CHANGE_PARTNER = 'changePartner';
    public const CHANGE_CONTRACT = 'changeContact';
    public const CHANGE_SERVICE ='changeService';
    public const LIST = [
        self::ADD_COMMENT, self::ADD_DOCUMENT, self::CHANGE_CONTRACT, self::CHANGE_DATE,
        self::CHANGE_EQUIPMENT, self::CHANGE_PARTNER, self::CHANGE_SERVICE, self::CHANGE_STATE,
        self::NEW_REQUEST, self::OFF_WAIT, self::ON_WAIT, self::UNCANCEL,
        self::UNCLOSE
    ];
}
