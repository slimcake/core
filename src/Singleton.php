<?php

namespace Slimcake\Core;

/**
 * Class Singleton
 * @package Slimcake\Core
 */
abstract class Singleton
{
    /**
     * @return static
     */
    public static function getInstance()
    {
        static $instance;
        if (empty($instance)) {
            $instance = new static();
        }

        return $instance;
    }
}
