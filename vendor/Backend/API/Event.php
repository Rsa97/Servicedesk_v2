<?php
namespace Backend\API;

class Event extends Entity
{
    protected static string $table = 'requestEvents';
    protected static string $desc = 'Событие по заявке';
    protected static array $map = [
        'id' => [
            'field' => 'id',
            'type' => '?integer',
            'readonly' => true,
            'required' => true,
            'desc' => 'ID события'
        ],
        'timestamp' => [
            'field' => 'timestamp',
            'type' => 'datetime',
            'required' => false,
            'desc' => 'Дата и время события'
        ],
        'event' => [
            'field' => 'event',
            'type' => 'event',
            'required' => true,
            'desc' => 'Тип события'
        ],
        'text' => [
            'field' => 'text',
            'type' => '?string',
            'desc' => 'Текстовое описание события'
        ],
        'newState' => [
            'field' => 'newState',
            'type' => '?state',
            'desc' => 'Новый статус заявки'
        ],
        'requestGuid' => [
            'field' => 'request_guid',
            'type' => '?uuid',
            'desc' => 'GUID заявки'
        ],
        'requestId' => [
            'field' => 'request_id',
            'type' => 'integer',
            'required' => true,
            'desc' => 'ID заявки'
        ],
        'request' => [
            'type' => '?ref',
            'refField' => 'requestId',
            'class' => '\Backend\API\Request',
            'readonly' => true,
            'desc' => 'Заявка, по которой произошло событие'
        ],
        'userId' => [
            'field' => 'user_guid',
            'type' => '?uuid',
            'required' => true,
            'desc' => 'GUID пользвателя'
        ],
        'user' => [
            'type' => '?ref',
            'refField' => 'userId',
            'class' => '\Backend\API\User',
            'readonly' => true,
            'desc' => 'Пользователь, сгенерировавший событие'
        ],
        'mailed' => [
            'field' => 'mailed',
            'type' => 'boolean',
            'required' => true,
            'default' => false,
            'desc' => 'Информация по событию отправлена'
        ],
        'document' => [
            'field' => 'document',
            'type' => '?string',
            'desc' => 'Название документа, привязанного к событию'
        ],
        'documentId' => [
            'field' => 'document_guid',
            'type' => '?uuid',
            'desc' => 'GUID документа'
        ],
    ];

    public function format()
    {
        $docFile = null;
        $docType = 'unknown';
        $docSize = 0;
        if ($this->documentId !== null) {
            $docFile = \Backend\Config\Document::DIR . $this->requestId . "/" .
                \Backend\Common\Strings::gudiWithDashes($this->documentId);
            if ($docFile !== null && file_exists(($docFile))) {
                $docType = mime_content_type($docFile);
                if ($docType === false) {
                    $docType = 'unknown';
                }
                $docSize = filesize($docFile);
                if ($docSize === false) {
                    $docSize = 0;
                }
            }
        }
        return [
            'id' => $this->id,
            'time' => strftime('%d.%m.%Y %H:%M', strtotime($this->timestamp)),
            'event' => $this->event,
            'user' => $this->userId === null ? '' : $this->user->shortName,
            'text' => $this->event === 'changeDate'
                ? strftime('%d.%m.%Y %H:%M', strtotime($this->text))
                : $this->text,
            'newState' => $this->newState,
            'document' => $this->documentId === null
                ? null
                : [
                    'id' => file_exists($docFile)
                        ? $this->documentId
                        : null,
                    'name' => $this->document,
                    'size' => $docSize,
                    'type' => $docType
                ]
        ];
    }
}
