<?php 
/**
* 活动聊天房间管理
**/
class RoomController extends BaseController {
  
  //房间列表
  public function RoomList() {
	  $data = array();
	  $pagesize = 20;
	  $conn = DB::table('competition_room')
			  ->select('competition_room.id','competition_room.c_id','competition_room.password','competition_room.content','competition_room.addtime','competition_room.closetime','competition_room.hx_id','competition_room.hx_name','competitionlist.name')
			  ->leftJoin('competitionlist','competitionlist.id','=','competition_room.c_id');
	  
	  $name= Input::get('name') ? Input::get('name') : '';
	  if(!empty($name)){
		  $conn = $conn->where('competitionlist.name','like','%'.$name.'%');
	  }
	  $data['name'] = $name;
	  $data['total']=$conn->count();
	  $conn = $conn->orderBy('competition_room.id','desc');
	  $roomlist = $conn->paginate($pagesize);
	  $data['roomlist'] = $roomlist;
	  //print_r($data);
	  return View::make('room.roomlist',$data);
  }
  
  //添加房间
  public function addRoom(){
	  $data=array();
	  if(!empty(Input::get('c_id')) && !empty(Input::get('password')) && !empty(Input::get('uid'))){
		  $arr=array();
		  $arr['c_id'] = !empty(Input::get('c_id'))?Input::get('c_id'):0;
		  $arr['hx_name'] = !empty(Input::get('hx_name'))?Input::get('hx_name'):'';
		  $arr['password'] = !empty(Input::get('password'))?Input::get('password'):'';
		  $arr['hx_uid'] = !empty(Input::get('uid'))?Input::get('uid'):'';
		  $arr['hx_num'] = !empty(Input::get('hx_num'))?Input::get('hx_num'):0;
		  $arr['addtime'] = time();
		  $arr['closetime'] = !empty(Input::get('closetime'))?strtotime(Input::get('closetime')):0;
		  $arr['updatetime'] = time();
		  $arr['content'] = !empty(Input::get('content'))?Input::get('content'):'';
		  
		  $apiroom = new ApiRoom;
		  $rlt = $apiroom->insertRoom($arr);
	  }
	  
	  //查询活动
	  $all_comp =array();
	  $sql="select id,name from competitionlist order by id desc";
	  $rlt=DB::select($sql);
	  foreach($rlt as $v){
		  $all_comp[$v['id']]=$v['name'];
	  }
	  $data['all_comp']=$all_comp;
	  //已经存在的聊天室
	  
	  
	  return View::make('room.addroom',$data);	
  }
  
  /*
  * 房间成员列表
  */
  public function roomUserList(){
		$data = array();
		$pagesize = 20;
		$hx_id = Input::get('hx_id');
		$conn = DB::table('competition_room_user')
			->select('competition_room_user.*','user.nick','user.gender')
			->leftJoin('user','competition_room_user.hx_uid','=','user.id');
		if(!empty($hx_id)){
			$conn=$conn->where('competition_room_user.hx_id','=',$hx_id);
		}
		$data['hx_id'] = $hx_id;
		$data['total']=$conn->count();
		$conn = $conn->orderBy('competition_room_user.addtime','desc');
		$data['list'] = $conn->paginate($pagesize);
		//获取单个房间信息
		$apiRoom=new ApiRoom;
		$data['all_room'] = ApiRoom::getAllRoom();
		//print_r($data);
		return View::make('room.roomuserlist',$data);
	  
  }
  
  


}

