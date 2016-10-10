<?php 
/**
*	后台公共模型
*	@author:wang.hongli
*	@since:2015/05/09
**/
class AdminCommon extends Eloquent
{
	protected  $url = '';
	protected $poem_url = '';
	public function __construct(){
		$this->url = Config::get('app.url');
		$this->poem_url = Config::get('app.poem_url');
	}
	//获取年-月-日  0点时间戳
	public function getOTS($timestamp='') {
		if(!empty($timestamp)) {
			$timestamp = strtotime(date('Y-m-d',$timestamp));
		} else {
			$timestamp = strtotime(date('Y-m-d',time()));
		}
		return $timestamp;
		
	}

	//判断某个文件夹是否存在，不存在创建
	public function isExistDir($filePath = '') {
		if(!file_exists($filePath)) {
			mkdir($filePath,0755,true);
		}
		return $filePath;
	}
	//添加消息列表
	//ownid  -- 作品主人id 	 	fromid -- 发表的人的id
	//opusid -- 作品id 			commentid-评论的id
	//plid   -- 发私信时plid 	pushid ---收到推送消息的人的id
	//content --推送的内容 		type   ---消息列表，推送的类型
	//toid 	  --评论对方的id
	public function addNotification($ownid='',$fromid='',$opusid='',$commentid='',$plid='',$pushid='',$content='',$type='',$toid='',$personalcustomid='') {
		$time = time();
		$ownid = !empty($ownid) ? $ownid : 0;
		$fromid = !empty($fromid) ? $fromid : 0;
		$toid = !empty($toid) ? $toid : 0;
		$opusid = !empty($opusid) ? $opusid : 0;
		$commentid = !empty($commentid) ? $commentid : 0;
		$personalcustomid = !empty($personalcustomid) ? $personalcustomid :0; //私人定制id
		$plid = !empty($plid) ? $plid : 0;
		$type = !empty($type) ? $type : 0;
		$addtime = !empty($addtime) ? $addtime : 0;
		//判断用户是ios还是andiroid
		$deviceTypeStr = DB::table('jpush')->where('uid',$toid)->pluck('devicetype');
		//默认是3android  4 ios
		$deviceTypeStr = !empty($deviceTypeStr) ? $deviceTypeStr : 3;
		$arr = array(
				'ownid'=>$ownid,
				'fromid'=>$fromid,
				'toid' => $toid,
				'opusid' => $opusid,
				'personalcustomid'=>$personalcustomid,
				'commentid'=>$commentid,
				'plid' => $plid,
				'type' =>$type,
				'isdel' => 0,
				'addtime'=>$time
		);
		if(5 == $type) {
			$id = DB::table('notification')->where('ownid',$ownid)->where('fromid',$fromid)->where('toid',$toid)->where('plid',$plid)->where('type',$type)->pluck('id');
			if(!empty($id)) {
				if(DB::table('notification')->where('ownid',$ownid)->where('fromid',$fromid)->where('toid',$toid)->where('plid',$plid)->where('type',$type)->update(array('addtime'=>$time,'isdel'=>0))) {
					//增加消息数量
					$this->incNotiNum($toid);
// 					$this->pushMsg($toid,$content,$type,$deviceTypeStr);
				}
			} else {
				if(DB::table('notification')->insert($arr)) {
					$this->incNotiNum($toid);
// 					$this->pushMsg($toid,$content,$type,$deviceTypeStr);
				}
			}
		} else {
			if(DB::table('notification')->insert($arr)) {
				//6为粉丝数增加，不推送
				if(6 != $type)
				{
					$this->incNotiNum($toid);
// 					$this->pushMsg($toid,$content,$type,$deviceTypeStr);
				}
					
			}
		}
	}
	/**
	 * @增加系统通知数量
	 * @author:wang.hongli
	 * @since:2016/04/25
	 * @param:uid 收到消息用户id， issystem 0 0为非系统消息 1 系统消息
	 */
	protected function incNotiNum($uid,$issystem=0){
		if(empty($uid))
			return false;
		//增加或者插入拥有的信息数
		$isexists_uid = DB::table('notificationNum')->where('uid',$uid)->pluck('uid');
		$type = 'notificationNum';
		$data['notificationNum'] = 1;
		$data['sysnotNum'] = 0;
		$data['uid'] = $uid;
		if(!empty($issystem)){
			$data['notificationNum'] = 0;
			$data['sysnotNum'] = 1;
			$type = 'sysnotNum';
		}
		if($isexists_uid){
			DB::table('notificationNum')->where('uid',$uid)->increment($type);
		}else{
			DB::table('notificationNum')->insert($data);
		}
		return true;
	}
	/**
	 * 推送消息版本2
	 * @author:wang.hongli
	 * @since:2016/04/25
	 */
	protected  function pushMsgV2($uid='',$content='',$type='',$devicetype = 3){
		if(empty($uid) || empty($content) || empty($type)) return false;
		$rs = DB::table('jpush')->where('uid','=',$uid)->first(array('jid','devicetype'));
		if(empty($rs)) {
			return false;
		}
		//统计有多少条消息
		$badgeNum = DB::table('notificationNum')->where('uid',$uid)->pluck('notificationNum');
		$sql = "SELECT (notificationNum+sysnotNum) as badgeNum FROM poem.notificationNum where uid = ?";
		$badgeNum = DB::select($sql,array($uid));
		$badgeNum = !empty($badgeNum[0]['badgeNum']) ? intval($badgeNum[0]['badgeNum']) : 1;
		
		$jid = trim($rs['jid']);
		$devicetype = $rs['devicetype'];
		
		if($devicetype==4){
			//ios推送改为友盟推送
			$key_arr = Config::get('youmengpush.ios');
			$key = $key_arr['key'];
			$secret = $key_arr['secret'];
			$validation_token = $jid;
			$umengPush = new UmengPush($key, $secret,$validation_token);
			$umengPush->sendIOSUnicast($content,false,$type,$badgeNum);
			return true;
		}else{
			//安卓推送
			
		}
		
	}
	//发送推送消息
	//消息类型1评论 2转发 3赞 4收藏5收到私信7,到期提醒
	protected function pushMsg($uid='',$content='',$type='',$devicetype = 3) {
		if(empty($uid) || empty($content) || empty($type)) return false;
		$rs = DB::table('jpush')->where('uid','=',$uid)->first(array('jid','devicetype'));
		if(empty($rs)) {
			return false;
		}
		//增加或者插入拥有的信息数
		$isexists_uid = DB::table('notificationNum')->where('uid',$uid)->pluck('uid');
		if($isexists_uid){
			DB::table('notificationNum')->increment('notificationNum');
		}else{
			DB::table('notificationNum')->insert(array('uid'=>$uid,'notificationNum'=>1));
		}
		//统计有多少条消息
		$badgeNum = DB::table('notificationNum')->where('uid',$uid)->pluck('notificationNum');
		$badgeNum = !empty($badgeNum) ? intval($badgeNum) : 1;
			
		$jid = trim($rs['jid']);
		$devicetype = $rs['devicetype'];
			
		if($devicetype == 4)
		{
			//ios推送改为友盟推送
			$key_arr = Config::get('youmengpush.ios');
			$key = $key_arr['key'];
			$secret = $key_arr['secret'];
			$validation_token = $jid;
			$umengPush = new UmengPush($key, $secret,$validation_token);
			$umengPush->sendIOSUnicast($content,false,$type,$badgeNum);
			return true;
		}
		else
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.jpush.cn/v3/push");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_HTTPHEADER,array(
					"User-Agent: {Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 (.NET CLR 3.5.30729)}",
					"Accept-Language: {en-us,en;q=0.5}",
			));
			curl_setopt($ch, CURLOPT_USERPWD, "6133929e321cf3dd8a98d63d:235fda1aadbeda73b60d06af");
			curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			$post_data = '
				{
				   "platform": ["android"],
				   "audience" : {
				   		"registration_id" : ["'.$jid.'"]
				   },
				    "notification" : {
				        "android" : {
				             "alert" : "'.$content.'",
				             "extras" : {
				             	"messageType" : "'.$type.'"
				             }
				        }
				    }
				}';
			curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
			$output = curl_exec($ch);
			curl_close($ch);
		}
		return true;
	}

}
 ?>