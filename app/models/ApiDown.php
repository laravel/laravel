<?php
/**
** 下载作品记录
*/
class ApiDown extends ApiCommon {  
    
       protected $down_money = '';
    public function __construct(){
         
         $this->down_money = Config::get('app.down_money');
    } 
    public function addDownInfo(){
        $apiUser = new ApiUserPermission();
        $apiCheck= new ApiCheckPermission();
        $info = $this->viaCookieLogin();
 
        if(empty($info['id'])) return 'nolog';
        if(!Input::has ( 'opusid' )) return " opus_error";
		$opusid = intval(Input::get ( 'opusid' ) );
        
        $flag=$apiUser->permission_config($info['id'],1);
        $data['uid']=$info['id'];
        $data['opus_id']=$opusid;
        $data['addtime']=time();
        $data['freetime']=time()+86400*7;
        $data['isdel']=0;
        $num=$this->down_money; 
        $jewel=$apiCheck->isEnough($info['id'],0,$num);
        $rs=DB::table('down_opus')->where('uid',$info['id'])->where('opus_id',$opusid)->first();        
       //无作品记录
        if(!$rs){
            if($flag['free_down'] == 0){
                //不是会员
                if($jewel){
                    //扣钻石
                    DB::table('user_asset_num')->where('uid',$info['id'])->decrement('jewel',$num);
                    DB::table('down_opus')->insert($data);
					return true;
                }else{
                    return 'nojewel';
                }
            }elseif($flag['free_down'] == 1){
                //是会员
                DB::table('down_opus_limit')->where('uid',$info['id'])->decrement('down_num',$num);   
                DB::table('down_opus')->insert($data);   
				return true;  
            }elseif($flag['free_down'] == 2){
                //是会员 没次数
                if($jewel){
                    //扣钻石
                    DB::table('user_asset_num')->where('uid',$info['id'])->decrement('jewel',$num);
                    DB::table('down_opus')->insert($data);
					return true;
                }else{
                    return 'nojewel';
                }
            }
        }else{
        if($rs['addtime']<=time() && $rs['freetime']>=time() ){
              DB::table('down_opus')->where('uid',$info['id'])->where('opus_id',$opusid)->update(array('addtime'=>time(),'isdel'=>0));
				return true;
		}else{
            if($flag['free_down'] == 0){
                //不是会员
                if($jewel){
                    //扣钻石
                    DB::table('user_asset_num')->where('uid',$info['id'])->decrement('jewel',$num);
                    DB::table('down_opus')->where('opus_id',$opusid)->update($data);
					return true;
                }else{
                    return 'nojewel';
                }
            }elseif($flag['free_down'] == 1){
                //是会员
                DB::table('down_opus_limit')->where('uid',$info['id'])->decrement('down_num',$num);  
                DB::table('down_opus')->where('opus_id',$opusid)->update($data);  
                return true;    
            }elseif($flag['free_down'] == 2){
                //是会员 没次数
                if($jewel){
                    //扣钻石
                   DB::table('user_asset_num')->where('uid',$info['id'])->decrement('jewel',$num);
                   DB::table('down_opus')->where('opus_id',$opusid)->update($data);  
                   return true;    
                }else{
                   return 'nojewel';
                }   
            }
		}    
    }
    }
    //下载提示信息
    public function down_message($id,$opusid){
        $apiUser = new ApiUserPermission();
        $rs=DB::table('down_opus')->where('uid',$id)->where('opus_id',$opusid)->first();
        if($rs){
            if($rs['addtime']<=time() && time() <= $rs['freetime'] ){
                return "free";
            }else{
                $flag=$apiUser->permission_config($id,1);
                if($flag['free_down']==0){
                    //不是会员
                    return  "jewel";
                }elseif($flag['free_down']==1){
                    return 'down_num';
                }
            }
        }else{
            //第一次下载
            $flag=$apiUser->permission_config($id,1);
            if($flag['free_down']==0){
                //不是会员
                return  "jewel";
            }elseif($flag['free_down']==1){
                return 'down_num';
            }
        }
    } 
    
    
    //下载作品列表
    public function showDownInfo(){
        $info = $this->viaCookieLogin();
        if(empty($info['id'])) return 'nolog';
        //分页
        $count = Input::has("count") ? Input::get("count"): 20;
        $pageIndex =Input::has("pageIndex") ?Input::get("pageIndex"):1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $list =DB::table('down_opus')->where('uid','=',$info['id'])->where('isdel','=',0)->orderBy('addtime','desc')->skip($offSet)->take($count)->get();
        //作品名  下载时间
        foreach ($list as $key => &$value) {
            $one=DB::table('opus')->where('id','=',$value['opus_id'])->first();
            $user=DB::table('user')->where('id','=',$one['uid'])->first();
            $value['name']=$one['name'];
            $value['url']= $this->poem_url.$one['url'];
            $value['opus_name']=$one['name'];
            $value['lyricurl']= $this->poem_url.$one['lyricurl'];
            $value['type']=$one['type'];
            $value['firstchar']=$one['firstchar'];
            $value['opustime']=$one['opustime'];
            $value['firstchar']=$one['firstchar'];
            $value['nick']=$one['reader'];
            $value['firstchar']=$one['firstchar'];
            $value['writername']=$one['writer'];
            $value['gender']=$user['gender'];
            $value['grade']=$user['grade'];
            $value['sportrait']= $this->poem_url.$user['sportrait'];
            $value['authtype']=$user['authtype'];
            $value['teenager']=$user['teenager'];
            $value['isleague']=$user['isleague'];          
        }
        $num = count ( $list );
        if ($num >= $count) {
            array_pop ( $list );
            $list['hasmore'] = 1;
        } else {
            $list['hasmore'] = 0;
        }
        return $list;
        
    }
    
    
    public function delDownOne(){
        $info = $this->viaCookieLogin();
        if(empty($info['id'])) return 'nolog';
        $opusid = Input::has ('opusid')?Input::get('opusid'):"";
        if($opusid==""){
            return "opus_error";
        }
        $delete=DB::table('down_opus')->where('opus_id',$opusid)->where('uid',$info['id'])->update(array('isdel'=>1));
        if($delete){
            return true;
        }else{
            return false;
        }
    }
    
    
    
}