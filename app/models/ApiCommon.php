<?php 
	//模型类公共类
	class ApiCommon extends Eloquent {
		//资源路径
		protected  $poem_url = '';
		//网站根路径
		protected $url = '';
		
		function __construct(){
			$this->poem_url = Config::get('app.poem_url');
			$this->url = Config::get('app.url');
		}
		//判断是否通过cookie登录
		public function viaCookieLogin() {
			$id = !empty($_COOKIE['id']) ? $_COOKIE['id'] : false;
			// $id = 35;
			if(!$id) {
				return false;
			} else {
				$tmpArr = explode('|', $id);
				if(empty($tmpArr)) return false;
				$id = $tmpArr[0];
				$token = $tmpArr[1];
				//从redis中取出用户信息
				$key = 'cookie_check:'.$id.':'.$token;
				$redisUserInfo = new RedisUserInfo();
				$info = $redisUserInfo->getCookieFromRedis($key);
				if(!empty($info)){
					return $info;
				}
				$info = DB::table('user')
							->where('id','=',$id)->where('token','=',$token)->where('isdel','=',0)
							->first(array('id','nick','email','phone',
								'gender','lnum','repostnum','attention',
								'praisenum','fans','opusnum','grade',
								'sportrait','portrait','bgpic','albums','signature',
								'authtype','addtime','accessToken','expirationDate','thPartid','pwd','isdel','isleague'));
				if(empty($info)) return false;
				if(!empty($info['pwd'])) {
					if(1 == $info['isdel']) return false; 
					$info['haspwd'] = 1;
				} else {
					$info['haspwd'] = 0;
				}
				unset($info['pwd']);
				
				$info['portrait'] = !empty($info['portrait']) ?  $this->poem_url.ltrim($info['portrait'],'.') : '';
				$info['sportrait'] = !empty($info['sportrait']) ? $this->poem_url.ltrim($info['sportrait'],'.') : '';
				$info['bgpic'] = !empty($info['bgpic']) ? $this->poem_url.ltrim($info['bgpic'],'.') : '' ;
				//标记用户为活跃用户
				// if(!empty($info['id'])){
				// 	$redisNotification = new RedisActiveUser();
				// 	$redisNotification->addActiveUser($info['id']);
				// }
				//将用户信息存入redis
				$redisUserInfo->addCookieToRedis($id,$token,$info,1800);
				return $info;
			}
		}
		//获取年-月-日  0点时间戳
		protected function getOTS($timestamp='') {
			if(!empty($timestamp)) {
				$timestamp = strtotime(date('Y-m-d',$timestamp));
			} else {
				$timestamp = strtotime(date('Y-m-d',time()));
			}
			return $timestamp;
		}
		//判断某个文件夹是否存在，不存在创建
		protected function isExistDir($filePath = '') {
			$fileName = $this->getOTS();
			$filePath = './upload/'.$filePath.'/'.$fileName.'/';
			if(!file_exists($filePath)) {
				mkdir($filePath,0755,true);
			}
			return $filePath;
		}

		//图片等比例缩放返回尺寸 宽度100
		//filePath:portrait,ablum等文件夹
		//sw 缩放后图片宽度
		//$uid 用户的id
		//$id 分表时候的albumindex 的id
		//$flag 空 头像  1 相册
		protected function uploadPic($tableName='',$uid='',$filePath='',$pw='',$flag='',$id='') {
			if(empty($filePath) || empty($tableName) || empty($uid)) return false;
			// $filePath = '../upload/'.$filePath.'/';
			$filePath = $this->isExistDir($filePath);
			$arr = Input::file('formName');
			if(!empty($arr)) {
				//判断作品类型，只能是图片格式
				$fileType = strtolower(my_file_type($arr->getRealPath()));
				$img_type_arr = array('gif','jpg','jpeg','png');
				if(empty($fileType) || !in_array($fileType, $img_type_arr))
				{
					return false;
				}
				$size = array();
				$ext = $arr->guessExtension();
				$name = time().uniqid();
				$name = $name.'.'.$ext;
				$lastFilePath = $filePath.$name;
				$arr->move($filePath,$name);
				//获取原图大小
				$size = getimagesize($lastFilePath);
				if(empty($size[3])) return false;
				$tmpArr = explode(' ',$size[3]);
				$sw = intval(substr(explode('=',$tmpArr[0])[1],1,-1));
				$sh = intval(substr(explode('=',$tmpArr[1])[1],1,-1));
				if(empty($flag)) {
					if($sw <=250) {
						$sql = "update {$tableName} set portrait=?,sportrait=? where id = ?";
						DB::update($sql,array($lastFilePath,$lastFilePath,$uid));
					} else {
						//sw > 100 进行等比例缩放
						$ph = $pw*$sh/$sw;
						$name = time().uniqid();
						$name = $name.'.'.$ext;
						$lastFilePath2 = $filePath.$name;	
						Image::make($lastFilePath)->resize($pw,$ph)->save($lastFilePath2);
						$sql = "update {$tableName} set portrait=?,sportrait=? where id = ?";
						DB::update($sql,array($lastFilePath,$lastFilePath2,$uid));
					}
				} else {
					$time = time();
					if($sw <=250) {
						$lastFilePath = ltrim($lastFilePath,'.');
						$sql = "insert into {$tableName} (id,uid,surl,url,iscover,addtime) values ({$id},{$uid},'{$lastFilePath}','{$lastFilePath}',0,{$time})";
						if(!DB::insert($sql))
							return false;
					} else {
						$ph = $pw*$sh/$sw;
						$name = time().uniqid();
						$name = $name.'.'.$ext;
						$lastFilePath2 = $filePath.$name;	
						Image::make($lastFilePath)->resize($pw,$ph)->save($lastFilePath2);
						$lastFilePath = ltrim($lastFilePath,'.');
						$lastFilePath2 = ltrim($lastFilePath2,'.');
						$sql = "insert into {$tableName} (id,uid,surl,url,iscover,addtime) values ({$id},{$uid},'{$lastFilePath2}','{$lastFilePath}',0,{$time})";
						if(!DB::insert($sql))
							return false;
					}
					return true;
				}
			} else {
				return false;
			}
		}

		//判断删除权限
		//仅限表中有uid的权限判断，相册表，作品表
		protected function deletePermission($id,$tableName,$uid) {
			if(empty($id) || empty($tableName) || empty($uid)) return false;
			$tmp_uid = DB::table($tableName)->where('id',$id)->pluck('uid');
			if(empty($tmp_uid)) return false;
			if($tmp_uid != $uid) return false;
			return true;
		}

		//下一页
		protected function hasMore($rs,$count) {
			if(empty($rs) || !is_array($rs)) return false;
			$num = count($rs);
			if($num >= $count) {
				return true;
			} else {
				return false; 
			}
		}	

		//判断下一页，相对路径转绝对路径 数组，只真对原诗
		protected function convUrlHasNext($rs,$count) {
			if(empty($rs) || !is_array($rs)) return false;
			foreach($rs as $key=>&$value) {
				$value['burl'] = $this->poem_url.$value['burl'];
				$value['yurl'] = $this->poem_url.$value['yurl'];
				$value['lyricurl'] = $this->poem_url.$value['lyricurl'];
			}
			unset($value);
			if($this->hasMore($rs,$count)) {
				array_pop($rs);
				$rs['hasmore'] = 1;
			} else {
				$rs['hasmore'] = 0;
			}
			return $rs;
		}
		//获取关注状态
		public static function attentionStatus($uid,$fid) {
			$relation = DB::table('follow')
								->where('uid','=',$uid)
								->where('fid','=',$fid)
								->pluck('relation');
			if(!empty($relation)) {
				return $relation;
			}
			$relation = DB::table('follow')
								->where('uid','=',$fid)
								->where('fid','=',$uid)
								->first(array('relation'));
			if(!empty($relation)) {
				if($relation == 3) {
					return 3;
				} else {
					return 2;//别人关注我
				}
			}
			return 0; //陌生人
		}
		/**
		 * 根据一部分人获取我关注的用户id
		 * @author:wang.hongli
		 * @since:2016/05/25
		 */
		public static function myAttenUser($uid,$fids){
			$atten_arr = DB::table('follow')->where('uid',$uid)->lists('fid');
			$atten_arr = !empty($atten_arr) ? $atten_arr : array();
			//取出数组交集
			$return = array_intersect($atten_arr, $fids);
			$return = !empty($return) ? $return : array();
			return $return;
		}

		//查看作品是否收藏
		protected function isCollection($uid,$opusId) {
			if($uid) {
				$flag = DB::table('collection')->where('uid',$uid)->where('opusid',$opusId)->pluck('id');
				if($flag) {
					return $colStatus = 1; //收藏
				} else {
					return $colStatus = 0;
				}
			} else {
				$colStatus = 0; //没登录都0
			}
		}

		//查看作品是否赞过
		protected function isPraise($uid,$opusId) {
			$redisOpusPraise = new RedisOpusPraise();
			return $redisOpusPraise->isPraise($uid, $opusId);
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
						$this->pushMsg($pushid,$content,$type,$deviceTypeStr);
					}
				} else {
					if(DB::table('notification')->insert($arr)) {
						$this->pushMsg($pushid,$content,$type,$deviceTypeStr);
					}
				}
			} else {
				if(DB::table('notification')->insert($arr)) {
					//6为粉丝数增加，不推送
					if(6 != $type)
					{
						$this->pushMsg($pushid,$content,$type,$deviceTypeStr);
					}
					
				}
			}
		}
		//发送推送消息 
		//消息类型1评论 2转发 3赞 4收藏5收到私信7,到期提醒
		public function pushMsg($uid='',$content='',$type='',$devicetype = 3) {
			$pusMsg = Config::get('app.pushMsg');
			if(!empty($pusMsg)){
				return false;
			}
			if(empty($uid) || empty($content) || empty($type)) return false;
			$rs = DB::table('jpush')->where('uid','=',$uid)->first(array('jid','devicetype'));
			if(empty($rs)) {
				return false;
			}
			//增加或者插入拥有的信息数
			// $sql = "insert into notificationNum(uid,notificationNum) values ({$uid},1) on duplicate key update notificationNum=notificationNum+1";
			// DB::insert($sql);
			//统计有多少条消息
			// $badgeNum = DB::table('notificationNum')->where('uid',$uid)->pluck('notificationNum');
			// $badgeNum = !empty($badgeNum) ? intval($badgeNum) : 1;
			//获取消息数量
			//redis 中获取记录
			$redisNotification  = new RedisNotification();
			$data = $redisNotification->getNotificationNum($uid);
			if($data){
				$badgeNum = $data['notificationNum'] + $data['fansNotiNum'];
			}
			if(empty($badgeNum)){
				$badgeNum = 1;
			}
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
				$umengPush->sendIOSUnicast($content,true,$type,$badgeNum);
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

		//根据作品id，查找作品主人id，作品名称
		public function getOpusAndUserInfo($opusId) {
			if(empty($opusId)) return false;
			$rs = DB::table('opus')->where('id',$opusId)->first(array('uid as id','name'));
			if(empty($rs)) return false;
			return $rs;
		}

		//根据人的id判断用户等级
		protected function setUserGrade($uid)
		{	
			if(empty($uid)) return;
			$userInfo = DB::table('user')->where('id','=',$uid)->first(array('id','grade','lnum'));
			if(empty($userInfo)) return;
			$grade = $userInfo['grade'];
			$gradeInfo = DB::table('grade')->where('grade','=',$grade)->first(array('lnum'));
			if((intval($userInfo['lnum']) > intval($gradeInfo['lnum'])) && $grade<10)
			{
				try {
					DB::table('user')->where('id',$uid)->increment('grade',1);
				} catch (Exception $e) {
				}
			}
		}
		/*
		* 判断是否是自由诵读
		*/
		public function isFree($name){
			$isfree=0;
			if(!empty($name)){
				if(preg_match('/自由诵读/i',$name)){$isfree=1;}
			}
			return $isfree;
		}
	}
