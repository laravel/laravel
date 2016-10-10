<?php 
/**
 * 新版个人中心
 * @author :wang.hongli
 * @since :2017/07/18
 */
class ApiUserCenterController extends ApiCommonController{

	private $apiUserCenter;

	public function __construct(){
		parent::__construct();
		$this->apiUserCenter = new ApiUserCenter();
	}
	/**
	 * 获取个人中心
	 * @author :wang.hongli
	 * @since :2016/07/18
	 */
	public function userCenter(){
		//判断用户是否登陆
		$info = ApiCommonStatic::viaCookieLogin();
		if(empty($info)){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$uid = $info['id'];
		$otherId = intval(Input::get('otherId',0));
		if(!empty($otherId)){
			$userInfo = $this->apiUserCenter->getUserInfo($otherId);
		}else{
			$userInfo = $this->apiUserCenter->getUserInfo($uid);
		}
		if(empty($userInfo)){
			$this->setReturn(0,Lang::get('messages.miss'));
			return;
		}
		$data['userInfo'] = $userInfo;
		//关注状态0陌生人，1我->他 2，他->我 3->相互
		if(empty($otherId)){
			$data['userInfo']['relation'] = 0;
		}else{
			$data['userInfo']['relation'] = ApiCommon::attentionStatus($uid,$otherId);
		}
		//获取个人中心列表
		$data['userCenterList'] = array_values($this->apiUserCenter->userCenterList($uid,$otherId));
		$this->setReturn(1,'success',$data);	
	}

	/**
	 * 判断一个人是否为会员
	 * @author :wang.hongli
	 * @since :2016/08/16
	 */
	public function isMember(){
		$uid = intval(Input::get('uid',0));
		$isMember = 0;
		if(!empty($uid)){
			//判断是否为会员
			$apicheckPermission = App::make('apicheckPermission');
			$isMember = $apicheckPermission->isMember($user_info['id']);
		}
		$this->setReturn(1,'success',$isMember);
	}
}

 ?>