<?php
namespace Backend\Common;

class DB extends \PDO
{
    private static $instance = null;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    /*
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton');
    }
    */

    public static function get() : \PDO
    {
        if (static::$instance === null) {
            if (defined('\Backend\Config\DB::SOCKET') && '' !== \Backend\Config\DB::SOCKET ?? '') {
                $dsn = 'mysql:unix_socket=' . \Backend\Config\DB::SOCKET;
            } else {
                $dsn = 'mysql:host=' . \Backend\Config\DB::HOST . ';port=' . \Backend\Config\DB::PORT;
            }
            $dsn .= ';dbname=' . \Backend\Config\DB::DATABASE . ';charset=utf8';
            static::$instance = new \PDO($dsn, \Backend\Config\DB::USER, \Backend\Config\DB::PASSWORD);
            static::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return static::$instance;
    }
}
