<?php
//总榜只去100  为实现
//送花要有消息推送
class ApiUserFlowers extends ApiCommon {
    /**
    * 送花接口
    * @author:hgz
    * @since:2016/07/04
    * @flag 未测试  消息推送 分类是鲜花消息
    */
    public function giveFlowers($id,$to_id,$opus_id,$num,$poem,$read) {
        $info['id']=$id;
        if($poem){  //判断能否送给
            $poem_info=DB::table('readpoemrel')->where('poemid',$poem)->first();
            $read_info=DB::table('user_reader_rel')->where('reader_id',$poem_info['readerid'])->first();
            if($read_info){
                $read=$read_info['reader_id'];
                $to_id=$read_info['uid'];
            }else{
                return "reader_error";
            }
        }
        //个人花数量
        $user_num=DB::table('user_asset_num')->where('uid','=',$info['id'])->first();
        
        if($user_num['jewel']<$num){
            return 'numempty';
        }else{
            $new_num=$user_num['jewel']-$num;
            $time = time();
            DB::table('user_asset_num')->where('uid','=',$info['id'])->update(array('jewel' => $new_num,'cost_jewel'=>$new_num+$user_num['cost_jewel']));
            DB::table('user_flowers_list')->insert(array('fromid'=>$info['id'],'toid'=>$to_id,'opusid'=>$opus_id,'num'=>$num,'poemid'=>$poem,'reader_id'=>$read,'time'=>$time));
            DB::table('user_diamond_list')->insert(array('fromid'=>$info['id'],'toid'=>$to_id,'opusid'=>$opus_id,'num'=>$num,'poemid'=>$poem,'reader_id'=>$read,'time'=>$time));
            //个人总数
            //当前周时间戳
            $curtime=time();
            $curweekday = date('w');
            //为0是 就是 星期七
            $curweekday = $curweekday?$curweekday:7;
            $startweek = strtotime(date('Y-m-d',$curtime - ($curweekday-1)*86400)." 00:00:00");
            $endweek = strtotime(date('Y-m-d',$curtime + (7 - $curweekday)*86400)." 23:59:59");
            
            $week=DB::table('flower_time_list')->where('uid',$info['id'])->where('flag',0)->where('starttime','=',$startweek)->where('endtime','=',$endweek)->first();
            if($week){
                DB::table('flower_time_list')->where('uid',$info['id'])->where('flag',0)->where('starttime','=',$startweek)->where('endtime','=',$endweek)->update(array('num'=>$num+$week['num']));
            }else{
                DB::table('flower_time_list')->insert(array('num'=>$num,'uid'=>$info['id'],'flag'=>0,'starttime'=>$startweek,'endtime'=>$endweek));
            }
            
            $startmonth=mktime(0,0,0,date('m'),1,date('Y'));
            $endmonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
            $month=DB::table('flower_time_list')->where('uid',$info['id'])->where('flag',1)->where('starttime','=',$startmonth)->where('endtime','=',$endmonth)->first();
            if($month){
                DB::table('flower_time_list')->where('uid',$info['id'])->where('flag',1)->where('starttime','=',$startmonth)->where('endtime','=',$endmonth)->update(array('num'=>$num+$month['num']));
            }else{
                DB::table('flower_time_list')->insert(array('num'=>$num,'uid'=>$info['id'],'flag'=>1,'starttime'=>$startmonth,'endtime'=>$endmonth));
            }
            
            //收花人是鲜花记录  有就修改  没有就插入
            //get_flower   所有收到的话记录
            $toid_num=DB::table('user_asset_num')->where("uid",'=',$to_id)->first();
            if(empty($toid_num)){
                DB::table('user_asset_num')->insert(array('uid'=>$to_id,'flower'=>$num,'get_flower'=>$num));
            }else{
                $flowers=$num+$toid_num['flower'];
                $get_flowers=$num+$toid_num['flower'];
                DB::table('user_asset_num')->where("uid",'=',$to_id)->update(array('flower'=>$flowers,'get_flower'=>$get_flowers));
            }
            //作品总花数
            if($opus_id != 0){
                $opus_param=DB::table('opus_param')->where("opusid",'=',$opus_id)->first();
                if($opus_param){
                    DB::table('opus_param')->where("uid",'=',$to_id)->update(array('flower'=>$num+$opus_param['flower']));
                }else{
                    DB::table('opus_param')->insert(array('opusid'=>$opus_id,'flower'=>$num,'uid'=>$to_id));
                }
            }
            //伴奏总花数
            if($poem != 0){
                $poem_list=DB::table('opus_param')->where("poemid",'=',$poem)->first();
                if($poem_list){
                    DB::table('opus_param')->where("poemid",'=',$poem)->update(array('flower'=>$num+$poem_list['flower']));
                }else{
                    DB::table('opus_param')->insert(array('opusid'=>0,'flower'=>$num,"poemid"=>$poem,'uid'=>$to_id));
                }
                
                
            }
            //送花消息
            //获取作品主人id，作品名称,推送消息
            return true;
            if($opus_id) {
                $nick=DB::table("user")->where('id',$info['id'])->pluck('nick');
                $opus=DB::table('opus')->where('id',$opus_id)->first();
                $content = $nick.'赠送您作品'.$opus['name'].''.$num.'朵鲜花';
                $pushId = $to_id; //给toid发送消息
                $data = array(
                'action'=>0,
                'type'=>8,
                'uid'=>$info['id'],
                'fromid'=>$info['id'],
                'toid'=>$to_id,
                'opusid'=>$opus_id,
                'name'=>$tmpRs['name'], //作品名
                'addtime'=>time(),
                'content'=>$content,
                'commentid'=>0
                );
            }else{
                $content = $nick.'赠送您'.$num.'朵鲜花';
                $pushId = $to_id; //给toid发送消息
                $data = array(
                'action'=>0,
                'type'=>8,
                'uid'=>$info['id'],
                'fromid'=>$info['id'],
                'toid'=>$to_id,
                'opusid'=>$opus_id,
                'name'=>'', //作品名
                'addtime'=>time(),
                'content'=>$content,
                'commentid'=>0
                );
            }
            $distributeMessage = new DistributeMessage();
            $distributeMessage->distriMessage($data);
            $this->pushMsg($pushId,$content,2);
        }
    }
    
    
    
