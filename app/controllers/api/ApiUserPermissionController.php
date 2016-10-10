<?php 
/**
 * 新功能相关用户权限检测
 * @author :wang.hongli <[<email address>]>
 * @since :2016/07/12 [<description>]
 */
class ApiUserPermissionController extends ApiCommonController {

	private $apiUserPermission;
	function __construct(){
		$this->apiUserPermission = new ApiUserPermission();
	}

	/**
	 * 获取会员权限列表
	 * @author :wang.hongli
	 * @since :2016/08/18 
	 */
	public function memberPermissionList(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		if(empty($user_info)){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$plat_form = Input::get('plat_form',0);
		$list = $this->apiUserPermission->memberPermissionList($user_info['id'],$plat_form);
		$this->setReturn(1,'success',$list);
	}

	/**
	 * 获取会员权限详细信息
	 * @author :wang.hongli
	 * @since :2016/08/18
	 */
	public function getMemberPerssionDetail(){
		$pid = intval(Input::get('id',1));
		if(empty($pid)){
			$this->setReturn(0,'获取会员权限详细信息失败');
			return;
		}
		$detail = $this->apiUserPermission->getMemberPerssionDetail($pid);
		$this->setReturn(1,'success',$detail);
	}
	/**
	 * 获取权限对应关系
	 * @author :wang.hongli
	 * @since : 2016/07/12
	 */
	public function permission_config(){
		$user_info = ApiCommonStatic::viaCookieLogin();
		$user_info['id'] = 100;
		if(empty($user_info)){
			$this->setReturn(-101,Lang::get('messages.nolog'));
			return;
		}
		$return = $this->apiUserPermission->permission_config($user_info['id']);
		$this->setReturn(1,'success',$return);
	}

	



}
?>
