<?php

namespace Slimcake\Core;

/**
 * Class Dispatcher
 * @package Slimcake\Core
 */
class Dispatcher
{
    /**
     * @return array
     * @throws Exception
     */
    public static function parseURI()
    {
        $path = Server::get('PATH_INFO');
        $routes = explode('/', trim($path, '/'));

        $args = array();
        if (count($routes) < 2) {
            throw new Exception(sprintf('Invalid URL format "%s"', $path));
        } elseif (count($routes) > 3) {
            $args = array_splice($routes, 3);
        }

        $method = str_replace('-', '_', array_pop($routes));
        $controller = str_replace('-', '_', implode('_', $routes));

        return array($controller, $method, $args);
    }

    /**
     * @param string $name
     * @return Controller
     * @throws Exception
     */
    public static function createController($name)
    {
        $controller = Inflector::camelcase(sprintf('%s_controller', $name));
        $controller = sprintf('App\\Controllers\\%s', $controller);

        if (class_exists($controller) === false) {
            throw new Exception(sprintf('Controller class "%s" not found', $controller));
        }

        return new $controller($name);
    }

    /**
     * @throws Exception
     * @throws \ReflectionException
     */
    public static function invoke()
    {
        if (defined('__ROOT__') === false) {
            throw new Exception('"__ROOT__" constant is not defined');
        }

        list($controllerName, $methodName, $arguments) = self::parseURI();
        $controller = self::createController($controllerName);
        $controller->beforeFilter();
        $controller->dispatchMethod(
            $methodName,
            $arguments
        );
        $controller->afterFilter();
        $controller->buildResponse();
    }
}
