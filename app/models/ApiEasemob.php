<?php
/*
* 环信接口操作类
* @author: 张宗亮
* @date: 2015-07-05
* @version: v1.0
*/
class ApiEasemob {
	private $client_id;
	private $client_secret;
	private $org_name;
	private $app_name;
	private $url;
	
	public static $config = array(
		'client_id'=>'YXA6UA2JAKagEeSo7YPSGHapug',
		'client_secret'=>'YXA6C1N6QpYYe-etRG7-uw0n4BkYViU',
		'org_name'=>'shoushan',
		'app_name'=>'weinidushi',
	);
	
	/**
	 * 初始化参数
	 *
	 * @param array $options   
	 * @param $options['client_id']    	
	 * @param $options['client_secret'] 
	 * @param $options['org_name']    	
	 * @param $options['app_name']   		
	 */
	public function __construct() {
		$options = self::$config;
		$this->client_id = isset ( $options ['client_id'] ) ? $options ['client_id'] : '';
		$this->client_secret = isset ( $options ['client_secret'] ) ? $options ['client_secret'] : '';
		$this->org_name = isset ( $options ['org_name'] ) ? $options ['org_name'] : '';
		$this->app_name = isset ( $options ['app_name'] ) ? $options ['app_name'] : '';
		if (! empty ( $this->org_name ) && ! empty ( $this->app_name )) {
			$this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
		}
	}
	
	/**
	 * 获取Token
	 */
	public function getToken() {
		//判断缓存是否为过期
		$file_path=base_path()."/app/config/token.txt";
		if(file_exists($file_path)){
			$content = file_get_contents($file_path);
			$data = unserialize($content);
			if($data['over_time']>time()){
				return $data['access_token'];
			}
		}
		$option ['grant_type'] = "client_credentials";
		$option ['client_id'] = $this->client_id;
		$option ['client_secret'] = $this->client_secret;
		$url = $this->url . "token";
		$result = $this->postCurl ( $url, $option, $head = 0 );
		$result = json_decode($result,true);
		$result['over_time'] = time()+$result['expires_in'];
		@file_put_contents($file_path,serialize($result));//存储到文件
		return $result['access_token'];
	}
	
	/*
	* 添加用户
	*/
	public function addUser($username,$password,$nickname=''){
		$url = $this->url."users";
		$token = $this->getToken();
		$header = array("Content-Type : application/json","Authorization: Bearer ".$token);
		$option = array('username'=>$username,'password'=>$password,'nickname'=>$nickname);
		return $this->postCurl($url,$option,$header,'POST');
	}
	
	/*
	* 删除用户
	*/
	public function delUser($username){
		$url = $this->url."users";
		$token = $this->getToken();
		$header = array("Content-Type : application/json","Authorization: Bearer ".$token);
		$option = array();
		return $this->postCurl($url,$option,$header,'DELETE');
	}
	/*
	* 用户信息
	*/
	public function getUser($username){
		$url = $this->url."users/".$username;
		$token = $this->getToken();
		$header = array("Authorization: Bearer ".$token);
		$option = array();
		return $this->postCurl($url,$option,$header,'GET');
	}
	
	//=================聊天室部分==========================================================
	
	/*
	* 添加聊天室
	*/
	public function addRoom($uid,$name,$description,$num=300){
		$url = $this->url."chatrooms";
		$token = $this->getToken();
		$header = array("Authorization: Bearer ".$token);
		$option = array('name'=>$name,'description'=>$description,'maxusers'=>$num,'owner'=>(string)$uid);
		return $this->postCurl($url,$option,$header,'POST');
	}
	
	/*
	* 修改聊天室
	*/
	public function updateRoom($id,$uid,$name,$description,$num=300){
		$url = $this->url."chatrooms/".$id;
		$token = $this->getToken();
		$header = array("Authorization: Bearer ".$token);
		$option = array('name'=>$name,'description'=>$description,'maxusers'=>$num);
		return $this->postCurl($url,$option,$header,'PUT');
	}
	
	/*
	* 聊天室-添加用户
	* $hx_id 聊天室id
	* $hx_uid 用户id
	*/
	public function addRoomUser($hx_id,$hx_uid){
		$url = $this->url."chatrooms/".$hx_id."/users/".$hx_uid;
		$token = $this->getToken();
		$header = array("Authorization: Bearer ".$token);
		$option = array();
		return $this->postCurl($url,$option,$header,'POST');
	}
	
