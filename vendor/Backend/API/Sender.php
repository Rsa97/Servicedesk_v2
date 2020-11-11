<?php
namespace Backend\API;

class Sender extends Entity
{
    protected static string $table = 'sendMethods';
    protected static string $desc = 'Способы отправки сообщений по пользователям и договорам';
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
            'desc' => 'GUID договора (или "00000000000000000000000000000000" если не надо учитывать договор)'
        ],
        'contract' => [
            'type' => '?ref',
            'refField' => 'contractId',
            'class' => '\Backend\API\Contract',
            'readonly' => true,
            'desc' => 'Договор'
        ],
        'event' => [
            'field' => 'event',
            'type' => 'event',
            'required' => true,
            'desc' => 'Тип события'
        ],
        'sendMethod' => [
            'field' => 'method',
            'type' => 'sendMethod',
            'required' => true,
            'desc' => 'Метод отправки'
        ]
    ];

    public function store()
    {
        $sql = "INSERT INTO `sendMethods` (`user_guid`, `contract_guid`, `event`, `method`, `updated`) "
            .   "VALUES (:userId, :contractId, :event, :method, 1) "
            .   "ON DUPLICATE KEY UPDATE `updated` = 1";
        $db = \Backend\Common\DB::get();
        $req = $db->prepare($sql);
        $req->execute([
            'userId' => static::typeToDB('uuid', $this->userId),
            'contractId' => static::typeToDB('uuid', $this->contractId),
            'event' => static::typeToDB('event', $this->event),
            'method' => static::typeToDB('sender', $this->sendMethod)
        ]);
    }

    public static function startUpdate(\Backend\API\User $user)
    {
        $sql = "UPDATE `sendMethods` SET `updated` = 0 WHERE `user_guid` = :userId";
        $db = \Backend\Common\DB::get();
        $db->query('START TRANSACTION READ WRITE');
        $req = $db->prepare($sql);
        $req->execute([
            'userId' => static::typeToDB('uuid', $user->id)
        ]);
    }

    public static function finishUpdate(\Backend\API\User $user)
    {
        $sql = "DELETE FROM `sendMethods` WHERE `user_guid` = :userId AND `updated` = 0";
        $db = \Backend\Common\DB::get();
        $req = $db->prepare($sql);
        $req->execute([
            'userId' => static::typeToDB('uuid', $user->id)
        ]);
        $db->query('COMMIT');
    }
}
