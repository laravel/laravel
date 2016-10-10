<?php
/**
*接口api和花相关的类
*/
class ApiUserFlowersController extends ApiCommonController {
    private $userFlowers = null;
    private $nolog = null;
    private $miss = null;
    public function __construct() {
        $this->userFlowers = new ApiUserFlowers();
        $this->nolog=Lang::get('messages.nolog');//请登录
        $this->miss=Lang::get('messages.miss');//获取信息失败
    }
    
    /**
    * 送花接口
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function giveFlowers(){
        $to_id = intval(Input::get('to_id'),0);
        $opus_id = intval(Input::get('opus_id'));
        $num = intval(Input::get('num'));
        $poem = intval(Input::get('poem',0));
        $read = intval(Input::get('read',0));
        // if($to_id==0){
        //     $this->setReturn(-2,"赠送人id错误");return;
        // }
        if($num<=0){
            $this->setReturn(-1,"数量错误");return;
        }
        $user_info = ApiCommonStatic::viaCookieLogin();
   
        if(!$user_info){
            $this->setReturn(-101,$this->nolog);return;
        }
        $apicheck=new ApiCheckPermission();
        $return=$apicheck->isEnough($user_info['id'],0,$num);
        if(!$return){
            $this->setReturn(-200,'钻石数量不足');return;
        }
        $rs = $this->userFlowers->giveFlowers($user_info['id'],$to_id,$opus_id,$num,$poem,$read);
        if($rs===true) {
            $this->setReturn(1,'success',$rs);return;
        }else {
            $this->setReturn(0,'error',$rs);return;
        }
    }
    /**
    * 个人主播列表接口
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function getFlowers(){
        $count = Input::has('ciount') ? Input::get('count'): 20 ;
        $pageIndex =Input::has('pageIndex')?Input::get('pageIndex'):1;
        $otherid=Input::get('otherid',0);
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $user_info = ApiCommonStatic::viaCookieLogin();
       if(!$user_info){
            $this->setReturn(-101,$this->nolog);return;
        }
        if($otherid!=0){
            $uid=$otherid;
        }else{
            $uid=$user_info['id'];
        }
        $rs = $this->userFlowers->getFlowers($uid,$count,$offSet,$user_info['id']);
        
        if(is_array($rs)) {
            $num = count ( $rs );
            if ($num >= $count) {
                array_pop ( $rs );
                $hasmore = 1;
            } else {
                $hasmore = 0;
            }
            $this->setReturn(1,'success',$rs,$hasmore);
        } else {
            $this->setReturn(1,'success',$rs);
        }
    }
    /**
    * 主播守护总榜
    * @author:hgz
    * @since:2016/07/05
    * @flag
    */
    public function allFlowers(){
        $count = Input::has('count') ? Input::get('count'): 20 ;
        $pageIndex =Input::has('pageIndex')?Input::get('pageIndex'):1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $user_info = ApiCommonStatic::viaCookieLogin();
       
        if(!$user_info){
            $this->setReturn(-101,$this->nolog);return;
        }
        $rs = $this->userFlowers->allFlowers($user_info['id'],$count,$offSet);
        if(is_array($rs) && $rs) {
            $num = count ( $rs );
            if ($num >= $count) {
                array_pop ( $rs );
                $hasmore = 1;
            } else {
                $hasmore = 0;
            }
            
            $this->setReturn(1,'success',$rs, $hasmore);return;
        } else {
            $this->setReturn(1,'success',array());return;
        }
    }
    /**
    * 年榜
    * @author:hgz
    * @since:2016/07/05
    * @flag
    */
    public function YearFlowers(){
        $count = Input::has('count') ? Input::get('count'): 20 ;
        $pageIndex =Input::has('pageIndex')?Input::get('pageIndex'):1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info){
            $this->setReturn(-101,$this->nolog);return;
        }
        $year=date("Y",time());
        $rs = $this->userFlowers->YearFlowers($year,$user_info['id'],$count,$offSet);
        if(is_array($rs) && $rs) {
            $num = count ( $rs );
            if ($num >= $count) {
                array_pop ( $rs );
                $hasmore = 1;
            } else {
                $hasmore = 0;
            }
            
            $this->setReturn(1,'success',$rs,$hasmore);
        } else {
            $this->setReturn(1,'success',$rs);
        }
    }
    /**
    * 月榜
    * @author:hgz
    * @since:2016/07/05
    * @flag
    */
    public function MonthFlowers(){
        $count = Input::has('count') ? Input::get('count'): 20 ;
        $pageIndex =Input::has('pageIndex')?Input::get('pageIndex'):1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info){
            $this->setReturn(-101,$this->nolog);return;
        }
        $month=date("Y-m",time());
        $rs = $this->userFlowers->MonthFlowers($month,$user_info['id'],$count,$offSet);
        if(is_array($rs) && $rs) {
            $num = count ( $rs );
            if ($num >= $count) {
                array_pop ( $rs );
                $hasmore = 1;
            } else {
                $hasmore = 0;
            }
            $this->setReturn(1,'success',$rs,$hasmore);
        } else {
            $this->setReturn(1,'success',$rs);
            
        }
    }
    /**
    * 周榜
    * @author:hgz
    * @since:2016/07/05
    * @flag
    */
    public function WeekFlowers(){
        $count = Input::has('count') ? Input::get('count'): 20 ;
        $pageIndex =Input::has('pageIndex')?Input::get('pageIndex'):1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $user_info = ApiCommonStatic::viaCookieLogin();
         
        if(!$user_info){
            $this->setReturn(-101,$this->nolog);return;
        }
        $rs = $this->userFlowers->WeekFlowers($user_info['id'],$count,$offSet);
        if(is_array($rs) && $rs) {
            $num = count ( $rs );
            if ($num >= $count) {
                array_pop ( $rs );
                $hasmore = 1;
            } else {
                $hasmore = 0;
            }
            $this->setReturn(1,'success',$rs,$hasmore);
        } else {
            $this->setReturn(1,'success',$rs);
            
        }
    }
    
    /**
    * 个人现有鲜花钻石及花费的鲜花钻石
    */
    public function costList(){
        $user_info = ApiCommonStatic::viaCookieLogin();
        $otherid=Input::get('otherid',0);        
        if(!$user_info){
            $this->setReturn(-101,$this->nolog);
        }
        if($otherid!=0){
            $uid=$otherid;
        }else{
            $uid=$user_info['id'];
        }
        $rs = $this->userFlowers->costList($uid );//收到鲜花
        $this->setReturn(1,'success', $rs,"");
    }
    
    
    //榜单列表
    public function rank_list(){
        $type=Input::get('type',0);  //0年  1月   2周
        
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info['id']){
            $this->setReturn(-101,$this->nolog);
            return ;
        }
        $rs= $this->userFlowers->rank_list($user_info['id'],$type);
        $this->setReturn(1,"success",$rs,0 );
    }
