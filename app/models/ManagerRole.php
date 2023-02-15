<?php

class ManagerRoleModel extends MysqlDB
{
    public $dbName = 'admin';
    public $tableName = 'manager_role';
    public $db = null;

    public function __construct()
    {
        $obj = parent::__construct($this->dbName, $this->tableName);
        $this->db = $obj->conn;
    }


    public function getManagerRoleByIds(array $roleIds): array
    {
        $sql = "SELECT * FROM {$this->tableName} WHERE id IN (" . implode(",", $roleIds) . ") 
        AND `status` = :status AND `delete` = :delete";
        $status = 1;
        $delete = 0;
        $select = $this->db->prepare($sql);
        $select->bindParam(':status', $status, PDO::PARAM_INT);
        $select->bindParam(':delete', $delete, PDO::PARAM_INT);
        $select->execute();
        return $select->fetchAll(PDO::FETCH_ASSOC);
    }


}