    //作品鲜花详情
    //flag  0 　作品　　　　　１　伴奏
    public function opusFlower($uid,$opusid,$count,$offSet,$flag){
        
        
        if($flag==0){
            $sql="select fromid , sum(num) as num  from user_flowers_list where opusid=? group by fromid order by num desc limit ? , ? ";
        }else{
            $sql="select fromid , sum(num) as num  from user_flowers_list where poemid=?  group by fromid order by num desc limit ? , ? ";
        }
        $list=DB::select($sql,array($opusid,$offSet,$count));
        if($list){
            $list=$this->adminlist($list,$uid);
            return $list;
        }else{
            return "";
        }
    }
    
    
    
    
    /**
    * 个人守护榜
    * @author:hgz
    * @since:2016/07/04
    * @flag 测试ok
    *  分页参数   count 每页显示数量		pageIndex  页数
    */
    public function getFlowers($uid,$count,$offSet,$self) {
        
        $info['id']=$uid;
        //限制100个人
        if($offSet>100 ){
            return array();
        }
        if($offSet<100 && ($offSet+$count)>100 ){
            $count=100-$offSet;
        }
        $sql="select fromid,sum(num) as num from user_flowers_list where toid=? and flag=0 group by fromid order by num desc  limit ?,?";
        $list=DB::select($sql,array($info['id'],$offSet,$count));
        $list=$this->adminlist($list,$self);
        return $list;
    }
    /**
    * 主播总榜
    * @author:hgz
    * @since:2016/07/05
    * @flag  测试ok  只有100条
    *分页参数  count 每页显示数量		pageIndex  页数 get
    */
    public function allFlowers($uid,$count,$offSet) {
        $info['id']=$uid;
        //限制100个人
        if($offSet>100 ){
            return false;
        }
        if($offSet<100 && ($offSet+$count)>100 ){
            $count=100-$offSet;
        }
        if(empty($info['id'])) return 'nolog';
        $sql="select uid as fromid,sum(num) as num from flower_time_list where flag=1  group by fromid order by num desc limit ?,?";
        $list =DB::select($sql,array($offSet,$count));
        $list=$this->adminlist($list,$info['id']);
        return $list;
    }
    
