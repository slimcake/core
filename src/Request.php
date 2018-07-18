<?php

namespace Slimcake\Core;

/**
 * Class Request
 * @package Slimcake\Core
 */
class Request
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }

    /**
     * @return array
     */
    public static function all()
    {
        return isset($_REQUEST) ? $_REQUEST : array();
    }
}
