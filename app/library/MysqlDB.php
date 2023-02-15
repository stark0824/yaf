<?php
// +----------------------------------------------------------------------
// | Author:stark
// +----------------------------------------------------------------------
// | Date:	2023/02/11
// +----------------------------------------------------------------------
// | Desc:	pdo操作mysql类库
// +----------------------------------------------------------------------
class MysqlDB {
    public $conn = null;

    public function __construct($dbName = 'admin',$tableName)
    {
        $this->conn = $this->conn($dbName ,$tableName );
        return $this;
    }

    private function conn($dbName ,$tableName )
    {
        $dbConfig = require APPLICATION_PATH .'/conf/mysql.php';
        $config = $dbConfig[$dbName];

        $dsn="mysql:host={$config['host']};dbname={$config['database']};port={$config['port']}";
        $user = $config['user'];
        $pass = $config['password'];
        try {
            $this->conn = new PDO($dsn, $user, $pass, array(PDO::ATTR_PERSISTENT => true));
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        return $this->conn;
    }
}