<?php
/**
** 作品模型
*/
class ApiOpus extends ApiCommon {
    
    // 作品上传
    public function uploadOpus() {
        $info = $this->viaCookieLogin ();
        if ($info) {
            $uid = $info ['id'];
            if (! Input::has ( 'poemId' ))
                return '原诗不存在';
            $poemId = intval(Input::get ( 'poemId' ));// 原诗id
            $opusName = trim ( Input::get ( 'opusName' ) ); // 作品名称
            if(my_sens_word($opusName))
            {
                return '题目中含有禁用词';
            }
            //针对自由诵读特殊处理
            // 			$nav_arr = array(21,86,87,88,89);
            // 			$nav_id = DB::table('navpoemrel')->where('poemid',$poemId)->lists('navid');
            // 			//交集
            // 			$intersect = array_intersect($nav_arr, $nav_id);
            // 			if(!empty($intersect)){
            // 				return '按照国家有关部门要求，“自由诵读”功能暂停使用！';
            // 			}
            
            if (empty ( $opusName )) {
                return '作品名称不能为空';
            }
            // 参赛 '1,2,3',如果是夏青杯，检测权限等
            $competitionId = intval(Input::get ( 'competitionId' ));
            if (! empty ( $competitionId )) {
                $apiCheckPermission = new ApiCheckPermission();
                $flag = $apiCheckPermission->check_permission($competitionId);
                // $apiCompetition = new ApiCompetition ();
                // $flag = $apiCompetition->dif_check_competition ( $competitionId );
                if ($flag ['status'] != 1) {
                    return $flag ['message'];
                }
            }
            // 对自由朗诵或者改编朗诵中的'版'字特殊处理
            $replace_str = '版';
            if (mb_strpos ( $opusName, $replace_str ) !== false) {
                $opusName = str_replace ( $replace_str, '', $opusName );
            }
            $str = "伴奏";
            if (mb_strpos ( $opusName, $str ) !== false) {
                if (mb_strpos ( $opusName, '·' )) {
                    $tmpName = explode ( '·', $opusName );
                    $opusName = '自由诵读·' . array_pop ( $tmpName );
                }
            }
            //根据伴奏id，特殊处理诗文比赛作品名
            if($poemId >=100000001 ){
                $opusName = '自由诵读·'.$opusName;
            }
            
            $pinyinName = '';
            $pattern = '/([\x{4e00}-\x{9fa5}a-zA-Z0-9])/u';
            preg_match_all ( $pattern, $opusName, $res );
            if (! empty ( $res )) {
                $pinyinName = implode ( "", $res [0] );
            }
            
            if (! Input::has ( 'opusTime' ))
                return '作品时长不符合规则';
            $opusTime = Input::get ( 'opusTime' );
            // 暂留禁用语功能
            if (! Input::has ( 'lyricurl' ))
                return '原始作品不存在';
            $lyricPath = Input::get ( 'lyricurl' ); // 原歌词地址,绝对地址
            $tmpArr = explode ( '/', $lyricPath );
            $dataLyricPath = '/' . implode ( array_slice ( $tmpArr, 3 ), '/' );
            $type = 0; // 标识是否是改词
            if (Input::get ( 'lyric' )) { // 修改后的歌词
                //进行敏感词过滤
                $lyric_content = Input::get('lyric');
                if(my_sens_word($lyric_content))
                {
                    return '由于作品中涉嫌包含敏感词导致无法上传！敬请谅解！';
                }
                $lyric = htmlspecialchars ( $lyric_content );
                $lyricDir = $this->isExistDir ( 'lyric' );
                $lyricName = time () . uniqid () . '.lrc';
                $lyricPath = $lyricDir . $lyricName;
                $dataLyricPath = ltrim ( $lyricPath, '.' );
                if (! file_put_contents ( $lyricPath, $lyric )) {
                    return '作品上传失败，请重试';
                }
                $type = 1;
            }
            // 作品上传
            $arr = Input::file ( 'formName' );
            if (! empty ( $arr )) {
                // 判断作品类型，只能是MP3格式
                $my_file_type = my_file_type ( $arr->getRealPath () );
                if (empty ( $my_file_type ) || strtolower ( $my_file_type ) != 'mp3') {
                    return '请上传正确文件类型';
                }
                $ext = 'mp3';
                $oName = time () . uniqid ();
                $name = $oName . '.' . $ext;
                $opusDir = $this->isExistDir ( 'poem' );
                $lastOpusPath = $opusDir . $name;
                $dataOpusPath = ltrim ( $lastOpusPath, '.' );
                $arr->move ( $opusDir, $name );
            } else {
                return '作品上传失败，请重试';
            }
            include './../app/commands/PinYin.php';
            $pinYin = new Pinyin ();
            $firstchar = @$pinYin->getFirstChar ( $pinyinName );
            $allchar = @$pinYin->getPinyin ( $pinyinName );
            $tmpPyArr = explode ( ' ', $allchar );
            $str = null;
            foreach ( $tmpPyArr as $k => $v ) {
                if (empty ( $v )) {
                    continue;
                }
                $str .= substr ( $v, 0, 1 );
            }
            //选出伴奏读者，写者
            if($poemId >= 100000001){
                try {
                    $writer_uid  = DB::table('opus_poetry')->where('id',$poemId)->pluck('uid');
                    $writer = DB::table('user')->where('id',$writer_uid)->pluck('nick');
                } catch (Exception $e) {
                }
            }else{
                $writer = DB::table('poem')->where('id',$poemId)->pluck('writername');
            }
            $writer = !empty($writer) ? $writer : '佚名';
            // 作品入库
            $time = time ();
            $data = array (
            'uid' => $uid,
            'name' => $opusName,
            'firstchar' => $firstchar,
            'pinyin' => $str,
            'lyricurl' => $dataLyricPath,
            'poemId' => $poemId,
            'url' => $dataOpusPath,
            'opustime' => $opusTime,
            'type' => $type,
            'addtime' => $time,
            'reader'=>$info['nick'],
            'writer'=>$writer,
            );
            $opusId = DB::table ( 'opus' )->insertGetId ( $data );
            if ($opusId) {
                // 作品数+1,最新作品修改名称
                DB::table('user')->where('id',$uid)->increment('opusnum',1,array('opusname'=>$opusName));
                // 作品分类操作,来源为普通伴奏poemId<100000001，插入分类，否则poemId>=100000001插入自由诵读
                $this->insertCat ( $poemId, $opusId,$uid );
                //增加作品收听数
                $this->incLisNum($opusId);
            } else {
                return '作品上传失败，请重试';
            }
            $data ['id'] = $opusId;
            $data ['lyricurl'] = $this->poem_url . $dataLyricPath;
            $data ['url'] = $this->poem_url . $dataOpusPath;
            
            // 定制听中增加作品
            $arr = array (
            'uid' => $uid,
            'opusid' => $opusId,
            'opustype' => 0,
            'repuid' => $uid,
            'isdel' => 0,
            // 'isself'=>1,
            'addtime' => time ()
            );
            DB::table ( 'personalcustom' )->insert ( $arr );
            if (! empty ( $competitionId )) {
                $tmpStr = trim ( $competitionId );
                $tmpRs = explode ( ',', $tmpStr );
                $sql = "insert into competition_opus_rel (competitionid,opusid,uid) values ";
                $tmpStr = ' ';
                foreach ( $tmpRs as $k => $v ) {
                    $tmpStr .= '(' . $v . ',' . $opusId . ',' . $uid . '),';
                }
                $sql .= rtrim ( $tmpStr, ',' );
                DB::insert ( $sql );
            }
            //同步ES用户
            $apiEsSearch = App::make('apiEsSearch');
            $apiEsSearch->addEsOpus(['id'=>$data['id'],'name'=>$data['name'],'pinyin'=>$data['pinyin']]);
            
            return $data;
        } else {
            return 'nolog';
        }
    }
    
