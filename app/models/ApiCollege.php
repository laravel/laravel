<?php
/**
** 诗学院颂学院相关
*/
class ApiCollege extends ApiCommon {
    protected $poem_url = '';
    public function __construct(){
        $this->poem_url = Config::get('app.poem_url');
    }
    //得到学院
    public function getCollege(){
        $info = $this->viaCookieLogin();
       
        if(empty($info['id'])) return'nolog';
        $list=DB::table('college_list')->where('pid',0)->where('isdel',0)->get();
        if($list){
            return $list;
        }else{
            return false;
        }
    }
    //得到学院下的年级   诗学院颂学院id
    public function getGrade(){
        $info = $this->viaCookieLogin();
 
        if(empty($info['id'])) return'nolog';
        $pid=Input::get('pid',0);
        if($pid==0)return "error";
        $list=DB::table('college_list')->where('pid',$pid)->get();
        if($list){
            return $list;
        }else{
            return false;
        }
    }
    //得到学院下的老师  诗学院颂学院id
    public function getGradeTeacher(){
        $info = $this->viaCookieLogin();
        if(empty($info['id'])) return'nolog';
        $pid=Input::get('pid',0);
        if($pid==0)return "error";
        $count = Input::has('count') ? Input::get('count'): 20;
        $pageIndex =Input::has('pageIndex')?Input::get('pageIndex'):1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $list=DB::table('class_teacher')->where('class_pid',$pid)->skip($offSet)->take($count)->get();
        if($list){
            //和user联查
            foreach ($list as $key => $value) {
                $user=DB::table('user')->where("id",$value['uid'])->first();
                $list[$key]['real_name']=$user['real_name'];//真实姓名
                $list[$key]['nick']=$user['nick'];//昵称
                $list[$key]['gender']=$user['gender'];//性别
                $list[$key]['grade']=$user['grade'];//等级
                $list[$key]['sportrait']=$this->poem_url.trim($user['sportrait']);//小头像
                $list[$key]['authtype']=$user['authtype'];
                $list[$key]['isleague']=$user['isleague'];//时候是会员
            }
            $num = count ( $list );
            if ($num >= $count) {
                array_pop ( $list );
                $list['hasmore'] = 1;
            } else {
                $list['hasmore'] = 0;
            }
            return $list;
        }else{
            return false;
        }
    }
    //得到班级列表 classid   年级id
    public function getClass(){
        $info = $this->viaCookieLogin();
     
        if(empty($info['id'])) return'nolog';
        $classid=Input::get('classid',0);
        if($classid==0)return 'error';
        $count = Input::has('count') ? Input::get('count'): 20 ;
        $pageIndex =Input::has('pageIndex')?Input::get('pageIndex'):1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $list=DB::table('class_active')->where('pid',$classid)->where('endtime','>=',time())->where('starttime','<=',time())->orderBy('sort','desc')->skip($offSet)->take($count)->get();
        if($list){
            foreach ($list as $key => $value) {
                $pic=[];
                foreach(unserialize($list[$key]['piclist']) as $k=>$v){
                    $pic[$k]=$this->poem_url.$v;
                }
                $list[$key]['piclist']=$pic;
                $list[$key]['mainpic']=$this->poem_url.$value['mainpic'];
                $group=DB::table("class_group" )->where('classid',$value['id'])->first();
                $good=DB::table("goods")->where('flag',1)->where('competition_id',$value['id'])->first();
                $order=DB::table("order_list")->where('uid',$info['id'])->where('goods_id',$good['id'])->where('status',2)->first();
                if($group['admin']==$info['id']){
                    $list[$key]['flag']=1;
                    $list[$key]['goupid']=$group['groupid'];
                }else{
                    if($group){
                        $list[$key]['goupid']=$group['groupid'];
                        $user=DB::table('class_group_user')->where('groupid',$group['groupid'])->where('uid',$info['id'])->first();
                        if($user){
                            $list[$key]['flag']=1;
                        }else if(!$user && $order)
                        $list[$key]['flag']=2;
                        else{
                            $list[$key]['flag']=0;
                        }
                    }else{
                        $list[$key]['flag']=0;
                        $list[$key]['goupid']="";
                    }
                }
            }
            $num = count ( $list );
            if ($num >= $count) {
                array_pop ( $list );
                $list['hasmore'] = 1;
            } else {
                $list['hasmore'] = 0;
            }
            
            return $list;
        }else{
            return false;
        }
    }
    //得到班级老师  classid   年级id
    public function getClassTeacher(){
        $info = $this->viaCookieLogin();
        if(empty($info['id'])) return'nolog';
        $classid=Input::get('classid',0);
        if($classid==0)return 'error';
        $list=DB::table('class_teacher')->where('class_id',$classid)->get();
        if($list){
            //和user联查
            foreach ($list as $key => $value) {
                $user=DB::table('user')->where("id",$value['uid'])->first();
                $list[$key]['real_name']=$user['real_name'];//真实姓名
                $list[$key]['nick']=$user['nick'];//昵称
                $list[$key]['gender']=$user['gender'];//性别
                $list[$key]['grade']=$user['grade'];//等级
                $list[$key]['sportrait']=$this->poem_url.trim($user['sportrait'],'.');//小头像
                $list[$key]['authtype']=$user['authtype'];
                $list[$key]['isleague']=$user['isleague'];//会员
            }
            return $list;
        }else{
            return  false;
        }
    }
      //得到群组全部成员  groupid 环信groupid
    public function getAlluser($id,$groupid){
        $list=DB::table('class_group_user')->where('groupid','=',$groupid)->get();
        $uids=array();
        foreach($list as $k => $v){
            $uids[]=$v['uid'];
        }
        if($list){
            array_push($uids,$list[0]['ower']);
        }
        
        $user_members=DB::table('user_members')->where('endtime',">=",time())->whereIn('uid',$uids)->lists('uid');
        
        $user_list=DB::table('user')->select('id','nick',"sportrait",'portrait','authtype','gender','teenager','isleague')->whereIn('id',$uids)->get();
        foreach($user_list as $k => $v){
            $user_list[$k]['ismember']=in_array($v['id'],$user_members)?1:0;
            $user_list[$k]['sportrait']=  $this->poem_url. $v['sportrait'];
            $user_list[$k]['portrait']= $this->poem_url.$v['portrait'];

        }
        return $user_list;
    }
    //得到班级信息
    public function getClassInfo($groupid,$classid){
        if($groupid != 0){
            $group=DB::table('class_group')->where('groupid',$groupid)->first();          
        }else{
            $group=DB::table('class_group')->where('classid',$classid)->first();
        }
        
        if($group){
            $list= DB::table('class_active')->where('id',$group['classid'])->first();
            $list['gropuname']=$group['groupname'];
            $list['grouppic']=$group['pic'];
            unset( $list['piclist']);
            return $list;
        }else{
            return "";
        }
       
    }
}