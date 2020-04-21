<?php
namespace Backend\ORM;

class EquipmentModel extends Entity
{
    protected static string $table = 'equipmentModels';
    protected static string $desc = 'Модель оборудования';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID модели'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название модели'
        ],
        'subTypeId' => [
            'field' => 'equipmentSubType_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID подтипа'
        ],
        'subType' => [
            'type' => '?ref',
            'refField' => 'equipmentSubTypeId',
            'class' => '\Backend\ORM\EquipmentSubType',
            'readonly' => true,
            'desc' => 'Подтип оборудованияz'
        ],
        'manufacturerId' => [
            'field' => 'equipmentManufacturer_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID производителя'
        ],
        'manufacturer' => [
            'type' => '?ref',
            'refField' => 'manufacturerId',
            'class' => '\Backend\ORM\Manufacturer',
            'readonly' => true,
            'desc' => 'Производитель оборудования'
        ]
    ];
}
