<?php
namespace Backend\API;

class SLA extends Entity
{
    protected static string $table = 'divServicesSLA';
    protected static string $desc = 'Уровень обслуживания (SLA)';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID SLA'
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
        'serviceId' => [
            'field' => 'service_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID услуги'
        ],
        'service' => [
            'type' => '?ref',
            'refField' => 'serviceId',
            'class' => '\Backend\API\Service',
            'readonly' => true,
            'desc' => 'Услуга'
        ],
        'divisionTypeId' => [
            'field' => 'divType_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID типа подразделения'
        ],
        'divisionType' => [
            'type' => '?ref',
            'refField' => 'divisionTypeId',
            'class' => '\Backend\API\DivisionType',
            'readonly' => true,
            'desc' => 'Тип подразделения'
        ],
        'level' => [
            'field' => 'slaLevel',
            'type' => 'slaLevel',
            'requlred' => true,
            'desc' => 'Уровень SLA'
        ],
        'dayType' => [
            'field' => 'dayType',
            'type' => 'dayType',
            'required' => true,
            'desc' => 'Типы дня (будни/выходной)'
        ],
        'toReact' => [
            'field' => 'toReact',
            'type' => 'numeric',
            'required' => true,
            'desc' => 'Время до принятия заявки, минуты'
        ],
        'toFix' => [
            'field' => 'toFix',
            'type' => 'numeric',
            'required' => true,
            'desc' => 'Время до восстановления работоспособности, минуты'
        ],
        'toRepair' => [
            'field' => 'toRepair',
            'type' => 'numeric',
            'required' => true,
            'desc' => 'Время до закрытия заявки, минуты'
        ],
        'quality' => [
            'field' => 'quality',
            'type' => 'numeric',
            'required' => true,
            'desc' => 'Требуемое качество выполнения заявок'
        ],
        'start' => [
            'field' => 'startDayTime',
            'type' => 'time',
            'required' => true,
            'default' => '00:00:00',
            'desc' => 'Начало рабочего дня по SLA'
        ],
        'end' => [
            'field' => 'endDayTime',
            'type' => 'time',
            'required' => true,
            'default' => '23:59:59',
            'desc' => 'Конец рабочего дня по SLA'
        ],
        'default' => [
            'field' => 'isDefault',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Услуга по умолчанию'
        ],
    ];

}
