<?php
/**
** 计划任务
*/
class ApiPlan extends ApiCommon {

	//对plan_user用户的所有作品，随机增加收听量、赞数、转发数量
	public static function planUserNum(){
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		$this_time=microtime(true);
		//配置
		$min_shou=10;
		$max_shou=20;
		$min_agree=1;
		$max_agree=5;
		$min_zhuan=1;
		$max_zhuan=5;
		$info = DB::table('plan_config')->where('name','plan_user_num')->first();
		if(empty($info) || $info['status']!=2){//关闭任务就不执行了
			return false;
		}else{
			$tmp = unserialize($info['contents']);
			if(!empty($tmp['min_shou']) && !empty($tmp['max_shou'])){
				$min_shou=$tmp['min_shou'];
				$max_shou=$tmp['max_shou'];
			}
			if(!empty($tmp['min_agree']) && !empty($tmp['max_agree'])){
				$min_agree=$tmp['min_agree'];
				$max_agree=$tmp['max_agree'];
			}
			if(!empty($tmp['min_zhuan']) && !empty($tmp['max_zhuan'])){
				$min_zhuan=$tmp['min_zhuan'];
				$max_zhuan=$tmp['max_zhuan'];
			}
		}
		
		//所有等级
		$allgrade=self::allGrade();
		
		//查询所有计划用户
		$sql="select * from plan_user limit 100";
		$rlt=DB::select($sql);
		
		
		//循环查询用户的作品
		foreach($rlt as $v){
			$uid = (int)$v['uid'];
			$sql="select id,uid from opus where uid=".$uid;
			$list=DB::select($sql);
			
			$user_num=array();
			foreach($list as $key=>$val){
				//随机加量
				$rand_lnum=rand($min_shou,$max_shou);
				$rand_praisenum=rand($min_agree,$max_agree);
				$rand_repostnum=rand($min_zhuan,$max_zhuan);
				//增加作品随机量
				$sql = "update opus set lnum=lnum+".$rand_lnum.",praisenum=praisenum+".$rand_praisenum.",repostnum=repostnum+".$rand_repostnum." where id=".$val["id"];
				DB::update($sql);
				//echo $sql."<br>";
				
				//用户数量累计
				if(isset($user_num['lnum'])){
					$user_num['lnum']+=$rand_lnum;
					$user_num['praisenum']+=$rand_praisenum;
					$user_num['repostnum']+=$rand_repostnum;
				}else{
					$user_num['lnum']=$rand_lnum;
					$user_num['praisenum']=$rand_praisenum;
					$user_num['repostnum']=$rand_repostnum;
				}
				
			}
			//用户的随机量和等级
			$userInfo = DB::table('user')->where('id','=',$uid)->first(array('id','lnum','grade'));
			$lnum=$userInfo['lnum']+$user_num['lnum'];
			$groupid = self::getGrade($lnum,$allgrade);
			if($userInfo['grade']<$groupid){
				$sql = "update user set lnum=lnum+".$user_num['lnum'].",praisenum=praisenum+".$user_num['praisenum'].",repostnum=repostnum+".$user_num['repostnum'].",grade=".$groupid." where id=".$uid;
				DB::update($sql);
				//echo $sql."<br>";
			}else{
				$sql = "update user set lnum=lnum+".$user_num['lnum'].",praisenum=praisenum+".$user_num['praisenum'].",repostnum=repostnum+".$user_num['repostnum']." where id=".$uid;
				DB::update($sql);
				//echo $sql."<br>";
			}

		}
		
		echo microtime(true)-$this_time;
		return true;
	}
	
	//增加所有作品，随机增加收听量、赞数、转发数量
	public static function planAll(){
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		//配置
		$min_shou=2;
		$max_shou=5;
		$min_agree=2;
		$max_agree=5;
		$min_zhuan=2;
		$max_zhuan=5;
		
		$info = DB::table('plan_config')->where('name','plan_all_num')->first();
		if(empty($info) || $info['status']!=2){//关闭任务就不执行了
			return false;
		}else{
			$tmp = unserialize($info['contents']);
			if(!empty($tmp['min_shou']) && !empty($tmp['max_shou'])){
				$min_shou=$tmp['min_shou'];
				$max_shou=$tmp['max_shou'];
			}
			if(!empty($tmp['min_agree']) && !empty($tmp['max_agree'])){
				$min_agree=$tmp['min_agree'];
				$max_agree=$tmp['max_agree'];
			}
			if(!empty($tmp['min_zhuan']) && !empty($tmp['max_zhuan'])){
				$min_zhuan=$tmp['min_zhuan'];
				$max_zhuan=$tmp['max_zhuan'];
			}
		}
		
		//所有等级
		$allgrade=self::allGrade();
		//查询所有作品
		$sql="select id,uid from opus where id<10 order by id asc";
		$list=DB::select($sql);
		
		$user_num=array();
		foreach($list as $key=>$val){
			$uid = $val['uid'];
			//随机数
			$rand_lnum=rand($min_shou,$max_shou);
			$rand_praisenum=rand($min_agree,$max_agree);
			$rand_repostnum=rand($min_zhuan,$max_zhuan);
			//更新作品
			$sql = "update opus set lnum=lnum+".$rand_lnum.",praisenum=praisenum+".$rand_praisenum.",repostnum=repostnum+".$rand_repostnum." where id=".$val["id"];
			DB::update($sql);
			//echo $sql."<br>";
			
			//用户数量累计
			if(isset($user_num[$val['uid']])){
				$user_num[$val['uid']]['lnum']+=$rand_lnum;
				$user_num[$val['uid']]['praisenum']+=$rand_praisenum;
				$user_num[$val['uid']]['repostnum']+=$rand_repostnum;
			}else{
				$user_num[$val['uid']]['lnum']=$rand_lnum;
				$user_num[$val['uid']]['praisenum']=$rand_praisenum;
				$user_num[$val['uid']]['repostnum']=$rand_repostnum;
			}

			
		}
		
		//用户的随机量和等级
		foreach($user_num as $uid=>$val){
			//计算用户等级
			$userInfo = DB::table('user')->where('id','=',$uid)->first(array('id','lnum','grade'));
			$lnum=$userInfo['lnum']+$val['lnum'];
			$groupid = self::getGrade($lnum,$allgrade);
			if($userInfo['grade']<$groupid){
				$sql = "update user set lnum=lnum+".$val['lnum'].",praisenum=praisenum+".$val['praisenum'].",repostnum=repostnum+".$val['repostnum'].",grade=".$groupid." where id=".$val["uid"];
				DB::update($sql);
				//echo $sql."<br>";
			}else{
				$sql = "update user set lnum=lnum+".$val['lnum'].",praisenum=praisenum+".$val['praisenum'].",repostnum=repostnum+".$val['repostnum']." where id=".$val["uid"];
				DB::update($sql);
				//echo $sql."<br>";
			}
		}
		
		return true;
	}
	
	//用户等级
	public static function allGrade(){
		$list = array();
		$sql="select * from grade order by grade desc";
		$rlt=DB::select($sql);
		foreach($rlt as $k=>$v){
			$list[$v['grade']]=$v['lnum'];
		}
		return $list;
	}
	//获取用户等级
	public static function getGrade($lnum,$allgrade){
		foreach($allgrade as $gid=>$val){
			if($lnum>=$val){
				return ($gid+1)>10?10:($gid+1);
			}
		}
		return 1;//默认1级
	}
	
	//日志
	public static function _log($name,$content){
	}
	

}