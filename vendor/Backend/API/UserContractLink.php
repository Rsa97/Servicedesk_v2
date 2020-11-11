<?php
namespace Backend\API;

class UserContractLink extends Entity
{
    protected static string $table = 'userContracts';
    protected static string $desc = 'Ответственные пользователи по договорам';
    protected static array $map = [
        'userId' => [
            'field' => 'user_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID пользователя'
        ],
        'user' => [
            'type' => '?ref',
            'refField' => 'userId',
            'class' => '\Backend\API\User',
            'readonly' => true,
            'desc' => 'Пользователь'
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
    ];
}
