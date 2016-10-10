<?php
use Illuminate\Support\Facades\Redirect;

/**
 * 后台消息管理
 * @author:wang.hongli
 * @since:2016/04/20
 */
class AdminNotificationController extends BaseController{
	
	//验证规则
	private $rules = array();
	//提示信息
	private $message = array();
	//资源地址
	private $poem_url = '';
	private $url = '';
	public function __construct(){
		$this->rules = array(
				'role_name'=>'required|alpha|unique:msg_role,role_name',
				'sportrait'=>'required|image'
		);
		$this->message = array(
				'role_name.required'=>'请填写角色昵称',
				'role_name.alpha'=>'角色名称必须为字符串',
				'role_name.unique'=>'该角色已存在',
				'sportrait.required'=>'角色头像不能为空',
				'sportrait.image'=>'头像必须为图片格式'
		);
		$this->poem_url = Config::get('app.poem_url');
		$this->url = Config::get('app.url');
	}
	/**
	 * 发送消息角色列表
	 * @author:wang.hongli
	 * @since:2016/04/20
	 */
	public function adminRoleList(){
		$adminNotification = new AdminNotification();
		$rs = $adminNotification->adminRoleList();
		
		return View::make('adminnotification.adminrolelist')->with('rs',$rs);
	}
	
	/**
	 * 添加角色
	 * @author:wang.hongli
	 * @since:2016/04/20
	 */
	public function addRole(){
		$data = Input::all();
		$validator = Validator::make(Input::all(), $this->rules,$this->message);
		if($validator->fails()){
			return Redirect::to('/admin/adminRoleList')->withErrors($validator)->withInput();
		}
		$adminNotification = new AdminNotification();
		$rs = $adminNotification->addRole($data);
		if(!$rs){
			return Redirect::to('/admin/defaultError')->with('message','请上传jpg,png类型文件');
		}
		//跳转到指定路由
		return Redirect::to('/admin/adminRoleList');
	}
	/**
	 * 修改角色昵称和头像
	 * @author:wang.hongli
	 * @since:2016/04/20
	 */
	public function modifyRole($id=0){
		if(empty($id)){
			return Redirect::to('/admin/defaultError')->with('message','错误，请重试');
		}
		if(!Input::has('role_name')){
			$rs = DB::table('poem.msg_role')->where('id',$id)->first(array('id','role_name','sportrait'));
			$rs['sportrait'] = $this->poem_url.'/'.$rs['sportrait'];
			return  View::make('adminnotification.modifyrole')->with('rs',$rs);
		}
		$rules = array(
				'role_name'=>'required|alpha',
				'sportrait'=>'image'
		);
		$message = array(
				'role_name.required'=>'请填写角色昵称',
				'role_name.alpha'=>'角色名称必须为字符串',
				'sportrait.image'=>'头像必须为图片格式'
		);
		$file = Input::file('sportrait');
		$role_name = htmlspecialchars(trim(Input::get('role_name')));
		
		$validator = Validator::make(Input::all(), $rules,$message);
		if($validator->fails()){
			return Redirect::to('/admin/adminRoleList')->withErrors($validator)->withInput();
		}
		$adminNotification = new AdminNotification();
		$flag = $adminNotification->modifyRole($id,$role_name,$file);
		if(!$flag){
			return Redirect::to('/admin/defaultError')->with('message','修改错误，请重试');
		}
		//跳转到指定路由
		return Redirect::to('/admin/adminRoleList');
	}
	
	/**
	 * 后台发送消息，通知
	 * @author:wang.hongli
	 * @since:2016/04/22
	 * @param action=>操作类型(1全部 2男 3女 4青少年用户 5认证用户 6联合会会员 7诵读比赛 ,诗文比赛,9培训班 ),
	 * @param type=>消息类型1评论 2转发 3赞 4收藏5收到私信6,被关注7系统消息
	 * @param competitionid=>比赛id
	 * @param uid=>用户id
	 * @param fromid 发送者id
	 * @param toid 接收人的id
	 * @param opusid 作品id
	 * @param name 暂时定位作品名称
	 * @param addtime 添加时间
	 * @param content 消息内容或者评论内容
	 * @param commentid 评论id
	 */
	public function adminSendNotifiaction(){
		//选出发送人
		$roles = DB::table('msg_role')->select('id','role_name')->get();
		//发送对象
		$to_users = array(1=>'全部',2=>'男',3=>'女',4=>'青少年用户',5=>'认证用户',6=>'联合会会员',7=>'诵读比赛 ,诗文比赛');
		$tmp_competition = DB::table('competitionlist')->where('isfinish',0)->orderBy('id','desc')->get(array('id','name'));
		$competition = array(0=>'选择');
		if(!empty($tmp_competition)){
			foreach($tmp_competition as $k=>$v){
				$competition[$v['id']] = $v['name'];
			}
		}
		if(!empty(Input::all())){
			$data = Input::all();
			$adminNotification = new AdminNotification();
			$flag = $adminNotification->adminSendNotifiaction($data);
			if($flag !== true){
				return Redirect::to('/admin/defaultError')->with('message',$flag);
			}
		}
		return View::make('adminnotification.sendnotification')->with('roles',$roles)->with('to_users',$to_users)->with('competition',$competition);
	}
}