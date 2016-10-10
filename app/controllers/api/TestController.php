<?php 
class TestController extends Controller
{
	/*
	* 阿里web支付客户通知页面
	*/
	public function callBackWap(){
		echo 'success';
	}
	
	/**
	* 微信支付回调
	**/
	public function callWeiXin()
	{
		$this->apiPay = new ApiPay;
		$return = $this->apiPay->callWeiXin();
		if($return){
			echo 'success';
		}else{
			echo 'fail';
		}
	}
	
	/*
	* 赛事服务条款
	*/
	public function getMatchClause(){
		$id=Input::get('id');
		$info = DB::table('competitionlist')
				->where('id','=',$id)
				->first(array('clause'));
		echo '<!doctype html>  
		<html>  
		<head lang="zh-CN">  
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<title>服务条款</title> 
		</head> 
		<body>
		'.$info['clause'].'
		</body>  
		</html>';
		
	}
	
	/*
	* 测试
	*/
	public function ceshi(){
		$id=10;
		
		echo Easemob::pwdHash($id)."<br>";
		
		$config = Easemob::$config;
		$easemob = new Easemob($config);
		$hx_password = $easemob->pwdHash($id);
		echo $hx_password."<br>";
		
	}
	
	/*
	* 环行添加用户
	*/
	public function addUsers(){
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		
		/*$info=array('id'=>3433,'nick'=>'小米');
		$config = Easemob::$config;
		$easemob = new Easemob($config);
		$password = $easemob->pwdHash($info['id']);
		$data=$easemob->addUser($info['id'],$password,$info['nick']);
		print_r($data);*/
		
		$config = Easemob::$config;
		$easemob = new Easemob($config);
		
		$sql="select id,nick from user order by id asc";
		$rlt=DB::select($sql);
		$info=array();
		foreach($rlt as $k=>$v){
			$tmp=array();
			$tmp['username']=$v['id'];
			$tmp['password']=md5(md5($v['id']).'pwd');
			$tmp['nickname']=$v['nick'];
			$info[]=$tmp;
			if($k%40==0 && $k>0){
				$easemob->_piliang($info);
				$info=array();//重置
				sleep(1);
			}
			
		}
		//补充执行
		$easemob->_piliang($info);
		
		echo "OK";
		
	}
}
 ?>