<?php
/**
* 群组
*/
class  AdminGroupController   extends BaseController{
   	public $ApiEasemob;
    public $AdminGroup;
    public function __construct(){
        $this->AdminGroup = new AdminGroup();
        $this->ApiEasemob = new ApiEasemob();
    }
    /*
    * 添加群组
    *$name 		群组名  		 必填
    *$desc 		群组描述		必填
    *$public 	是否公开		必填
    *$maxusers 	人数上限		可选
    *$approval 	入群权限		加入公开群true须审核  false加入公开群不审核
    *$owner 	群管理员		必须
    */
    //添加房间
    public function addGroup(){
        $data=array();
        $arr['class_id']=Input::get('class_id');
        $arr['name']=Input::get('name');
        $arr['desc']=Input::get('desc');
        $arr['public']=true;
        $arr['maxusers']=Input::get('maxusers')?intval(Input::get('maxusers')):2000;
        $arr['approval']=false;
        $one=DB::table('class_group')->where('classid','=',$arr['class_id'])->first();
        if($one){
            echo "<script>alert('班级存在群组,请重新选择')</script>";
        }
        if(!empty(Input::get('class_id')) &&!empty(Input::get('name')) && !empty(Input::get('owner')) &&!empty(Input::get('desc')) ){
            //判断uid是否注册
            $arr['owner']=Input::get('owner');
            $userinfo=$this->ApiEasemob->getUser($arr['owner']);
            
            $user=json_decode($userinfo);
            if(isset($user->error)){
                
                $password=md5(md5($arr['owner'])).'pwd';
                $nick=DB::table('user')->where('id',	$arr['owner'])->first();
                $this->ApiEasemob->addUser($arr['owner'],$password,$nick['nick_name']);
            }
            $a=$this->AdminGroup->addGroup($arr);
            
            if(isset($a) && $a !='00'){
                
                $filePath = './upload/grouppic/';
                $file = Input::file('file');
                $lastFilePath = null;
                if(empty($file)) {
                    return Redirect::to('/admin/addGroup');
                } else {
                    $ext = $file->guessExtension();
                    $imgName = time().uniqid();
                    $imgName = $imgName.'.'.$ext;
                    $lastFilePath = $filePath.$imgName;
                    $file->move($filePath,$imgName);
                    $lastFilePath = ltrim($lastFilePath,'.');
                    $sql = "update class_group set pic = '{$lastFilePath}' where classid = {$arr['class_id']}";
                    DB::update($sql);
                }
                echo "<script>alert('添加成功')</script>";
            }else{
                echo "<script>alert('添加失败')</script>";
            }
            
        }
        
        $common=DB::table('class_active')->where('isdel',0)->where('pid',0)->get();
        $qwe=DB::table('class_active')->where('isdel',0)->where('pid','<>',0)->get();
        $college=array();
        foreach ($qwe as $key => $value) {
            $a=DB::table("college_list")->where('id',$value['pid'])->first();
            $b=DB::table("college_list")->where('id',$a['pid'])->first();
            $college[$b['name']][$a['name']]=$value;
        }
        //查询活动
        $all_comp =array();
        $sql="select id,name from class_active where isdel=0   order by id desc";
        $rlt=DB::select($sql);
        foreach($rlt as $v){
            $all_comp[$v['id']]=$v['name'];
        }
        $data['all_comp']=$all_comp;
        
        return View::make('group.addroom',$data)->with('common',$common)->with('college',$college);
    }
    
    //群组列表
    public function listGroup(){
        
        $name=Input::get('name')?trim(Input::get('name')):"";
        $info1=DB::table("class_group")->select('class_group.id',"class_group.groupname","class_group.groupid","class_group.groupinfo","class_group.addtime","user.nick",'class_active.sort')
        ->leftJoin('user', 'user.id', '=', 'class_group.admin')
        ->leftJoin('class_active', 'class_active.id', '=', 'class_group.classid');
        if ($name) {
            
            $info1->where('class_group.groupname','like',$name."%");
        }
        $info=$info1->orderBy("class_active.sort",'desc')->paginate(10);
        
        return View::make('group.listgroup')->with('info',$info)->with('name',$name);
    }
    //ajax修改排序
    public function changeSort(){ 
            $id=Input::get('id');
            $sort=Input::get('sort');
            if(!$id || !$sort){
                echo "error";
            }
            $a=DB::table('class_active')->where('id',$id)->update(array('sort'=>$sort));
            if($a){
                echo  true;
            }else{
                echo 'error';
            }
    }