    // 我 or 他的作品列表
    public function getOpusList() {
        $count = ! empty ( Input::get ( 'count' ) ) ? intval(Input::get ( 'count' )) : 20;
        $pageIndex = ! empty ( Input::get ( 'pageIndex' ) ) ? intval(Input::get ( 'pageIndex' )) : 1;
        $offSet = ($pageIndex - 1) * $count;
        ++ $count;
        $userid = 0;
        $info = $this->viaCookieLogin ();
        
        if (! empty ( $info ))
            $userid = $info ['id'];
        // 他人作品列表
        if (Input::get ( 'otherId' ) !=0 ) {
            $uid = intval(Input::get ( 'otherId' ));
        } else {
            if (! $info)return 'nolog';
            $uid = $info ['id'];
        }
        //置顶数据
        $top= DB::table('opus_param')->where('uid',$uid) ->where('istop',"=",1)->first();
        
        //有数据修改数据分页 就第一也第一个是置顶数据
        if($top){
            if($pageIndex==1){
                $top_opus=DB::table('opus')->where('uid',$uid)->where('isdel','=',0)->where('id',$top['opusid'])->first();
                $rs = DB::table('opus')->where('uid',$uid)->where('id',"<>",$top['opusid'])->where('isdel','=',0) ->orderBy('id','desc')->skip($offSet)->take($count)->get();
                array_unshift($rs,$top_opus);
            }else{
                $rs = DB::table('opus')->where('uid',$uid)->where('isdel','=',0)->orderBy('id','desc')->skip($offSet)->take($count)->get();
            }
        }else{
            $rs = DB::table('opus')->where('uid',$uid)->where('isdel','=',0)->orderBy('id','desc')->skip($offSet)->take($count)->get();
        }
        
        if (!empty ( $rs )) {
            // 获取登录用户收藏的所有作品
            $collection_opus = ApiCommonStatic::isCollection_v2 ( $userid );
            // 获取登录用户赞过的所有作品
            $praise_opus = ApiCommonStatic::isPraise_v2 ( $userid );
            $opus_array=array();
            
            foreach ( $rs as $key => &$value ) {
                if($value['id']==$top['opusid']){
                    $value['istop']=1;
                }else{
                    $value['istop']=0;
                }
                $value ['lyricurl'] = $this->poem_url.$value ['lyricurl'];
                $value ['url'] = $this->poem_url . $value ['url'];
                $value ['colStatus'] = in_array ( $value ['id'], $collection_opus ) ? 1 : 0; // 是否收藏0没有1有
                $value ['praStatus'] = in_array ( $value ['id'], $praise_opus ) ? 1 : 0; // 是否赞0没有1有
                $opus_array[]=$value['id'];
            }
            
            //花数
            $opus_param=DB::table('opus_param')->whereIn("opusid",$opus_array)->get();
            $flowers=array();
            foreach($opus_param as $k =>$v){
                $flowers[$v['opusid']]=$v['flower'];
            }
            
            foreach( $rs as $k =>$v){
                $rs[$k]['flower']=isset($flowers[$v['id']] )? intval($flowers[$v['id']]):0;
            }
            unset ( $value );
            $num = count ( $rs );
            if ($num >= $count) {
                array_pop ( $rs );
                $rs ['hasmore'] = 1;
            } else {
                $rs ['hasmore'] = 0;
            }
            return $rs;
        }
        return $rs;
    }
    // 删除作品
    public function delOpus() {
        $info = $this->viaCookieLogin ();
        
        if (! $info)
            return 'nolog';
        $uid = $info ['id'];
        if (! Input::has ( 'opusId' ))
            return '没有此作品';
        $opusId = intval(Input::get ( 'opusId' ));
        // 判断是否有权限删除作品
        if (! $this->deletePermission ( $opusId, 'opus', $uid )){
            return '没有权限删除此作品';
        }elseif (! ApiCommonStatic::delOpus ( $uid, $opusId ))
        {
            return '删除失败,请重试';
        }
        return true;
        
    }
    /**
    * 收听作品，将收听数量限制放入redis
    * @author:wang.hongli
    * @since:2016/04/07
    */
    public function opusListen(){
        $info = $this->viaCookieLogin();
        if($info){
            $uid = intval($info['id']);
            if(!Input::has('opusId')) return '作品不存在';
            $opusId = intval(Input::get('opusId'));
            $redisOpusListen = new RedisOpusListen();
            $flag = $redisOpusListen->opusListen($uid,$opusId);
            if($flag){
                // 				return '此作品收听次数过多,明天再来！';
                return true;
            }else{
                //增加个人作品收听次数
                $this->incLisNum($opusId);
                return true;
            }
        }else{
            return 'nolog';
        }
    }
    
