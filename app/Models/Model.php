<?php

namespace App\Models;

use App\Core\Database\Connection;
use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Io\LazyDatabase;
use React\Promise\PromiseInterface;

abstract class Model
{
    /**
     * Database table to perform query on
     * @var string
     */
    public string $table = '';
    protected DatabaseInterface $database;
    protected array $selectColumns = [];
    protected array $whereValues = [];

    public function __construct()
    {
        $this->database = Connection::create();
    }

    /**
     * Insert new record to database
     * @param array $data
     * @return PromiseInterface
     */
    public function create(array $data)
    {
        $columns = $this->implode($data);
        $values = $this->implode($data, true);
        $query = "INSERT INTO {$this->table}({$columns}) VALUES ({$values});";
        return $this->database->query($query);
    }

    protected function implode($data, $isValue = false)
    {
        if ($isValue) {
            $values = array_values($data);
            return "'" . implode("', '", $values) . "'";
        }

        $columns = array_keys($data);
        return implode(', ', $columns);
    }

    public function select(...$arguments)
    {
        $this->selectColumns = $arguments;
        return $this;
    }

    public function where($key, $value = null)
    {
        if (is_array($key)) {
            array_merge($this->whereValues, $key);
        } else {
            $this->whereValues[$key] = $value;
        }

        return $this;
    }

    public function get()
    {
        $selectKeys = $this->implode($this->selectColumns, true);
        //$whereKeys = $this->implode($this->whereValues);
        $plainSQL = "SELECT {$selectKeys} FROM {$this->table}";
        $hasWhere = false;
        if (count($this->whereValues) > 0) {
            $hasWhere = true;
            $plainSQL .= " WHERE ";
            foreach ($this->whereValues as $whereKey => $whereValue) {
                $plainSQL .= "{$whereKey} = ?, ";
            }
            $plainSQL = substr($plainSQL, 0, strlen($plainSQL) - 2);
        }

        var_dump($plainSQL);

        if ($hasWhere) {
            return $this->query($plainSQL, array_values($this->whereValues));
        }

        return $this->query($plainSQL);
    }

    /**
     * Execute sql query on current database connection
     * @param string $query
     * @param array $bindValue
     * @return PromiseInterface
     */
    public function query(string $query, array $bindValue = [])
    {
        if (count($bindValue) > 0) {
            return $this->database->query($query, $bindValue);
        }

        return $this->database->query($query);
    }

    /**
     * Execute sql query on current database connection
     * @param string $query
     * @param array $bindValue
     * @return PromiseInterface
     */
    public function execute(string $query, array $bindValue = [])
    {
        if (count($bindValue) > 0) {
            return $this->database->query($query, $bindValue);
        }

        return $this->database->query($query);
    }

    /**
     * Get current database connection
     * @return DatabaseInterface|LazyDatabase
     */
    public function getDatabase()
    {
        return $this->database;
    }
}