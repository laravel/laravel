<?php

/**
* 后台班级活动管理
* @author:wang.hongli
* @since:2016/05/26
*/
class AdminClassActiveController extends BaseController{
    
    private $goods_category = array();
    private $url;
    private $down_list;
    function __construct(){
        $this->goods_category = array(
        0=>'比赛商品',
        1=>'班级活动商品'
        );
        $this->url = Config::get('app.url');
    }
    /**
    * 班级活动列表
    * @author:wang.hongli
    * @since:2016/05/31
    */
    public function classActiveList(){
        $data=Input::all();
        
        
        if(Request::method()=='POST' && $data['isdel']==1){
            $college=$data['college'];
            $list1 = DB::table('class_active')->where('isdel',1);
            $type=1;
        }else{
            $college=0;
            $list1 = DB::table('class_active')->where('isdel',0);
            $type=2;
        }
        if(Request::method()=='POST' && $data['college']==='-1'){
            $list1->orderBy('id','desc')->orderBy('sort','desc');
        }else{
            $college=isset($data['college'])?$data['college']:0;
            $list1->where('pid',$college)->orderBy('sort','desc')->orderBy('id','desc');
        }
        $list=$list1->paginate(20);
        $pid=DB::table('college_list')->where('pid',0)->get();
        if($pid){
            foreach ($pid as $key => $value) {
                $coll =DB::table('college_list')->where('pid',$value['id'])->get();
                $option[$value['name']]= $coll;
            }
        }
        $pid_name=[];
        if(!empty($list)){
            foreach($list as $k=>&$v){
                
                if($v['pid']==0){
                    $pid_name[$k]['pid_name']="普通班级";
                }else{
                    $a=DB::table('college_list')->where("id",$v['pid'])->first();
                    $b=DB::table('college_list')->where("id",$a['pid'])->first();
                    $pid_name[$k]['pid_name']=$b['name']."->".$a['name'];
                }
                if(!empty($v['mainpic'])){
                    $pid_name[$k]['mainpic'] = $this->url.'/'.$v['mainpic'];
                }
            }
        }
        return View::make('adminclassactive.classactivelist')->with('pid_name',$pid_name)->with('list',$list)->with('college',$college)->with('type',$type)->with('option',$option);
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
    * 添加学院年级
    * @author:hgz
    * @since:2016/08/04
    */
    public function addColloegeActive(){
        $list=DB::table('college_list')->where('pid','=',0)->get();
        if(Input::all()){
            if (!Input::has('name') ) {
                echo "<script>alert('请输入名称')</script>";
            }else{
                $name=Input::get('name');
                $pid=Input::get('pid');
                $desc=Input::get('desc');
                DB::table('college_list')->insert(array('pid'=>$pid,"name"=>$name,'desc'=>$desc));
                return Redirect::to('/admin/listColloegeActive');
            }
        }
        return View::make('adminclassactive.addcolloege')->with('list',$list);
    }
    /**
    * 学院年级列表
    * @author:hgz
    * @since:2016/08/04
    */
    public function listColloegeActive(){
        $list=DB::table('college_list')->where('pid','=',0)->get();
        if(!Input::all()){
            $id='';
            $college="";
        }else{
            $id=Input::get("id")?Input::get("id"):0;
            $college=DB::table('college_list')->where('pid','=',$id)->get();
        }
        return View::make('adminclassactive.listcolloege')->with('list',$list)->with('id',$id)->with('college',$college);
    }
    /**
    * 删除学院年级
    * @author:hgz
    * @since:2016/08/04
    */
    public function delColloegeActive(){
        $flag=Input::get('flag');
        $id=Input::get('id');
        if ($flag==1) {
            $a=DB::table('college_list')->where('id','=',$id)->update(array('isdel'=>1));
            if($a){
                echo "true";
            }else{
                echo "false";
            }
        }else{
            $a=DB::table('college_list')->where('id','=',$id)->update(array('isdel'=>0));
            if($a){
                echo "true";
            }else{
                echo "false";
            }
        }
    }
    /**
    * 修改学院年级
    * @author:hgz
    * @since:2016/08/04
    */
    public function changeColloegeActive(){
        if(Input::has('name') && Input::has('desc')){
            $id=Input::get("id");
            $name=Input::get("name");
            $desc=Input::get("desc");
            $a=DB::table('college_list')->where('id','=',$id)->update(array('name'=>$name,"desc"=>$desc));
            if($a){
                echo "<script>alert('修改成功')</script>";
                return Redirect::to('/admin/listColloegeActive');
            }else{
                echo "<script>alert('修改失败')</script>";
                return Redirect::to('/admin/listColloegeActive?id='.$id);
            }
        }else{
            $id=Input::get("id");
            $college=DB::table('college_list')->where('id','=',$id)->first();
        }
        return View::make('adminclassactive.changecolloege')->with('id',$id)->with('college',$college);
    }
    /**
    * 学院年级or班级老师
    * @author:hgz
    * @since:2016/08/05
    */
    public function teacherActive(){
        $list=DB::table('college_list')->where('pid','=',0)->get();
        $id="";
        $teacher_list="";
        $search['uid']="";
        $search['nick']="";
        $search['real_name']="";
        $user="";
        $find_user="";
        if(Input::has('pid')){
            $id=Input::get('pid');
            $teacher_list=DB::table('class_teacher')->where('class_pid',$id)->where('class_id',0)->orderBy('addtime','desc')->paginate(10);
            foreach ($teacher_list as $key => &$value) {
                $user[$key]=DB::table('user')->where('id',$value['uid'])->first();
            }
        }
        if(Input::has('uid')  ||  Input::has('nick') ||Input::has('real_name')){
            if(!Input::has('pid'))echo '<script>alert("请选择学院")</script>';
            $search['uid']=Input::get('uid');
            $search['nick']=Input::get('nick');
            $search['real_name']=Input::get('real_name');
            $uids=array();
            if($search['uid']){
                array_push($uids,$search['uid']);
            }
            if($search['nick']){
                $a=DB::table('user')->where('nick',$search['nick'])->first();
                array_push($uids,$a['id']);
            }
            if($search['real_name']){
                $b=DB::table('user')->where('real_name',$search['real_name'])->first();
                array_push($uids,$b['id']);
            }
            if($uids){
                $find_user=DB::table("user")->whereIn('id',$uids)->get();
            }
        }
        return View::make('adminclassactive.teachercolloege')->with('url',$this->url)->with('find_user',$find_user)->with('search',$search)->with('list',$list)->with('id',$id)->with('user',$user)->with('teacher_list',$teacher_list);
    }
    
    /**
    * 添加老师 ajax
    * @author:hgz
    * @since:2016/08/05
    */
    public function addteacherActive(){
        $id=Input::get('id');
        $class_id=Input::get('class_id');
        $a=DB::table('class_teacher')->where('uid',$id)->where('class_pid',$class_id)->first();
        if($a){
            echo '用户已经添加';
        }else{
            $b=DB::table('class_teacher')->insert(array('uid'=>$id,'class_pid'=>$class_id ,'addtime'=>time()  ));
            if($b){
                echo "true";
            }else{
                echo '添加失败';
            }
        }
    }
    
    /**
    * 移除老师 ajax
    * @author:hgz
    * @since:2016/08/05
    */
    public function delteacherActive(){
        $id=Input::get('id');
        $class_id=Input::get('class_id');
        $classid=DB::table('class_teacher')->where('uid',$id)->where('class_pid',$class_id)->groupBy('class_id')->get();
        
        $a=DB::table('class_teacher')->where('uid',$id)->where('class_pid',$class_id)->delete();
        
        if($a){
            $cids=array();
            foreach($classid as $k=>$v){
                if($v['class_id']!=0){
                    $cids[]=$v['class_id'];
                }
            }
            $group=DB::table('class_group')->whereIn('classid',$cids)->get();
            $hx=new ApiEasemob();
            foreach($group as $key=>$value){
                $hx->delGroupsUser($value['groupid'],$id);
                DB::table('class_group_user')->where('groupid',$value['groupid'])->where('uid',$id)->delete();
            }
            echo "用户删除成功";
        }else{
            echo '用户删除失败';
        }
    }
    /**
    * 添加班级老师
    * @author:hgz
    * @since:2016/08/05
    */
    public function addclassteacherActive(){
        $pid=DB::table('college_list')->where('pid',0)->get();
        if($pid){
            foreach ($pid as $key => $value) {
                $college=DB::table('college_list')->where('pid',$value['id'])->get();
                $option[$value['name']]= $college;
            }
            $id=Input::get('id');
            $now_pid=DB::table('college_list')->where('id',$id)->first();
            $list=DB::table('class_teacher')->where("class_pid",$now_pid['pid'])->groupBy('uid')->orderBy('addtime','desc')->paginate(10);
            $user=array();
            foreach ($list as $key => $value) {
                $user[$key]=DB::table('user')->where('id',$value['uid'])->first();
                $a=DB::table('class_teacher')->where('class_id',$id)->where('class_pid',$now_pid['pid'])->where('uid',$user[$key]['id'])->first();
                if($a){
                    $user[$key]['flag']='1';
                }else{
                    $user[$key]['flag']='0';
                }
            }
            return View::make('adminclassactive.classteacher')->with('option',$option)->with('url',$this->url)->with('id',$id)->with('user',$user)->with('list',$list);
        }
        return View::make('adminclassactive.classteacher')->with('option',"")->with('url',$this->url)->with('id',"");
    }
    public function doaddteacher(){
        $flag=Input::get('flag');
        $uid=Input::get('id');
        $class_id=Input::get('class_id');
        if($flag){
            $class_pid=DB::table('college_list')->where('id',$class_id)->first();
            $a=DB::table('class_teacher')->insert(array('uid'=>$uid,"class_id"=>$class_id,"class_pid"=>$class_pid['pid'],'addtime'=>time()));
            if($a){
                echo "true";
            }else{
                echo "添加失败";
            }
        }else{
            $b=DB::table('class_teacher')->where('uid',$uid)->where('class_id',$class_id)->delete();
            if($b){
                $group=DB::table('class_group')->where('classid',$class_id)->get();
                $hx=new ApiEasemob();
                foreach($group as $key=>$value){
                    $hx->delGroupsUser($value['groupid'],$uid);
                    DB::table('class_group_user')->where('groupid',$value['groupid'])->where('uid',$uid)->delete();
                }
                echo "true";
            }else{
                echo "移除失败";
            }
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
    * 添加班级活动
    * @author:wang.hongli
    * @since:2016/05/26
    */
    public function addClassActive(){
        $rules = array(
        'name'=>'required',
        'desc'=>'required',
        'sort'=>'required|integer',
        'piclist0'=>'image',
        
        'piclist5'=>'image',
        
        'piclist2'=>'image',
        'piclist3'=>'image',
        'piclist4'=>'image',
        'starttime'=>'required|date',
        'endtime'=>'required|date'
        );
        $message = array(
        'name.required'=>'请填写活动名称',
        'desc.required'=>'请填写描述信息',
        'sort.required'=>'请填写排序',
        'sort.integer'=>'排序格式错误',
        'piclist0.image'=>'图片格式错误',
        'piclist5.image'=>'图片格式错误',
        'piclist1.image'=>'图片格式错误',
        'piclist2.image'=>'图片格式错误',
        'piclist3.image'=>'图片格式错误',
        'piclist4.image'=>'图片格式错误',
        'starttime.required'=>'填写开始时间',
        'starttime.date'=>'时间格式错误',
        'endtime.required'=>'填写结束时间',
        'endtime.date'=>'结束时间格式错误'
        );
        if(Request::method()=='POST'){
            $data = Input::all();
            
            unset($data['_token']);
            //验证
            $validator = Validator::make($data, $rules,$message);
            if($validator->fails()){
                $msg =  $validator->messages()->first();
                return Redirect::to('/admin/defaultError')->with('message',$msg);
            }
            $filePath = './upload/classactive/';
            $mainpic = '';
            $piclist = '';
            $piclist = array();
            for($i=0;$i<=5;$i++){
                $name = 'piclist'.$i;
                if(!empty($data[$name])){
                    //图片上传
                    $ext = $data[$name]->guessExtension();
                    $imgName = time().uniqid();
                    $imgName = $imgName.'.'.$ext;
                    $lastFilePath = trim($filePath.$imgName,".");
                    $data[$name]->move($filePath,$imgName);
                    if($i==0)
                    {
                        $mainpic = $lastFilePath;
                        
                    }
                    else  if($i==5)
                    {
                        $smallpic = $lastFilePath;
                        
                    }
                    else
                    {
                        $piclist[$name] = $lastFilePath;
                    }
                }
            }
            $price=$data['price'];
            unset($data['price'],$data['piclist0'],$data['piclist1'],$data['piclist2'],$data['piclist3'],$data['piclist4'],$data['piclist5']);
            $data['starttime'] = strtotime($data['starttime']);
            $data['endtime'] = strtotime($data['endtime'])+86399;
            $data['mainpic'] = $mainpic;
            $data['smallpic'] = $smallpic;
            $data['piclist'] = serialize($piclist);
            $id=DB::table('class_active')->insertGetId($data);
            if($price){
                DB::table('goods')->insert(array('good_pid'=>0,'name'=>$data["name"],'price'=>$price,'type'=>4,'description'=>$data['name'],
                'competition_id'=>$id,'flag'=>1,'start_time'=>$data['starttime'],'end_time'=>$data['endtime'],'discount_price'=>0,'sort'=>0,'isdel'=>0
                
                ));
            }
        }
        $parr=DB::table('college_list')->where('pid',0)->where('isdel',0)->get();
        $arr=array();
        foreach ($parr as $key => $value) {
            $arr[$value['name']]=DB::table('college_list')->where('pid',$value['id'])->where('isdel',0)->get();
        }
        return View::make('adminclassactive.addclassactive')->with("college",$arr);
    }
    /**
    * 修改班级活动
    * @author:hgz
    * @since:2016/06/29
    */
    public function changeClassActive($id){
        $list = DB::table('class_active')->where('id',$id)->get();
        $list[0]['piclist']=unserialize($list[0]['piclist']);
        $pic[0]=isset($list[0]['mainpic'])?$list[0]['mainpic']:"";
        $pic[1]=isset($list[0]['piclist']['piclist1'])?$list[0]['piclist']['piclist1']:"";
        $pic[2]=isset($list[0]['piclist']['piclist2'])?$list[0]['piclist']['piclist2']:"";
        $pic[3]=isset($list[0]['piclist']['piclist3'])?$list[0]['piclist']['piclist3']:"";
        $pic[4]=isset($list[0]['piclist']['piclist4'])?$list[0]['piclist']['piclist4']:"";
        $pic[5]=isset($list[0]['smallpic'])?$list[0]['smallpic']:"";
        
        $rules = array(
        'name'=>'required',
        'desc'=>'required',
        'sort'=>'required|integer',
        'piclist0'=>'image',
        'piclist1'=>'image',
        'piclist2'=>'image',
        'piclist3'=>'image',
        'piclist4'=>'image',
        'piclist5'=>'image',
        'starttime'=>'required|date',
        'endtime'=>'required|date'
        );
        $message = array(
        'name.required'=>'请填写活动名称',
        'desc.required'=>'请填写描述信息',
        'sort.required'=>'请填写排序',
        'sort.integer'=>'排序格式错误',
        'piclist0.image'=>'图片格式错误',
        'piclist1.image'=>'图片格式错误',
        'piclist2.image'=>'图片格式错误',
        'piclist3.image'=>'图片格式错误',
        'piclist4.image'=>'图片格式错误',
        'piclist5.image'=>'图片格式错误',
        'starttime.required'=>'填写开始时间',
        'starttime.date'=>'时间格式错误',
        'endtime.required'=>'填写结束时间',
        'endtime.date'=>'结束时间格式错误'
        );
        if(Request::method()=='POST'){
            $data = Input::all();
            unset($data['_token']);
            //验证
            $validator = Validator::make($data, $rules,$message);
            if($validator->fails()){
                $msg =  $validator->messages()->first();
                return Redirect::to('/admin/defaultError')->with('message',$msg);
            }
            $filePath = '/upload/classactive/';
            $mainpic = '';
            
            $piclist = array();
            for($i=0;$i<=5;$i++){
                $name = 'piclist'.$i;
                if($data[$name]){
                    //图片上传
                    $ext = $data[$name]->guessExtension();
                    $imgName = time().uniqid();
                    $imgName = $imgName.'.'.$ext;
                    $lastFilePath = $filePath.$imgName;
                    $data[$name]->move($filePath,$imgName);
                    if($i==0)
                    {
                        $mainpic = $lastFilePath;
                    }
                    if($i==5)
                    {
                        $smallpic =$lastFilePath;
                    }
                    else
                    {
                        $piclist[$name] = $lastFilePath;
                    }
                }else{
                    if($i==0)
                    {
                        $mainpic = $pic[$i];
                    }
                    else if($i==5)
                    {
                        $smallpic = $pic[$i];
                    }
                    else
                    {
                        $piclist[$name]=$pic[$i];
                    }
                }
            }
            unset($data['piclist0'],$data['piclist1'],$data['piclist2'],$data['piclist3'],$data['piclist4'],$data['piclist5']);
            $data['starttime'] = strtotime($data['starttime']);
            $data['endtime'] = strtotime($data['endtime'])+86399;
            $data['mainpic'] = $mainpic;
            $data['smallpic'] = $smallpic;
            $data['piclist'] = serialize($piclist);
            DB::table('class_active')->where("id",$id)->update($data);
            #跳转
            return Redirect::to('admin/classActiveList');
        }
        return View::make('adminclassactive.changeclassactive')->with('list',$list);
    }
    /**
    * 删除班级活动
    *ajax
    * @author:hgz
    * @since:2016/06/29
    */
    public function delClassActive($id){
        $row=DB::table('class_active')->where("id",$id)->get();
        if($row[0]['isdel']==0){
            $stauts=DB::table('class_active')->where("id",$id)->update(array('isdel' => 1));
            if($stauts==1){
                #删除成功
                echo "1";
            }else{
                #删除失败
                echo "2";
            }
        }
        if($row[0]['isdel']==1){
            $stauts=DB::table('class_active')->where("id",$id)->update(array('isdel' => 0));
            if($stauts==1){
                #还原成功
                echo "3";
            }else{
                #还原失败
                echo "4";
            }
        }
    }
    /**
    * 报名学员列表
    * @author:wang.hongli
    * @since:2016/06/13
    */
    public function applyStudentList(){
        $pagesize = 20;
        $adminClassActive = new AdminClassActive();
        //获取班级报名列表
        $data['classList'] = $adminClassActive->getActiveClasses();
        $data['classList'][0] = '全部';
        $conn = DB::table('class_active_form');
        //用户id
        $search['uid'] = Input::has('uid') ? intval(Input::get('uid')) : '';
        if(!empty($search['uid'])){
            $conn->where('uid',$search['uid']);
        }
        //真实姓名
        $search['name'] = Input::has('name') ? Input::get('name') : '';
        if(!empty($search['name'])){
            $conn->where('name',$search['name']);
        }
        //手机号
        $search['mobile'] = Input::has('mobile') ? Input::get('mobile') : '';
        if(!empty($search['mobile'])){
            $conn->where('mobile',$search['mobile']);
        }
        //电子邮箱
        $search['email']  = Input::has('email') ? Input::get('email') : '';
        if(!empty($search['email'] )){
            $conn->where('email',$search['email'] );
        }
        //参赛码
        $search['intivitationcode'] = Input::has('intivitationcode') ? Input::get('intivitationcode') : '';
        if(!empty($search['intivitationcode'])){
            $conn->where('intivitationcode',$search['intivitationcode']);
        }
        //开始时间
        $search['starttime'] = Input::has('starttime') ? Input::get('starttime') : '';
        if(!empty($search['starttime'])){
            $conn->where('addtime','>=',strtotime($search['starttime']));
        }
        //结束时间
        $search['endtime'] = Input::has('endtime') ? Input::get('endtime') : '';
        if(!empty($search['endtime'])){
            $conn->where('addtime','<=',strtotime($search['endtime'])+86399);
        }
        //昵称
        $search['nick'] = Input::has('nick') ? Input::get('nick') : '';
        if(!empty($search['nick'])){
            $conn->where('nick','like','%'.$search['nick'].'%');
        }
        //培训班
        $search['competition_id'] = Input::has('competition_id') ? intval(Input::get('competition_id')) : 0;
        if(!empty($search['competition_id'])){
            $conn->where('competition_id',$search['competition_id']);
        }
        //交费
        $search['status'] = Input::has('status') ?  Input::get('status') : -1;
        if($search['status'] != -1){
            $conn->where('status',$search['status']);
        }
        //处理
        $search['deal_status'] = Input::has('deal_status') ? Input::get('deal_status') : -1;
        if($search['deal_status'] != -1){
            $conn->where('deal_status',$search['deal_status']);
        }
        //获取所有省份
        $data['all_province'] = ApiCity::getAllProvince();
        //获取所有城市
        $data['all_city'] = ApiCity::getAllCity();
        //获取所有地区
        $data['all_area'] = ApiCity::getAllArea();
        $data['all_province'][0] = '全部';
        
        $search['province_id'] = Input::has('province_id') ? intval(Input::get('province_id')) : 0;
        $data['city'] = array(0=>'全部');
        if(!empty($search['province_id'])){
            $conn->where('province_id',$search['province_id']);
            $data['city'] = $data['all_city'][$search['province_id']];
        }
        //市
        $data['area'] = array(0=>'全部');
        $search['city_id'] = Input::has('city_id') ? intval(Input::get('city_id')) : 0;
        if(!empty($search['city_id'])){
            $conn->where('city_id',$search['city_id']);
            $data['area'] = $data['all_area'][$search['city_id']];
        }
        //地区
        $search['area_id'] = Input::has('area_id') ? intval(Input::get('area_id')) : 0;
        if(!empty($search['area_id'])){
            $conn->where('area_id',$search['area_id']);
        }
        $conn->orderBy('id','desc');
        $this->down_list =$conn->get();
        $data['list'] = $conn->paginate($pagesize);
        return View::make('adminclassactive.applystudentlist')->with('data',$data)->with('search',$search);
    }
    /**
    * 培训班学员处理的状态，交费的状态
    * @author:wang.hongli
    * @since:2016/06/14
    */
    public function dealClassActiveStatudent(){
        if(!Request::ajax()){
            echo '提交方式错误，请重试';
            return;
        }
        $id = Input::has('id') ? intval(Input::get('id')) : 0;
        if(empty($id)){
            echo "错误，请联系管理员";
            return;
        }
        $status = Input::has('status') ? intval(Input::get('status')) : 0;
        $flag = Input::has('flag') ? intval(Input::get('flag')) : 0;//0 交费 1处理
        $status = ($status==0)?1:0;
        try{
            switch($flag){
                case 0:
                    DB::table('class_active_form')->where('id',$id)->update(array('status'=>$status));
                    break;
                case 1:
                    DB::table('class_active_form')->where('id',$id)->update(array('deal_status'=>$status));
                    break;
        }
        echo 1;
    } catch (Exception $e) {
        echo "错误，请重试";
        return;
    }
}
public function downStudentList(){
    $adminClassActive = new AdminClassActive();
    $data = array();
    $conn = DB::table('class_active_form');
    //用户id
    $search['uid'] = Input::has('uid') ? intval(Input::get('uid')) : '';
    if(!empty($search['uid'])){
        $conn->where('uid',$search['uid']);
    }
    //真实姓名
    $search['name'] = Input::has('name') ? Input::get('name') : '';
    if(!empty($search['name'])){
        $conn->where('name',$search['name']);
    }
    //手机号
    $search['mobile'] = Input::has('mobile') ? Input::get('mobile') : '';
    if(!empty($search['mobile'])){
        $conn->where('mobile',$search['mobile']);
    }
    //电子邮箱
    $search['email']  = Input::has('email') ? Input::get('email') : '';
    if(!empty($search['email'] )){
        $conn->where('email',$search['email'] );
    }
    //参赛码
    $search['intivitationcode'] = Input::has('intivitationcode') ? Input::get('intivitationcode') : '';
    if(!empty($search['intivitationcode'])){
        $conn->where('intivitationcode',$search['intivitationcode']);
    }
    //开始时间
    $search['starttime'] = Input::has('starttime') ? Input::get('starttime') : '';
    if(!empty($search['starttime'])){
        $conn->where('addtime','>=',strtotime($search['starttime']));
    }
    //结束时间
    $search['endtime'] = Input::has('endtime') ? Input::get('endtime') : '';
    if(!empty($search['endtime'])){
        $conn->where('addtime','<=',strtotime($search['endtime'])+86399);
    }
    //昵称
    $search['nick'] = Input::has('nick') ? Input::get('nick') : '';
    if(!empty($search['nick'])){
        $conn->where('nick','like','%'.$search['nick'].'%');
    }
    //培训班
    $search['competition_id'] = Input::has('competition_id') ? intval(Input::get('competition_id')) : 0;
    if(!empty($search['competition_id'])){
        $conn->where('competition_id',$search['competition_id']);
    }
    //交费
    $search['status'] = Input::has('status') ?  Input::get('status') : -1;
    if($search['status'] != -1){
        $conn->where('status',$search['status']);
    }
    //处理
    $search['deal_status'] = Input::has('deal_status') ? Input::get('deal_status') : -1;
    if($search['deal_status'] != -1){
        $conn->where('deal_status',$search['deal_status']);
    }
    //获取所有省份
    $data['all_province'] = ApiCity::getAllProvince();
    //获取所有城市
    $data['all_city'] = ApiCity::getAllCity();
    //获取所有地区
    $data['all_area'] = ApiCity::getAllArea();
    $data['all_province'][0] = '全部';
    $search['province_id'] = Input::has('province_id') ? intval(Input::get('province_id')) : 0;
    $data['city'] = array(0=>'全部');
    if(!empty($search['province_id'])){
        $conn->where('province_id',$search['province_id']);
        $data['city'] = $data['all_city'][$search['province_id']];
    }
    //市
    $data['area'] = array(0=>'全部');
    $search['city_id'] = Input::has('city_id') ? intval(Input::get('city_id')) : 0;
    if(!empty($search['city_id'])){
        $conn->where('city_id',$search['city_id']);
        $data['area'] = $data['all_area'][$search['city_id']];
    }
    //地区
    $search['area_id'] = Input::has('area_id') ? intval(Input::get('area_id')) : 0;
    if(!empty($search['area_id'])){
        $conn->where('area_id',$search['area_id']);
    }
    $conn->orderBy('id','desc');
    $list=$conn->get();
    $tmp=array();
    $classList= $adminClassActive->getActiveClasses();
    foreach($list as $k=>$v){
        $tmp[$k][]=$v['id'];
        $tmp[$k][]=$v['uid'];
        $tmp[$k][]=$v['name'];
        $tmp[$k][]=$v['nick'];
        $tmp[$k][]=$v['gender']==0?"女":'男';
        $tmp[$k][]=$v['card'];
        $tmp[$k][]=accorCardGetAge($v['card']);
        $tmp[$k][]=$v['mobile'];
        $tmp[$k][]=date('Y-m-d H:i:s',$v['addtime']);
        $tmp[$k][]=$v['invitationcode'];
        $tmp[$k][]=$classList[$v['competition_id']]?$classList[$v['competition_id']]:"未知";
        $tmp[$k][]=$v['status'] == 0?"未交费":"已交费";
        $tmp[$k][]=$v['deal_status'] == 0?"未处理":"已处理";
        $tmp[$k][]=$data['all_province'][$v['province_id']]?$data['all_province'][$v['province_id']]:"";
        $tmp[$k][]=$data['all_city'][$v['province_id']][$v['city_id']]?$data['all_city'][$v['province_id']][$v['city_id']]:"";
        $tmp[$k][]=$data['all_area'][$v['city_id']][$v['area_id']]?$data['all_area'][$v['city_id']][$v['area_id']]:'';
        $tmp[$k][]=$v['address']?$v['address']:"";
        $tmp[$k][]=$v['company']?$v['company']:"";
        $tmp[$k][]=$v['zip']?$v['zip']:"";
        $tmp[$k][]=$v['email']?$v['email']:"";
        $tmp[$k][]=$v['birthday']?date('Y-m-d',$v['birthday']):"";
        $tmp[$k][]=$v['orderid']?$v['orderid']:"";
        $tmp[$k][]=$v['goods_id']?$v['goods_id']:"";
    }
    //生成xls文件==========================================
    require_once ("../app/ext/PHPExcel.php");
    $excel=new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel5($excel);
    $excel->setActiveSheetIndex(0);
    $sheet=$excel->getActiveSheet();
    $sheet->setTitle('报名学员列表');
    $sheetTitle=array('ID','用户ID','姓名','昵称',"性别",'身份证号','年龄','手机号','申请时间','参赛码','培训班','交费/未交费','处理未处理',"省份",'城市','县区','地址','单位名称','邮编','邮箱','生日','订单号','所购商品');
    $cNum=0;
    foreach($sheetTitle as $val){
        $sheet->setCellValueByColumnAndRow($cNum,1,$val);
        $cNum++;
    }
    $rNum=2;
    foreach($tmp as $val){
        $cNum=0;
        foreach($val as $row){
            $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
            $cNum++;
        }
        $rNum++;
    }
    $outputFileName = "studentList.xls";
    $file='upload/'.$outputFileName;
    $objWriter->save($file);
    $excel_url =  Config::get('app.url').'/upload/'.$outputFileName;
    echo "<a href='$excel_url'>下载</a>";
}
}