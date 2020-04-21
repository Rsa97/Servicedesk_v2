<?php
namespace Backend\ORM;

class Workplace extends Entity
{
    protected static string $table = 'divisionWorkplaces';
    protected static string $desc = 'Рабочее место';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID рабочего места'
        ],
        'divisionId' => [
            'field' => 'division_guid',
            'type' => '?uuid',
            'desc' => 'GUID подразделения'
        ],
        'division' => [
            'type' => '?ref',
            'refField' => 'divisionId',
            'class' => '\Backend\ORM\Division',
            'readonly' => true,
            'desc' => 'Подразделение'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Наименование'
        ],
        'description' => [
            'field' => 'description',
            'type' => '?string',
            'desc' => 'Описание рабочего места'
        ],
        'equipments' => [
            'type' => '?backRef',
            'refField' => 'workplaceId',
            'class' => '\Backend\ORM\Equipment',
            'readonly' => true,
            'desc' => 'Список оборудования'
        ]
    ];
}
