<?php
namespace Backend\API;

class EquipmentSubType extends Entity
{
    protected static string $table = 'equipmentSubTypes';
    protected static string $desc = 'Подтип оборудования';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID подтипа'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название'
        ],
        'typeId' => [
            'field' => 'equipmentType_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID типа'
        ],
        'type' => [
            'type' => '?ref',
            'refField' => 'typeId',
            'class' => '\Backend\API\EquipmentType',
            'readonly' => true,
            'desc' => 'Тип оборудования'
        ],
        'equipmentModels' => [
            'type' => '?backRef',
            'refField' => 'equipmentSubTypeId',
            'class' => '\Backend\API\EquipmentModel',
            'readonly' => true,
            'desc' => 'Модели оборудования с таким подтипом'
        ]
    ];
}
