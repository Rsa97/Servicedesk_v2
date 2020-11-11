<?php
namespace Backend\API;

class Manufacturer extends Entity
{
    protected static string $table = 'equipmentManufacturers';
    protected static string $desc = 'Производитель оборудования';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID производителя'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название производителя'
        ],
        'models' => [
            'type' => '?backRef',
            'class' => '\Backend\API\EquipmentModel',
            'refField' => 'manufacturerId',
            'readonly' => true,
            'desc' => 'Список моделей'
        ]
    ];
}
