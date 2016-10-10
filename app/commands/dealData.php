<?php
require_once './DBconnect.php';
error_reporting(E_ALL);
set_time_limit(0);
header('Content-type:text/html;charset=utf-8');
date_default_timezone_set('Asia/shanghai');

$key = $argv; 
if(empty($key[1]) || $key[1] != 'QBpFm#boKgRuGSDc')
{
	die('error please check arg !');
}
$instance = DBconnect::getInstance();
$pdo = $instance->getConn();
if(!$pdo) 
{
	die('数据库链接失败');
}
print_r($pdo);
$datetime = strtotime(date('Y-m-d',strtotime("-2 day")));
$sql = "select count(*) from opuslisten where addtime <= '".$datetime."'";
$rs = $pdo->query($sql);
foreach($rs as $row)
{
	print_r($row);
}
?>