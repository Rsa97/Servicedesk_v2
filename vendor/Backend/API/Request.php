<?php
namespace Backend\API;

class Request extends Entity
{
    protected static string $table = 'requests';
    protected static string $desc = 'Заявка';
    protected static array $map = [
        'id' => [
            'field' => 'id',
            'type' => '?integer',
            'readonly' => true,
            'required' => true,
            'default' => null,
            'desc' => 'ID заявки'
        ],
        'guid' => [
            'field' => 'guid',
            'type' => '?uuid',
            'desc' => 'GUID заявки'
        ],
        'num1C' => [
            'field' => 'num1c',
            'type' => '?string',
            'desc' => 'Номер в 1С'
        ],
        'problem' => [
            'field' => 'problem',
            'type' => 'string',
            'required' => true,
            'desc' => 'Заявленная проблема'
        ],
        'createdAt' => [
            'field' => 'createdAt',
            'type' => 'datetime',
            'required' => true,
            'desc' => 'Дата и время создания заявки'
        ],
        'reactBefore' => [
            'field' => 'reactBefore',
            'type' => 'datetime',
            'required' => true,
            'desc' => 'Дата и время, до котрых надо принять заявку'
        ],
        'reactedAt' => [
            'field' => 'reactedAt',
            'type' => '?datetime',
            'desc' => 'Дата и время принятия заявки'
        ],
        'fixBefore' => [
            'field' => 'fixBefore',
            'type' => 'datetime',
            'required' => true,
            'desc' => 'Дата и время, до которых надо восстановить работоспособность'
        ],
        'fixedAt' => [
            'field' => 'fixedAt',
            'type' => '?datetime',
            'desc' => 'Дата и время восстановления работоспособности'
        ],
        'repairBefore' => [
            'field' => 'repairBefore',
            'type' => 'datetime',
            'required' => true,
            'desc' => 'Дата и время, до которых надо закрыть заявку'
        ],
        'repairedAt' => [
            'field' => 'repairedAt',
            'type' => '?datetime',
            'desc' => 'Дата и время закрытия заявки'
        ],
        'state' => [
            'field' => 'currentState',
            'type' => 'state',
            'required' => true,
            'desc' => 'Текущий статус заявки'
        ],
        'contactId' => [
            'field' => 'contactPerson_guid',
            'type' => '?uuid',
            'required' => true,
            'desc' => 'GUID контактного лица'
        ],
        'contact' => [
            'type' => '?ref',
            'refField' => 'contactId',
            'class' => '\Backend\API\User',
            'readonly' => true,
            'desc' => 'Контактное лицо'
        ],
        'divisionId' => [
            'field' => 'contractDivision_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID подразделения'
        ],
        'division' => [
            'type' => '?ref',
            'refField' => 'divisionId',
            'class' => '\Backend\API\Division',
            'readonly' => true,
            'desc' => 'Подразделение'
        ],
        'level' => [
            'field' => 'slaLevel',
            'type' => 'level',
            'required' => true,
            'default' => SlaLevel::LOW,
            'desc' => 'Уровень SLA'
        ],
        'engineerId' => [
            'field' => 'engineer_guid',
            'type' => '?uuid',
            'desc' => 'GUID инженера'
        ],
        'engineer' => [
            'type' => '?ref',
            'refField' => 'engineerId',
            'class' => '\Backend\API\User',
            'readonly' => true,
            'desc' => 'Инженер, принявший заявку'
        ],
        'equipmentId' => [
            'field' => 'equipment_guid',
            'type' => '?uuid',
            'desc' => 'GUID оборудования'
        ],
        'equipment' => [
            'type' => '?ref',
            'refField' => 'equipmentId',
            'class' => '\Backend\API\Equipment',
            'readonly' => true,
            'desc' => 'Оборудование, с которым возникли проблемы'
        ],
        'serviceId' => [
            'field' => 'service_guid',
            'type' => '?uuid',
            'desc' => 'GUID услуги'
        ],
        'service' => [
            'type' => '?ref',
            'refField' => 'serviceId',
            'class' => '\Backend\API\Service',
            'readonly' => true,
            'desc' => 'Услуга по заявке'
        ],
        'wait' => [
            'field' => 'onWait',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Заявка приостановлена'
        ],
        'waitFrom' => [
            'field' => 'onWaitAt',
            'type' => '?datetime',
            'desc' => 'Дата и время приостановки заявки'
        ],
        'alarm' => [
            'field' => 'alarm',
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
            'desc' => 'Уровень тревоги для отсылки email'
        ],
        'realProblem' => [
            'field' => 'solutionProblem',
            'type' => '?string',
            'desc' => 'Обнаруженная проблема'
        ],
        'solution' => [
            'field' => 'solution',
            'type' => '?string',
            'desc' => 'Решение проблемы'
        ],
        'recomendation' => [
            'field' => 'solutionRecomendation',
            'type' => '?string',
            'desc' => 'Рекомендации клиенту'
        ],
        'toReact' => [
            'field' => 'toReact',
            'type' => 'numeric',
            'required' => true,
            'default' => 10,
            'desc' => 'Время на принятие заявки, минуты'
        ],
        'toFix' => [
            'field' => 'toFix',
            'type' => 'numeric',
            'required' => true,
            'default' => 10,
            'desc' => 'Время на восстановление работоспособности, минуты'
        ],
        'toRepair' => [
            'field' => 'toRepair',
            'type' => 'numeric',
            'required' => true,
            'default' => 10,
            'desc' => 'Время на звкрытие заявки, минуты'
        ],
        'reactRate' => [
            'field' => 'reactRate',
            'type' => '?numeric',
            'required' => true,
            'default' => null,
            'desc' => 'Скорость принятия заявки, доля'
        ],
        'fixRate' => [
            'field' => 'fixRate',
            'type' => '?numeric',
            'required' => true,
            'default' => null,
            'desc' => 'Скорость восстановления работоспособности, доля'
        ],
        'repairRate' => [
            'field' => 'repairRate',
            'type' => '?numeric',
            'required' => true,
            'default' => null,
            'desc' => 'Скорость закрытия заявки, доля'
        ],
        'synced' => [
            'field' => 'syncId',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Статус синхронизации с 1С'
        ],
        'totalWait' => [
            'field' => 'totalWait',
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
            'desc' => 'Общее время приостановки работ по заявке'
        ],
        'planned' => [
            'field' => 'isPlanned',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Заявка плановая'
        ],
        'partnerId' => [
            'field' => 'partner_guid',
            'type' => '?uuid',
            'desc' => 'GUID партнёра'
        ],
        'partner' => [
            'type' => '?ref',
            'refField' => 'partnerId',
            'class' => '\Backend\API\Partner',
            'readonly' => true,
            'desc' => 'Партнёр, которому назначена заявка'
        ],
        'events' => [
            'type' => '?backRef',
            'class' => '\Backend\API\Event',
            'refField' => 'requestId',
            'readonly' => true,
            'desc' => 'Список событий по заявке'
        ],
        'waitTo' => [
            'field' => 'waitTo',
            'type' => '?date',
            'default' => null,
            'desc' => 'Дата автоматического снятия ожидания'
        ]
    ];

