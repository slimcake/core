<?php

namespace Slimcake\Core;

/**
 * Class Model
 * @package Slimcake\Core
 */
class Model
{
    const TABLE_NAME = null;

    /** @var int $id */
    public $id;

    /**
     * Model constructor.
     * @param array $data
     */
    public function __construct($data = array())
    {
        foreach ($data as $k => $v) {
            $property = Inflector::camelcase($k, true);
            if (property_exists($this, $property)) {
                $this->{$property} = $v;
            }
        }
    }

    /**
     * Get validation constraints
     * @return array
     */
    protected function constraints()
    {
        return array();
    }

    /**
     * @return Validator
     */
    protected function validator()
    {
        static $validator;
        if (empty($validator)) {
            $validator = new Validator($this);
        }

        return $validator;
    }

    /**
     * Validate model constraints
     *
     * @return bool
     */
    public function validate()
    {
        return $this->validator()->validate($this->constraints());
    }

    /**
     * Save model properties
     */
    public function save()
    {
        $properties = get_object_vars($this);
        unset($properties['id']);

        $data = array();
        foreach ($properties as $k => $v) {
            $data[Inflector::underscore($k)] = $v;
        }

        $con = DataSource::getInstance();
        if (empty($this->id)) {
            $con->insert(static::TABLE_NAME, $data);
            $this->id = $con->lastInsertId();
            return;
        }

        $con->update(static::TABLE_NAME, $data, array('id' => $this->id));
    }

    /**
     * @throws Exception
     */
    public function delete()
    {
        DataSource::getInstance()->delete(
            static::TABLE_NAME,
            array('id' => $this->id)
        );
    }
}