    // 根据作品id获取作品信息
    public function accorOpusIdGetInfo() {
        $info = $this->viaCookieLogin ();
        $errorMsg = '获取信息失败';
        
        if(empty($info)) return $errorMsg;
        $uid = $info['id'];
        if(!Input::has('opusId')) return $errorMsg;
        $opusId = intval(Input::get('opusId'));
        $opus_info = DB::table('opus')->where('id',$opusId)->where('isdel','<>',1)->first(array('id','uid','name','lnum','praisenum','repostnum','commentnum','sharenum','lyricurl','poemid','url','opustime','addtime'));
        if(empty($opus_info)) return $errorMsg;
        $user_info = DB::table('user')->where('id',$opus_info['uid'])->where('isdel','<>',1)->first(array('nick','portrait','sportrait','gender','grade','authtype','teenager','isleague'));
        if(empty($user_info)) return $errorMsg;
        //花数
        $opus_param=DB::table('opus_param')->where("opusid",$opusId)->first();
        $opus_info['flower']=$opus_param['flower']?intval($opus_param['flower']):0;
        $user_info['sportrait'] = $this->poem_url.ltrim($user_info['sportrait'],'.');
        $user_info['portrait'] = $this->poem_url.ltrim($user_info['portrait'],'.');
        $opus_info['lyricurl'] = $this->poem_url.ltrim($opus_info['lyricurl'],'.');
        $opus_info['url'] = $this->poem_url.ltrim($opus_info['url'],'.');
        $opus_info['colStatus'] = $this->isCollection($uid, $opusId);
        $opus_info['praStatus'] = $this->isPraise($uid, $opusId);
        $rs = array_merge($opus_info,$user_info);
        return $rs;
        
    }
    
