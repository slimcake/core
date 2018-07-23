<?php

namespace Slimcake\Core;

/**
 * Class Session
 * @package Slimcake\Core
 */
class Session extends Singleton
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * @return array
     */
    public function all()
    {
        return isset($_SESSION) ? $_SESSION : array();
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public function set($key, $data = null)
    {
        $_SESSION[$key] = $data;
    }

    /**
     * @param string $key
     */
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function flash($key, $default = null)
    {
        $data = $this->get($key, $default);
        $this->remove($key);

        return $data;
    }
}
