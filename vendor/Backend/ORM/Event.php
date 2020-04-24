<?php
namespace Backend\ORM;

class Event extends Entity
{
    protected static string $table = 'requiestEvents';
    protected static string $desc = 'Событие по заявке';
    protected static array $map = [
        'id' => [
            'field' => 'id',
            'type' => '?numeric',
            'readonly' => true,
            'required' => true,
            'desc' => 'ID события'
        ],
        'timestamp' => [
            'field' => 'timestamp',
            'type' => 'datetime',
            'required' => true,
            'desc' => 'Дата и время события'
        ],
        'event' => [
            'field' => 'event',
            'type' => 'event',
            'required' => true,
            'desc' => 'Тип события'
        ],
        'text' => [
            'field' => 'text',
            'type' => '?string',
            'desc' => 'Текстовое описание события'
        ],
        'newState' => [
            'field' => 'newState',
            'type' => '?state',
            'desc' => 'Новый статус заявки'
        ],
        'requestGuid' => [
            'field' => 'request_guid',
            'type' => '?uuid',
            'desc' => 'GUID заявки'
        ],
        'requestId' => [
            'field' => 'request_id',
            'type' => 'numeric',
            'required' => true,
            'desc' => 'ID заявки'
        ],
        'request' => [
            'type' => '?ref',
            'refField' => 'requestId',
            'class' => '\Backend\ORM\Request',
            'readonly' => true,
            'desc' => 'Заявка, по которой произошло событие'
        ],
        'userId' => [
            'field' => 'user_guid',
            'type' => '?uuid',
            'required' => true,
            'desc' => 'GUID пользвателя'
        ],
        'user' => [
            'type' => '?ref',
            'refField' => 'userId',
            'class' => '\Backend\ORM\User',
            'readonly' => true,
            'desc' => 'Пользователь, сгенерировавший событие'
        ],
        'mailed' => [
            'field' => 'mailed',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Информация по событию отправлена'
        ],
        'document' => [
            'field' => 'document',
            'type' => '?string',
            'desc' => 'Название документа, привязанного к событию'
        ],
        'documentID' => [
            'field' => 'document',
            'type' => '?uuid',
            'desc' => 'GUID документа'
        ],
    ];
}
