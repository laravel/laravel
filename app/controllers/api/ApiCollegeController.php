<?php

/**
* 诗学院颂学院
**/
class ApiCollegeController extends ApiCommonController {
    private $ApiCollege;
    private $nolog=null;
    function __construct(){
        $this->ApiCollege = new ApiCollege();
        $this->nolog=Lang::get('messages.nolog');//请登录
        
    }
    //得到学院
    public function getCollege(){
        $rs=$this->ApiCollege->getCollege();
        if('nolog' === $rs) {
            $this->setReturn(-101,$this->nolog);
        }elseif(false === $rs) {
            $this->setReturn(1,"列表未空");
        }else{
            $this->setReturn(1,"获取成功",$rs);
        }
    }
    //得到学院下的年级
    public function getGrade(){
        $rs=$this->ApiCollege->getGrade();
        if('nolog' === $rs) {
            $this->setReturn(-101,$this->nolog);
        }elseif(false === $rs) {
            $this->setReturn(1,"列表未空",array(),0);
        }elseif('error' === $rs) {
            $this->setReturn(-1,"传值错误");
        }else{
            $this->setReturn(1,"获取成功",$rs);
        }
    }
    //得到学院下的老师
    public function getGradeTeacher(){
        $rs=$this->ApiCollege->getGradeTeacher();
        if('nolog' === $rs) {
            $this->setReturn(-101,$this->nolog);
        }elseif(false === $rs) {
             $this->setReturn(1,"列表未空",array(),0);
        }elseif('error' === $rs) {
            $this->setReturn(-1,"传值错误");
        }else{
            $hasmore = $rs['hasmore'];
				unset($rs['hasmore']);
            $this->setReturn(1,"获取成功",$rs,$hasmore);
        }
    }
    //得到学院下的班级列表
    public function getClass(){
        $rs=$this->ApiCollege->getClass();
        if('nolog' === $rs) {
            $this->setReturn(-101,$this->nolog);
        }elseif(false === $rs) {
             $this->setReturn(1,"列表未空",array(),0);
        }elseif('error' === $rs) {
            $this->setReturn(-1,"传值错误");
        }else{
            $hasmore = $rs['hasmore'];
			unset($rs['hasmore']);
            $this->setReturn(1,"获取成功",$rs,$hasmore);
        }
    }
	//得到班级老师
    public function getClassTeacher(){
        $rs=$this->ApiCollege->getClassTeacher();
        if('nolog' === $rs) {
            $this->setReturn(-101,$this->nolog);
        }elseif(false === $rs) {
             $this->setReturn(1,"列表未空",array(),0);
        }elseif('error' === $rs) {
            $this->setReturn(-1,"传值错误");
        }else{
            $this->setReturn(1,"获取成功",$rs);
        }
    }
    //聊天室全部成员
    public function getAlluser(){
        $user_info = ApiCommonStatic::viaCookieLogin();
		if(!$user_info) {
			$this->setReturn(-101,$this->nolog);
			return ;
		}
        $groupid=Input::get('groupid');
        if(!$groupid){
            $this->setReturn(-1,'noid');
			return ;
        }
        $rs=$this->ApiCollege->getAlluser($user_info['id'],$groupid);
        if($rs) {
            $this->setReturn(1,'success',$rs);return ;
        }else{
            $this->setReturn(0,"success",array());return ;
        }
    }
    //得到班级信息
    public function getClassInfo(){
       $user_info = ApiCommonStatic::viaCookieLogin();
		 $user_info['id']=2;
        if(!$user_info) {
			$this->setReturn(-101,$this->nolog);
			return ;
		}
        $groupid=Input::get('groupid',0);
        $classid=Input::get('classid',0);
        if(!$groupid && !$classid){
            $this->setReturn(-1,'noid');return ;
        }

        $rs=$this->ApiCollege->getClassInfo($groupid,$classid);

        $this->setReturn(1,'success',$rs,0);return;





    }
}