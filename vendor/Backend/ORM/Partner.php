<?php
namespace Backend\ORM;

class Partner extends Entity
{
    protected static string $table = 'partners';
    protected static string $desc = 'Партнёр';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID партнёра'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Наименование'
        ],
        'address' => [
            'field' => 'address',
            'type' => '?string',
            'desc' => 'Адрес'
        ],
        'divisions' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\PartnerDivisionLink',
            'backRefField' => 'partnerId',
            'class' => '\Backend\ORM\Division',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Список обслуживаемых подразделений'
        ]
    ];
}
