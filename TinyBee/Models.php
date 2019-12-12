<?php
class Models
{
    const FETCH_ONE = 1;
    const FETCH_ALL = 2;
    public $db;
    public $redis;

    /**
     * Setup connection to redis and pdo, then make all models methods accessible from the controllers.
     */
    public function __construct()
    {
        $this->setupRedis();
        $this->db = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createObjects(createClassArray(dirname(__DIR__).'/model'));
    }

    /**
     * Check if a redis is setup properly and connect to redis
     * @return void
     */
    private function setupRedis()
    {
        if (!extension_loaded('redis') || REDIS_HOST === null || REDIS_PORT === null) {
            die();
        }
        $this->redis = new Redis();
        $connect = $this->redis->connect(REDIS_HOST, REDIS_PORT);
        $pwd = REDIS_PASSWORD;
        if (isset($pwd) && !empty($pwd)) {
            if (!$this->redis->auth($pwd)) {
                die('redis : password error');
            }
        }
        if (!$connect) {
            die('redis : connection error to redis-server');
        }
    }

    /**
     * Create properties to access all models methods.
     * @param  array - an associative array (key : class name, value : class instance)
     * @return void
     */
    private function createObjects($classArray)
    {
        foreach ($classArray as $key => $value) {
            if (!property_exists($this, $key)) {
                $propertyName = str_replace('models', '', strtolower($key));
                // Todo : relations entre les tables (inner join, left join...).
                $value->db = $this->db;
                $value->redis = $this->redis;
                $this->{$propertyName} = $value;
            }
        }
    }

    /**
     * Build a select PDO query.
     * @param  string - table name
     * @param  array - query conditions
     * @return string - query string
     */
    private function buildSelect($table, $columns)
    {
        $sql = "SELECT * ";
        $sql .= " FROM ";
        $sql .= $table;
        $sql .= " WHERE ";
        $sql.=  implodeToPdo('=', ' AND ', $columns);
        return ($sql);
    }

    /**
     * Build an insert PDO query.
     * @param  string - table name
     * @param  array - query values
     * @return string - query string
     */
    private function buildInsert($table, $columns)
    {
        $sql = "INSERT INTO ";
        $sql .= $table." ";
        $sql .= '('.implode(",", array_keys($columns)).')';
        $sql .= " VALUES ";
        $sql .= '('.implodeToPdo('?', ', ', $columns, true).')';
        return ($sql);
    }

    /**
     * Build an update PDO query.
     * @param  string - table name
     * @param  array - table columns to update
     * @param  array - query conditions
     * @return string - query string
     */
    private function buildUpdate($table, $columns, $condition)
    {
        $sql = "UPDATE ";
        $sql .= $table." SET ";
        $sql .= implodeToPdo('=', ',', $columns);
        $sql .= " WHERE ";
        $sql .= implodeToPdo('=', ' AND ', $condition);
        return ($sql);
    }

    /**
     * Build a delete PDO query.
     * @param string - table name
     * @param array - query values
     * @return string - query string
     */
    private function buildDelete($table, $columns)
    {
        $sql = "DELETE FROM ";
        $sql .= $table;
        $sql .= " WHERE ";
        $sql .= implodeToPdo('=', ' AND ', $columns);
        return ($sql);
    }

    /**
     * Fetch one result
     * @param  string - table name
     * @param  array - table columns
     * @param  boolean - PDO fetch option
     * @return PDO results
     */
    public function fetch($table, $columns, $option = false)
    {
        $sql = $this->buildSelect($table, $columns);
        $result = $this->exec($sql, $columns, $option, self::FETCH_ONE);
        return ($result);
    }

    /**
     * Fetch all result
     * @param  string - table name
     * @param  array - table columns
     * @param  boolean - PDO fetch option
     * @return PDO results
     */
    public function fetchAll($table, $columns, $option = false)
    {
        $sql = $this->buildSelect($table, $columns);
        $result = $this->exec($sql, $columns, $option, self::FETCH_ALL);
        return ($result);
    }

    /**
     * Insert query
     * @param string  - table name
     * @param array - table columns
     * @return void
     */
    public function insert($table, $columns)
    {
        $sql = $this->buildInsert($table, $columns);
        $this->exec($sql, $columns);
    }

    /**
     * Update query
     * @param  string - table name
     * @param  array - table columns
     * @param  array - query conditions
     * @return void
     */
    public function update($table, $columns, $conditions)
    {
        $sql = $this->buildUpdate($table, $columns, $conditions);
        $this->exec($sql, array_merge($columns, $conditions));
    }

    /**
     * Delete query
     * @param  string - table name
     * @param  array - query conditions
     * @return void
     */
    public function delete($table, $columns)
    {
        $sql = $this->buildDelete($table, $columns);
        $this->exec($sql, $columns);
    }

    /**
     * Execute a PDO query with some options if they are provided.
     * @param  string - query string
     * @param  array - values to bind with PDO
     * @param  boolean - PDO fetch option
     * @param  boolean - PDO fetch method (one or every results)
     * @return void
     */
    private function exec($sql, $data, $option = false, $fetchMethod = false)
    {
        $prepare = $this->db->prepare($sql);
        try {
            $prepare->execute(array_values($data));
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die();
        }

        if ($fetchMethod !== false && ($fetchMethod == self::FETCH_ALL || self::FETCH_ONE)) {
            return ($fetchMethod === self::FETCH_ALL ? $prepare->fetchAll($option) : $prepare->fetch($option));
        }
    }
}

/*
    --> Usage <--
    class A extends Models
    {
        // acces a $this->ModelclassName->method();
    }
*/
