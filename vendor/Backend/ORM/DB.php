<?php
namespace Backend\ORM;

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
        if (null === static::$instance) {
            if (defined('\Backend\Config\DB::SOCKET') && '' !== \Backend\Config\DB::SOCKET ?? '') {
                $dsn = 'mysql:unix_socket=' . \Backend\Config\DB::SOCKET;
            } else {
                $dsn = 'mysql:host=' . \Backend\Config\DB::HOST . ';port=' . \Backend\Config\DB::PORT;
            }
            $dsn .= ';dbname=' . \Backend\Config\DB::DATABASE . ';charset=utf8';
            $instance = new \PDO($dsn, \Backend\Config\DB::USER, \Backend\Config\DB::PASSWORD);
            $instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $instance;
    }
}
