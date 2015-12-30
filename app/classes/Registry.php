<?php

/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 07.11.15
 * Time: 01:37
 */
final class Registry
{
    private static $_instance, $data = [];

    private function __construct()
    {
        self::getInstance();
    }

    private function __clone(){}

    private function __wakeup(){}

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function set($name, $value)
    {
        if (isset(self::$data[$name]))
            throw new Exception('Unable to set var `' . $name . '`. Already set.');

        self::$data[$name] = $value;
    }

    public static function get($name)
    {
        if (array_key_exists($name, self::$data)) {
            return self::$data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Неопределенное свойство в __get(): ' . $name .
            ' в файле ' . $trace[0]['file'] .
            ' на строке ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
}