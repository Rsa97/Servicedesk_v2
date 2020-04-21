<?php
namespace Backend\ORM;

class Contragent extends Entity
{
    protected static string $table = 'contragents';
    protected static string $desc = 'Контрагент';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID контрагента'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Краткое наименование'
        ],
        'fullName' => [
            'field' => 'fullName',
            'type' => 'string',
            'required' => true,
            'desc' => 'Полное наименование'
        ],
        'parentId' => [
            'field' => 'parent_guid',
            'type' => '?uuid',
            'desc' => 'GUID контрагента-родителя'
        ],
        'parent' => [
            'type' => 'ref',
            'refField' => 'parentId',
            'class' => 'Contragent',
            'desc' => 'Контрагент-родитель'
        ],
        'inn' => [
            'field' => 'INN',
            'type' => '?string',
            'desc' => 'ИНН'
        ],
        'kpp' => [
            'field' => 'KPP',
            'type' => '?string',
            'desc' => 'КПП'
        ],
        'smsName' => [
            'field' => 'smsName',
            'type' => '?string',
            'desc' => 'Сокращённое наименование для SMS'
        ],
        'contracts' => [
            'type' => '?backRef',
            'refField' => 'contragentId',
            'class' => '\Backend\ORM\Contract',
            'readonly' => true,
            'desc' => 'Список договоров по контрагенту'
        ]
    ];
}
