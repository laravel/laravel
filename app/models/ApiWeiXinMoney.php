<?php 
/**
*	微信提线操作类
*	@author:黄
*	@since:2016/09/02
**/
class ApiWeiXinMoney extends ApiCommon
{
	//将前端回传的信息 uniouid
	public function getUserUnionid($uid,$unionid){
 		return DB::table('weixin_appuser')->insert(array('uid'=>$uid,'unionid'=>$unionid));

	}

	//写入opendi
	public function getUserInfo($openid,$nickname,$sex,$city,$province,$country,$unionid){
		$rs=DB::table('weixin_openid')->where('openid',$openid)->first();
		if(!$rs){
		return DB::table('weixin_openid')->insert(array('openid'=>$openid,'nickname'=>$nickname,'sex'=>$sex,'city'=>$city,'province'=>$province,'country'=>$country,'unionid'=>$unionid));
		}else{
			return 'pass';
		}

	}
	//判断用户是否授权或者关注
	public function checkUser($id){
		$apiuser = DB::table('weixin_appuser')->where('uid',$id)->first();
		if(!$apiuser){
			return "no_sq";//未授权
		}else{
			$openid = DB::table('weixin_openid')->where('unionid',$apiuser['unionid'])->first();
			if(!empty($openid) && $openid != ''){
				return 'pass';//可以提现			
			}else{
				return "no_gz";//未关注
			}
		}
	}
	//用户提现信息
	public function tocash($uid,$num){
		DB::beginTransaction();
    	$a=DB::table('weixin_cash')->insert(array('uid'=>$Uid,'num'=>$num,'time'=>time()));
		$b=DB::table('user_asset_num')->decrement('flower', $num );
		$c=DB::table('user_flowers_list')->insert(array('fromid'=>0,'toid'=>$uid,'opusid'=>0,'num'=>$num,'time'=>time(),'good_id'=>0,'orderid'=>0,'flag'=>3,'poemid'=>0,'reader_id'=>0));
		if($a && $b && $c){
                DB::commit();
				return true;
		}else{
	            DB::rollback();
				return false;
		}
	}		

	//根据用户得到openid
	public function getopenid($uid){

		$sql=" select * from weixin_appuser as u  left join weixin_openid as o on u.unionid=o.unionid where u.uid=? ";
		return DB::select($sql,array($uid));


	}

}
 ?>