    // 将作品导航分类
    protected function insertCat($poemId, $opusId,$uid) {
        if (empty ( $poemId ) || empty ( $opusId ) || empty($uid))
            return;
        $poemId = intval($poemId);
        $opusId = intval($opusId);
        //如果poemId>=100000001 来源为诗文比赛，插入自由诵读
        $time = time ();
        // 		if($poemId>=100000001){
        // 			DB::table('navopusrel')->insert(array('categoryid'=>21,'opusid'=>$opusId,'poemid'=>$poemId,'addtime'=>$time));
        // 			return;
        // 		}
        // 根据原诗id查找导航分类
        $rs = DB::table('navpoemrel')->where('poemid',$poemId)->lists('navid');
        if(!empty($rs)){
            foreach($rs as $k=>$v){
                $tmp_table_id = $v%10;
                DB::table('nav_opus_table_id')->insert(array('opusid'=>$opusId,'table_id'=>$tmp_table_id));
                $table_name = 'nav_opus_'.$tmp_table_id;
                DB::table($table_name)->insert(array('uid'=>$uid,'opusid'=>$opusId,'categoryid'=>$v,'commentnum'=>0,'lnum'=>1,'repostnum'=>0,'praisenum'=>0,'addtime'=>$time,'poemid'=>$poemId));
            }
        }
        return;
    }
    
