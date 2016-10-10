<?php
use Illuminate\Support\Facades\Redirect;

/**
 * 后台通知相关管理
 * @author:wang.hongli
 * @since:2016/04/20
 */
class AdminNotification extends AdminCommon{
	
	private $filePath = '';
	private $send_msg = array();
	public function __construct(){
		parent::__construct();
		$this->filePath = 'upload/icon/';
		$this->send_msg  = array(
			'action'=>0,
			'type'=>7,
			'fromid'=>0,
			'toid'=>0,
			'opusid'=>0,
			'name'=>'',
			'addtime'=>time(),
			'content'=>'',
			'commentid'=>0,
			'competitionid'=>0,
		);
	}
	/**
	 * 后台发送通知角色列表
	 * @author:wang.hongli
	 * @since:2016/04/20
	 */
	public function adminRoleList(){
		$return = array();
		$rs = DB::table('msg_role')->select('id','role_name','sportrait')->get();
		if(!empty($rs)){
			foreach($rs as $k=>&$v){
				$v['sportrait'] = $this->poem_url.'/'.$v['sportrait'];
			}
			$return = $rs;
		}
		return $return;
	}
	/**
	 * 后台添加角色
	 * @author:wang.hongli
	 * @param  $data 表单提交过来的数据
	 */
	public function addRole($data){
		$rs['role_name'] = trim($data['role_name']);
		$file = $data['sportrait'];
		$ext = $file->guessExtension();
		$imgName = time().uniqid();
		$imgName = $imgName.'.'.$ext;
		$rs['sportrait'] = $this->filePath.$imgName;
		try {
			$file->move($this->filePath,$imgName);
			DB::table('msg_role')->insert($rs);
			return true;
		} catch (Exception $e) {
				return false;
		}		
	}
	
	/**
	 * 后台修改角色昵称，头像
	 * @author:wang.hongli
	 * @since:2016/04/20
	 */
	public function modifyRole($id,$role_name,$file){
		if(empty($id)) return false;
		$data = array();
		if(!empty($file)){
			$ext = $file->guessExtension();
			$imgName = time().uniqid();
			$imgName = $imgName.'.'.$ext;
			$data['sportrait'] = $this->filePath.$imgName;
			try {
				$file->move($this->filePath,$imgName);
			} catch (Exception $e) {
				return false;
			}
		}
		$data['role_name'] = $role_name;
		try {
			DB::table('msg_role')->where('id',$id)->update($data);
		} catch (Exception $e) {
			return false;
		}
		return true;
	}
	/**
	 * 后台发送消息通知
	 * @author:wang.hongli
	 * @since:2016/04/22
	 */
	public function  adminSendNotifiaction($data){
		ini_set('memory_limit',-1);
		//验证规则
		$rules = array(
				'role_name'=>'required|integer',
				'content'=>'required',
				'touser'=>'integer',
				'otheruser'=>'integer',
		);
		$validator = Validator::make($data, $rules);
		if($validator->fails()){
			return '参数错误，请检查参数是否正确';
		}
		if(empty($data['role_name'])){
			return '请选择发送人';
		}
		if(empty($data['touser']) && empty($data['otheruser'])){
			return '发送对象，用户id不能同时为空';
		}
		$data['content'] = serialize($data['content']);
		$otheruser = !empty($data['otheruser']) ? intval($data['otheruser']) : 0;
		$distributeMessage = new DistributeMessage();
		//指定用户id
		$this->send_msg['fromid'] = $data['role_name'];
		$this->send_msg['content'] = $data['content'];

		if(!empty($otheruser)){
			$this->send_msg['action'] = 0;
			$this->send_msg['toid'] = $otheruser;
		}elseif($data['touser'] == 7){
			if(empty($data['competitionid'])){
				return '请选择发送消息的比赛';
			}
			$this->send_msg['action'] = 7;
			$this->send_msg['toid'] = 7;
			$this->send_msg['competitionid'] = $data['competitionid'];
		}else{
			$this->send_msg['action'] = $data['touser'];
			$this->send_msg['toid'] = $data['touser'];
		}
		$distributeMessage->distriMessage($this->send_msg);
		return true;
	}
}
?>