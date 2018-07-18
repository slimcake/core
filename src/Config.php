<?php

namespace Slimcake\Core;

/**
 * Class Config
 * @package Slimcake\Core
 */
class Config
{
    /** @var array $data */
    protected static $data = array();

    /**
     * @param string $path
     */
    public static function load($path)
    {
        if (file_exists($path) === false) {
            Logger::warn(sprintf('Config INI file "%s" not found', $path));
            return;
        }

        $data = (array)parse_ini_file($path, false);
        foreach ($data as $k => $v) {
            self::set($k, $v);
        }
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public static function set($key, $data = null)
    {
        self::$data[$key] = $data;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : $default;
    }
}
