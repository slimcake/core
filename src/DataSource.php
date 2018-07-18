<?php

namespace Slimcake\Core;

/**
 * Class DataSource
 * @package Slimcake\Core
 */
class DataSource
{
    /** @var \PDO $pdo */
    protected $pdo;

    /**
     * DataSource constructor.
     */
    protected function __construct()
    {
        $options = array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false
        );

        $dsn = Config::get('DATABASE_DSN', null);
        $user = Config::get('DATABASE_USER', 'root');
        $pass = Config::get('DATABASE_PASS', null);
        $options = array_merge($options, Config::get('DATABASE_OPTIONS', array()));

        $this->pdo = new \PDO($dsn, $user, $pass, $options);
    }

    /**
     * @return DataSource
     */
    public static function getInstance()
    {
        static $instance;
        if (empty($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    /** Begin transaction */
    public function begin()
    {
        $this->pdo->beginTransaction();
    }

    /** Commit transaction */
    public function commit()
    {
        $this->pdo->commit();
    }

    /** Rollback transaction */
    public function rollback()
    {
        $this->pdo->rollBack();
    }

    /** @return int */
    public function lastInsertId()
    {
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * @param string $sql
     * @param array $data
     * @return bool|\PDOStatement
     */
    public function execute($sql, $data = array())
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($data);

        return $statement;
    }

    /**
     * @param string $table
     * @param array $data
     * @return bool|\PDOStatement
     */
    public function insert($table, $data = array())
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = 'INSERT'
            . ' INTO `%s`(`%s`)'
            . ' VALUES (%s)';

        return $this->execute(
            sprintf($query, $table, $columns, $placeholders),
            array_values($data)
        );
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool|\PDOStatement
     */
    public function update($table, $data = array(), $where = array())
    {
        $callback = function ($v) {
            return sprintf('`%s` = ?', $v);
        };

        $query = 'UPDATE `%s`'
            . ' SET %s'
            . ' WHERE %s';

        $updates = implode(', ', array_map($callback, array_keys($data)));
        $conditions = implode(' AND ', array_map($callback, array_keys($where)));
        $parameters = array_merge(array_values($data), array_values($where));

        return $this->execute(
            sprintf($query, $table, $updates, $conditions),
            $parameters
        );
    }

    /**
     * @param string $table
     * @param array $where
     * @return bool|\PDOStatement
     */
    public function delete($table, $where = array())
    {
        $conditions = implode(' AND ', array_map(function ($v) {
            return sprintf('`%s` = ?', $v);
        }, array_keys($where)));

        $query = 'DELETE'
            . ' FROM %s'
            . ' WHERE %s';

        return $this->execute(
            sprintf($query, $table, $conditions),
            array_values($where)
        );
    }

    /**
     * @param string $query
     * @param array $data
     * @return mixed
     */
    public function find($query, $data = array())
    {
        return $this->execute($query, $data)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array $data
     * @return array
     */
    public function findAll($query, $data = array())
    {
        return $this->execute($query, $data)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
