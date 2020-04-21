<?php
namespace Backend\ORM;

class Request extends Entity
{
    protected static string $table = 'requests';
    protected static string $desc = 'Заявка';
    protected static array $map = [
        'id' => [
            'field' => 'id',
            'type' => '?numeric',
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
            'type' = 'datetime',
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
            'type' = 'datetime',
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
            'class' => '\Backend\ORM\User',
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
            'class' => '\Backend\ORM\Division',
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
            'field' => 'contractDivision_guid',
            'type' => '?uuid',
            'desc' => 'GUID инженера'
        ],
        'engineer' => [
            'type' => '?ref',
            'refField' => 'engineerId',
            'class' => '\Backend\ORM\User',
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
            'class' => '\Backend\ORM\Equipment',
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
            'class' => '\Backend\ORM\Service',
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
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
            'desc' => 'Скорость принятия заявки, доля'
        ],
        'fixRate' => [
            'field' => 'fixRate',
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
            'desc' => 'Скорость восстановления работоспособности, доля'
        ],
        'repairRate' => [
            'field' => 'repairRate',
            'type' => 'numeric',
            'required' => true,
            'default' => 0,
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
            'class' => '\Backend\ORM\Partner',
            'readonly' => true,
            'desc' => 'Партнёр, которому назначена заявка'
        ],
        'events' => [
            'type' => '?backRef',
            'class' => '\Backend\ORM\Event',
            'refField' => 'eventId',
            'readonly' => true,,
            'desc' => 'Список событий по заявке'
        ]
    ];
}
