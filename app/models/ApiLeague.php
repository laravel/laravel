<?php 
/**
*	中华朗诵会
*	@author:zhang.zongliang
*	@since:2015/05/12
**/
class ApiLeague extends ApiCommon
{
	protected $table = 'league';
	
	//测试覆盖
	/*public function viaCookieLogin(){
		return array('id'=>11,'nick'=>'张宗');
	}*/
	
	/**
	*	获取下过表单信息
	**/
	public function getInfoByUid()
	{
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return array('code'=>-100,'请登录');
		
		$uid = $info['id'];
		$info = DB::table('league')
				->where('uid','=',$uid)
				->first(array('id','uid','card','name','nick_name','company','address','zip','mobile','email','cause','province_id','city_id','area_id'));
		if($info) 
		{
			return array('code'=>1,'data'=>$info);
		}
		return array('code'=>-1,'msg'=>'操作失败');
	}
	
	/**
	*	添加数据
	**/
	public function add($arr)
	{
		$id = DB::table('league')->insertGetId($arr);
		if($id>0) 
		{
			return $id;
		}
		return 0;
	}
	
	/*
	* 更新数据
	*/
	public function upInfo($uid,$arr){
		if(!is_array($arr) || empty($arr) || empty($uid)){
			return 0;
		}
		
		$tmp=array();
		foreach($arr as $k=>$v){
			$tmp[]=$k."='".$v."'";
		}
		
		$sql = "update league set ".implode(",",$tmp)." where uid = '".$uid."'";
		if(DB::update($sql)) {
			return 1;
		}else{
			return 0;
		}
	}
	
	
	/*
	* 以用户uid为主键，判断是更新还是添加
	*/
	public function updateInfo($arr){
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return array('code'=>-100,'msg'=>'请登录');
		$uid = $info['id'];
		$data = DB::table('league')->where('uid','=',$uid)->first();
		$id=0;
		if(!empty($data)){
			//存在就更新
			unset($arr['addtime']);
			unset($arr['status']);
			$id = $data["id"];
			$this->upInfo($data['uid'],$arr);
		}else{
			//不存在就添加
			$arr['uid'] = $info['id'];
			$arr['nick_name'] = $info['nick'];
			$id = $this->add($arr);
		}
		
		if($id>0){
			return array('code'=>1,'id'=>$id,'msg'=>'提交成功');
		}else{
			return array('code'=>0,'msg'=>'操作失败');
		}
		
	}
	
	
	//===================================================================
	
	
	
	
	
	/*
	* 获取整个列表
	* $where 条件
	* return array
	*/
	public function getList($where,$page=1,$page_size=10,$order=''){
		$where_str='';
		
		$order=' order by addtime desc';
		if(!empty($order)){
			$order=' order by '.$order.' desc';
		}
		
		$limit='';
		if($page>0 && $page_size>0){
			$limit=' limit '.(($page-1)*$page_size).','.$page_size;
		}
		
		$sql = "select * from league where 1 ".$where_str." ".$order." ".$limit;
		
		$rs = DB::select($sql);
	}
	
	/*
	* 统计数
	*/
	public function getCount($where){
		/*$where_str='';
		$sql = "select count(*) as num from league where 1 ".$where_str;
		$rlt = DB::table('league')->where('id', $id)->first();
		return $rlt['num'];*/
	}
	
	/*
	* 获取单个信息
	*/
	public function getInfoById($id){
		return DB::table('league')->where('id', $id)->first();
	}
	
	/*
	* 测试
	*/
	public function  test(){
		$sql = "select * from league where id=1";
		$rs = DB::select($sql);
		$rs=DB::table('league')->where('id','=','1')->first();
		return $rs;
	}
	/*
	 * 获取有效的通过的会员列表
	 * @author:wang.hongli
	 * @since:2016/05/22
	 */
	public function getLeague($where,$page=1,$page_size=10){
		$info = $this->viaCookieLogin();
		if(empty($info)){
			return 'nolog';
		}
		$uid = $info['id'];
		$page = intval($page);
		$page_size = intval($page_size);
	
		$offSet = ($page-1)*$page_size;
		$page_size++;
		// 		排序规则 praisenum desc,lnum desc,repostnum desc
		$tmp_user_id = DB::table('league_user')->orderBy('praisenum','desc')->orderBy('lnum','desc')->orderBy('repostnum','desc')->skip($offSet)->take($page_size)->lists('uid');
		$tmp_user_id_string = implode(',', $tmp_user_id);
		$rs = DB::table('user')
		->select('id','nick','phone','gender','lnum','repostnum',
				'attention','praisenum','fans','opusnum','grade','sportrait',
				'portrait','albums','signature','authtype','opusname','isedit','issex','teenager','addtime','bgpic','isleague')
		->whereIn('id',$tmp_user_id)->orderByRaw(DB::raw("FIELD(id,$tmp_user_id_string)"))->get();
		
		if(empty($rs)){
			return  array('list'=>array(),'hasmore'=>0);
		}
		//获取关注人的id
		$atten_arr = self::myAttenUser($uid, $tmp_user_id);
		foreach($rs as $k=>&$v){
			$v['portrait'] = !empty($v['portrait']) ?  $this->poem_url.ltrim($v['portrait'],'.') : '';
			$v['sportrait'] = !empty($v['sportrait']) ? $this->poem_url.ltrim($v['sportrait'],'.') : '';
			$v['bgpic'] = !empty($v['bgpic']) ? $this->poem_url.ltrim($v['bgpic']) : '' ;
			//判断关注状态
			$v['relation'] = in_array($v['id'], $atten_arr) ? 1:0; //关注状态0陌生人，1我->他 2，他->我 3->相互
		}
		$hasmore = 0;
		if(count($rs) == $page_size){
			$hasmore = 1;
			array_pop($rs);
		}
		return array('list'=>$rs,'hasmore'=>$hasmore);
	}
}