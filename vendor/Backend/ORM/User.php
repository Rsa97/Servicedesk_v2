<?php
namespace Backend\ORM;

class User extends Entity
{
    protected static string $table = 'users';
    protected static string $desc = 'Пользователь';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID пользователя'
        ],
        'lastName' => [
            'field' => 'lastName',
            'type' => 'string',
            'required' => true,
            'desc' => 'Фамилия'
        ],
        'firstName' => [
            'field' => 'firstName',
            'type' => 'string',
            'required' => true,
            'desc' => 'Имя'
        ],
        'middleName' => [
            'field' => 'middleName',
            'type' => '?string',
            'desc' => 'Отчество'
        ],
        'login' => [
            'field' => 'login',
            'type' => 'string',
            'required' => true,
            'desc' => 'Логин'
        ],
        'passwordHash' => [
            'field' => 'passwordHash',
            'type' => '?string',
            'desc' => 'Хэш пароля'
        ],
        'disabled' => [
            'field' => 'isDisabled',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Блокировка пользователя'
        ],
        'rights' => [
            'field' => 'rights',
            'type' => 'rights',
            'required' => true,
            'default' => UserRights::CLIENT,
            'desc' => 'Права'
        ],
        'email' => [
            'field' => 'email',
            'type' => '?string',
            'desc' => 'Адрес электронной почты'
        ],
        'phone' => [
            'field' => 'phone',
            'type' => '?string',
            'desc' => 'Номер рабочего телефона'
        ],
        'cellphone' => [
            'field' => 'phone',
            'type' => '?string',
            'desc' => 'Номер телефона для SMS'
        ],
        'address' => [
            'field' => 'address',
            'type' => '?string',
            'desc' => 'Реальный адрес'
        ],
        'partnerId' => [
            'field' => 'partner_guid',
            'type' = '?uuid',
            'desc' => 'GUID партнёра'
        ],
        'partner' => [
            'type' => '?ref',
            'refField' => 'partnerId',
            'class' => '\Backend\ORM\Partner',
            'readonly' => true,
            'desc' => 'Партнёр'
        ],
        'contracts' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\UserContractList',
            'backRefField' => 'userId',
            'class' => '\Backend\ORM\Contract',
            'refField' => 'contractId',
            'readonly' => true,
            'desc' => 'Договоры, по которым пользователь является ответственным'
        ],
        'divisions' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\UserDivisionLink',
            'backRefField' => 'userId',
            'class' => '\Backend\ORM\Division',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Подразделения, по которым пользователь является ответственным'
        ]
    ];
}