    protected static function filteredDivisionIds(array $filter, \Backend\API\User $user) : array
    {
        $allowedDivisionIds = $user->getAllowedDivisionIds();
        $divisions = [];
        $contragentId = $filter['contragent'] ?? null;
        if ($contragentId !== null) {
            $contragents = \Backend\API\Contragent::getListByIds([$contragentId]);
            foreach ($contragents as $contragent) {
                foreach ($contragent->contracts as $contract) {
                    foreach ($contract->divisions as $division) {
                        $divisions[] = $division->id;
                    }
                }
            }
        }
        $contractId = $filter['contract'] ?? null;
        if ($contractId !== null) {
            $contracts = \Backend\API\Contract::getListByIds([$contractId]);
            foreach ($contracts as $contract) {
                foreach ($contract->divisions as $division) {
                    $divisions[] = $division->id;
                }
            }
        }
        $divisionId = $filter['division'] ?? null;
        if ($divisionId !== null) {
            $divisions[] = $divisionId;
        }
        if ($contragentId !== null || $contractId !== null || $divisionId !== null) {
            $divisions = array_intersect($divisions, $allowedDivisionIds);
        } else {
            $divisions = $allowedDivisionIds;
        }
        $divisionType = $filter['divisionTypes'] ?? null;
        if ($divisionType !== null) {
            $divisions = array_intersect(
                $divisions,
                \Backend\API\Division::getIdsListByFilter([ 'AND', [ 'typeId', '=', $divisionType ] ])
            );
        }
        return $divisions;
    }

    protected static function filteredInterval(array $filter) : array
    {
        $interval = $filter['interval'] ?? null;
        $fromDate = null;
        $toDate = null;
        switch ($interval) {
            case '1y':
                $fromDate = strftime('%Y-%m-%d 00:00:00', strtotime('-1 year'));
                break;
            case '3m':
                $fromDate = strftime('%Y-%m-%d 00:00:00', strtotime('-3 months'));
                break;
            case '1m':
                $fromDate = strftime('%Y-%m-%d 00:00:00', strtotime('-1 month'));
                break;
            case '1w':
                $fromDate = strftime('%Y-%m-%d 00:00:00', strtotime('-1 week'));
                break;
            default:
                $fromDate = $filter['fromDate'] ?? '';
                if (!preg_match('/^\d\d\d\d-\d\d-\d\d$/', $fromDate)) {
                    $fromDate = strftime('%Y-%m-%d', strtotime('-3 months'));
                }
                $fromDate .= ' 00:00:00';
                $toDate = $filter['toDate'] ?? '';
                if (!preg_match('/^\d\d\d\d-\d\d-\d\d$/', $toDate)) {
                    $toDate = null;
                } else {
                    $toDate .= ' 23:59:59';
                }
                break;
        }
        $filteredInterval = [ [ 'createdAt', '>=', $fromDate ] ];
        if ($toDate !== null) {
            $filteredInterval[] = [ 'createdAt', '<=', $toDate ];
        }
        return $filteredInterval;
    }

