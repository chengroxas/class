<?php
require_once 'Dbfactory.class.php';
$db=Dbfactory::create('Mysql');
$table="user";

$where=array(
  'id' => '1'
);
$ret=$db->getList($table,'',$data);
var_dump($ret);
