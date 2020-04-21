<?php
namespace Backend\ORM;

class PartnerDivisionLink extends Entity
{
    protected static string $table = 'partnerDivisions';
    protected static string $desc = 'Связь партнёров и подразделений';
    protected static array $map = [
        'partnerId' => [
            'field' => 'partner_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID партнёра'
        ],
        'partner' => [
            'type' => '?ref',
            'refField' => 'partnerId',
            'class' => '\Backend\ORM\Partner',
            'readonly' => true,
            'desc' => 'Партнёр'
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
