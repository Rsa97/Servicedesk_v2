<?php
namespace Backend\ORM;

class Division extends Entity
{
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
            'type' = 'uuid',
            'required' => true,
            'desc' => 'GUID договора'
        ],
        'contract' => [
            'type' => '?ref',
            'refField' => 'contractId',
            'class' => '\Backend\ORM\Contract',
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
            'class' => '\Backend\ORM\Contragent',
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
            'class' => '\Backend\ORM\DivisionType',
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
            'class' => '\Backend\ORM\User',
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
            'class' => '\Backend\ORM\Workplace',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Список рабочих мест'
        ],
        'equipment' => [
            'type' => '?backRef',
            'class' => '\Backend\ORM\Equipment',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Список оборудования'
        ],
        'partners' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\PartnerDivisionLink',
            'backRefField' => 'divisionId',
            'class' => '\Backend\ORM\Partner',
            'refField' => 'partnerId',
            'readonly' => true,
            'desc' => 'Список партнёров, обслуживающих подразделение'
        ],
        'users' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\UserDivisionLink',
            'backRefField' => 'divisionId',
            'class' => '\Backend\ORM\User',
            'refField' => 'userId',
            'readonly' => true,
            'desc' => 'Список пользователей клиента, ответственных за подразделение'
        ]
    ];
}
