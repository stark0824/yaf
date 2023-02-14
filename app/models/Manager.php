<?php

class ManagerModel extends MysqlDB
{
    public $dbName = 'admin';
    public $tableName = 'manager';
    public $db = null;

    public function __construct()
    {
        $obj = parent::__construct($this->dbName, $this->tableName);
        $this->db = $obj->conn;
    }


    public function checkUser(string $username)
    {
        $sql = "select id, password from {$this->tableName} where username = :userName";
        $select = $this->db->prepare($sql);
        $select->bindParam(':userName', $username, PDO::PARAM_STR);
        $select->execute();
        return $select->fetch(PDO::FETCH_ASSOC);
    }

    public function getManagerById(int $id)
    {
        $sql = "select * from {$this->tableName} where id = :id";
        $select = $this->db->prepare($sql);
        $select->bindParam(':id', $id, PDO::PARAM_INT);
        $select->execute();
        return $select->fetch(PDO::FETCH_ASSOC);
    }


}