    protected static function buildFilter(array $filter, \Backend\API\User $user) : array
    {
        $filteredWhere = [
            [ 'divisionId', 'IN', static::filteredDivisionIds($filter, $user) ]
        ];
        $partnerId = $filter['partner'] ?? null;
        if ($partnerId !== null) {
            $filteredWhere[] = [ 'partnerId', '=', $partnerId ];
        }
        $excludedServices = $filter['excludedServices'] ?? [];
        if (count($excludedServices) !== 0) {
            $filteredWhere[] = [ 'serviceId', 'NOT IN', $excludedServices ];
        }
        $text = $filter['text'] ?? '';
        if ($text !== '') {
            $filteredWhere[] = [ 'problem', 'LIKE', "%{$text}%" ];
        }
        
        $filteredWhere[] = [
            'OR', [
                static::filteredInterval($filter),
                [ 'state', 'NOT IN', ['closed', 'canceled' ] ]
            ]
        ];
        return $filteredWhere;
    }

    protected static function mapCounts(array $counts) : array
    {
        $result = [];
        foreach ($counts as $count) {
            $result[$count['state']] = +$count['count'];
        }
        return $result;
    }

    public static function getCounts(array $params, array $payload) : array
    {
        $filter = $params['filter'] ?? [];
        $user = \Backend\API\User::getById($payload['user']);
        $totalWhere = [
            [ 'divisionId', 'IN', $user->getAllowedDivisionIds() ],
            [
                'OR', [
                    [ 'createdAt', '>=', strftime('%Y-%m-%d 00:00:00', strtotime('-1 years')) ],
                    [ 'state', 'NOT IN', ['closed', 'canceled' ] ]
                ]
            ]
        ];
        $filteredWhere = static::buildFilter($filter, $user);
        return [
            'total' => static::mapCounts(\Backend\API\Request::getCountsByFilter($totalWhere, ['state'])),
            'filtered' => static::mapCounts(\Backend\API\Request::getCountsByFilter($filteredWhere, ['state']))
        ];
    }

    public static function getList(array $params, array $payload) : array
    {
        $filter = $params['filter'] ?? [];
        $states = $params['states'] ?? [];
        $user = \Backend\API\User::getById($payload['user']);
        $filter = static::buildFilter($filter, $user);
        $filter[] = [ 'state', 'IN', $states ];
        $requests = \Backend\API\Request::getListByFilter($filter);
        return array_map(
            function ($req) {
                $num1C = null;
                if ($req->synced) {
                    $num1C = $req->num1C ?? '';
                }
                return [
                    'number' => $req->id,
                    'state' => [
                        'current' => $req->state,
                        'wait' => $req->wait,
                        'sync1C' => $num1C
                    ],
                    'slaLevel' => \Backend\API\SlaLevel::NAMES[$req->level],
                    'partner' => [
                        'id' => $req->partnerId,
                        'name' => $req->partnerId === null ? null : $req->partner->name
                    ],
                    'service' => [
                        'name' => $req->service->name,
                        'shortName' => $req->service->shortName
                    ],
                    'problem' => \Backend\Common\Strings::preformat($req->problem),
                    'time' => [
                        'createdAt' => strftime('%d.%m.%Y %H:%M', strtotime($req->createdAt)),
                        'reactBefore' => strftime('%d.%m.%Y %H:%M', strtotime($req->reactBefore)),
                        'reactedAt' => $req->reactedAt === null
                            ? null
                            : strftime('%d.%m.%Y %H:%M', strtotime($req->reactedAt)),
                        'fixBefore' => strftime('%d.%m.%Y %H:%M', strtotime($req->fixBefore)),
                        'fixedAt' => $req->fixedAt === null
                            ? null
                            :strftime('%d.%m.%Y %H:%M', strtotime($req->fixedAt)),
                        'repairBefore' => strftime('%d.%m.%Y %H:%M', strtotime($req->repairBefore)),
                        'repairedAt' => $req->repairedAt === null
                            ? null
                            : strftime('%d.%m.%Y %H:%M', strtotime($req->repairedAt)),
                        'reactRate' => $req->reactRate,
                        'fixRate' => $req->fixRate,
                        'repairRate' => $req->repairRate,
                        'waitFrom' => $req->wait
                            ? strftime('%d.%m.%Y %H:%M', strtotime($req->waitFrom))
                            : null,
                        'waitTo' => $req->waitTo !== null
                            ? strftime('%d.%m.%Y', strtotime($req->waitTo))
                            : null
                    ],
                    'contragent' => $req->division->contract->contragent->name,
                    'contract' => $req->division->contract->number,
                    'division' => $req->division->name,
                    'contact' => [
                        'name' => $req->contact === null ? '' : $req->contact->fullName,
                        'email' => $req->contact === null ? '' : $req->contact->email,
                        'phone' => $req->contact === null ? '' : $req->contact->phone
                    ],
                    'engineer' => $req->engineerId === null
                        ? null
                        : [
                            'name' => $req->engineer->fullName,
                            'shortName' => $req->engineer->shortName,
                            'email' => $req->engineer->email,
                            'phone' => $req->engineer->phone
                        ],
                    'hasPartners' => count($req->division->partners) > 0
                ];
            },
            $requests
        );
    }

    public static function getRates(array $params, array $payload) : array
    {
        $numbers = $params['numbers'] ?? [];
        if (count($numbers) === 0) {
            return [];
        }
        $requests = static::getListByIds($numbers);
        $result = [];
        foreach ($requests as $req) {
            $result[$req->id] = $req->calcRates();
        }
        return $result;
    }

    protected function canChange(string $what, \Backend\API\User $user) : bool
    {
        return in_array(
            "change_{$what}",
            \Backend\Config\User::ALLOWED_ACTIONS[$user->rights][$this->state]
        );
    }

