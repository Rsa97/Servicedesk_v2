<?php
namespace Backend\API;

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
            'class' => '\Backend\API\Partner',
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
            'class' => '\Backend\API\Division',
            'readonly' => true,
            'desc' => 'Подразделение'
        ],
    ];
}
