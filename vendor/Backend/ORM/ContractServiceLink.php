<?php
namespace Backend\ORM;

class ContractServiceLink extends Entity
{
    protected static string $table = 'contractServices';
    protected static string $desc = 'Связь договора с услугой';
    protected static array $map = [
        'contractId' => [
            'field' => 'contract_guid',
            'type' => 'uuid',
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
        'serviceId' => [
            'field' => 'service_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID услуги'
        ],
        'service' => [
            'type' => '?ref',
            'refField' => 'serviceId',
            'class' => '\Backend\ORM\Service',
            'readonly' => true,
            'desc' => 'Услуга'
        ],
    ];
}