    //修改信息
    public function changeGroup(){
        
        $id=Input::get('id');
        $group=DB::table('class_group')->where('id','=',$id)->first();
        return View::make('group.change')->with('group',$group)->with('id',$id);
    }
    //执行修改
    public function dochangeGroup(){
        $id=Input::get('id');
        $name=Input::get('name');
        $desc=Input::get('desc');
        $maxusers=Input::get('maxusers');
        $group=DB::table('class_group')->where('id','=',$id)->first();
        $easemob = new ApiEasemob;
        $rlt = $easemob-> updateGroup($group['groupid'],$name,$desc,$maxusers);
        $bb=json_decode($rlt,true);

        if(isset($bb['data']) && $bb['data']){
            $a=DB::table('class_group')->where('groupid','=',$group['groupid'])->update(
            array("groupname"=>$name,"groupinfo"=>$desc,"num"=>$maxusers));
            $filePath ='./upload/grouppic/';
            $file = Input::file('file');
            $lastFilePath = null;
            if(empty($file)) {
                return Redirect::to('/admin/addGroup');
            } else {
                $ext = $file->guessExtension();
                $imgName = time().uniqid();
                $imgName = $imgName.'.'.$ext;
                $lastFilePath = $filePath.$imgName;
                $file->move($filePath,$imgName);
                $lastFilePath = ltrim($lastFilePath,'.');
                $sql = "update class_group set pic = '{$lastFilePath}' where classid = {$arr['class_id']}";
                DB::update($sql);
            }
            
            
            return Redirect::to('/admin/listGroup');
        }
    }
    //删除
    public  function delGroup(){
        $id=Input::get('id');
        $group=DB::table('class_group')->where('id','=',$id)->first();
        $easemob = new ApiEasemob;
        $rlt=$easemob->delGroup($group['groupid']);
        $bb=json_decode($rlt,true);
    
        if(isset($bb['data']['success']) && $bb['data']['success']==1){
            $a=DB::table('class_group')->where('id','=',$id)->delete();
            //删除照片

        }
        return Redirect::to('/admin/listGroup');
        
    }
    //交费列表
    public function userGroup(){
        $class=DB::table('class_active')->where('isdel',0)->orderBy('id','desc')->get();
        $search=Input::get('class')?intval(Input::get('class')):0;
        if($search){
            $classinfo=DB::table("class_group")->where('classid','=',$search)->first();
            $good=DB::table('goods')->where('flag','=',1)->where('competition_id','=',$search)->first();
            if($good != ""){
                $order=DB::table('order_list')->where('goods_id','=',$good['id'])->where('status','=',2)->paginate(10);
                $userinfo=array();
                foreach ($order as $key => &$value) {
                    $user=DB::table('user')->where('id',$value['uid'])->first();
                    $userinfo[$key]['real_name']=$user['real_name']?$user['real_name']:"";
                    $userinfo[$key]['nick']=$user['nick'];
                    $userinfo[$key]['gender']=$user['gender']?'男':'女';
                    $userinfo[$key]['phone']=$user['phone'];
                    $flag=DB::table("class_group_user")->where('groupid','=',$classinfo['groupid'])->where('uid','=',$value['uid'])->first();
           
                    $userinfo[$key]['flag']=$flag?1:0;
                }
            }else{  
                $order="";
                $userinfo=array();
            }
            
            return View::make('group.userlist')->with('class',$class)->with('search',$search)->with('info',$order)->with('userinfo',$userinfo)->with('classinfo',$classinfo);
        }else{
            $info="";
            return View::make('group.userlist')->with('class',$class)->with('search',$search)->with('info',$info)->with('classinfo',"");
        }
        
    }
    //培训班老师列表
    public function teacherGroup(){
        $class=DB::table('class_active')->where('isdel',0)->orderBy('id','desc')->get();
        $search=Input::get('class')?intval(Input::get('class')):0;
      
        if($search){
            $pid=DB::table('class_active')->where('id',$search) ->first();
         
            $classinfo=DB::table("class_group")->where('classid','=',$search)->first();
            $uids=DB::table('class_teacher')->where('class_id',$pid['pid'])->lists("uid");
         
            if($uids){
                $user_list=DB::table('user')->whereIn('id',$uids)->get();
                foreach($user_list as $key =>$value){
                    $flag=DB::table('class_group_user')->where('groupid','=',$classinfo['groupid'])->where('uid',$value['id'])->first();
                    if($flag){
                        $user_list[$key]['flag']=1;
                    }else{
                        $user_list[$key]['flag']=0;
                    }
                }
          
            }else{
                $user_list="";
            }
          
          
            return View::make('group.teacherlist')->with('class',$class)->with('search',$search)->with('user_list',$user_list)->with('classinfo',$classinfo);
        }else{
            return View::make('group.teacherlist')->with('class',$class)->with('search',$search)->with('user_list',"")->with('classinfo',"");
        }
        
    }
    
    //添加or删除群成员
    public function addOrDelGroupUser(){
        $uid=Input::get('uid');
        $flag=Input::get('flag');
        $id=Input::get('groupid');
        $groupid=DB::table("class_group")->where('classid',$id)->first();
 
        //判断uid是否注册
        $userinfo=$this->ApiEasemob->getUser($uid);
        $user=json_decode($userinfo);
        if(isset($user->error)){
            $password=md5(md5($uid)).'pwd';
            $nick=DB::table('user')->where('id',$uid)->first();
            $this->ApiEasemob->addUser($uid,$password,"");
        }
        if($flag==1){
            $rlt=$this->ApiEasemob->addGroupsUser($groupid['groupid'],$uid);
        
            $bb=json_decode($rlt,true);
         

            if(isset($bb['data']['result']) && $bb['data']['result']==1){
                $group=DB::table('class_group')->where('groupid','=',$groupid['groupid'])->first();
                $a=DB::table("class_group_user")->insert(array('groupid'=>$groupid['groupid'],'uid'=>$uid,'ower'=>$group['admin'] ,"addtime"=>time()));
                if(!$a){
                    echo 'error';
                }else{
                    echo 1;
                }
                
            }else{
                echo 'error';
            }
        }
        if($flag==0){
            //删除
            $rlt=$this->ApiEasemob->delGroupsUser($groupid['groupid'],$uid);echo $rlt;
            $bb=json_decode($rlt,true);
            if(isset($bb['data']['result']) && $bb['data']['result']==1){
                $a=DB::table("class_group_user")->where('uid',"=",$uid)->where('groupid','=',$groupid['groupid'])->delete();
                if(!$a){
					echo 'error';
				}else{
					echo 1; 
				}
            }else{
                echo 'error';
            }
        }
        
    }


 
 
}