	/*
	* 聊天室-删除用户
	* $hx_id 聊天室id
	* $hx_uid 用户id
	*/
	public function deleteRoomUser($hx_id,$hx_uid){
		$url = $this->url."chatrooms/".$hx_id."/users/".$hx_uid;
		$token = $this->getToken();
		$header = array("Authorization: Bearer ".$token);
		$option = array();
		return $this->postCurl($url,$option,$header,'DELETE');
	}
	//===============================================群组============================================
	/*群组添加
	*$name 		群组名  		 必填
	*$desc 		群组描述		必填
	*$public 	是否公开		必填
	*$maxusers 	人数上限		可选
	*$approval 	入群权限		加入公开群true须审核  false加入公开群不审核
	*$owner 	群管理员		必须
	*/
	public function addGroup($name,$desc,$punlic=true,$maxusers=300,$approval=false,$owner){
			$url = $this->url."chatgroups";
			$token = $this->getToken();
			$header = array("Authorization: Bearer ".$token);
			$option = array('groupname'=>$name,'desc'=>$desc,'public'=>$punlic,'maxusers'=>$maxusers,'approval'=>$approval,'owner'=>$owner);
			return $this->postCurl($url,$option,$header,'POST');
	}
	/*群组列表
 
	*/
	public function listGroup(){
			$url = $this->url."chatgroups";
			$token = $this->getToken();
			$header = array("Authorization: Bearer ".$token);
			$option = array();
			return $this->postCurl($url,$option,$header,'GET');
	}
	/*群组修改
	*$groupid   群组id		  必填
	*$name 		群组名  		 必填
	*$desc 		群组描述		必填	 
	*$maxusers 	人数上限		可选
	*/
	public function updateGroup($groupid,$name,$desc,$maxusers=300,$public=true){
	
			$url = $this->url."chatgroups/".$groupid;
			$token = $this->getToken();
			$header = array("Authorization: Bearer ".$token);
			$option = array('groupname'=>$name,'desc'=>$desc,'public'=>$public,'maxusers'=>$maxusers);
			return $this->postCurl($url,$option,$header,'PUT');
	}
	/*删除群组
	*/
	public function delGroup($groupid){
			$url = $this->url."chatgroups/".$groupid;
			$token = $this->getToken();
			$header = array("Authorization: Bearer ".$token);
			$option = array();
			return $this->postCurl($url,$option,$header,'DELETE');
	}

		/*群组删除
	*$groupid   群组id		  必填
	*/
	public function allGroup($groupid){
			$url = $this->url."chatgroups/".$groupid;
			$token = $this->getToken();
			$header = array("Authorization: Bearer ".$token);
			$option = array();
			return $this->postCurl($url,$option,$header,'DELETE');
	}
 	/**
     * 获取群组成员
     *
     * @param     $group_id
     */
    public function groupsUser($group_id) {
        $url = $this->url."chatgroups/".$group_id . "/users";
		$token = $this->getToken();
        $header = array("Authorization: Bearer ".$token);
		$option = array();
        $result = $this->postCurl( $url, $option, $header,  "GET" );

        return $result;
    }
    /**
     * 群组添加成员
     *
     * @param
     *          $group_id
     * @param
     *          $username
     */
    public function addGroupsUser($groupid,$username) {
		$url = $this->url."chatgroups/".$groupid."/users/".$username;
        $token = $this->getToken();
        $header = array("Authorization: Bearer ".$token);
		$option = array();
        $result = $this->postCurl($url,$option,$header,'POST');
        return $result;
    }
		 
    /**
     * 群组删除成员
     *
     * @param
     *          $group_id
     * @param
     *          $username
     */
    public function delGroupsUser($groupid,$username) {
        $url = $this->url."chatgroups/".$groupid."/users/".$username;
       	$token = $this->getToken(); 
        $header = array("Authorization: Bearer ".$token);
		$option = array();
        $result = $this->postCurl($url, $option , $header,  "DELETE" );
        return $result;
    }
 
	/*
		群组批量减人  $username=array() 
		 username格式  a,b,c,d
	*/
	function deleteGroupMembers($group_id,$usernames){
		$username=trme(join(",",$username),",");
		$url=$this->url.'chatgroups/'.(string)$group_id.'/users/'.$usernames;
		$token = $this->getToken();
		//$body=json_encode($usernames);
		$header = array("Authorization: Bearer ".$token);
		$result=$this->postCurl($url,'',$header,'DELETE');
		return $result;
	}
	/*
		/**
		* CURL Post
	 */
	public function postCurl($url, $option, $header = '', $type = 'POST') {
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		if (! empty ( $option )) {
			$options = json_encode ( $option );
			curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
		}
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
		if(!empty($header)){
			curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
		}
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $type );
		$result = curl_exec ( $curl ); // 执行操作
		//$res = object_array ( json_decode ( $result ) );
		//$res ['status'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		//pre ( $res );
		curl_close ( $curl ); // 关闭CURL会话
		return $result;
	}
	
	/*
	* 处理环信密码
	*/
	public static function pwdHash($id){
		return md5(md5($id)."pwd");
	}
	
	
	/*
	* 批量用户
	*/
	public function _piliang($option){
		$url = $this->url."users";
		$token = $this->getToken();
		$header = array("Content-Type : application/json","Authorization: Bearer ".$token);
		return $this->postCurl($url,$option,$header,'POST');
	}
	
	/*
	* 批量获得用户列表
	*/
	public function _userlist($where){
		/*$url = $this->url."users";
		$token = $this->getToken();
		$url = "https://a1.easemob.com/".$config['org_name']."/".$config['app_name']."/users".$where;
		$header = array("Authorization: Bearer ".$token);
		$option=array();
		return $this->postCurl($url,$option,$header,'GET');*/
	}
}
?>