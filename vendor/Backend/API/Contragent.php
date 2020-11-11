<?php
namespace Backend\API;

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
            'class' => '\Backend\API\Contract',
            'readonly' => true,
            'desc' => 'Список договоров по контрагенту'
        ]
    ];

    public static function getAllowed(array $params, array $payload) : array
    {
        $all = $params['all'] ?? false;
        $user = \Backend\API\User::getById($payload['user']);
        $divisions = $user->getAllowedDivisions(!$all);
        $contragentIds = [];
        foreach ($divisions as $division) {
            $contragentIds[] = $division->contract->contragentId;
        }
        return array_map(
            function ($contragent) {
                return [
                    'id' => $contragent->id,
                    'name' => $contragent->name
                ];
            },
            static::getListByIds(array_unique($contragentIds))
        );
    }
}
