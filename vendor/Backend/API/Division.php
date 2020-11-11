<?php
namespace Backend\API;

class Division extends Entity
{
    protected $slaIds = null;

    protected static string $table = 'contractDivisions';
    protected static string $desc = 'Подразделение клиента в разрезе договора';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID подразделения'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название'
        ],
        'email' => [
            'field' => 'email',
            'type' => '?string',
            'desc' => 'Адрес электронной почты'
        ],
        'phone' => [
            'field' => 'phone',
            'type' => '?string',
            'desc' => 'Номер телефона'
        ],
        'address' => [
            'field' => 'address',
            'type' => '?string',
            'desc' => 'Реальный адрес'
        ],
        'yurAddress' => [
            'field' => 'yurAddress',
            'type' => '?string',
            'desc' => 'Юридический адрес'
        ],
        'contractId' => [
            'field' => 'contract_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID договора'
        ],
        'contract' => [
            'type' => '?ref',
            'refField' => 'contractId',
            'class' => '\Backend\API\Contract',
            'readonly' => true,
            'desc' => 'Договор'
        ],
        'contragentId' => [
            'field' => 'contragent_guid',
            'type' => '?uuid',
            'desc' => 'ID контрагента'
        ],
        'contragent' => [
            'type' => '?ref',
            'refField' => 'contragentId',
            'class' => '\Backend\API\Contragent',
            'readonly' => true,
            'desc' => 'Контрагент (если подразделение - отдельное юрлицо)'
        ],
        'typeId' => [
            'field' => 'type_guid',
            'type' => '?uuid',
            'desc' => 'ID типа подразделения'
        ],
        'type' => [
            'type' => '?ref',
            'refField' => 'typeId',
            'class' => '\Backend\API\DivisionType',
            'readonly' => true,
            'desc' => 'Тип подразделения'
        ],
        'additionalProblem' => [
            'field' => 'addProblem',
            'type' => '?string',
            'desc' => 'Дополнительные задачи на следующий выезд'
        ],
        'disabled' => [
            'field' => 'isDisabled',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Подразделение заблокировано'
        ],
        'engineerId' => [
            'field' => 'engineer_guid',
            'type' => '?uuid',
            'desc' => 'ID ответственного инженера'
        ],
        'engineer' => [
            'type' => '?ref',
            'refField' => 'typeId',
            'class' => '\Backend\API\User',
            'readonly' => true,
            'desc' => 'Ответственный инженер'
        ],
        'smsToDuty' => [
            'field' => 'smsToDuty',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Отправлять ли SMS дежурному инженеру при приходе заявки'
        ],
        'workplaces' => [
            'type' => '?backRef',
            'class' => '\Backend\API\Workplace',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Список рабочих мест'
        ],
        'equipment' => [
            'type' => '?backRef',
            'class' => '\Backend\API\Equipment',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Список оборудования'
        ],
        'partners' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\API\PartnerDivisionLink',
            'backRefField' => 'divisionId',
            'class' => '\Backend\API\Partner',
            'refField' => 'partnerId',
            'readonly' => true,
            'desc' => 'Список партнёров, обслуживающих подразделение'
        ],
        'users' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\API\UserDivisionLink',
            'backRefField' => 'divisionId',
            'class' => '\Backend\API\User',
            'refField' => 'userId',
            'readonly' => true,
            'desc' => 'Список пользователей клиента, ответственных за подразделение'
        ],
        'slaIds' => [
            'type' => 'refm2m',
            'class' => '\Backend\API\SLA',
            'readonly' => true,
            'desc' => 'Список SLA, связанный с подразделением'
        ]
    ];

    public function __get(string $name)
    {
        switch ($name) {
            case 'slaIds':
                if ($this->slaIds === null) {
                    $ids = \Backend\API\SLA::getIdsListByFilter([
                        'AND', [
                            ['contractId', '=', $this->contractId],
                            ['divisionTypeId', '=', $this->typeId]
                        ]
                    ]);
                    $this->slaIds = $ids;
                }
                return $this->slaIds;
            case 'slas':
                return \Backend\API\SLA::getListByIds($this->slaIds);
            default:
                return parent::__get($name);
        }
    }
    
    public static function getList(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $divs = $user->getAllowedDivisions();
        $divsList = [];
        $typesList = [];
        foreach ($divs as $div) {
            if (!array_key_exists($div->contract->contragentId, $divsList)) {
                $divsList[$div->contract->contragentId] = [
                    'id' => $div->contract->contragentId,
                    'name' => $div->contract->contragent->name,
                    'contracts' => [],
                    'divTypeIds' => []
                ];
            }
            if (!array_key_exists($div->contragentId, $divsList[$div->contract->contragentId]['contracts'])) {
                $divsList[$div->contract->contragentId]['contracts'][$div->contragentId] = [
                    'id' => $div->contractId,
                    'name' => $div->contract->number,
                    'divisions' => [],
                    'divTypeIds' => []
                ];
            }
            $divsList[$div->contract->contragentId]['contracts'][$div->contragentId]['divisions'][] = [
                'id' => $div->id,
                'name' => $div->name,
                'divTypeIds' => [$div->typeId]
            ];
            if (!in_array($div->typeId, $divsList[$div->contract->contragentId]['divTypeIds'])) {
                $divsList[$div->contract->contragentId]['divTypeIds'][] = $div->typeId;
            }
            if (!in_array(
                $div->typeId,
                $divsList[$div->contract->contragentId]['contracts'][$div->contragentId]['divTypeIds']
            )) {
                $divsList[$div->contract->contragentId]['contracts'][$div->contragentId]['divTypeIds'][] = $div->typeId;
            }
            if (!array_key_exists($div->typeId, $typesList)) {
                $typesList[$div->typeId] = [
                    'id' => $div->typeId,
                    'name' => $div->type->name
                ];
            }
        }
        $divsList = array_values($divsList);
        foreach ($divsList as &$contragent) {
            $contragent['contracts'] = array_values($contragent['contracts']);
        }
        $typesList = array_values($typesList);
        return ['contragents' => $divsList, 'divisionTypes' => $typesList];
    }

    public static function getAllowed(array $params, array $payload) : array
    {
        $all = $params['all'] ?? false;
        $contractId = $params['contract'] ?? null;
        $user = \Backend\API\User::getById($payload['user']);
        $divisionIds = $user->getAllowedDivisionIds(!$all);
        $conditions = [['id', 'IN', array_unique($divisionIds)]];
        if ($contractId !== null) {
            $conditions[] = ['contractId', '=', $contractId];
        }
        return array_map(
            function ($division) {
                return [
                    'id' => $division->id,
                    'name' => $division->name,
                    'address' => $division->address
                ];
            },
            static::getListByFilter($conditions)
        );
    }
}
