<?php 
/**
* 	链接数据库
*	@author:wang.hongli
*	@since:2015/06/22
**/

class DBconnect
{
	private static $_instance;
	public $pdo;
	private function __construct()
	{
		
	}
	public function __clone()
	{
		trigger_error('Clone is not allow!',E_USER_ERROR);
	}

	public static function getInstance()
	{
		if(!(self::$_instance instanceof self))
		{
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public function getConn()
	{
		try 
		{
			$this->pdo = new PDO('mysql:host=192.168.0.3;dbname=poem','poem','EQJfuZV&Ks2V1f5V');
			$this->pdo->query('set names utf8');
			return $this->pdo;
		} 
		catch(Exception $e) 
		{
			return false;
		}
	}
}
?>