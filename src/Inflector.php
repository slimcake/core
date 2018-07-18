<?php

namespace Slimcake\Core;

/**
 * Class Inflector
 * @package Slimcake\Core
 */
class Inflector
{
    /**
     * @param string $string
     * @param bool $lower
     * @return string
     */
    public static function camelcase($string, $lower = false)
    {
        $camelcase = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        return $lower ? lcfirst($camelcase) : $camelcase;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function underscore($string)
    {
        return strtolower(preg_replace('/([a-z]+(?=[A-Z])|[A-Z]+(?=[A-Z][a-z]))/', '\\1_', $string));
    }
}
