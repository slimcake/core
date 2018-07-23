<?php

namespace Slimcake\Core;

/**
 * Class DataSource
 * @package Slimcake\Core
 */
class DataSource extends Singleton
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

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s',
            Config::get('DATABASE_HOST', '127.0.0.1'),
            Config::get('DATABASE_PORT', 3306),
            Config::get('DATABASE_NAME', null)
        );

        $user = Config::get('DATABASE_USER', 'root');
        $pass = Config::get('DATABASE_PASS', null);
        $options = array_merge($options, Config::get('DATABASE_OPTIONS', array()));

        $this->pdo = new \PDO($dsn, $user, $pass, $options);
    }

    /**
     * @param string $table
     * @param array $where
     * @param array $order
     * @return array
     */
    protected function createQuery($table, $where = array(), $order = array())
    {
        $data = array();
        $query = 'SELECT *'
            . ' FROM `%s`';

        if (empty($where) === false) {
            $filters = array_map(function ($k) {
                return sprintf('`%s` = ?', Inflector::underscore($k));
            }, array_keys($where));

            $data = array_values($where);
            $query = sprintf('%s WHERE %s', $query, implode(' AND ', $filters));
        }

        if (empty($order) === false) {
            $orders = array_map(function ($k, $v) {
                return sprintf('`%s` %s', Inflector::underscore($k), strtoupper($v));
            }, array_keys($order), $order);

            $query = sprintf('%s ORDER BY %s', $query, implode(', ', $orders));
        }

        return array(sprintf($query, $table), $data);
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
     * @param string $table
     * @param array $where
     * @param array $order
     * @return array
     */
    public function find($table, $where = array(), $order = array())
    {
        list($query, $data) = $this->createQuery($table, $where, $order);

        return $this->execute($query, $data)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $table
     * @param array $where
     * @param array $order
     * @return array
     */
    public function findAll($table, $where = array(), $order = array())
    {
        list($query, $data) = $this->createQuery($table, $where, $order);

        return $this->execute($query, $data)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
