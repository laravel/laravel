<?php 
/**
*	评委管理
*	@author:wang.hongli
*	@since:2015/05/09
**/
class JuryController extends BaseController
{
	/**
	*	获取评委列表
	*	@author:wang.hongli
	*	@since:2015/05/09
	**/
	public function juryList()
	{
		$pagesize = 20;
		//获取比赛列表
		$adminCompetition = new AdminCompetition();
		$jury_competition = $adminCompetition->getJueryCompetitionList();
		$search = [];
		$search['jury_competition'] = Input::has('jury_competition') ? intval(Input::get('jury_competition')) : 0;
		$conn = DB::table('jury');
		if(!empty($search['jury_competition'])){
			$conn->where('type',$search['jury_competition']);
		}
		$jurylist = $conn->select('id','uid','name','type','level','status','thumb','sort')->orderBy('id','desc')->paginate($pagesize);
		$return = array();
		$all_competition = [];
		if(!empty($jurylist))
		{
			//获取所有比赛列表
			$all_competition = new AdminCompetition();
			$all_competition = $adminCompetition->getCompetitionList(['isfinish'=>'all']);
			$uids = array();
			foreach($jurylist as $k=>$v)
			{
				$uids[] = $v['uid'];
			}
			if(empty($uids))
			{
				//列表为空
				return Redirect::to('admin/defaultError')->with('message', '请上传评委图片');
			}
			$tmp_users = DB::table('user')
					->select('id','nick','sportrait')->whereIn('id',$uids)
					->get();
			$user_info = array();
			if(!empty($tmp_users))
			{
				foreach($tmp_users as $k=>$v)
				{
					$user_info[$v['id']] = array('nick'=>$v['nick'],'sportrait'=>ltrim($v['sportrait'],'.'));
				}
			}
			//组合结果
			foreach($jurylist as $k=>$v)
			{
				$return[$v['id']] = $v;
				$return[$v['id']]['userInfo'] = isset($user_info[$v['uid']]) ? $user_info[$v['uid']] : array();
			}
		}
		return View::make('jury.jurylist')->with('jurylist',$return)->with('links',$jurylist)->with('jury_competition',$jury_competition)->with('search',$search)->with('all_competition',$all_competition);
	}

	/**
	*	添加评委
	*	@auhtor:wang.hongli
	*	@since:2015/05/09
	**/
	public function addJury()
	{
		
		$source = Input::file('thumb');
		$uid = Input::get('uid') ? Input::get('uid') : 0;
		
		$adminPicUpload = new AdminPicUpload;
		$time = date('Ymd',$adminPicUpload->getOTS());
		$filePath  = './upload/jurythumb/'.$time;
		//原图
		$src_file_name = time().uniqid();
		//获取所有未结束比赛列表
		$adminCompetition = new AdminCompetition();
		$not_finish_competitionlist = $adminCompetition->getCompetitionList(['isfinish'=>'all']);
		if(!empty(Input::get('name')))
		{
			//上传图片
			$url = $adminPicUpload->upload($source,$filePath,$src_file_name,'200_200');
			if(!$url){
				return Redirect::to('admin/defaultError')->with('message', '请上传评委图片'); 
			}
			$arr['uid'] = !empty(Input::get('uid')) ? Input::get('uid') : 0;
			$arr['name'] = Input::get('name');
			$arr['type'] = Input::get('type');
			$arr['level'] = Input::get('level');
			//判断是否在赛事中添加过此评委
			if(!empty($arr['uid'])){
				$id = DB::table('jury')->where('type',$arr['type'])->where('uid',$arr['uid'])->where('level',$arr['level'])->pluck('id');
				if(!empty($id)){
					return Redirect::to('admin/defaultError')->with('message', '此评委已在比赛中添加'); 
				}
			}
			$arr['sort'] = !empty(Input::get('sort')) ? Input::get('sort') : 1000;
			$arr['status'] = 2;
			$arr['thumb'] = ltrim($url,'.');
			DB::table('jury')->insert($arr);
		}
		else
		{
			//return Redirect::to('admin/defaultError')->with('message', '请上传评委图片'); 
		}
		
		return View::make('jury.addjury')->with('not_finish_competitionlist',$not_finish_competitionlist);

	}

