<?php
namespace Backend\ORM;

class UserDivisionLink extends Entity
{
    protected static string $table = 'userContractDivisions';
    protected static string $desc = 'Ответственные пользователи по подразделениям';
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
            'class' => '\Backend\ORM\User',
            'readonly' => true,
            'desc' => 'Пользователь'
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
    ];
}
