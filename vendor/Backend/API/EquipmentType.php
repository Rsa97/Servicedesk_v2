<?php
namespace Backend\API;

class EquipmentType extends Entity
{
    protected static string $table = 'equipmentTypes';
    protected static string $desc = 'Тип оборудования';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID типа'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название типа'
        ],
        'equipmentSubTypes' => [
            'type' => '?backRef',
            'refField' => 'equipmentTypeId',
            'class' => '\Backend\API\EquipmentType',
            'readonly' => true,
            'desc' => 'Список подтипов'
        ]
    ];
}
