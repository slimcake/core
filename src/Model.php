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
    protected static function validations()
    {
        return array();
    }

    /**
     * @param array $data
     * @return static[]
     */
    protected static function populate(array $data)
    {
        $obj = array();
        foreach ($data as $v) {
            $obj[] = new static($v);
        }

        return $obj;
    }

    /**
     * @param int $id
     * @return array
     */
    public static function find($id)
    {
        return static::findBy(array('id' => $id));
    }

    /**
     * @param array $where
     * @param array $order
     * @return null|Model|static
     */
    public static function findBy($where = array(), $order = array())
    {
        $data = DataSource::getInstance()->find(static::TABLE_NAME, $where, $order);

        return empty($data) ? null : new static($data);
    }

    /**
     * @param array $where
     * @param array $order
     * @return array|Model[]|static[]
     */
    public static function findAll($where = array(), $order = array())
    {
        $data = DataSource::getInstance()->findAll(static::TABLE_NAME, $where, $order);

        return empty($data) ? array() : static::populate($data);
    }

    /**
     * Validate model constraints
     *
     * @return bool
     */
    public function validate()
    {
        $validator = new Validator($this);

        return $validator->validate(static::validations());
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
