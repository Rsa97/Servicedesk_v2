<?php
namespace Backend\API;

class Equipment extends Entity
{
    protected static string $table = 'equipment';
    protected static string $desc = 'Единица оборудования';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID оборудования'
        ],
        'serviceNumber' => [
            'field' => 'serviceNumber',
            'type' => 'string',
            'required' => true,
            'desc' => 'Сервисный номер'
        ],
        'serialNumber' => [
            'field' => 'serialNumber',
            'type' => '?string',
            'desc' => 'Серийный номер'
        ],
        'warrantyEnd' => [
            'field' => 'warrantyEnd',
            'type' => '?datetime',
            'desc' => 'Дата окончания гарантии'
        ],
        'onService' => [
            'field' => 'onService',
            'type' => 'boolean',
            'required' => true,
            'default' => true,
            'desc' => 'Оборудование на обслуживании'
        ],
        'equipmentModelId' => [
            'field' => 'equipmentModel_guid',
            'type' => 'uuid',
            'required' => true,
            'desc' => 'GUID модели'
        ],
        'equipmentModel' => [
            'type' => '?ref',
            'refField' => 'equipmentModelId',
            'class' => '\Backend\API\EquipmentModel',
            'readonly' => true,
            'desc' => 'Модель оборудования'
        ],
        'comment' => [
            'field' => 'rem',
            'type' => '?string',
            'desc' => 'Примечание'
        ],
        'conractId' => [
            'field' => 'contract_guid',
            'type' => '?uuid',
            'desc' => 'GUID договора'
        ],
        'contract' => [
            'type' => '?ref',
            'refField' => 'contractId',
            'class' => '\Backend\API\Contract',
            'readonly' => true,
            'desc' => 'Договор, по которому обслуживается оборудование'
        ],
        'divisionId' => [
            'field' => 'contractDivision_guid',
            'type' => '?uuid',
            'desc' => 'GUID подразделения'
        ],
        'division' => [
            'type' => '?ref',
            'refField' => 'divisionId',
            'class' => '\Backend\API\Division',
            'readonly' => true,
            'desc' => 'Подразделение, в котором находится оборудование'
        ],
        'workplaceId' => [
            'field' => 'workplace_guid',
            'type' => '?uuid',
            'desc' => 'GUID рабочего места'
        ],
        'workplace' => [
            'type' => '?ref',
            'refField' => 'workplaceId',
            'class' => '\Backend\API\Workplace',
            'readonly' => true,
            'desc' => 'Рабочее место, на котором установлено оборудование'
        ]
    ];

    public static function getAllowed(array $params, array $payload) : array
    {
        $all = $params['all'] ?? false;
        $divisionId = $params['division'] ?? null;
        $user = \Backend\API\User::getById($payload['user']);
        if (!in_array($divisionId, $user->getAllowedDivisionIds(!$all))) {
            return [];
        }
        $division = \Backend\API\Division::getById($divisionId);
        return array_map(
            function ($eq) {
                return [
                    'id' => $eq->id,
                    'serviceNumber' => $eq->serviceNumber,
                    'serialNumber' => $eq->serialNumber,
                    'manufacturer' => $eq->equipmentModel->manufacturer->name,
                    'model' => $eq->equipmentModel->name,
                    'type' => $eq->equipmentModel->subType->type->name,
                    'subType' => $eq->equipmentModel->subType->name
                ];
            },
            $division->equipment
        );
    }
}