    /**
    * 当年份榜
    * @author:hgz
    * @since:2016/07/05
    * @flag  测试ok  只有100条
    * 分页参数  count 每页显示数量		pageIndex  页数
    */
    public function YearFlowers($year,$uid,$count,$offSet) {
        $info['id']=$uid;
        //始末时间
        $start=strtotime($year."-01-01 00:00:00");
        $end=strtotime($year."-12-31 23:59:59");
        
        if(empty($info['id'])) return 'nolog';
        //限制100个人
        if($offSet>100 ){
            return false;
        }
        if($offSet<100 && ($offSet+$count)>100 ){
            $count=100-$offSet;
        }
        $sql="select uid as fromid,sum(num) as num from flower_time_list  WHERE starttime >= ? AND endtime <= ? and flag=1 group by uid order by num desc limit ?,?";
        $list=DB::select($sql,array($start,$end,$offSet,$count));
        $list=$this->adminlist($list,$info['id']);
        
        return $list;
    }
    
    /**
    * 月份总榜
    * @author:hgz
    * @since:2016/07/04
    * @flag  测试ok 只有100条
    */
    public function MonthFlowers($month,$uid,$count,$offSet) {
        $info['id']=$uid;
        $startmonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endmonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        //限制100个人
        if($offSet>100 ){
            return false;
        }
        if($offSet<100 && ($offSet+$count)>100 ){
            $count=100-$offSet;
        }
        
        $sql="select uid as fromid,sum(num) as num from flower_time_list  WHERE starttime = ? AND endtime= ? and flag=1 group by uid order by num desc limit ?,?";
        $list=DB::select($sql,array($startmonth,$endmonth,$offSet,$count));
        $list=$this->adminlist($list,$info['id']);
        
        return $list;
    }
    /**
    * 周榜
    * @author:hgz
    * @since:2016/07/04
    * @flag  测试ok 只有100条
    */
    public function WeekFlowers($uid,$count,$offSet) {
        
        $info['id']=$uid;
        $curtime=time();
        $curweekday = date('w');
        //为0是 就是 星期七
        $curweekday = $curweekday?$curweekday:7;
        $start = $curtime - ($curweekday-1)*86400;
        $end = $curtime + (7 - $curweekday)*86400;
        //周起始时间戳
        $start_time=strtotime(date("Y-m-d",$start)."00:00:00");
        $end_time=strtotime(date("Y-m-d",$end)."23:59:59");
        //return date("Y-m-d",$start)."=".date("Y-m-d",$end);
        $week=date("Y-m-d",$start);
        
        
        //限制100个人
        if($offSet>100 ){
            return false;
        }
        if($offSet<100 && ($offSet+$count)>100 ){
            $count=100-$offSet;
        }
        
        $sql="select uid as fromid,sum(num) as num from flower_time_list  WHERE starttime = ? AND endtime= ? and flag=0 group by uid order by num desc limit ?,?";
        $list=DB::select($sql,array($start_time,$end_time,$offSet,$count));
        
        $list=$this->adminlist($list,$info['id']);
        
        return $list;
        
    }
    /**
    * 个人送花详单
    * @author:hgz
    * @since:2016/07/05
    * @flag  测试ok
    */
    public function userFlowersList() {
        $info = $this->viaCookieLogin();
        
        if(empty($info['id'])) return 'nolog';
        //分页
        $count = !empty(Input::get('count')) ? Input::get('count') : 20;
        $pageIndex = !empty(Input::get('pageIndex')) ? Input::get('pageIndex') : 1;
        $offSet = ($pageIndex-1)*$count;
        ++$count;
        $sql="select f.fromid,f.toid,f.num,u.nick,u.gender,u.sportrait, u.isleague from user_flowers_list as f left join user as u on u.id= f.toid where f.fromid=?  and f.flag=0 order by time desc limit ?,?";
        $list=DB::select($sql,array($info['id'],$offSet,$count));
        if(!empty($list) && is_array($list)){
            foreach ($list as $key => $value) {
                $follow=DB::table('follow')->where('uid','=',$info['id'])->where("fid",'=',$list[$key]['fromid'])->first();
                $list[$key]['relation']=$follow['relation']?1:0;
                $list[$key]['sportrait']=$this->poem_url.trim($value['sportrait'],'.');
            }
            $num = count ( $list );
            if ($num >= $count) {
                array_pop ( $list );
                $list ['hasmore'] = 1;
            } else {
                $list ['hasmore'] = 0;
            }
            return $list;
        }else{
            return "nodata";
        }
    }
    
    
    //榜单列表  $type 0年  1月  2周
    public function rank_list($uid,$type){
        $result=DB::table('flower_rank_list')->where('type',$type)->groupBy('name')->orderBy('addtime','desc')->get();
        foreach ($result as $key => &$value) {
            $value['url']=$this->url.$value['url'];
        }
        return $result;
    }
    
