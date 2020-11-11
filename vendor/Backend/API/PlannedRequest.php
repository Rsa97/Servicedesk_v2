<?php
namespace Backend\API;

class PlannedRequest extends Entity
{
    protected static string $table = 'plannedRequests';
    protected static string $desc = 'Плановая заявка';
    protected static array $map = [
        'id' => [
            'field' => 'id',
            'type' => '?integer',
            'readonly' => true,
            'required' => true,
            'default' => null,
            'desc' => 'ID плановой зявки'
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
            'desc' => 'Подразделения'
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
            'desc' => 'Услуга'
        ],
        'level' => [
            'field' => 'slaLevel',
            'type' => 'level',
            'required' => true,
            'default' => SlaLevel::LOW,
            'desc' => 'Уровень SLA'
        ],
        'intervalYears' => [
            'field' => 'intervalYears',
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
            'desc' => 'Периодичность, лет'
        ],
        'intervalMonths' => [
            'field' => 'intervalMonths',
            'type' => 'numeric',
            'required' => true,
            'default' => 1,
            'desc' => 'Периодичность, месяцев'
        ],
        'intervalWeeks' => [
            'field' => 'intervalWeeks',
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
            'desc' => 'Периодичность, недель'
        ],
        'intervalDays' => [
            'field' => 'intervalDays',
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
            'desc' => 'Периодичность, дней'
        ],
        'nextDate' => [
            'field' => 'nextDate',
            'type' => 'date',
            'required' => true,
            'desc' => 'Дата следующего планового выезда'
        ],
        'preStart' => [
            'field' => 'preStart',
            'type' => 'numeric',
            'required' => true,
            'default' => 3,
            'desc' => 'За сколько дней можно заранее взять заявку'
        ],
        'problem' => [
            'field' => 'problem',
            'type' => 'string',
            'required' => true,
            'desc' => 'Работы по заявке'
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
            'desc' => 'Партнёр, для которого предназначена заявка'
        ]
    ];

    protected static function filteredDivisionIds(array $filter, \Backend\API\User $user) : array
    {
        $allowedDivisionIds = $user->getAllowedDivisionIds(true);
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
                \Backend\API\Division::getIdsListByFilter([ [ 'typeId', '=', $divisionType ] ])
            );
        }
        return $divisions;
    }

    protected static function getFilteredList(array $filter, \Backend\API\User $user) : array
    {
        $divisionIds = static::filteredDivisionIds($filter, $user);
        $toDate = strftime('%Y-%m-%d 23:59:59', strtotime('+1 month'));
        $text = $filter['text'] ?? '';
        return array_filter(
            static::getListByFilter([
                'AND', [
                    [ 'divisionId', 'IN', $divisionIds ],
                    [ 'nextDate', '<', $toDate ]
                ]
            ]),
            function ($req) use ($text) {
                return $req->nextDate <= $req->division->contract->end &&
                    ($text === '' ||
                        mb_stripos($req->problem, $text) !== false ||
                        mb_stripos($req->division->additionalProblem, $text) !== false
                    );
            }
        );
    }

    protected function toNextDate()
    {
        $sql = "UPDATE " . static::$table
            . " SET `nextDate` = `nextDate` + INTERVAL `intervalYears` YEAR + INTERVAL `intervalMonths` MONTH "
            .   "+ INTERVAL `intervalWeeks` WEEK + INTERVAL `intervalDays` DAY "
            . " WHERE `id` = :number";
        $db = \Backend\Common\DB::get();
        $req = $db->prepare($sql);
        $req->execute(['number' => $this->number]);
    }

    public static function getList(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $filter = $params['filter'] ?? [];
        return array_map(
            function ($req) {
                return [
                    'number' => $req->id,
                    'state' => [
                        'current' => 'planned',
                        'canGet' => (strtotime($req->nextDate) - time() < $req->preStart * 24 * 60 * 60),
                    ],
                    'slaLevel' => \Backend\API\SlaLevel::NAMES[$req->level],
                    'service' => [
                        'name' => $req->service->name,
                        'shortName' => $req->service->shortName
                    ],
                    'partner' => $req->partnerId === null
                        ? [ 'id' => null, 'name' => null ]
                        : [ 'id' => $req->partnerId, 'name' => $req->partner->name ],
                    'date' => strftime('%Y-%m-%d', strtotime($req->nextDate)),
                    'division' => $req->division->name,
                    'contract' => $req->division->contract->number,
                    'contragent' => $req->division->contract->contragent->name,
                    'problem' => \Backend\Common\Strings::preformat($req->problem),
                    'addProblem' => \Backend\Common\Strings::preformat($req->division->additionalProblem ?? ''),
                    'partners' => $req->division->partners === null
                        ? []
                        : array_map(
                            function ($partner) {
                                return $partner->id;
                            },
                            $req->division->partners
                        )
                ];
            },
            array_values(static::getFilteredList($filter, $user))
        );
    }

    public static function getCounts(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $filter = $params['filter'] ?? [];
        return [
            'total' => count(static::getFilteredList([], $user)),
            'filtered' => count(static::getFilteredList($filter, $user))
        ];
    }

    public static function doNow(array $params, array $payload) : array
    {
        $user = \Backend\API\User::getById($payload['user']);
        $number = $params['number'] ?? null;
        $request = self::getById($number);
        if (!in_array($request->divisionId, $user->getAllowedDivisionIds())) {
            throw new \Exception('Недостаточно прав для активации заявки', -32221);
        }
        if (strtotime($request->nextDate) - time() >= $request->preStart * 24 * 60 * 60) {
            throw new \Exception('Слишком рано для активации заявки', -32228);
        }
        $contacts = $request->division->users;
        if (!is_array($contacts)) {
            $contacts = $request->division->contract->users;
        }
        if (is_array($contacts)) {
            $contact = $contacts[0];
        } else {
            $contact = null;
        }
        $result = \Backend\API\Request::new(
            [
                'division' => $request->divisionId,
                'service' => $request->serviceId,
                'level' => $request->level,
                'problem' => \Backend\Common\Strings::preformat($request->problem) . '\n'
                    . \Backend\Common\Strings::preformat($request->division->additionalProblem ?? ''),
                'partner' => $request->partnerId,
                'contact' => $contact->id
            ],
            $payload
        );
        $request->toNextDate();
        return $result;
    }
}
