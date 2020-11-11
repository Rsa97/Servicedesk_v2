<?php
namespace Backend\API;

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
            'linkClass' => '\Backend\API\PartnerDivisionLink',
            'backRefField' => 'partnerId',
            'class' => '\Backend\API\Division',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Список обслуживаемых подразделений'
        ]
    ];

    public static function getList($params, $payload)
    {
        if (in_array($payload['rights'], [\Backend\API\UserRights::CLIENT, \Backend\API\UserRights::PARTNER])) {
            return ['partners' => []];
        }
        $partners = static::getListByFilter(['id', 'IS NOT NULL']);
        return [
            'partners' => array_map(
                function ($partner) {
                    return [
                        'id' => $partner->id,
                        'name' => $partner->name
                    ];
                },
                $partners
            )
        ];
    }

    public static function getAllowed(array $params, array $payload) : array
    {
        $all = $params['all'] ?? false;
        $divisionId = $params['division'] ?? null;
        $user = \Backend\API\User::getById($payload['user']);
        if (!in_array(
            'change_partner',
            \Backend\Config\User::ALLOWED_ACTIONS[$user->rights][\Backend\API\RequestState::RECEIVED]
        ) ||
            !in_array($divisionId, $user->getAllowedDivisionIds(!$all))
        ) {
            return [];
        }
        $division = \Backend\API\Division::getById($divisionId);
        return array_map(
            function ($partner) {
                return [
                    'id' => $partner->id,
                    'name' => $partner->name
                ];
            },
            $division->partners
        );
    }
}
