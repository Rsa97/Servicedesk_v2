<?php
namespace Backend\API;

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
            'type' => '?string',
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
            'field' => 'cellphone',
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
            'type' => '?uuid',
            'desc' => 'GUID партнёра'
        ],
        'partner' => [
            'type' => '?ref',
            'refField' => 'partnerId',
            'class' => '\Backend\API\Partner',
            'readonly' => true,
            'desc' => 'Партнёр'
        ],
        'contracts' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\API\UserContractList',
            'backRefField' => 'userId',
            'class' => '\Backend\API\Contract',
            'refField' => 'contractId',
            'readonly' => true,
            'desc' => 'Договоры, по которым пользователь является ответственным'
        ],
        'divisions' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\API\UserDivisionLink',
            'backRefField' => 'userId',
            'class' => '\Backend\API\Division',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Подразделения, по которым пользователь является ответственным'
        ]
    ];

    public static function getByLogin(string $login) : ?\Backend\API\User
    {
        $user = self::getListByFilter(['login', '=', $login]);
        if (count($user) === 0) {
            return null;
        }
        return $user[0];
    }

    private function syncWithLDAP() : bool
    {
        $ldap = \Backend\Common\LDAP::get();
        $updateUser = false;
        $data = $ldap->getUserData(
            $this->login,
            [
                \Backend\Config\LDAP::USER_FIRST_NAME_ATTR, \Backend\Config\LDAP::USER_LAST_NAME_ATTR,
                \Backend\Config\LDAP::USER_MIDDLE_NAME_ATTR, \Backend\Config\LDAP::USER_PHONE_ATTR
            ]
        );
        if (count($data) !== 0) {
            $firstName = ($data[\Backend\Config\LDAP::USER_FIRST_NAME_ATTR] ?? [$this->firstName]);
            $lastName = ($data[\Backend\Config\LDAP::USER_LAST_NAME_ATTR] ?? [$this->lastName]);
            $middleName = ($data[\Backend\Config\LDAP::USER_MIDDLE_NAME_ATTR] ?? [$this->middleName]);
            $phone = ($data[\Backend\Config\LDAP::USER_PHONE_ATTR] ?? [$this->phone]);
            if (preg_match('/^(\d\d\d)(?:,|$)/', $phone, $matches)) {
                $phone = \Backend\Config\LDAP::BASE_PHONE . ", доб. {$matches[1]}";
            } else {
                $phone = $this->phone;
            }
            if ($this->firstName != $firstName) {
                $this->firstName = $firstName;
                $updateUser = true;
            }
            if ($this->lastName != $lastName) {
                $this->lastName = $lastName;
                $updateUser = true;
            }
            if ($this->middleName != $middleName) {
                $this->middleName = $middleName;
                $updateUser = true;
            }
            if ($this->phone != $phone) {
                $this->phone = $phone;
                $updateUser = true;
            }
        }
        $rights = $ldap->getUserRights($this->login);
        if ($rights != $this->rights) {
            $this->rights = $rights;
            $updateUser = true;
        }
        return $updateUser;
    }

    public function checkAuth(string $password) : bool
    {
        if ($this->disabled) {
            return false;
        }
        $updateUser = false;
        $ldap = \Backend\Common\LDAP::get();
        if ($ldap !== null && $ldap->isUserExists($this->login)) {
            if (!$ldap->checkAuth($this->login, $password)) {
                return false;
            }
            $updateUser = $this->syncWithLDAP();
            if (!password_verify($password, $this->passwordHash)) {
                $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $updateUser = true;
            }
        } else {
            if ($this->passwordHash === md5($password)
                || $this->passwordHash === md5($password . $this->login . 'reppep')) {
                $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $updateUser = true;
            }
            if (!password_verify($password, $this->passwordHash)) {
                return false;
            }
            if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
                $this->passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $updateUser = true;
            }
        }
        if ($updateUser) {
            $this->store();
        }
        return true;
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'shortName':
                return $this->lastName
                    . (($this->firstName ?? '') === '' ? '' : ' ' . mb_substr($this->firstName, 0, 1)) . '.'
                    . (($this->middleName ?? '') === '' ? '' : ' ' . mb_substr($this->middleName, 0, 1)) . '.';
            case 'fullName':
                return $this->lastName
                    . (($this->firstName ?? '') === '' ? '' : " {$this->firstName}")
                    . (($this->middleName ?? '') === '' ? '' : " {$this->middleName}");
            default:
                return parent::__get($name);
        }
    }

    protected function getAllAllowedDivisionIds() : array
    {
        $divisions = [];
        switch ($this->rights) {
            case \Backend\API\UserRights::ADMIN:
            case \Backend\API\UserRights::OPERATOR:
            case \Backend\API\UserRights::ENGINEER:
                $divisions = \Backend\API\Division::getListByFilter(['id', 'IS NOT NULL']);
                break;
            case \Backend\API\UserRights::PARTNER:
                $divisions = $this->partner->divisions;
                break;
            case \Backend\API\UserRights::CLIENT:
                $divs = array_map(
                    function ($div) {
                        return $div->id;
                    },
                    $this->divisions
                );
                foreach ($this->contracts as $contract) {
                    $divs = array_merge(
                        $divs,
                        array_map(
                            function ($div) {
                                return $div->id;
                            },
                            $contract->divisions
                        )
                    );
                }
                $divisions = \Backend\API\Division::getListByIds([array_unique($divs)]);
        }
        $divisionIds = [
            'active' => [],
            'blocked' => []
        ];
        $today = strftime('%Y-%m-%d 00:00:00', time());
        $tomorrow = strftime('%Y-%m-%d 00:00:00', strtotime('+1 day'));
        foreach ($divisions as $div) {
            if ($div->disabled || !$div->contract->active || $div->contract->stopped ||
                $div->contract->end <= $tomorrow || $div->contract->start > $today
            ) {
                $divisionIds['blocked'][] = $div->id;
            } else {
                $divisionIds['active'][] = $div->id;
            }
        }
        return $divisionIds;
    }

    public function getAllowedDivisions(bool $activeOnly = false) : array
    {
        return \Backend\API\Division::getListByIds($this->getAllowedDivisionIds($activeOnly));
    }

    public function getAllowedDivisionIds(bool $activeOnly = false) : array
    {
        $divisionIds = \apcu_entry(
            "user_{$this->id}_allowedDivisions",
            [$this, 'getAllAllowedDivisionIds'],
            \Backend\Config\Cache::APCU_TTL
        );
        if ($activeOnly) {
            return $divisionIds['active'];
        } else {
            return $divisionIds['active'] + $divisionIds['blocked'];
        }
    }

    public static function getAllowedActions(array $payload, array $params) : array
    {
        return \Backend\Config\User::ALLOWED_ACTIONS;
    }

    public static function getMessageSettings(array $params, array $payload) : array
    {
        $user = static::getById($payload['user']);
        $events = array_map(
            function ($el) {
                return [
                    'id' => $el,
                    'name' => \Backend\API\MessageType::NAMES[$el]
                ];
            },
            array_keys(\Backend\Config\User::SEND_EVENTS[$user->rights])
        );
        $methods = array_reduce(
            array_values(\Backend\Config\User::SEND_EVENTS[$user->rights]),
            function ($acc, $el) {
                return array_unique(array_merge($acc, array_keys($el)));
            },
            []
        );
        $senders = \Backend\API\Sender::getListByFilter([
            ['userId', '=', $user->id],
            ['sendMethod', 'IN', $methods]
        ]);
        $settings = [
            '00000000000000000000000000000000' => \Backend\Config\User::SEND_EVENTS[$user->rights]
        ];
        if (count($senders) > 0) {
            foreach ($settings['00000000000000000000000000000000'] as $event => &$eventMethods) {
                foreach ($eventMethods as $method => &$props) {
                    $props['send'] = false;
                }
            }
            foreach ($senders as $send) {
                if (!array_key_exists($send->contractId, $settings)) {
                    $settings[$send->contractId] = [];
                }
                if (!array_key_exists($send->event, $settings[$send->contractId])) {
                    $settings[$send->contractId][$send->event] = array_fill_keys($methods, false);
                }
                if (!array_key_exists($send->sendMethod, $settings[$send->contractId][$send->event])) {
                    $settings[$send->contractId][$send->event][$send->sendMethod] = [];
                }
                $settings[$send->contractId][$send->event][$send->sendMethod]['send'] = true;
            }
        }
        $divisions = $user->getAllowedDivisions(true);
        $contracts = [];
        foreach ($divisions as $division) {
            $contracts[$division->contractId] = [
                'id' => $division->contractId,
                'name' => "{$division->contract->contragent->name} ({$division->contract->number})"
            ];
        }
        return [
            'role' => \Backend\API\UserRights::NAMES[$user->rights],
            'methods' => $methods,
            'email' => $user->email,
            'cellphone' => $user->cellphone,
            'events' => $events,
            'settings' => $settings,
            'contracts' => array_values($contracts)
        ];
    }

    public static function setMessageSettings(array $params, array $payload) : array
    {
        $user = static::getById($payload['user']);
        $user->cellphone = $params['cellphone'];
        $user->store();
        \Backend\API\Sender::startUpdate($user);
        foreach ($params['settings'] as $def) {
            try {
                $sender = new \Backend\API\Sender([
                    'userId' => $user->id,
                    'contractId' => $def['contract'],
                    'event' => $def['event'],
                    'sendMethod' => $def['method']
                ]);
                $sender->store();
            } catch (\Exception $e) {
            }
        }
        \Backend\API\Sender::finishUpdate($user);
        return ['ok' => 'ok'];
    }
}
