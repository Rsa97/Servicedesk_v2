<?php
namespace Backend\API;

class Document extends Entity
{
    protected static string $table = 'documents';
    protected static string $desc = 'Документ';
    protected static array $map = [
        'id' => [
            'field' => 'guid',
            'type' => 'uuid',
            'readonly' => true,
            'required' => true,
            'desc' => 'GUID документа'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'string',
            'required' => true,
            'desc' => 'Название'
        ],
        'uniqueName' => [
            'field' => 'uniqueName',
            'type' => 'string',
            'required' => true,
            'desc' => 'Уникальное название в хранилище'
        ],
        'eventId' => [
            'field' => 'requestEvent_id',
            'type' => 'numeric',
            'desc' => 'ID события'
        ],
        'event' => [
            'type' => '?ref',
            'refField' => 'eventId',
            'class' => '\Backend\API\Event',
            'readonly' => true,
            'desc' => 'Событие добавления документа'
        ]
    ];
}
