<?php 
/**
*	mongodb 操作类
*	@author:wang.hongli
*	@since:2015/06/10
**/
class MongoOperator
{
	/**
	*	链接mongodb
	*	@author:wang.hongli
	*	@since:2015/06/10
	**/
	public static function getMonConn()
	{
		$mongodb = require_once('../app/config/database.php');
		$mongodb = $mongodb['mongodb'];
		$url = $mongodb['driver'].'://'.$mongodb['username'].':'.$mongodb['password'].'@'.$mongodb['host'];
		$conn = new MongoClient($url);
		return $conn;
	}

	/**
	*	链接到指定数据库
	*	@author:wang.hongli
	*	@since:2015/06/10
	**/
	public static function getInstance($db_name)
	{
		if(empty($db_name))
		{
			return false;
		}
		$db_instance = self::getMonConn()->$db_name;
		return $db_instance;
	}


}
 ?>