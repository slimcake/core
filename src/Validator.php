<?php

namespace Slimcake\Core;

/**
 * Class Validator
 * @package Slimcake\Core
 */
class Validator
{
    /** @var Model $model */
    protected $model;

    /** @var array $errors */
    protected $errors = array();

    /**
     * Validator constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $constraints
     * @return bool
     */
    public function validate(array $constraints)
    {
        $properties = get_object_vars($this->model);
        foreach ($properties as $k => $v) {
            if (isset($constraints[$k]) === false) {
                continue;
            }

            foreach ($constraints[$k] as $rule => $message) {
                if (method_exists($this->model, $rule)) {
                    $validated = $this->model->{$rule}($v);
                } elseif (function_exists($rule)) {
                    $validated = $rule($v);
                } else {
                    $message = sprintf('Rule method "%s" not found', $rule);
                    $validated = false;
                }

                if ($validated === false) {
                    $this->errors[$k][$rule] = $message;
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
}
