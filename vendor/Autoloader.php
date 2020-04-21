<?php

spl_autoload_register(
    function (string $class_name) : void {
        if ('cli' == php_sapi_name()) {
            $base = '/var/www/vendor/';
        } else {
            $base = $_SERVER['DOCUMENT_ROOT'] . '/vendor/';
        }
        $classFile = $base . str_replace(['_', '\\'], '/', $class_name) . '.php';
        if (file_exists($classFile)) {
            require_once($classFile);
        }
    }
);