	/**
	 * 修改评委排序
	 * @author :wang.hongli
	 * @since :2016/08/02
	 */
	public function modifyJurySort(){
		$id = Input::get('id');
		$old_sort = Input::get('old_sort');
		$sort = Input::get('sort');
		$type = Input::get('type');
		$level = Input::get('level');
		if(empty($id) || empty($old_sort) || empty($sort) || empty($type) || empty($level)){
			echo 0;
			return false;
		}
		//原来顺序大于当前顺序
		if($old_sort>$sort){
			DB::table('jury')->where('type',$type)->where('level',$level)->where('sort','>=',$sort)->where('sort','<',$old_sort)->increment('sort');
		}
		//原来顺序小于当前顺序
		if($old_sort<$sort){
			DB::table('jury')->where('type',$type)->where('level',$level)->where('sort','>',$old_sort)->where('sort','<',$sort)->decrement('sort');
		}
		DB::table('jury')->where('id',$id)->update(['sort'=>$sort]);
		echo 1;

	}

	/**
	*	删除评委
	*	@author:wang.hongli
	*	@since:2015/05/15
	**/
	public function delJury()
	{
		$id = Input::get('id');
		$status = Input::get('data_status');
		if(empty($id) || empty($status))
		{
			echo -1;
			return;
		}
		$adminJury = new AdminJury;
		if($adminJury->delJury($id,$status))
		{
			echo 1;
		}
		else
		{
			echo -1;
		}
	}
	
	//活动观众报名列表
	public function audienceList(){
		$a_id=isset($_GET['a_id'])?(int)$_GET['a_id']:0;
		$pagesize = 20;
		$list = DB::table('user_audience')->select('*');
		if($a_id>0){
			$list = $list->where('a_id','=',$a_id);
		}
		$total = $list->count();
		$list = $list->orderBy('id','desc')->paginate($pagesize);
		
		//所有活动
		
		$all=array();
		$sql="select * from activities";
		$rlt = DB::select($sql);
		foreach($rlt as $v){
			$all[$v['id']]=$v['name'];
		}
		//所有用户
		$users=array();
		if($total>0){
			$uids=array();
			foreach($list as $v){
				$uids[$v['uid']]=$v['uid'];
			}
			$sql="select id,nick,gender from  user where id in ('".implode("','",$uids)."')";
			$rlt = DB::select($sql);
			foreach($rlt as $v){
				$users[$v['id']]=$v;
			}
		}
		
		return View::make('jury.audiencelist')->with('list',$list)->with('total',$total)->with('all',$all)->with('users',$users)->with('a_id',$a_id);
	}
	
	//线下活动列表
	public function activitiesList(){
		$pagesize = 20;
		$list = DB::table('activities')->select('*');
		//$list = $list->where('status','=',2)
		$total = $list->count();
		$list = $list->orderBy('id','desc')->paginate($pagesize);
		
		return View::make('jury.activitieslist')->with('list',$list)->with('total',$total);
	}
	
	//添加线下活动
	public function addActivities(){
		$name=!empty(Input::get('name'))?Input::get('name'):"";
		if(!empty($name)){
			$sql="insert into activities (name,addtime) values ('".$name."','".time()."')";
			DB::insert($sql);
			echo 1;
		}else{
			echo 0;
		}
	}
	
	//参赛码列表
	public function inviteCodeList(){
		$pagesize = 20;
		$list = DB::table('invitecode')->select('*');
		$list->where('status','=',2);
		$total = $list->count();
		$list = $list->orderBy('id','desc')->paginate($pagesize);
		return View::make('jury.invitecodelist')->with('list',$list)->with('total',$total);
	}
	
	//参赛码添加
	public function addInviteCode(){
		
		if(!empty(Input::get('code'))){
			$arr=array();
			$arr['name'] = Input::get('name');
			$arr['code'] = Input::get('code');
			$arr['mobile'] = Input::get('mobile');
			$arr['address'] = Input::get('address');
			$arr['addtime'] = time();
			$arr['status'] = 2;
			DB::table('invitecode')->insert($arr);
		}
		return View::make('jury.addinvitecode');	
	}
	
	//删除参赛码
	public function delInviteCode(){
		$id=(int)Input::get('id');
		$sql="update invitecode set status=0 where id=".$id;
		$rlt=DB::update($sql);
		echo $rlt?1:0;
	}
	
	
	

}
 ?>