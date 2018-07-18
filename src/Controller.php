<?php

namespace Slimcake\Core;

/**
 * Class Controller
 * @package Slimcake\Core
 */
class Controller
{
    const VIEW = View::class;

    /** @var string $name */
    protected $name;

    /** @var View $view */
    protected $view;

    /**
     * Controller constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        $view = static::VIEW;
        $this->view = new $view($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /** Before filter */
    public function beforeFilter()
    {
    }

    /** After filter */
    public function afterFilter()
    {
    }

    /**
     * Dispatch method
     * @param string $methodName
     * @param array $args
     * @throws Exception
     * @throws \ReflectionException
     */
    public function dispatchMethod($methodName, $args = array())
    {
        $class = get_class($this);
        $methods = get_class_methods(Controller::class);
        $method = Inflector::camelcase($methodName, true);

        if (in_array($method, $methods)) {
            throw new Exception(sprintf('Invalid method "%s::%s"', $class, $method));
        }

        if (method_exists($this, $method) === false) {
            throw new Exception(sprintf('Method "%s::%s" not found', $class, $method));
        }

        $reflectionMethod = new \ReflectionMethod($this, $method);
        if ($reflectionMethod->isPublic() === false) {
            throw new Exception(sprintf('Method "%s::%s" should be public', $class, $method));
        }

        if ($reflectionMethod->isStatic()) {
            throw new Exception(sprintf('Method "%s::%s" should not be static', $class, $method));
        }

        $this->view->render($method);
        call_user_func_array(array($this, $method), $args);
    }

    /**
     * @throws Exception
     */
    public function buildResponse()
    {
        echo($this->view->build());
    }
}
