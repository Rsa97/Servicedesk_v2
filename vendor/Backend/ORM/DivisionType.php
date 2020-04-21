<?php
namespace Backend\ORM;

class DivisionType extends Entity
{
    protected static string $table = 'divisionTypes';
    protected static string $desc = 'Тип подразделения';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID типа подразделения'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название'
        ],
        'comment' => [
            'field' => 'comment',
            'type' => '?string',
            'required' => false,
            'desc' => 'Примечание'
        ]
    ];
}
