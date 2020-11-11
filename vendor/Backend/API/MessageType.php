<?php
namespace Backend\API;

class MessageType
{
    public const NEW_REQUEST = 'open';
    public const ACCEPTED = 'changeState.accepted';
    public const FIXED = 'changeState.fixed';
    public const REPAIRED = 'changeState.repaired';
    public const CLOSED = 'changeState.closed';
    public const CANCELED = 'changeState.canceled';
    public const UNCLOSED = 'unClose';
    public const UNCANCELED = 'unCancel';
    public const ON_WAIT = 'onWait';
    public const OFF_WAIT = 'offWait';
    public const ADD_COMMENT = 'comment';
    public const ADD_DOCUMENT = 'addDocument';
    public const TIME_50 = 'time50';
    public const TIME_20 = 'time20';
    public const TIME_00 = 'time00';
    public const AUTOCLOSE = 'autoclose';
    public const CHANGE_EQUIPMENT = 'eqChange';
    public const CHANGE_CONTACT = 'changeContact';
    public const CHANGE_PARTNER = 'changePartner';
    public const CHANGE_SERVICE = 'changeService';

    public const NAMES = [
        self::NEW_REQUEST => 'Поступила заявка',
        self::ACCEPTED => 'Заявка принята',
        self::FIXED => 'Работоспособность восстановлена',
        self::REPAIRED => 'Работы завершены',
        self::CLOSED => 'Заявка закрыта',
        self::CANCELED => 'Заявка отменена',
        self::UNCLOSED => 'Заявка открыта повторно',
        self::UNCANCELED => 'Заявка возвращена',
        self::ON_WAIT => 'Заявка приостановлена',
        self::OFF_WAIT => 'Заявка возобновлена',
        self::ADD_COMMENT => 'Добавлен коммментарий',
        self::ADD_DOCUMENT => 'Добавлен документ',
        self::TIME_50 => 'Осталось 50% времени',
        self::TIME_20 => 'Осталось 20% времени',
        self::TIME_00 => 'Заявка просрочена',
        self::AUTOCLOSE => 'Заявка закрыта автоматически',
        self::CHANGE_EQUIPMENT => 'Изменено оборудование',
        self::CHANGE_CONTACT => 'Изменено контактное лицо',
        self::CHANGE_PARTNER => 'Изменён партнёр',
        self::CHANGE_SERVICE => 'Изменена услуга'
    ];
}
