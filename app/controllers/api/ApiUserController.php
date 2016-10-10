<?php
	/**
	*接口api用户相关的类
	*/	
	class ApiUserController extends ApiCommonController {
		private $user = null;
		public function __construct() {
			$this->user = new ApiUser();
		}
		//用户注册
		public function register() {
			$rs = $this->user->register();
			if(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(0,$rs);
			}
		}
		/**
		 * 用户注册V2
		 * @author:wang.hongli
		 * @since:2016/05/27
		 */
		public function registerV2(){
			$rs = $this->user->registerV2();
			if(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(0,$rs);
			}
		}
		
		//用户登录
		public function login() {
			$rs = $this->user->login();
			if(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(0,$rs);
			}
		}
		//发送验证码
		public function passwordRetake() {
			$rs=$this->user->passwordRetake();
			if(is_array($rs)) {
				if(1 == $rs['sign'])  {
					$this->setReturn(1,'success',$rs['code']);
				} else {
					$this->setReturn(2,'success',$rs['code']);
				}
			} else {
				$this->setReturn(0,$rs);
			}
		}
		//修改密码
		public function modifyPass() {
			$rs = $this->user->modifyPass();
			if(true === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}
		//修改个人信息
		public function editPersonInfo() {
			$rs = $this->user->editPersonInfo();
			if('nolog' === $rs) {
				$this->setReturn(-100,'请登录');
			} elseif(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//第三方登录 qq sina微博 微信
		public function thirdPartLogin() {
			$rs = $this->user->thirdPartLogin();
			if(-101 === $rs) {
				$this->setReturn(2,'此昵称已经存在');
			} else if(-102 === $rs) {
				$this->setReturn(2,'昵称不能超过八位');
			}else if(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} else if(-103 == $rs) { 
				$this->setReturn(0,'账号被禁用');
			} else {
				$this->setReturn(0,'登录失败');
			}
		}
		/**
		*	@author:wang.hongli
		*	@since:2015/06/28
		*	设置 state --临时票据 session方式 防止CSRF
		**/
		public function getWeiXinState()
		{
			$weixinState = $this->user->getWeiXinState();
			$this->setReturn(1,'success',$weixinState);
		}

		/**
		*	@author:wang.hongli
		*	@since:2015/06/28
		*	获取微信登录access_token
		**/
		public function getWeiXinUserInfo()
		{
			//临时票据
			$code = Input::get('code');
			// //防止 csfr 攻击
			$state = Input::get('state');
			$rs = $this->user->getAccessToken($code,$state);
			if(!$rs)
			{
				$this->setReturn(0,'获取微信登录access_token失败');
			}
			else
			{
				//获取用户信息
				$access_token = $rs['access_token'];
				$openid = $rs['openid'];
				$userinfo = $this->user->getWeiXinUserInfo($access_token,$openid);
				if($userinfo)
				{
					$this->setReturn(1,'success',$userinfo);
				}
				else
				{
					$this->setReturn(0,'获取用户信息失败');
				}
			}
		}

		//拉黑 or 取消拉黑 flag 1 拉黑 2 取消拉黑
		public function editBlackList() {
			$rs = $this->user->editBlackList();
			if('nolog' === $rs) {
				$this->setReturn(-100,'请登录');
			} elseif(true === $rs) {
				$this->setReturn(1,'success');
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//获取他人信息
		public function getOtherInfo() {
			$rs = $this->user->getOtherInfo();
			if(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} elseif('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} else {
				$this->setReturn(0,$rs);
			}
		}
		//设置主页背景图
		public function setBgPic() {
			$rs = $this->user->setBgPic();
			if('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} elseif(true === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//绑定手机号
		public function bindPhoneNum() {
			$rs = $this->user->bindPhoneNum();
			if('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} elseif(true === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}
		
		/**
		 * 绑定手机号
		 * @author:wang.hongli
		 * @since:2016/05/27
		 * @modify:增加验证码功能
		 */
		public function bindPhoneNumV2(){
			$rs = $this->user->bindPhoneNumV2();
			if('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} elseif(true === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}
		
		//获取好友通讯录
		public function getBindList() {
			$rs = $this->user->getBindList();
			if('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} elseif(is_array($rs)) {
				$this->setReturn(1,'success',$rs);
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//机关推送-用户id绑定
		public function bindJId() {
			$rs = $this->user->bindJId();
			if('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} elseif(true === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//第三方登陆，只修改密码
		public function thirdPartEditPass() {
			$rs = $this->user->thirdPartEditPass();
			if('nolog' === $rs) {
				$this->setReturn(-101,'请登录');
			} elseif(true === $rs) {
				$this->setReturn(1);
			} else {
				$this->setReturn(0,$rs);
			}
		}

		//报名读诗用户
		public function signUpUser() {
			$rs = $this->user->signUpUser();
			if(true === $rs) {
				$this->setReturn(1,'success');
			} else {
				$this->setReturn(0,$rs);
			}
		}

		/**
		* add_activer_user
		*@author:wang.hongli
		*@since:2016/06/15
		**/
		public function addActiveUser(){
			$this->user->addActiveUser();
			$this->setReturn(1);
		}
	}