// 
    //静态榜单内容
    public function  rank_info(){
        $type=Input::get('id',0);//历史榜单id
        if($type==0){
          $this->setReturn(-1,"error",array(),0);  return;
        }
        $count =  Input::has('count')  ? Input::get('count') : 10;
        $pageIndex =  Input::has('pageIndex') ? Input::get('pageIndex') : 1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info['id']){
            $this->setReturn(-101,$this->nolog);
            return ;
        }
        $rs= $this->userFlowers->rank_info($user_info['id'],$type,$count,$offSet);
        $num=count($rs);
        if($num==$count){
            $hasmore = 1;
            array_pop($rs);
        }else{  
            $hasmore = 0;
       }
        $this->setReturn(1,"success",$rs,$hasmore );
        
    }
    
    
    // 作品收花列表
	public function opusFlower(){   
        $opusid=Input::get('opusid',0);//目标作品id
        $flag=Input::get('flag',0);
       
        $count =  Input::has('count')  ? Input::get('count') : 20;
        $pageIndex =  Input::has('pageIndex') ? Input::get('pageIndex') : 1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
		$user_info = ApiCommonStatic::viaCookieLogin();
        if(empty($user_info['id'])){
            $this->setReturn(-101,$this->nolog);    
            return ;
        }
        $rs = $this->userFlowers->opusFlower($user_info['id'],$opusid,$count,$offSet,$flag); 
        $num=count($rs);
        if($num==$count){
            $hasmore = 1;
            array_pop($rs);
        }else{
            $hasmore = 0;
        }
        $this->setReturn(1,"success",$rs,$hasmore );
    }
 
    
}