    public function rank_info($uid,$rankid,$count,$offSet){
        
        $result=DB::table('flower_rank_list')->where('id',$rankid)->first();
        $info=file_get_contents(public_path($result['url']));
        $info=unserialize($info);
        $rs=array_slice($info,$offSet,$count);
        return $this->adminlist($rs,$uid);
        
    }
    //将信息整合部分单独拿出来
    public function  adminlist($list,$id){
        $userlist="";
        $apicheck=new ApiCheckPermission();
        foreach ($list as $key => $value) {
            $userlist[$key]['fromid']=isset($value[0])?$value[0]:$value['fromid'];
            $userlist[$key]['num']=isset($value[1])?$value[1]:$value['num'];
            $user=DB::table('user')->select("nick",'grade','isleague','sportrait','gender')->where('id','=',$userlist[$key]['fromid'])->first();
            $userlist[$key]['nick']=$user['nick'];
            $userlist[$key]['gender']=$user['gender'];
            $userlist[$key]['grade']=$user['grade'];
            $userlist[$key]['isleague']=$user['isleague'];
            $userlist[$key]['sportrait']=$this->poem_url.trim($user['sportrait'],'.');
            $userlist[$key]['ismember']=$apicheck->isMember($value['fromid']);
            
            //1关注  0 未关注
            if($id==$userlist[$key]['fromid']){
                $userlist[$key]['relation']=4;//自己
            }else{
                
                $userlist[$key]['relation']= ApiCommonStatic::attentionStatus($id,$value['fromid']);
                
            }
        }
        return $userlist;
    }
    
    /**
    * 个人现有鲜花钻石及花费的鲜花钻石
    */
    public function costList( $id ){
        $rs= DB::table('user_asset_num')->where('uid',$id)->first(['get_flower as getflower' ,'cost_jewel as usejewel','flower as nowflower','jewel as nowjewel']);
        if(!$rs){
            $result['getflower']=0;
            $result['usejewel']=0;
            $result['nowflower']=0;
            $result['nowjewel']=0;
            return $result;
        }else{
            return $rs;
        }
    }
    
    
}