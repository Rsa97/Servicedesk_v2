<?php
namespace Backend\ORM;

class PlannedRequest extends Entity
{
    protected static string $table = 'plannedRequests';
    protected static string $desc = 'Плановая заявка';
    protected static array $map = [
        'id' => [
            'field' => 'id',
            'type' => '?numeric',
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
            'class' => '\Backend\ORM\Division',
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
            'class' => '\Backend\ORM\Service',
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
            'class' => '\Backend\ORM\Partner',
            'readonly' => true,
            'desc' => 'Партнёр, для которого предназначена заявка'
        ]
    ];
}
