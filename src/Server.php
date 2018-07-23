<?php

namespace Slimcake\Core;

/**
 * Class Server
 * @package Slimcake\Core
 */
class Server extends Singleton
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public function set($key, $data = null)
    {
        $_SERVER[$key] = $data;
    }

    /**
     * @return array
     */
    public function all()
    {
        return isset($_SERVER) ? $_SERVER : array();
    }
}
