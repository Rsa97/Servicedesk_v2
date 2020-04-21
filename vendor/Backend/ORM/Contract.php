<?php
namespace Backend\ORM;

class Contract extends Entity
{
    protected static string $table = 'contracts';
    protected static string $desc = 'Договор';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID договора'
        ],
        'number' => [
            'field' => 'number',
            'type' => 'string',
            'required' => true,
            'desc' => 'Номер договора'
        ],
        'email' => [
            'field' => 'email',
            'type' => '?string',
            'desc' => 'Адрес основной электронной почты'
        ],
        'phone' => [
            'field' => 'phone',
            'type' => '?string',
            'desc' => 'Основной номер телефона'
        ],
        'address' => [
            'field' => 'address',
            'type' => '?string',
            'desc' => 'Основной физический адрес'
        ],
        'yurAddress' => [
            'field' => 'yurAddress',
            'type' => '?string',
            'desc' => 'Основной юридический адрес'
        ],
        'start' => [
            'field' => 'contractStart',
            'type' => '?datetime',
            'desc' => 'Дата и время начала действия договора'
        ],
        'end' => [
            'field' => 'contractEnd',
            'type' => '?datetime',
            'desc' => 'Дата и время окончания действия договора'
        ],
        'contragentId' => [
            'field' => 'contragent_guid',
            'type' => '?uuid',
            'desc' => 'GUID контрагента'
        ],
        'contragent' => [
            'type' => '?ref',
            'refField' => 'contragentId',
            'class' => '\Backend\ORM\Contragent',
            'readonly' => true,
            'desc' => 'Контрагент, с которым заключён договор'
        ],
        'active' => [
            'field' => 'isActive',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Действует ли договор'
        ],
        'stopped' => [
            'field' => 'isStopped',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Приостановка действия'
        ],
        'services' => [
            'type' => '?refm2m',
            'linkClass' => '\Backend\ORM\ContractServiceLink',
            'backRefField' => 'contractId',
            'class' => '\Backend\ORM\Service',
            'refField' => 'serviceId',
            'readonly' => true,
            'desc' => 'Список услуг по договору'
        ],
        'divisions' => [
            'type' => '?backRef',
            'class' => '\Backend\ORM\Division',
            'refField' => 'contractId',
            'readonly' => true,
            'desc' => 'Список подразделений договора'
        ],
        'equipments' => [
            'type' => '?backRef',
            'class' => '\Backend\ORM\Equipment',
            'refField' => 'contractId',
            'readonly' => true,
            'desc' => 'Список оборудования по договору'
        ],
        'senders' => [
            'type' => '?backRef',
            'class' => '\Backend\ORM\Sender',
            'refField' => 'contractId',
            'readonly' => true,
            'desc' => 'Список методов отправки по договору'
        ],
        'users' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\UserContractLink',
            'backRefField' => 'contractId',
            'class' => '\Backend\ORM\User',
            'refField' => 'userId',
            'readonly' => true,
            'desc' => 'Список ответственных пользователей по договору'
        ]
    ];
}
