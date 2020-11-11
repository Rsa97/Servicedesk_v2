<?php
namespace Backend\ORM;

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
            'class' => '\Backend\ORM\Partner',
            'readonly' => true,
            'desc' => 'Партнёр'
        ],
        'contracts' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\UserContractList',
            'backRefField' => 'userId',
            'class' => '\Backend\ORM\Contract',
            'refField' => 'contractId',
            'readonly' => true,
            'desc' => 'Договоры, по которым пользователь является ответственным'
        ],
        'divisions' => [
            'type' => 'refm2m',
            'linkClass' => '\Backend\ORM\UserDivisionLink',
            'backRefField' => 'userId',
            'class' => '\Backend\ORM\Division',
            'refField' => 'divisionId',
            'readonly' => true,
            'desc' => 'Подразделения, по которым пользователь является ответственным'
        ]
    ];

    public function getByLogin(string $login) : ?\Backend\ORM\User
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
}
