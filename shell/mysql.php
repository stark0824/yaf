<?php

$pdo = new PDO('mysql:host=192.168.31.252;dbname=admin;port=3306', 'root', 'rootroot');
//
//var_dump($db);

//$dsn = "mysql:host=192.168.31.252;port=3306;dbname=admin";
//$opts = array(PDO::ATTR_AUTOCOMMIT=>0, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_AUTOCOMMIT=>0 , PDO::ATTR_TIMEOUT => 3 );
//$pdo = new PDO($dsn, 'root', '', $opts);
//ee($pdo->getAttribute(PDO::ATTR_AUTOCOMMIT)); // setAttribute 可以设置属性
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);  #设置获取的方式

$sql = "select * from manager where username = 'stark宇';";
$obj = $pdo->query($sql);
var_dump($obj);