    protected function allowedServices(\Backend\API\User $user) : array
    {
        $serviceIds = [$this->serviceId];
        if ($this->canChange('service', $user)) {
            $slas = $this->division->slaIds;
            foreach (\Backend\API\SLA::getListByIds($slas) as $sla) {
                if (!$sla->service->utility && !$sla->service->autoOnly) {
                    $serviceIds[] = $sla->serviceId;
                }
            }
        }
        return \Backend\API\Service::getListByIds(array_unique($serviceIds));
    }

    protected function allowedLevels(\Backend\API\User $user) : array
    {
        $levels = [$this->level];
        if ($this->canChange('service', $user)) {
            $slas = \Backend\API\SLA::getListByFilter([
                ['serviceId', '=', $this->serviceId],
                ['divisionTypeId', '=', $this->division->typeId],
                ['contractId', '=', $this->division->contractId]
            ]);
            foreach ($slas as $sla) {
                $levels[] = $sla->level;
            }
        }
        return array_values(array_unique($levels));
    }

    protected function allowedEquipment(\Backend\API\User $user) : array
    {
        $eqIds = $this->equipmentId === null
            ? []
            : [$this->equipmentId];
        if ($this->canChange('equipment', $user)) {
            foreach ($this->division->equipment as $eq) {
                $eqIds[] = $eq->id;
            }
        }
        return \Backend\API\Equipment::getListByIds(array_unique($eqIds));
    }

    protected function allowedPartners(\Backend\API\User $user) : array
    {
        $partnerIds = $this->partnerId === null
            ? []
            : [$this->partnerId];
        if ($this->canChange('partner', $user)) {
            foreach ($this->division->partners as $partner) {
                $partnerIds[] = $partner->id;
            }
        }
        return \Backend\API\Partner::getListByIds(array_unique($partnerIds));
    }

    public function allowedContacts(\Backend\API\User $user) : array
    {
        $contactIds = $this->contactId === null
            ? []
            : [$this->contactId];
        if ($this->canChange('contact', $user)) {
            foreach ($this->division->users as $contact) {
                $contactIds[] = $contact->id;
            }
            foreach ($this->division->contract->users as $contact) {
                $contactIds[] = $contact->id;
            }
        }
        return \Backend\API\User::getListByIds(array_unique($contactIds));
    }

