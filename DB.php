<?php

require_once 'Database.php';
require_once 'QueryBuilder.php';

class DB
{
    protected static $instance;

    public static function table($tableName)
    {
        $model = new Model();
        $model->table($tableName);
        return new QueryBuilder($model);
    }

    public static function query($sql, $bindings = [])
    {
        $model = new Model();
        return (new QueryBuilder($model))->query($sql, $bindings);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