    // 收听作品，作品收听次数+1 人总收听次数+1
    public function incLisNum($opusId = 0) {
        if (empty ( $opusId ))
            return false;
        $opusId = intval ( $opusId );
        $uid = DB::table('opus')->where('id',$opusId)->pluck('uid');
        if (empty ( $uid ))
            return false;
        try {
            DB::table('user')->where('id',$uid)->increment('lnum',1);
            DB::table('opus')->where('id',$opusId)->increment('lnum',1);
            //根据分类分表中作品收听数增加
            $this->lnumNavOpus($opusId);
            //user_league 朗诵会会员表中收听数增加
            DB::table('league_user')->where('uid',$uid)->increment('lnum',1);
            // 判断等级
            $this->setUserGrade ( $uid );
        } catch (Exception $e) {
        }
        
    }
    /**
    * 根据分类分表中作品收听数增加
    * @author:wang.hongli
    * @since:2016/04/16
    */
    protected  function lnumNavOpus($opusId){
        $table_ids = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$opusId)->lists('table_id');
        if(empty($table_ids)) return;
        foreach($table_ids as $k=>$v){
            $table_name = 'nav_opus_'.$v;
            try {
                DB::table($table_name)->where('opusid',$opusId)->increment('lnum');
                //当周 当月  当前  ++1
                DB::table("opus_now_lnum")->where('opusid',$opusId)->increment('nowmonthnum','nowweeknum','nowyearnum');
            } catch (Exception $e) {
            }
        }
    }
    
    // 第三方转发成功后作品转发数增加，人的转发数增加
    public function successShareInNum() {
        $info = $this->viaCookieLogin ();
        if (! empty ( $info )) {
            $return = '转发失败,请重试';
            $uid = $info ['id'];
            $opusid = intval(Input::get ( 'opusid' ));
            if (empty ( $opusid ))
                return $return;
            // 根据作品id找出人的id
            $userId = DB::table('opus')->where('id','=',$opusid)->pluck('uid');
            if (empty ( $userId ))
                return $return;
            // 作品转发数+1
            try {
                DB::table('opus')->where('id',$opusid)->increment('repostnum');
                DB::table('user')->where('id',$userId)->increment('repostnum');
                //按照导航分表中数据转发数增加
                $this->shareNumNavOpus($opusid);
                //league_user  朗诵会会员冗余表，转发数增加
                DB::table('league_user')->where('uid',$userId)->increment('repostnum',1);
            } catch (Exception $e) {
                return $return;
            }
            return true;
        } else {
            return 'nolog';
        }
    }
    
    /**
    * 第三方转发成功后-导航分表中的数据转发数增加
    * @author:wang.hongli
    * @since:2016/05/16
    */
    public  function shareNumNavOpus($opusId){
        $table_id = DB::table('nav_opus_table_id')->distinct('table_id')->where('opusid',$opusId)->lists('table_id');
        if(empty($table_id)) return;
        foreach($table_id as $k=>$v){
            $table_name = 'nav_opus_'.$v;
            try {
                DB::table($table_name)->where('opusid',$opusId)->increment('repostnum');
            } catch (Exception $e) {
            }
        }
        
    }
    // 举报作品
    public function report() {
        $return = 0;
        $opusid = intval(Input::get('opusId'));
        $reason = htmlspecialchars(Input::get('reason'));
        if (empty ( $opusid ) || empty ( $reason ))
            return $return;
        $time = time ();
        try {
            $info = $this->viaCookieLogin();
            $fromid = !empty($info['id']) ? $info['id'] : 0;
            $from_nick = !empty($info['nick']) ? $info['nick'] : '';
            $data = array(
            'opusid'=>$opusid,
            'reason'=>$reason,
            'status'=>0,
            'addtime'=>$time,
            'fromid'=>$fromid,
            'from_nick'=>$from_nick
            );
            $insert_flag = DB::table('reportOpus')->insert($data);
            if($insert_flag){
                $return = 1;
            }
        } catch (Exception $e) {
        }
        return $return;
    }
    /**
    * 作品置顶
    * @author:hgz
    * @since:2016/07/07
    * get传作品id
    */
    public function totop(){
        $info = $this->viaCookieLogin ();
        if(empty($info['id'])) return 'nolog';
        //查找历史
        $opus_id=Input::get('id',0);
        if(empty($opus_id)) return 'noid';
        $top= DB::table('opus_param')->where('uid','=',$info['id'])->where('istop',"=",1)->first();
        if(empty($top)){
            //判断作品id是否在表中
            $flag=DB::table('opus_param')->where('uid','=',$info['id'])->where('opusid',"=",$opus_id)->first();
            if($flag){
                $a=DB::table('opus_param')->where('uid','=',$info['id'])->where('opusid','=',$opus_id)->update(array("istop"=>1));
                if($a){
                    return true;
                }else{
                    return false;
                }
            }else{
                $a=DB::table('opus_param')->insert(array('uid'=>$info['id'],"opusid"=>$opus_id,'flower'=>0,'istop'=>1));
                if($a){
                    return true;
                }else{
                    return false;
                }
            }
        }else{
            $c=DB::table('opus_param')->where('uid','=',$info['id'])->where('id',"=",$top['id'])->update(array("istop"=>0));
            
            $flag=DB::table('opus_param')->where('uid','=',$info['id'])->where('opusid',"=",$opus_id)->first();
            if($flag){
                $d=DB::table('opus_param')->where('uid','=',$info['id'])->where('opusidd','=',$opus_id)->update(array("istop"=>1));
                if($c && $d){
                    return true;
                }else{
                    return false;
                }
            }else{
                $d=DB::table('opus_param')->insert(array('uid'=>$info['id'],"opusid"=>$opus_id,'flower'=>0,'istop'=>1));
                if($c && $d){
                    return true;
                }else{
                    return false;
                }
            }
            
        }
    }
}