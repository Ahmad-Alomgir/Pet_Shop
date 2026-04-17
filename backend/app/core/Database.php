<?php

class Database
{
    protected $connection;
    protected $statement;

    public function __construct()
    {
        $this->connection = DatabaseConfig::getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Query
    |--------------------------------------------------------------------------
    */
    public function query($sql)
    {
        $this->statement = $this->connection->prepare($sql);
    }

    /*
    |--------------------------------------------------------------------------
    | Bind Values
    |--------------------------------------------------------------------------
    */
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->statement->bindValue($param, $value, $type);
    }

    /*
    |--------------------------------------------------------------------------
    | Execute Query
    |--------------------------------------------------------------------------
    */
    public function execute()
    {
        return $this->statement->execute();
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch All Results
    |--------------------------------------------------------------------------
    */
    public function resultSet()
    {
        $this->execute();
        return $this->statement->fetchAll();
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch Single Row
    |--------------------------------------------------------------------------
    */
    public function single()
    {
        $this->execute();
        return $this->statement->fetch();
    }

    /*
    |--------------------------------------------------------------------------
    | Row Count
    |--------------------------------------------------------------------------
    */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    /*
    |--------------------------------------------------------------------------
    | Last Insert ID
    |--------------------------------------------------------------------------
    */
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}