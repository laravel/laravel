<?php

/**
* 地址控制器
**/
class ApiAddressController extends ApiCommonController {
    private $ApiUserAddress;
    private $nolog=null;
    function __construct(){
        $this->ApiUserAddress = new ApiUserAddress();
        $this->nolog=Lang::get('messages.nolog');//请登录
        
    }
    
    //增加地址
    public function addUserAddress() {
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info) {
            $this->setReturn(-101,$this->nolog);return ;
        }
        $rs = $this->ApiUserAddress->addUserAddress($user_info['id']);
        if(is_array($rs) && $rs ) {
            $this->setReturn(1,"succsee",$rs);return ;
        }elseif(false == $rs) {
            $this->setReturn(0,"error");return ;
        }
    }
    /**
    * 收货地址列表
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function listUserAddress(){
        $user_info = ApiCommonStatic::viaCookieLogin();
        
        if(!$user_info) {
            $this->setReturn(-101,$this->nolog);
            return ;
        }
        $count =  intval(Input::get ( 'count',20 ));
        $pageIndex =intval(Input::get ( 'pageIndex',1 )) ;
        $offSet = ($pageIndex - 1) * $count;
        ++$count;
        $rs = $this->ApiUserAddress->listUserAddress($user_info['id'],$count,$offSet,$pageIndex);
        if(is_array($rs) && $rs) {
            $num=count ($rs);
            if ($num >= $count) {
                array_pop ( $rs );
                $hasmore= 1;
            } else {
                $hasmore = 0;
            }
            $this->setReturn(1,"succsee",$rs,$hasmore);return;
            
        } else {
            $this->setReturn(1,"succsee",array(),0);return ;
            
        }
    }
    
    /*
    * 修改地址
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function updateUserAddress(){
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info) {
            $this->setReturn(-101,$this->nolog);return ;
        }
        $address_id=Input::get('address_id');
        $rs = $this->ApiUserAddress->updateUserAddress($user_info['id'],$address_id);
        if(is_array($rs)) {
            $this->setReturn(1,"succsee",$rs);return ;
        } else {
            $this->setReturn(0,"获取失败");return ;
        }
    }
    /**
    * 执行修改地址
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function doUpdateUserAddress(){
        $user_info = ApiCommonStatic::viaCookieLogin();
       
        if(!$user_info) {
            $this->setReturn(-101,$this->nolog);return ;
        }
        $address_id=Input::get('address_id');
        
        $data['province_id']=Input::get('province_id');
        $data['city_id']=Input::get('city_id');
        $data['area_id']=Input::get('area_id');
        $data['address']=Input::get('address');
        $data['name']=Input::get('name');
        $data['tel']=Input::get('tel');
        $data['istop']=Input::get('istop',0);
        
        $rs = $this->ApiUserAddress->doUpdateUserAddress($user_info['id'],$address_id,$data);
        if($rs==="succsee") {
            $this->setReturn(1,"succsee",$rs);return ;
        } else {
            $this->setReturn(0,"error",$rs);return ;
        }
    }
    /**
    * 删除地址
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function delUserAddress(){
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info) {
            $this->setReturn(-101,$this->nolog);return ;
        }
        $address_id=Input::get('address_id');
        if(!$address_id) {
            $this->setReturn(-1,'noid');return ;
        }
        $rs = $this->ApiUserAddress->delUserAddress($address_id);
        if($rs===true) {
            $this->setReturn(1,"succsee",$rs);return ;
        } else {
            $this->setReturn(0,$rs);return ;
        }
    }
    /**
    * 设置默认地址
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function topUserAddress(){
        $user_info = ApiCommonStatic::viaCookieLogin();
        
        if(!$user_info) {
            $this->setReturn(-101,$this->nolog);return ;
        }
        $address_id=Input::get('address_id');
        if(!$address_id) {
            $this->setReturn(-1,'noid');return ;
        }
        $rs = $this->ApiUserAddress->topUserAddress($user_info['id'],$address_id);
        if($rs===true) {
            $this->setReturn(1,"succsee",$rs);return;
        } else {
            $this->setReturn(0,"error",$rs);return;
        }
    }
    /**
    * 得到默认地址
    * @author:hgz
    * @since:2016/07/04
    * @flag
    */
    public function oldUserAddress(){
        $user_info = ApiCommonStatic::viaCookieLogin();
        if(!$user_info) {
            $this->setReturn(-101,$this->nolog);return ;
        }
        $rs = $this->ApiUserAddress->oldUserAddress($user_info['id']);
        if(is_array($rs)) {
            $this->setReturn(1,"succsee",$rs);return ;
        } else {
            $this->setReturn(0,$rs);return ;
        }
    }
}