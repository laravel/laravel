<?php
class ApiUserAddress extends ApiCommon {
    
    /**
    * 新增收货地址
    * @author:hgz
    * @since:2016/07/07
    * @flag
    */

    public function addUserAddress($id){
        
        $data['uid']=$id;
        $data['province_id']=intval(Input::get('province_id'));
        $data['city_id']=intval(Input::get('city_id'));
        $data['area_id']=intval(Input::get('area_id'));
        $data['address']=Input::get('address');
        $data['name']=Input::get('name');
        $data['tel']=Input::get('tel');
        $data['istop']=Input::get('istop')?1:0;
        if($data['istop']==1){
                DB::table("user_address")->where('uid',$id)->update(array('istop'=>0));
        }
        $user_address=DB::table("user_address")->insertGetId($data);
        if($user_address){
            $rs=$this->numtohanzi($data);
            $data['province_id_num']=$rs['province_id'];
            $data['city_id_num']=$rs['city_id'];
            $data['area_id_num']=$rs['area_id'];
            $data['address_id']=$user_address;
            return $data;
        }else{
            return array();
        }
        
    }
    
    /**
    * 收货地址列表
    * 带默认地址的在第一个
    * @author:hgz
    * @since:2016/07/07
    * @flag
    */
    public function listUserAddress($id,$count,$offSet,$pageIndex){
  
            $user_address=DB::table("user_address")->where("uid",'=',$id)->where('isdel',0)->orderBy('istop','desc')->skip($offSet)->take($count)->get();
             
            if($user_address && is_array($user_address)){
                foreach ($user_address as $key => $value) {
                    $data=$this->numtohanzi($value);
                    $user_address[$key]['province_id']=$data['province_id'];
                    $user_address[$key]['city_id']=$data['city_id'];
                    $user_address[$key]['area_id']=$data['area_id'];
                    $user_address[$key]['province_id_num']=$value['province_id'];
                    $user_address[$key]['city_id_num']=$value['city_id'];
                    $user_address[$key]['area_id_num']=$value['area_id'];
                }
                return $user_address;
            }else{
                return FALSE;
            }
        }
    
    /**
    * 修改收货地址原数据
    * @author:hgz
    * @since:2016/07/07
    * @flag  get 传address_id
    */
    public function updateUserAddress($id,$address_id){

        if(isset($address_id)){
            $list=DB::table("user_address")->where("id",'=',$address_id)->first();
            return $list;
        }else{
            return "数据错误";
        }
    }
    /**
    * 更新要修改地址
    * @author:hgz
    * @since:2016/07/07
    * @flag  传address_id
    */
    public function doUpdateUserAddress($id,$address_id,$data){	
        if($data['istop']==1){
            DB::table("user_address")->where('uid',$id)->where("istop",'=',1)->update(array('istop'=>0));
        }
        $user_address=DB::table("user_address")->where("id",'=',$address_id)->update($data);
        if($user_address){
            $rs="succsee";
            return $rs;
        }else{
            $rs="error";
            return $rs;
        }
        
    }
    /** 删除地址
    * @author:hgz
    * @since:2016/07/07
    * @flag
    */
    public function delUserAddress( $address_id){
        $sql =DB::table("user_address")->where("id",'=',$address_id)->update(array('isdel'=>1));
        if($sql){
            return true;
        }else{
            
            return false;
        }
    }
    /**
    * 设置默认地址
    * @author:hgz
    * @since:2016/07/07
    * @flag
    */
    public function topUserAddress($id,$address_id){
        //查找历史   
     
    
            $c=DB::table('user_address')->where('uid','=',$id)->update(array("istop"=>0));
            $d=DB::table('user_address')->where('uid','=',$id)->where('id','=',$address_id)->update(array("istop"=>1));
            if($d){
                return true;
            }else{
                return false;
            }
        
    }
    /**
    * 直接得到默认地址
    * @author:hgz
    * @since:2016/07/07
    * @flag
    **/
    public function oldUserAddress($id){
        $top= DB::table('user_address')->where('uid','=',$id)->where('istop',"=",1)->first();
        if(empty($top)){
            return  '请设置默认地址';
        }else{
            $top['province_id_num']=$top['province_id'];
            $top['city_id_num']=$top['city_id'];
            $top['area_id_num']=$top['area_id'];
            $data=$this->numtohanzi($top);
            $top['province_id']=$data['province_id'];
            $top['city_id']=$data['city_id'];
            $top['area_id']=$data['area_id'];
            
            return $top;
        }
    }
    
    /**
    * 得到地址数字转汉字
    * @author:hgz
    * @since:2016/07/08
    * @flag
    */
    public function numtohanzi($top){
        //获取所有省份
        $data['all_province'] = ApiCity::getAllProvince();
        //获取所有城市
        $data['all_city'] = ApiCity::getAllCity();
        //获取所有地区
        $data['all_area'] = ApiCity::getAllArea();
        $data['all_province'][0] = '全部';
        
        $data['province_id'] = $data['all_province'][$top['province_id']];
        $data['city_id'] = $data['all_city'][$top['province_id']][$top['city_id']];
        $data['area_id'] = $data['all_area'][$top['city_id']][$top['area_id']];
        return $data;
    }
    
    
    
}