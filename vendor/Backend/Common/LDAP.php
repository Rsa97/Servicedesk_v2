<?php
namespace Backend\Common;

class LDAP
{
    private static $ldap = null;
    private static string $host = '';
    private static ?\Backend\Common\LDAP $instance = null;

    public function __construct()
    {
    }

    public function __clone()
    {
    }

    public function __wakeup()
    {
    }

    public static function get() : ?\Backend\Common\LDAP
    {
        if (self::$instance === null) {
            foreach (\Backend\Config\LDAP::HOSTS as $host) {
                $ldap = ldap_connect($host);
                if ($ldap !== false) {
                    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, \Backend\Config\LDAP::PROTOCOL_VERSION);
                    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
                    if (ldap_bind($ldap, \Backend\Config\LDAP::BIND_DN, \Backend\Config\LDAP::BIND_PASSWORD)) {
                        break;
                    }
                }
                $ldap = false;
            }
            if ($ldap !== false) {
                self::$instance = new self();
                self::$ldap = $ldap;
                self::$host = $host;
            }
        }
        return self::$instance;
    }

    public function isUserExists(string $uid) : bool
    {
        $filter = '(' . \Backend\Config\LDAP::USER_UID_ATTR . '=' . $uid . ')';
        $result = ldap_search(
            self::$ldap,
            \Backend\Config\LDAP::BASE_DN,
            $filter,
            [\Backend\Config\LDAP::USER_UID_ATTR]
        );
        if ($result === false) {
            return false;
        }
        $info = ldap_get_entries(self::$ldap, $result);
        return $info['count'] === 1;
    }

    public function getUserRights(string $uid) : ?string
    {
        $filter = '(' . \Backend\Config\LDAP::USER_UID_ATTR . '=' . $uid . ')';
        $result = ldap_search(
            self::$ldap,
            \Backend\Config\LDAP::BASE_DN,
            $filter,
            [\Backend\Config\LDAP::USER_GROUP_ATTR, 'dn']
        );
        if ($result === false) {
            return null;
        }
        $info = ldap_get_entries(self::$ldap, $result);
        if ($info['count'] !== 1) {
            return null;
        }
        $groupDNs = array_map(
            function ($el) {
                return mb_strtolower($el);
            },
            $info[0][mb_strtolower(\Backend\Config\LDAP::USER_GROUP_ATTR)]
        );
        foreach (\Backend\Config\LDAP::GROUP_MAP as $group => $dn) {
            if (in_array(mb_strtolower($dn), $groupDNs)) {
                return $group;
            }
        }
        return null;
    }

    public function checkAuth(string $uid, string $password) : bool
    {
        $ldap = ldap_connect(self::$host);
        return ldap_bind($ldap, $uid . '@' . \Backend\Config\LDAP::DOMAIN_NAME, $password);
    }

    public function getUserData(string $uid, array $attrs) : array
    {
        $filter = '(' . \Backend\Config\LDAP::USER_UID_ATTR . '=' . $uid . ')';
        $result = ldap_search(self::$ldap, \Backend\Config\LDAP::BASE_DN, $filter, $attrs);
        if ($result === false) {
            return [];
        }
        $info = ldap_get_entries(self::$ldap, $result);
        if ($info['count'] === 0) {
            return [];
        }
        $result = [];
        foreach ($attrs as $attr) {
            $lattr = mb_strtolower($attr);
            if (array_key_exists($lattr, $info[0])) {
                $result[$attr] = $info[0][$lattr][0];
            }
        }
        return $result;
    }
}
