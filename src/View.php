<?php

namespace Slimcake\Core;

/**
 * Class View
 * @package Slimcake\Core
 */
class View
{
    const EXTENSION = 'phtml';

    /** @var Controller $controller */
    protected $controller;

    /** @var string $render */
    protected $render;

    /** @var $path */
    protected $path;

    /** @var array $data */
    protected $data = array();

    /**
     * @return string
     * @throws Exception
     */
    protected function extract()
    {
        if (file_exists(sprintf('%s/%s', $this->path, $this->render)) === false) {
            throw new Exception(sprintf('View file "%s" not found', $this->render));
        }

        extract($this->data, EXTR_SKIP);
        ob_start('ob_gzhandler');
        ob_implicit_flush(0);

        /** @noinspection PhpIncludeInspection */
        include_once(sprintf('%s/%s', $this->path, $this->render));
        $this->data = get_defined_vars();

        return ob_get_clean();
    }

    /**
     * View constructor.
     * @param Controller $controller
     */
    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
        $this->path = sprintf('%s/src/Views', __ROOT__);
    }

    /**
     * @param string $key
     * @param mixed $data
     */
    public function set($key, $data = null)
    {
        $this->data[$key] = $data;
    }

    /**
     * @param string $render
     */
    public function render($render)
    {
        if (strpos($render, '/') === false) {
            $render = sprintf('%s/%s', $this->controller, $render);
        }

        $this->render = sprintf('%s.%s', ltrim($render, '/'), static::EXTENSION);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function build()
    {
        return $this->extract();
    }
}
