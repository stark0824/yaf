<?php
class ManagerAuthorityModel extends MysqlDB
{
    public $dbName = 'admin';
    public $tableName = 'manager_authority';
    public $db = null;

    public function __construct()
    {
        $obj = parent::__construct($this->dbName, $this->tableName);
        $this->db = $obj->conn;
    }

    public function getAuthList()
    {
        $sql= "select * from {$this->tableName} where `delete` = 0 order by `order` ASC, `id` ASC";
        $select = $this->db->prepare($sql);
        $select->execute();
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkAuth($controller,$action)
    {
        $sql= "select * from {$this->tableName} where `delete` = 0 And controller = :controller
AND action = :action ";
        $select = $this->db->prepare($sql);
        $select->bindParam(':controller', $status, PDO::PARAM_STR);
        $select->bindParam(':action', $action, PDO::PARAM_STR);
        $select->execute();
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }





}