    public static function getRequest(array $params, array $payload) : array
    {
        $number = $params['number'] ?? null;
        if ($number === null) {
            throw new \Exception('Неверный номер заявки', -32220);
        }
        $user = \Backend\API\User::getById($payload['user']);
        $divisions = $user->getAllowedDivisionIds();
        $request = \Backend\API\Request::getById($number);
        if (!in_array($request->divisionId, $divisions)) {
            throw new \Exception('Недостаточно прав для просмотра заявки', -32221);
        }
        return [
            'number' => $request->id,
            'contragents' => [
                [
                    'id' => $request->division->contract->contragentId,
                    'name' => $request->division->contract->contragent->name,
                ]
            ],
            'contracts' => [
                [
                    'id' => $request->division->contractId,
                    'name' => $request->division->contract->number,
                    'address' => $request->division->contract->address
                ]
            ],
            'divisions' => [
                [
                    'id' => $request->divisionId,
                    'name' => $request->division->name,
                    'address' => $request->division->address,
                    'default' => true
                ]
            ],
            'services' => array_map(
                function ($service) use ($request) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'shortName' => $service->shortName,
                        'default' => $service->id === $request->serviceId,
                        'autoOnly' => $service->autoOnly
                    ];
                },
                $request->allowedServices($user)
            ),
            'levels' => array_map(
                function ($level) use ($request) {
                    return [
                        'id' => $level,
                        'name' => \Backend\API\SlaLevel::NAMES[$level],
                        'default' => $level === $request->level
                    ];
                },
                $request->allowedLevels($user)
            ),
            'problem' => \Backend\Common\Strings::preformat($request->problem),
            'equipment' => array_map(
                function ($eq) use ($request) {
                    return [
                        'id' => $eq->id,
                        'serviceNumber' => $eq->serviceNumber,
                        'serialNumber' => $eq->serialNumber,
                        'manufacturer' => $eq->equipmentModel->manufacturer->name,
                        'model' => $eq->equipmentModel->name,
                        'type' => $eq->equipmentModel->subType->type->name,
                        'subType' => $eq->equipmentModel->subType->name,
                        'default' => $eq->id === $request->equipmentId
                    ];
                },
                $request->allowedEquipment($user)
            ),
            'canChangeEquipment' => $request->canChange('equipment', $user),
            'createdAt' => strftime('%d.%m.%Y %H:%M', strtotime($request->createdAt)),
            'repairBefore' => strftime('%d.%m.%Y %H:%M', strtotime($request->repairBefore)),
            'partners' => array_map(
                function ($partner) use ($request) {
                    return [
                        'id' => $partner->id,
                        'name' => $partner->name,
                        'default' => $partner->id === $request->partnerId
                    ];
                },
                $request->allowedPartners($user)
            ),
            'canChangePartner' => $request->canChange('partner', $user),
            'contacts' => array_map(
                function ($contact) use ($request) {
                    return [
                        'id' => $contact->id,
                        'name' => $contact->fullName,
                        'email' => $contact->email,
                        'phone' => $contact->phone,
                        'default' => $contact->id === $request->contactId
                    ];
                },
                $request->allowedContacts($user)
            ),
            'canChangeContact' => $request->canChange('contact', $user),
            'solution' => [
                'problem' => $request->realProblem,
                'solution' => $request->solution,
                'recomendation' => $request->recomendation
            ],
            'events' => array_map(
                function ($event) {
                    return $event->format();
                },
                $request->events
            ),
        ];
    }

    public static function getPartners(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = \Backend\API\Request::getById($params['number']);
        return $request->allowedPartners($user);
    }

    public static function calcTime(array $params, array $payload) : array
    {
        $divisionId = $params['division'] ?? null;
        $serviceId = $params['service'] ?? null;
        $levelId = $params['level'] ?? null;
        $createdAt = time();
        if (array_key_exists('createdAt', $params) && $params['createdAt'] !== null) {
            $createdAt = date_create_from_format('d.m.Y H:i', $params['createdAt'])->format('U');
        }
        $times = \Backend\API\Calendar::calcTime($divisionId, $serviceId, $levelId, $createdAt);
        return [
            'createdAt' => strftime('%d.%m.%Y %H:%M', $times['createdAt']),
            'reactBefore' => strftime('%d.%m.%Y %H:%M', $times['reactBefore']),
            'fixBefore' => strftime('%d.%m.%Y %H:%M', $times['fixBefore']),
            'repairBefore' => strftime('%d.%m.%Y %H:%M', $times['repairBefore'])
        ];
    }

    public function calcRates() : array
    {
        $result = [
            'reactRate' => $this->reactRate,
            'fixRate' => $this->fixRate,
            'repairRate' => $this->repairRate
        ];
        $select = [];
        $values = [
            'id' => $this->id,
            'createdAt' => $this->createdAt
        ];
        if ($this->reactRate === null && $this->toReact !== null && $this->toReact != 0) {
            $select[] = "calcTime_V4(:id, :createdAt, IFNULL(:reactedAt, NOW())) AS `reactTime`";
            $values['reactedAt'] = $this->wait ? $this->waitFrom : $this->reactedAt;
        }
        if ($this->fixRate === null && $this->toFix !== null && $this->toFix != 0) {
            $select[] = "calcTime_V4(:id, :createdAt, IFNULL(:fixedAt, NOW())) AS `fixTime`";
            $values['fixedAt'] = $this->wait ? $this->waitFrom : $this->fixedAt;
        }
        if ($this->repairRate === null && $this->toRepair !== null && $this->toRepair != 0) {
            $select[] = "calcTime_V4(:id, :createdAt, IFNULL(:repairedAt, NOW())) AS `repairTime`";
            $values['repairedAt'] = $this->wait ? $this->waitFrom : $this->repairedAt;
        }
        if (count($select) === 0) {
            return $result;
        }
        $sql = "SELECT " . implode(',', $select);
        $db = \Backend\Common\DB::get();
        $req = $db->prepare($sql);
        $req->execute($values);
        if ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            if (array_key_exists('reactTime', $row)) {
                $result['reactRate'] = $row['reactTime'] / $this->toReact;
            }
            if (array_key_exists('fixTime', $row)) {
                $result['fixRate'] = $row['fixTime'] / $this->toFix;
            }
            if (array_key_exists('repairTime', $row)) {
                $result['repairRate'] = $row['repairTime'] / $this->toRepair;
            }
        }
        return $result;
    }

    public static function new(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $allowedDivisionIds = $user->getAllowedDivisionIds();
        $divisionId = $params['division'] ?? null;
        if (!in_array($divisionId, $allowedDivisionIds)) {
            throw new \Exception('Недостаточно прав для создания заявки по филиалу', -32221);
        }
        $division = \Backend\API\Division::getById($divisionId);
        $serviceId = $params['service'] ?? null;
        $levelId = $params['level'] ?? null;
        $slas = \Backend\API\SLA::getListByFilter([
            ['contractId', '=', $division->contractId],
            ['divisionTypeId', '=', $division->typeId],
            ['serviceId', '=', $serviceId],
            ['level', '=', $levelId]
        ]);
        if (count($slas) === 0) {
            throw new \Exception('Неверные параметры заявки', -32223);
        }
        $sla = $slas[0];
        $times = \Backend\API\Calendar::calcTime($divisionId, $serviceId, $levelId, time());
        $request = new self([
            'contactId' => $params['contact'] ?? null,
            'divisionId' => $divisionId,
            'equipmentId' => $params['equipment'] ?? null,
            'level' => $levelId,
            'planned' => false,
            'problem' => $params['problem'] ?? '',
            'serviceId' => $serviceId,
            'state' => \Backend\API\RequestState::PRERECEIVED,
            'synced' => false,
            'totalWait' => 0,
            'wait' => false,
            'createdAt' => strftime('%Y-%m-%d %H:%M:00', $times['createdAt']),
            'stateChangedAt' => strftime('%Y-%m-%d %H:%M:00', $times['createdAt']),
            'reactBefore' => strftime('%Y-%m-%d %H:%M:%S', $times['reactBefore']),
            'fixBefore' => strftime('%Y-%m-%d %H:%M:%S', $times['fixBefore']),
            'repairBefore' => strftime('%Y-%m-%d %H:%M:%S', $times['repairBefore']),
            'toReact' => $sla->toReact,
            'toFix' => $sla->toFix,
            'toRepair' => $sla->toRepair
        ]);
        $request->store();
        $request->new1C();
        $request->addEvent($user, \Backend\API\EventType::NEW_REQUEST);
        if ($request->synced) {
            $request->store();
            $partnerId = $params['partner'] ?? null;
            if ($request->$partnerId !== null) {
                $request->changePartner($user, $partnerId);
            }
        }
        return ['ok' => 'ok', 'number' => $request->id];
    }

    protected function checkAction(\Backend\API\User $user, string $action, bool $allowOnWait = false) : bool
    {
        if (!in_array($action, \Backend\Config\User::ALLOWED_ACTIONS[$user->rights][$this->state])) {
            throw new \Exception('Недостаточно прав для операции', -32224);
        }
        if ($user->rights === \Backend\API\UserRights::PARTNER && $user->partnerId !== $this->partnerId) {
            throw new \Exception('Недостаточно прав для операции', -32224);
        }
        if ($this->wait && !$allowOnWait) {
            throw new \Exception('Операция невозможна для приостановленной заявки', -32225);
        }
        if (!$this->synced) {
            throw new \Exception('Операция невозможна для заявки, не синхронизированной с 1С', -32226);
        }
        return true;
    }

    protected function changePartner(\Backend\API\User $user, ?string $partnerId)
    {
        $this->checkAction($user, 'change_partner');
        if ($partnerId !== null) {
            $partnerIds = array_map(
                function ($partner) {
                    return $partner->id;
                },
                $this->division->partners
            );
            if (!in_array($partnerId, $partnerIds)) {
                throw new \Exception('Некорректные данные', -32229);
            }
        }
        $this->changePartner1C();
        $this->partnerId = $partnerId;
        $this->store();
        $this->addEvent(
            $user,
            \Backend\API\EventType::CHANGE_PARTNER,
            $this->partnerId === null ? null : $this->partner->name
        );
    }

    public static function accept(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'accept');
        $request->accept1C();
        $request->state = \Backend\API\RequestState::ACCEPTED;
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $request->reactedAt = $request->stateChangedAt;
        $rates = $request->calcRates();
        $request->reactRate = $rates['reactRate'];
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::CHANGE_STATE);
        return ['ok' => 'ok'];
    }

    public static function fix(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'fix');
        $request->fix1C();
        $request->state = \Backend\API\RequestState::FIXED;
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $request->fixedAt = $request->stateChangedAt;
        $rates = $request->calcRates();
        $request->fixRate = $rates['fixRate'];
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::CHANGE_STATE);
        return ['ok' => 'ok'];
    }

    public static function repair(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $solution = $params['solution'] ?? null;
        if ($solution === null || ($solution['problem'] ?? '') === '' || ($solution['solution'] ?? '') === '') {
            throw new \Exception('Не указаны обязательные поля', -32227);
        }
        $request = self::getById($params['number']);
        $request->checkAction($user, 'repair');
        $request->repair1C();
        $request->state = \Backend\API\RequestState::REPAIRED;
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $request->realProblem = $solution['problem'];
        $request->solution = $solution['solution'];
        $request->recomendation = $solution['recomendation'];
        $request->repairedAt = $request->stateChangedAt;
        $rates = $request->calcRates();
        $request->repairRate = $rates['repairRate'];
        if ($request->fixedAt === null) {
            $request->fixedAt = $request->repairedAt;
            $request->fixRate = $rates['fixRate'];
        }
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::CHANGE_STATE);
        return ['ok' => 'ok'];
    }

    public static function close(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'close');
        $request->close1C();
        $request->state = \Backend\API\RequestState::CLOSED;
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $request->repairedAt = $request->stateChangedAt;
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::CHANGE_STATE);
        return ['ok' => 'ok'];
    }

    public static function cancel(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'cancel');
        $request->cancel1C();
        $request->state = \Backend\API\RequestState::CANCELED;
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $request->waitFrom = $request->stateChangedAt;
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::CHANGE_STATE, $params['cause'] ?? '');
        return ['ok' => 'ok'];
    }

    public static function unCancel(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'reopen');
        $request->unCancel1C();
        $request->revertState();
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $inactiveTime = \Backend\API\Calendar::calcInterval(
            $request->divisionId,
            $request->serviceId,
            $request->level,
            strtotime($request->waitFrom),
            time()
        );
        $request->totalWait += $inactiveTime;
        switch ($request->state) {
            case \Backend\API\RequestState::RECEIVED:
                $request->toReact += $inactiveTime;
                // no-break
            case \Backend\API\RequestState::ACCEPTED:
                $request->toFix += $inactiveTime;
                // no-break
            case \Backend\API\RequestState::FIXED:
                $request->toRepair += $inactiveTime;
        }
        $request->waitFrom = null;
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::UNCANCEL, $params['cause'] ?? '');
        return ['ok' => 'ok'];
    }

    public static function reopen(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'reopen');
        $request->reopen1C();
        $request->state = \Backend\API\RequestState::RECEIVED;
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $request->reactedAt = null;
        $request->reactRate = null;
        $request->fixedAt = null;
        $request->fixRate = null;
        $request->repairedAt = null;
        $request->repairRate = null;
        $request->waitFrom = null;
        $request->solution = null;
        $request->recomendation = null;
        $request->repairedAt = null;
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::UNCLOSE, $params['cause'] ?? '');
        return ['ok' => 'ok'];
    }

    public static function deny(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'deny');
        $request->deny1C();
        $request->revertState();
        $request->stateChangedAt = strftime('%Y-%m-%d %H:%M:00', time());
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::UNCLOSE, $params['cause'] ?? '');
        return ['ok' => 'ok'];
    }

    public static function waitOn(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'waitOn');
        $request->waitOn1C();
        $request->wait = true;
        $request->waitTo = $params['date'] ?? null;
        $request->waitFrom = strftime('%Y-%m-%d %H:%M:00', time());
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::ON_WAIT, $params['cause'] ?? '');
        return ['ok' => 'ok'];
    }

    public static function waitOff(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'waitOff', true);
        $request->waitOff1C();
        $request->wait = false;
        $inactiveTime = \Backend\API\Calendar::calcInterval(
            $request->divisionId,
            $request->serviceId,
            $request->level,
            strtotime($request->waitFrom),
            time()
        );
        $request->totalWait += $inactiveTime;
        switch ($request->state) {
            case \Backend\API\RequestState::RECEIVED:
                $request->toReact += $inactiveTime;
                // no-break
            case \Backend\API\RequestState::ACCEPTED:
                $request->toFix += $inactiveTime;
                // no-break
            case \Backend\API\RequestState::FIXED:
                $request->toRepair += $inactiveTime;
        }
        $request->waitFrom = null;
        $request->waitTo = null;
        $request->store();
        $request->addEvent($user, \Backend\API\EventType::OFF_WAIT, $params['comment'] ?? '');
        return ['ok' => 'ok'];
    }

    public static function setPartner(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        $request->checkAction($user, 'change_partner', true);
        $request->changePartner($user, $params['partnerId']);
        return ['ok' => 'ok'];
    }

    public static function addComment(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        if (!in_array($request->divisionId, $user->getAllowedDivisionIds())) {
            throw new \Exception('Недостаточно прав для операции', -32224);
        }
        $request->addComment1C();
        $event = $request->addEvent($user, \Backend\API\EventType::ADD_COMMENT, $params['comment'] ?? '');
        return ['ok' => 'ok', 'event' => $event->format()];
    }

    public static function addDocument(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        if (!in_array($request->divisionId, $user->getAllowedDivisionIds())) {
            throw new \Exception('Недостаточно прав для операции', -32224);
        }
        $name = $params['name'] ?? 'Unknown';
        $content = base64_decode($params['content'] ?? '');
        $length = strlen($content);
        if ($length === 0 || $length !== ($params['size'] ?? 0)) {
            throw new \Exception('Некорректные данные', -32229);
        }
        $documentGuid = $request->addDocument1C($name, $content);
        $dir = \Backend\Config\Document::DIR . $request->id;
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        if (!is_dir($dir)) {
            throw new \Exception('Ошибка сохранения файла', -32230);
        }
        $fileName = \Backend\Common\Strings::gudiWithDashes($documentGuid);
        $file = fopen("$dir/$fileName", 'w');
        if ($file === false || fwrite($file, $content, $length) !== $length) {
            throw new \Exception('Ошибка сохранения файла', -32230);
        }
        fclose($file);
        $event = $request->addEvent($user, \Backend\API\EventType::ADD_DOCUMENT, null, $name, $documentGuid);
        return ['ok' => 'ok', 'event' => $event->format()];
    }

    public static function getDocument(array $params, array $payload) : array
    {
        $docId = $params['id'] ?? null;
        $events = \Backend\API\Event::getListByFilter([
            ['documentId', '=', $docId]
        ]);
        if (count($events) === 0) {
            throw new \Exception('Файл не найден', -32231);
        }
        $event = $events[0];
        $user = \Backend\API\User::getById($payload['user']);
        if (!in_array($event->request->divisionId, $user->getAllowedDivisionIds())) {
            throw new \Exception('Недостаточно прав для операции', -32224);
        }
        $fileName = \Backend\Config\Document::DIR . $event->requestId . '/'
            . \Backend\Common\Strings::gudiWithDashes($event->documentId);
        if (!file_exists($fileName)) {
            throw new \Exception('Файл не найден', -32231);
        }
        $docType = mime_content_type($fileName);
        if ($docType === false) {
            $docType = 'unknown';
        }
        return [
            'ok' => 'ok',
            'content' => base64_encode(file_get_contents($fileName)),
            'type' => $docType
        ];
    }

    public static function change(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $request = self::getById($params['number']);
        if (!in_array($request->divisionId, $user->getAllowedDivisionIds())) {
            throw new \Exception('Недостаточно прав для операции', -32224);
        }
        $newServiceId = $request->serviceId;
        $newLevel = $request->level;
        $changeService = false;
        if (array_key_exists('service', $params) && $params['service'] !== $newServiceId) {
            $newServiceId = $params['service'];
            $changeService = true;
        }
        if (array_key_exists('level', $params) && $params['level'] !== $newLevel) {
            $newLevel = $params['level'];
            $changeService = true;
        }
        if ($changeService) {
            $request->changeService($user, $newServiceId, $newLevel);
        }
        if (array_key_exists('partner', $params) && $params['partner'] !== $request->partnerId) {
            $request->changePartner($user, $params['partner']);
        }
        if (array_key_exists('contact', $params) && $params['contact'] !== $request->contactId) {
            $request->changeContact($user, $params['contact']);
        }
        return ['ok' => 'ok'];
    }

    protected function changeService(\Backend\API\User $user, string $newServiceId, string $newLevelId)
    {
        $this->checkAction($user, 'change_service');
        $slas = \Backend\API\SLA::getListByFilter([
            ['contractId', '=', $this->division->contractId],
            ['divisionTypeId', '=', $this->division->typeId],
            ['serviceId', '=', $newServiceId],
            ['level', '=', $newLevelId]
        ]);
        if (count($slas) === 0) {
            throw new \Exception('Неверные параметры заявки', -32223);
        }
        $sla = $slas[0];
        $this->changeService1C();
        $this->serviceId = $newServiceId;
        $this->level = $newLevelId;
        $times = \Backend\API\Calendar::calcTime(
            $this->divisionId,
            $this->serviceId,
            $this->level,
            date_create_from_format('Y-m-d H:i:s', $this->createdAt)->format('U')
        );
        $this->reactBefore = strftime('%Y-%m-%d %H:%M:%S', $times['reactBefore']);
        $this->fixBefore = strftime('%Y-%m-%d %H:%M:%S', $times['fixBefore']);
        $this->repairBefore = strftime('%Y-%m-%d %H:%M:%S', $times['repairBefore']);
        $this->toReact = $sla->toReact;
        $this->toFix = $sla->toFix;
        $this->toRepair = $sla->toRepair;
        $this->store();
        $this->addEvent(
            $user,
            \Backend\API\EventType::CHANGE_SERVICE,
            $this->service->name . ' (' . \Backend\API\SlaLevel::NAMES[$this->level] . ')'
        );
    }

    protected function changeContact(\Backend\API\User $user, string $newContactId)
    {
        $this->checkAction($user, 'change_contact');
        $this->changeContact1C();
        $this->contactId = $newContactId;
        $this->store();
        $this->addEvent($user, \Backend\API\EventType::CHANGE_CONTACT, $this->contact->fullName);
    }

    protected function addEvent(
        \Backend\API\User $user,
        string $eventType,
        ?string $text = null,
        ?string $docName = null,
        ?string $docGuid = null
    ) {
        switch ($eventType) {
            case \Backend\API\EventType::NEW_REQUEST:
            case \Backend\API\EventType::CHANGE_STATE:
            case \Backend\API\EventType::UNCANCEL:
            case \Backend\API\EventType::UNCLOSE:
            case \Backend\API\EventType::ON_WAIT:
            case \Backend\API\EventType::OFF_WAIT:
            case \Backend\API\EventType::CHANGE_PARTNER:
            case \Backend\API\EventType::ADD_COMMENT:
            case \Backend\API\EventType::ADD_DOCUMENT:
            case \Backend\API\EventType::CHANGE_SERVICE:
                // default values
                break;
            default:
                return;
        }
        $event = new \Backend\API\Event([
            'event' => $eventType,
            'mailed' => false,
            'newState' => $this->state,
            'requestId' => $this->id,
            'text' => $text,
            'document' => $docName,
            'documentId' => $docGuid,
            'userId' => $user->id,
            'timestamp' => strftime('%Y-%m-%d %H:%M:00', time())
        ]);
        $event->store();
        return $event;
    }

    protected function getPreviousState() : string
    {
        $state = \Backend\API\RequestState::RECEIVED;
        $events = $this->events;
        usort(
            $events,
            function ($a, $b) {
                return $b->id - $a->id;
            }
        );
        foreach ($events as $event) {
            if ($event->event === \Backend\API\EventType::CHANGE_STATE
                && $event->newState !== $this->state) {
                $state = $event->newState;
                break;
            }
        }
        return $state;
    }

    protected function revertState()
    {
        $this->state = $this->getPreviousState();
        switch ($this->state) {
            case \Backend\API\RequestState::RECEIVED:
                $this->reactedAt = null;
                $this->reactRate = null;
                // no-break
            case \Backend\API\RequestState::ACCEPTED:
                $this->fixedAt = null;
                $this->fixRate = null;
                // no-break
            case \Backend\API\RequestState::FIXED:
                $this->repairedAt = null;
                $this->repairRate = null;
        }
        $this->solution = null;
        $this->recomendation = null;
        $this->repairedAt = null;
    }

    protected function new1C()
    {
        $this->guid = '0123456789012345678901' . time();
        $this->num1C = 'ЦСО-12345';
        $this->synced = true;
        $this->state = \Backend\API\RequestState::RECEIVED;
    }

    protected function accept1C()
    {
    }

    protected function fix1C()
    {
    }

    protected function repair1C()
    {
    }

    protected function close1C()
    {
    }

    protected function cancel1C()
    {
    }

    protected function unCancel1C()
    {
    }

    protected function deny1C()
    {
    }

    protected function waitOn1C()
    {
    }

    protected function waitOff1C()
    {
    }

    protected function changePartner1C()
    {
    }

    protected function addComment1C()
    {
    }

    protected function changeService1C()
    {
    }

    protected function changeContact1C()
    {
    }

    protected function addDocument1C(string $name, string $content) : string
    {
        return $this->guid = '0123456789012345678901' . time();
    }
}
