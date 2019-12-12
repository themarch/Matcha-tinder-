<?php

include_once(dirname(__DIR__)."/TinyBee/utils.php");

class ManageScheme
{
    private $db;
    private $redis;
    private $create;
    private $dbName;
    public $displayCreate = true;
    const MAX_COLUMN_WIDTH = 25;

    public function __construct($info)
    {
        if (isset($info) && is_array($info) && keysExist(['username', 'password', 'host', 'db_name'], $info)) {
            $this->initDB($info);
        } else {
            throw new Exception("Invalid database information.");
        }
    }

    private function initDB($info)
    {
        try {
            $this->db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=Matcha', $info['username'], $info['password']);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbName = $info['db_name'];
            $flushDataQuery = "DROP DATABASE IF EXISTS Matcha";
            $this->execute("Drop DB", $table, $flushDataQuery);
            $newDB = "CREATE DATABASE IF NOT EXISTS Matcha";
            $this->execute("Create DB", $table, $newDB);
            $this->execute("Use DB", $table, 'use Matcha');
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    private function isModelRegistered($table, $type)
    {
        if (!isset($this->sqlModel)) {
            return (0);
        }
        foreach ($this->sqlModel as $model) {
            if ($model->table == $table && $model->type == $type) {
                return (1);
            }
        }
        return (0);
    }

    private function findModelQuery($table)
    {
        foreach ($this->sqlModel as $model) {
            if ($model->table == $table && $model->type == "create") {
                return ($model->sql);
            }
        }
        return (0);
    }

    public function add($table, $type, $sql = null)
    {
        if (!isset($type) || $type == "" || !$this->isSupportedType($type)) {
            throw new Exception("Unsupported type !");
        }
        if (!isset($table) || $table == "") {
            throw new Exception("Invalid table name !");
        }
        if (($type == "reset" || $type == "delete") && $sql !== null) {
            throw new Exception("This type".$type."doesnt need an sql query !" . PHP_EOL);
        }
        if (($type == "create" || $type == 'refresh') && $sql == null) {
            throw new Exception("This type : ".$type." need an sql query !" . PHP_EOL);
        }
        if ($this->isModelRegistered($table, $type)) {
            throw new Exception($table." is already associated with a type !" . PHP_EOL);
        }
        //if ($type == 'delete')
        // ne creer pas la table.
        $this->sqlModel[] = (object)['type' => $type, 'sql' => $sql, 'table' => $table];
    }

    private function putError($table, $type, $msg)
    {
        $len = (strlen($table) + strlen($type));
        $pad = self::MAX_COLUMN_WIDTH - $len;
        vprintf(
            "%s %s".str_repeat(" ", $pad)."\e[31m%s\e[0m" . PHP_EOL,
            [$type, $table, $msg]
        );
    }

    private function putSuccess($table, $type, $msg)
    {
        $len = (strlen($table) + strlen($type));
        $pad = self::MAX_COLUMN_WIDTH - $len;
        vprintf(
            "%s %s".str_repeat(" ", $pad)."\e[92m%s\e[0m" . PHP_EOL,
            [$type, $table, 'sucess']
        );
    }

    private function isSupportedType($type)
    {
        return ($type == 'delete' || $type == 'create' || $type == 'reset' || $type == 'modify');
    }

    public function run()
    {
        if (!isset($this->sqlModel) || !is_array($this->sqlModel)) {
            throw new Exception("No sql model added !");
        }
        foreach ($this->sqlModel as $model) {
            if ($model->type == "create") {
                $this->create($model->table, $model->sql);
            }
            if ($model->type == "reset") {
                $this->reset($model->table);
            }
            if ($model->type == "delete") {
                $this->delete($model->table);
            }
            if ($model->type == "refresh") {
                if (!($query = findModelQuery($model->table))) {
                    $this->putError($model->table, $model->type, "Error : ".$table." doesnt have a model query !");
                } else {
                    $this->delete($model->table);
                    $this->create($model->table, $query);
                }
            }
        }
    }

    private function execute($type, $table, $query)
    {
        $pad = self::MAX_COLUMN_WIDTH - strlen($table);
        try {
            $this->db->exec($query);
            if ($type !== 'Create') {
                $this->putSuccess($table, $type, 'success');
            } elseif ($type == 'Create' && $this->displayCreate === true) {
                $this->putSuccess($table, $type, 'success');
            }
        } catch (PDOException $e) {
            $this->putError($table, $type, 'DB error');
            echo $e->getMessage() . PHP_EOL;
            die();
        }
    }

    public function create($table, $sql)
    {
        $query = '';
        $pad = self::MAX_COLUMN_WIDTH - strlen($table);
        $query.= "CREATE TABLE IF NOT EXISTS ";
        $query.= $table." (";
        $query.= trim($sql).")";
        $this->execute("Create", $table, $query);
    }

    public function reset($table)
    {
        $query = '';
        $query.= "TRUNCATE TABLE ";
        $query.= $table;
        $this->execute("Reset", $table, $query);
    }

    public function delete($table)
    {
        $query = '';
        $query.= "DROP TABLE ";
        $query.= $table;
        $this->execute("Delete", $table, $query);
    }

    public function resetAll()
    {
        if (!isset($this->sqlModel) || !is_array($this->sqlModel)) {
            throw new Exception("No sql model added !");
        }
        foreach ($this->sqlModel as $model) {
            $this->reset($model->table);
        }
    }

    public function deleteAll()
    {
        if (!isset($this->sqlModel) || !is_array($this->sqlModel)) {
            throw new Exception("No sql model added !");
        }
        foreach ($this->sqlModel as $model) {
            $this->delete($model->table);
        }
    }

    public function refreshAll()
    {
        if (!isset($this->sqlModel) || !is_array($this->sqlModel)) {
            throw new Exception("No sql model added !");
        }
        foreach ($this->sqlModel as $model) {
            if (!($query = $this->findModelQuery($model->table))) {
                $this->putError($model->table, $model->type, "Error : ".$table." doesnt have a model query !");
            } else {
                $this->delete($model->table);
                $this->create($model->table, $query);
            }
        }
    }
}
