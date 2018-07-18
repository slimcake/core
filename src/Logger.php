<?php

namespace Slimcake\Core;

/**
 * Class Logger
 * @package Slimcake\Core
 */
class Logger
{
    const DEBUG = 'debug';
    const INFO = 'info';
    const WARN = 'warn';
    const ERROR = 'error';

    /** @var array $levels */
    protected static $levels = array(
        self::DEBUG => 0,
        self::INFO => 1,
        self::WARN => 2,
        self::ERROR => 3,
    );

    /**
     * @param string $message
     * @param array $data
     * @param string $level
     */
    public static function log($message, $data = array(), $level = self::DEBUG)
    {
        $lvl = Config::get('LOG_LEVEL', self::DEBUG);
        if ($level < $lvl) {
            return;
        }

        $date = date('Y-m-d H:i:s');
        $data = empty($data) ? '' : json_encode($data);
        $msg = sprintf('[%s][%s] %s %s', $date, $level, $message, $data) . PHP_EOL;

        if (php_sapi_name() === 'cli') {
            echo($msg);
            return;
        }

        $path = sprintf('%s/var/log/%s.log', __ROOT__, Config::get('APP_ENV', 'dev'));
        if (is_dir(dirname($path)) === false) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $msg, FILE_APPEND);
    }

    /**
     * @param string $message
     * @param array $data
     */
    public static function debug($message, $data = array())
    {
        self::log($message, $data, self::DEBUG);
    }

    /**
     * @param string $message
     * @param array $data
     */
    public static function info($message, $data = array())
    {
        self::log($message, $data, self::INFO);
    }

    /**
     * @param string $message
     * @param array $data
     */
    public static function warn($message, $data = array())
    {
        self::log($message, $data, self::WARN);
    }

    /**
     * @param string $message
     * @param array $data
     */
    public static function error($message, $data = array())
    {
        self::log($message, $data, self::ERROR);
    }
}
