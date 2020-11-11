<?php
namespace Backend\API;

class Service extends Entity
{
    protected static string $table = 'services';
    protected static string $desc = 'Услуга';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID услуги'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название услуги'
        ],
        'shortName' => [
            'field' => 'shortName',
            'type' => 'string',
            'required' => true,
            'desc' => 'Сокращённое название услуги'
        ],
        'utility' => [
            'field' => 'utility',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Служебная услуга (например, плановая)'
        ],
        'autoOnly' => [
            'field' => 'autoOnly',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Услуга назначается только автоматически (например, неизвестная)'
        ]
    ];

    public static function getList($params, $payload)
    {
        $user = \Backend\API\User::getById($payload['user']);
        $slaIds = array_reduce(
            $user->getAllowedDivisions(),
            function ($acc, $el) {
                return array_unique(array_merge($acc, $el->slaIds));
            },
            []
        );
        $serviceIds = array_reduce(
            \Backend\API\SLA::getListByIds($slaIds),
            function ($acc, $el) {
                if (!in_array($el->serviceId, $acc)) {
                    $acc[] = $el->serviceId;
                }
                return $acc;
            },
            []
        );
        foreach (\Backend\API\Service::getListByIds($serviceIds) as $service) {
            $result[] = [
                'id' => $service->id,
                'name' => $service->name,
                'shortName' => $service->shortName
            ];
        }
        return ['services' => $result];
    }

    public static function getAllowed(array $params, array $payload) : array
    {
        $all = $params['all'] ?? false;
        $divisionId = $params['division'] ?? null;
        $user = \Backend\API\User::getById($payload['user']);
        if (!in_array($divisionId, $user->getAllowedDivisionIds(!$all))) {
            return [];
        }
        $division = \Backend\API\Division::getById($divisionId);
        $serviceIds = [];
        foreach ($division->slaIds as $slaId) {
            $serviceIds[] = \Backend\API\SLA::getById($slaId)->serviceId;
        }
        $services = array_filter(
            \Backend\API\Service::getListByIds(array_unique($serviceIds)),
            function ($service) {
                return (!$service->autoOnly && !$service->utility);
            }
        );
        return array_map(
            function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'shortName' => $service->shortName
                ];
            },
            array_values($services)
        );
    }

    public static function getLevels(array $params, array $payload) : array
    {
        $all = $params['all'] ?? false;
        $divisionId = $params['division'] ?? null;
        $serviceId = $params['service'] ?? null;
        $user = \Backend\API\User::getById($payload['user']);
        if ($serviceId === null || !in_array($divisionId, $user->getAllowedDivisionIds(!$all))) {
            return [];
        }
        $division = \Backend\API\Division::getById($divisionId);
        $slas = \Backend\API\SLA::getListByFilter([
            ['contractId', '=', $division->contractId],
            ['divisionTypeId', '=', $division->typeId],
            ['serviceId', '=', $serviceId]
        ]);
        $levels = [];
        foreach ($slas as $sla) {
            $levels[] = [
                'id' => $sla->level,
                'name' => \Backend\API\SlaLevel::NAMES[$sla->level],
                'default' => $sla->default
            ];
        }
        return $levels;
    }
}
