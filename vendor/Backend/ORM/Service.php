<?php
namespace Backend\ORM;

class Service extends Entity
{
    protected static string $table = 'services';
    protected static string $desc = 'Услуга';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID услуги'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название услуги'
        ],
        'shortName' => [
            'field' => 'shortName',
            'type' => 'string',
            'required' => true,
            'desc' => 'Сокращённое название услуги'
        ],
        'utility' => [
            'field' => 'utility',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Служебная услуга (например, плановая)'
        ],
        'autoOnly' => [
            'field' => 'autoOnly',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Услуга назначается только автоматически (например, неизвестная)'
        ]
    ];
}
