<?php 
/**
*	导入歌友圈用户
*	@author:wang.hongli
*	@since:2015/08/16
**/
set_time_limit(0);
header('Content-type:text/html;charset=utf-8');
date_default_timezone_set('Asia/shanghai');
ini_set('memory_limit',-1);

if(!$pdo = conDataBase())
{
	die('conn fail');
}
$start = $argv[1]; //开始
$pageSize = !empty($argv[2]) ? $argv[2] : 10000; //每次导入的数量
//歌友圈用户
$sql = "select nick,tel,gender from zcl_user limit $start,$pageSize";
$statement = $pdo->query($sql);
$zcl_tmpRs = $statement->fetchAll(PDO::FETCH_ASSOC);
if(empty($zcl_tmpRs))
{
	die('zcl_tmpRs is empty');
}
//为你读诗用户
$sql2 = "select nick,phone from user;";
$statement = $pdo->query($sql2);
$wnds_tmpRs = $statement->fetchAll(PDO::FETCH_ASSOC);
if(empty($wnds_tmpRs))
{
	die('weinidushi is empty');
}
$nick_arr = array();
$phone_arr = array();
foreach($wnds_tmpRs as $key=>$value)
{
	$nick_arr[$value['nick']] = 1;
	$phone_arr[$value['phone']] = 1;
}
$pwd = md5('1a2b3c@*');
$bgpic = '/upload/bgpic/default.png';
$f = fopen('./importUser/importuser_'.date('Y-m-d').'_.txt', 'a+');
foreach($zcl_tmpRs as $key=>$value)
{
	if(isset($nick_arr[$value['nick']]) || isset($phone_arr[$value['tel']]))
	{
		continue;
	}
	//zcl gender 1 男 2 女
	// weinidushi gender 0 女 1 男
	$gender = 1;
	if($value['gender'] == 2){
		$gender = 0;
	}
	if(!empty($gender)) 
	{
		$sportrait = './upload/portrait/smale.png';
		$portrait = './upload/portrait/male.png';
		$anchor = 17;
	} 
	else 
	{
		$sportrait = './upload/portrait/sfemale.png';
		$portrait = './upload/portrait/female.png';
		$anchor = 18;
	}
	$time = mt_rand(1409483667,1439908924);
	$token = uniqid().mt_rand(10000,99999);
	$sql = "insert into user(`nick`,`pwd`,`phone`,`gender`,`bgpic`,`anchor`,`sportrait`,`portrait`,`addtime`,`token`) 
			values
			('{$value['nick']}','{$pwd}',{$value['tel']},{$gender},'{$bgpic}',$anchor,'{$sportrait}','{$portrait}',$time,'{$token}');";
	if($pdo->exec($sql))
	{
		fwrite($f, $sql."\n");
	}
}
fclose($f);
//链接数据库
function conDataBase() {
	try {
		// $pdo = new PDO('mysql:host=localhost;dbname=poem','root','poemproject@2014');
		$pdo = new PDO('mysql:host=192.168.0.3;dbname=poem','poem','EQJfuZV&Ks2V1f5V');
		$pdo->query('set names utf8');
		return $pdo;
	} catch(Exception $e) {
		return false;
	}
}
?>