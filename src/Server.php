<?php

namespace Slimcake\Core;

/**
 * Class Server
 * @package Slimcake\Core
 */
class Server
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public static function set($key, $data = null)
    {
        $_SERVER[$key] = $data;
    }

    /**
     * @return array
     */
    public static function all()
    {
        return isset($_SERVER) ? $_SERVER : array();
    }
}
