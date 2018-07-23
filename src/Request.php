<?php

namespace Slimcake\Core;

/**
 * Class Request
 * @package Slimcake\Core
 */
class Request extends Singleton
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public function set($key, $data = null)
    {
        $_REQUEST[$key] = $data;
    }

    /**
     * @return array
     */
    public function all()
    {
        return isset($_REQUEST) ? $_REQUEST : array();
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method)
    {
        $server = Server::getInstance();
        return strtoupper($method) === strtoupper($server->get('REQUEST_METHOD'));
    }
}
