<?php 
/**
*	后台赛事管理
*	@author:wang.hongli
*	@since:2014/11/1
**/
class CompetitionController extends BaseController
{
	/**
	*	获取赛事列表
	*	@author:wang.hongli
	*	@since:2014/11/1
	**/
	public function getCompetitionList()
	{	
		$arr = array(0=>'其他朗诵会',1=>'官方朗诵会',2=>'社团朗诵会',3=>'名人朗诵会',4=>'高端赛事',5=>'普通赛事',6=>'诗文高端赛事',7=>'诗文普通赛事');
		$monArr = array(0=>'否',1=>'是');//是否有月榜
		$name = !empty(Input::get('name')) ? Input::get('name') : '';
		$where = '';
		if(!empty($name))
		{
			$where = ' and name like "%'.$name.'%"';
		}
		$pid = !empty(Input::get('pid')) ? Input::get('pid') : 0;
		if($pid>-1){
			$where = " and pid={$pid}";
		}
		
		$sql = "select * from competitionlist where 1 {$where} order by id desc";
		$rs = DB::select($sql);
		if(!empty($rs))
		{
			foreach($rs as $key=>&$value)
			{	
				$value['pid'] = $arr[$value['pid']];
				$value['monthflag'] = $monArr[$value['monthflag']];
			}
		}
		
		//收费赛事
		$price = array();
		$sql = "select * from goods where competition_id>0";
		$rlt = DB::select($sql);
		foreach($rlt as $v){
			$price[$v['competition_id']]="价格：".$v['price']."元";
		}
		
		return View::make('competition.competition')->with('compeition',$rs)->with('search',array('name'=>$name,'pid'=>$pid,'price'=>$price));
	}

	/**
	*	夏青杯作品列表
	*	@author:wang.hongli
	*	@since:2015/04/29
	**/
	public function getSummCupOpusList()
	{
		$data = array();
		$conn = DB::table('competition_opus_rel')
				->leftJoin('opus','competition_opus_rel.opusid','=','opus.id')
				->leftJoin('user','competition_opus_rel.uid','=','user.id')
				->select('opus.id','opus.uid','opus.name','opus.lnum','opus.praisenum','opus.repostnum','opus.commentnum','opus.downnum','opus.opustime','opus.addtime','user.nick','opus.url')
				->where('competition_opus_rel.competitionid','=',22)
				->where('opus.isdel','=',0);
		
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('competition_opus_rel.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('opus.name','like','%'.$data['name'].'%');
		}
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		
	
		$data['total']=$conn->count();
		$relList = $conn->orderBy('competition_opus_rel.opusid','desc')->paginate(20);
		
		return View::make('competition.summcupopuslist')->with('relList',$relList)->with('data',$data);
	}
	//导出夏青杯作品列表xls
	public function summCupOpusXls(){
		$data = array();
		$conn = DB::table('competition_opus_rel')
				->leftJoin('opus','competition_opus_rel.opusid','=','opus.id')
				->leftJoin('user','competition_opus_rel.uid','=','user.id')
				->select('opus.id','opus.uid','opus.name','opus.lnum','opus.praisenum','opus.repostnum','opus.commentnum','opus.downnum','opus.opustime','opus.addtime','user.nick','opus.url')
				->where('competition_opus_rel.competitionid','=',22)
				->where('opus.isdel','=',0);
		
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('competition_opus_rel.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('opus.name','like','%'.$data['name'].'%');
		}
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		
		$list = $conn->orderBy('competition_opus_rel.opusid','desc')->get();
		
		$data=array();
		foreach($list as $v){
			//array('作品ID','作品名称','用户ID','用户昵称','收听数','赞数','转发数','评论数','下载数','时长','发布时间','作品url地址');			
			$tmp=array();
			$tmp[]=$v['id'];
			$tmp[]=$v['name'];
			$tmp[]=$v['uid'];
			$tmp[]=$v['nick'];
			$tmp[]=$v['lnum'];
			$tmp[]=$v['praisenum'];
			$tmp[]=$v['repostnum'];
			$tmp[]=$v['commentnum'];
			$tmp[]=$v['downnum'];
			$tmp[]=$v['opustime'];
			$tmp[]=date("Y-m-d H:i",$v['addtime']);
			$tmp[]="http://www.weinidushi.com.cn".$v['url'];
			$data[]=$tmp;
		}
		
		//生成xls文件==========================================
		require_once ("../app/ext/PHPExcel.php");
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
		  
		//设置当前表
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('夏青杯作品列表');		
		//设置第一行内容
		$sheetTitle=array('作品ID','作品名称','用户ID','用户昵称','收听数','赞数','转发数','评论数','下载数','时长','发布时间','作品url地址');	
		$cNum=0;
		foreach($sheetTitle as $val){
		  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
		  $cNum++;
		}
		
		$rNum=2;
		foreach($data as $val){
		  $cNum=0;
		  foreach($val as $row){
			  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
			  $cNum++;
		  }
		  $rNum++;
		}
		  
		$outputFileName = "summcupopuslist.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		echo "<a href='http://www.weinidushi.com.cn/upload/".$outputFileName."'>下载</a>";
		
	}

	/**
	*	添加赛事
	*	@author:wang.hongli
	*	@since:2014/11/1
	**/
	public function addCompetition()
	{
		$arr = Input::file();
		if(!empty($arr))
		{	
			$count = 0;
			foreach($arr as $k=>$v)
			{
				if(!empty($v))
				{
					$count++;
				}
			}
			$filePath = './upload/competition/';
			$name = trim($_POST['name']);
			$pid = $_POST['pid'];
			$sort = $_POST['sort'];
			$clause = $_POST['clause'];
			$name_short = $_POST['name_short'];
			$clause_title = $_POST['clause_title'];
			
			$has_invitation=$_POST["has_invitation"];
			$code_content=$_POST["code_content"];
			
			if(!empty(Input::get('starttime')))
			{
				$starttime = strtotime(Input::get('starttime'));
			}
			else
			{
				$starttime = strtotime(date('Y-m-d',time()));
			}
			$endtime = strtotime($_POST['endtime'])+86399;
			$monthflag = $_POST['monthflag'];
			//上传图片
			$piclist = '';
			$mainpic = '';
			for($i=0;$i<$count;$i++)
			{
				$pic = 'pic'.$i;
				$tmpPic = Input::file($pic);
				$ext = $tmpPic->guessExtension();
				$imgName = time().uniqid();
				$imgName = $imgName.'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$tmpPic->move($filePath,$imgName);
				$lastFilePath = ltrim($lastFilePath,'.');
				if($i==0)
				{
					$mainpic = $lastFilePath;
				}
				else
				{
					$piclist .= $lastFilePath.';';
				}
			}
			$piclist = rtrim($piclist,';');
			/*$sql = "insert into competitionlist (name,mainpic,piclist,pid,sort,starttime,endtime,monthflag) values ('{$name}','{$mainpic}','{$piclist}','{$pid}',$sort,$starttime,$endtime,$monthflag)";
			DB::insert($sql);*/
			$data = array(
				'name'=>$name,
				'mainpic'=>$mainpic,
				'piclist'=>$piclist,
				'pid'=>$pid,
				'sort'=>$sort,
				'starttime'=>$starttime,
				'endtime'=>$endtime,
				'monthflag'=>$monthflag,
				'name_short'=>$name_short,
				'has_invitation'=>$has_invitation,
				'code_content'=>$code_content,
				'clause_title'=>$clause_title,
				'clause'=>$clause
			);
			$id = DB::table('competitionlist')->insertGetId($data);
			if($id>0){
				if($pid == 4 || $pid == 6 || $pid==7){
					$goods_name=$_POST['goods_name'];
					$goods_price=(int)$_POST['goods_price'];
					$sql="insert into goods (name,price,type,description,competition_id) values ('{$goods_name}','{$goods_price}',4,'{$goods_name}','{$id}')";
					DB::insert($sql);
				}
			}
		}	
		return View::make('competition.addcompetition');
	}
	
	/*
	* 修改赛事图片
	*/
	public function updateCompetition(){
		$id = (int)$_GET["id"];
		$sql = "select * from competitionlist where id='".$id."'";
		$info = DB::table('competitionlist')->where('id','=',$id)->first();
		$piclist = !empty($info['piclist']) ? explode(";",$info['piclist']) : array();
		$mainpic=$info['mainpic'];
		if(!empty($_FILES)){
			$filePath = './upload/competition/';
			for($i=0;$i<7;$i++){
				$pic = 'pic'.$i;
				$tmpPic = Input::file($pic);
				if(!empty($tmpPic)){
					$ext = $tmpPic->guessExtension();
					$imgName = time().uniqid().'.'.$ext;
					$lastFilePath = $filePath.$imgName;
					$tmpPic->move($filePath,$imgName);
					$piclist[$i] = ltrim($lastFilePath,'.');
				}
			}
			$_piclist = implode(";",$piclist);
			$name =  Input::get('name');
			$name_short =  Input::get('name_short');
			$starttime =  strtotime(Input::get('starttime'));
			$endtime =  strtotime(Input::get('endtime'))+86399;
			$has_invitation= Input::get('has_invitation');
			$code_content= Input::get('code_content');
			$sort= (int)Input::get('sort');
			//主图处理			
			if(Input::file('mainpic')){
				$tmpPic = Input::file('mainpic');
				$ext = $tmpPic->guessExtension();
				$imgName = time().uniqid().'.'.$ext;
				$lastFilePath = $filePath.$imgName;
				$tmpPic->move($filePath,$imgName);
				$mainpic = ltrim($lastFilePath,'.');
			}
			
			$sql = "update  competitionlist set name='".$name."',name_short='".$name_short."',piclist='".$_piclist."',starttime='".$starttime."',endtime='".$endtime."',mainpic='".$mainpic."',has_invitation='".$has_invitation."',code_content='".$code_content."',sort='".$sort."' where id=".$id;
			DB::update($sql);
			//echo $sql;
		}
		
		return View::make('competition.updatecompetition',array('info'=>$info,'piclist'=>$piclist,'mainpic'=>$mainpic));
	}
	public function delCompetitionPic(){
		$id=(int)$_GET["id"];
		$index=(int)$_GET["index"];
		$info = DB::table('competitionlist')->where('id','=',$id)->first();
		$piclist = !empty($info['piclist']) ? explode(";",$info['piclist']) : array();
		if(isset($piclist[$index])){
			unset($piclist[$index]);
			$_piclist = implode(";",$piclist);
			$sql="update  competitionlist set piclist='".$_piclist."' where id=".$id;
			DB::update($sql);
			echo 1;
		}
	}

	/**
	*	ajax结束比赛
	*	@author:wang.hongli
	*	@since:2014/12/07
	*/
	public function finishCompetition()
	{
		$id = (int)Input::get('id');
		$isfinish = (int)Input::get('isfinish');
		if(empty($id))
		{
			echo -1;
			return;
		}
		$isfinish = !empty($isfinish) ? 0 : 1;
		return DB::update('update competitionlist set isfinish = ? where id = ?',array($isfinish,$id)) ? 1 : -1;
	}

	/**
	*	ajax 置顶比赛
	*	@author:wang.hongli
	*	@since:2014/12/07
	**/
	public function makeTop()
	{
		$id = (int)Input::get('id');
		if(empty($id))
		{
			echo -1; 
			return;
		}
		$sql = "update competitionlist set `sort` = 1 where id = {$id}";
		if(DB::update('update competitionlist set sort = ? where id = ?',array(1,$id)))
		{
			return DB::update('update competitionlist set sort = ? where id != ?',array(0,$id)) ? 1 : -1;
		}
	}

	/**
	*	ajax 删除作品
	*	@author:wang.hongli
	*	@since:2015/05/01
	*/
	public function admin_del_opus()
	{
		$uid = Input::get('uid');
		$opusid = Input::get('opusid');
		if(empty($uid) || empty($opusid))
		{
			echo 'error';
			return;
		}
		//删除作品
		if(!ApiCommonStatic::delOpus($uid,$opusid)) 
		{
			echo 'error';
			return;
		}
	}

	/**
	*	诵读联盟 申请列表 : 
	*	@author:wang.hongli
	*	@since:2015/05/01
	*/
	public function admin_league_list()
	{
		$data = array();
		$conn = DB::table('order_list')
				->select('league.id','league.uid','league.nick_name','league.card','league.name','company','address','zip','league.mobile','league.email','cause','league.status','order_list.addtime','order_list.audit_time','order_list.orderid','user.nick','user.gender')
				->leftJoin('league','league.uid','=','order_list.uid')
				->leftJoin('user','user.id','=','order_list.uid')
				->where('order_list.status','=',2)
				// ->where('order_list.addtime','>',1463122689)
				->where('order_list.goods_id','=',1);
		
		$data['status']=isset($_GET['status'])?Input::get('status'):-1;
		if($data['status']!=-1){
			$conn->where('league.status','=',$data['status'])->where('order_list.audit_status',$data['status']);
		}
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('league.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('league.name','like','%'.$data['name'].'%');
		}
		$data['email']=Input::get('email');
		if(!empty($data['email'])){
			$conn->where('league.email','=',$data['email']);
		}
		$data['mobile']=Input::get('mobile');
		if(!empty($data['mobile'])){
			$conn->where('league.mobile','=',$data['mobile']);
		}
		
		$data['sdate']=Input::get('sdate');
		if(!empty($data['sdate'])){
			$conn->where('order_list.addtime','>=',strtotime($data['sdate']));
		}
		
		$data['edate']=Input::get('edate');
		if(!empty($data['edate'])){
			$conn->where('order_list.addtime','<',strtotime($data['edate']));
		}
		
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		
		//统计多次
		$data['grouby']=Input::get('grouby');
		if(!empty($data['grouby'])){
			$uids=array();
			$sql="select uid from order_list where status=2 and goods_id=1 GROUP BY uid having count(uid)>1";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$uids[]=$v["uid"];
			}
			$conn->whereIn('order_list.uid',$uids);
			$conn->orderBy('order_list.uid','desc');
		}
		$data['total']=$conn->count();
		$leagueList = $conn->orderBy('order_list.id','desc')->paginate(20);
		//用户作品数量
		$user_num = array();
		if($data['total']>0){
			$uids=array();
			foreach($leagueList as $v){
				$uids[$v['uid']]=$v['uid'];
			}
			$sql="SELECT uid,count(uid) as num from opus where uid in (".implode(",",$uids).") and isdel=0 GROUP BY uid";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$user_num[$v['uid']]=$v['num'];
			}
		}
		//自动通过审核用户
		$order_ids = DB::table('league_auto_pass')->where('type',0)->lists('order_id');
		if(empty($order_ids)){
			$order_ids = [];
		}
		$data['user_num']=$user_num;
			
		return View::make('competition.adminleaguelist')->with('leagueList',$leagueList)->with('data',$data)->with('order_ids',$order_ids);
	}

	/**
	*	诵读联盟用户列表--审核功能
	*	@author:wang.hongli
	*	@since:2015/05/11
	**/
	public function pass_league()
	{
		$orderid = Input::get('orderid');
		if(empty($orderid))
		{
			echo -1;
			return;
		}
		//根据用户订单数，增加朗诵联盟时间
		$order_info = DB::table('order_list')
		    ->where('orderid','=',$orderid)
		    ->where('status','=',2)
		    ->where('audit_status','=',0)
		    ->first();
		$num = 1;
		if(empty($order_info))
		{
			echo -2;
			return;
		}
		//修改数据库状态
		DB::table('order_list')->where('orderid','=',$orderid)->update(array('audit_status'=>2,'audit_time'=>time()));
		// DB::table('league')->where('uid','=',$uid)->update(array('status'=>2));
		$rs = DB::table('user_permission')->where('uid','=',$order_info['uid'])->where('type','=',1)->first();
		$time = time();
		$add_time = $num*86400*365;
		$over_time = $time + $add_time;
		if($rs)
		{
			$sql = "update user_permission set over_time=over_time+{$add_time},update_time={$time} where uid={$order_info['uid']} and type=1";
			DB::update($sql);
		}
		else
		{
			DB::table('user_permission')->insert(array('uid'=>$order_info['uid'],'type'=>1,'over_time'=>$over_time,'update_time'=>$time,'good_id'=>1));
		}
		DB::table('user')->where('id','=',$order_info['uid'])->update(array('isleague'=>1));
		//将数据插入到league_user联合会冗余表中
		$user_info = DB::table('user')->where('id',$order_info['uid'])->first(array('praisenum','lnum','repostnum'));
		//如果不存在插入
		$id = DB::table('league_user')->where('uid',$order_info['uid'])->pluck('id');
		if(empty($id)){
			Db::table('league_user')->insert(array('id'=>0,'uid'=>$order_info['uid'],'praisenum'=>$user_info['praisenum'],'lnum'=>$user_info['lnum'],'repostnum'=>$user_info['repostnum'],'addtime'=>time()));
		}
		return;
	}
	
	//导出朗诵会的xls文件
	public function leagueXls(){
		$data = array();
		$conn = DB::table('order_list')
				->select('league.id','league.uid','league.nick_name','league.card','league.name','company','address','zip','league.mobile','league.email','cause','league.status','order_list.addtime','order_list.audit_time','user.nick','user.gender','league.province_id','league.city_id','league.area_id')
				->leftJoin('league','league.uid','=','order_list.uid')
				->leftJoin('user','user.id','=','order_list.uid')
				->where('order_list.status','=',2)
				->where('order_list.goods_id','=',1);
		$data['status']=isset($_GET['status'])?Input::get('status'):-1;
		if($data['status']!=-1){
			$conn->where('league.status','=',$data['status']);
		}
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('league.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('league.name','like','%'.$data['name'].'%');
		}
		$data['email']=Input::get('email');
		if(!empty($data['email'])){
			$conn->where('league.email','=',$data['email']);
		}
		$data['mobile']=Input::get('mobile');
		if(!empty($data['mobile'])){
			$conn->where('league.mobile','=',$data['mobile']);
		}
		$data['sdate']=Input::get('sdate');
		if(!empty($data['sdate'])){
			$conn->where('order_list.addtime','>=',strtotime($data['sdate']));
		}
		$data['edate']=Input::get('edate');
		if(!empty($data['edate'])){
			$conn->where('order_list.addtime','<',strtotime($data['edate']));
		}
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		//统计多次
		$data['grouby']=Input::get('grouby');
		if(!empty($data['grouby'])){
			$uids=array();
			$sql="select uid from order_list where status=2 and goods_id=1 GROUP BY uid having count(uid)>1";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$uids[]=$v["uid"];
			}
			$conn->whereIn('order_list.uid',$uids);
			$conn->orderBy('order_list.uid','desc');
		}
		
		$leagueList = $conn->orderBy('order_list.addtime','desc')->get();
		
		//用户作品数量
		$user_num = array();
		if(!empty($leagueList)){
			$uids=array();
			foreach($leagueList as $v){
				$uids[$v['uid']]=$v['uid'];
			}
			$sql="SELECT uid,count(uid) as num from opus where uid in (".implode(",",$uids).") and isdel=0 GROUP BY uid";
			$rlt=DB::select($sql);
			foreach($rlt as $v){
				$user_num[$v['uid']]=$v['num'];
			}
		}
		
		//城市
		$allprovince=array();
		$_rlt=ApiCity::getProvince();
		foreach($_rlt as $v){
			$allprovince[$v["id"]]=$v['name'];
		}
		
		$allcity=array();
		$_rlt=ApiCity::getCity();
		foreach($_rlt as $k=>$v){
			foreach($v as $key=>$val){
				$allcity[$val["id"]]=$val['name'];
			}
		}
		$allarea=array();
		$_rlt=ApiCity::getArea();
		foreach($_rlt as $k=>$v){
			foreach($v as $key=>$val){
				$allarea[$val["id"]]=$val['name'];
			}
		}
		
		$data=array();
		$all_status=array(0=>'审核中',1=>'审核失败',2=>'已通过审核');
		foreach($leagueList as $v){
			//array('ID','用户ID','真名','用户昵称','身份证号','移动电话','电子邮箱','单位名称','地址','邮编','申请入会理由','申请时间','通过时间','审核状态');
			$tmp=array();
			$tmp[]=$v['id'];
			$tmp[]=$v['uid'];
			$tmp[]=$v['name'];
			$tmp[]=$v['nick'];
			$tmp[]=$v['gender']==1?'男':'女';
			$tmp[]=$v['card'];
			$tmp[]=$v['mobile'];
			$tmp[]=$v['email'];
			$tmp[]=$v['company'];
			$tmp[]=$v['address'];
			$tmp[]=$v['zip'];
			$tmp[]=$v['cause'];
			$tmp[]=date("Y-m-d H:i",$v['addtime']);
			$tmp[]=!empty($v['audit_time'])?date("Y-m-d",$v['audit_time']):'-';
			$tmp[]=$all_status[$v['status']];
			$tmp[]=isset($allprovince[$v['province_id']])?$allprovince[$v['province_id']]:'';
			$tmp[]=isset($allcity[$v['city_id']])?$allcity[$v['city_id']]:'';
			$tmp[]=isset($allarea[$v['area_id']])?$allarea[$v['area_id']]:'';
			$data[]=$tmp;
		}
		
		//生成xls文件==========================================
		require_once ("../app/ext/PHPExcel.php");
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
		  
		//设置当前表
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('中华诵读联合会会员列表');		
		//设置第一行内容
		$sheetTitle=array('ID','用户ID','真名','用户昵称','性别','身份证号','移动电话','电子邮箱','单位名称','地址','邮编','申请入会理由','申请时间','通过时间','审核状态','省份','城市','县区','作品数量');
		$cNum=0;
		foreach($sheetTitle as $val){
		  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
		  $cNum++;
		}
		
		$rNum=2;
		foreach($data as $val){
		  $cNum=0;
		  foreach($val as $row){
			  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
			  $cNum++;
		  }
		  
		  //作品数量
		  $_num=isset($user_num[$val[1]])?$user_num[$val[1]]:0;
		  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$_num);
		  
		  $rNum++;
		}
		  
		$outputFileName = "leaguelist.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		echo "<a href='http://www.weinidushi.com.cn/upload/".$outputFileName."'>下载</a>";
	}

	/**
	*	夏青杯用户列表
	*	@author:wang.hongli
	*	@since:2015/05/15
	**/
	public function getSumCupUserList()
	{
		$data = array();
		$conn = DB::table('order_list')
				->leftJoin('summercup','summercup.uid','=','order_list.uid')
				->select('summercup.id','order_list.uid','summercup.nick_name','summercup.card','summercup.name','company','address','zip','summercup.mobile','summercup.email','cause','summercup.status','order_list.addtime','order_list.updatetime','summercup.year')
				->where('order_list.status','=',2)
				->where('order_list.goods_id','=',2);
		
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('summercup.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('summercup.name','like','%'.$data['name'].'%');
		}
		$data['email']=Input::get('email');
		if(!empty($data['email'])){
			$conn->where('summercup.email','=',$data['email']);
		}
		$data['mobile']=Input::get('mobile');
		if(!empty($data['mobile'])){
			$conn->where('summercup.mobile','=',$data['mobile']);
		}
		
		$data['sdate']=Input::get('sdate');
		if(!empty($data['sdate'])){
			$conn->where('order_list.addtime','>=',strtotime($data['sdate']));
		}
		
		$data['edate']=Input::get('edate');
		if(!empty($data['edate'])){
			$conn->where('order_list.addtime','<',strtotime($data['edate']));
		}
		
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('summercup.nick_name','like','%'.$data['nick_name'].'%');
		}
		
	
		$data['total']=$conn->count();
		
		$cupList = $conn->orderBy('order_list.addtime','desc')->paginate(20);
		
		return View::make('competition.adminsumercup')->with('cupList',$cupList)->with('data',$data);
		
	}
	
	//导出夏青杯的xls文件
	public function summCupXls(){
		$data = array();
		$conn = DB::table('order_list')
				->leftJoin('summercup','summercup.uid','=','order_list.uid')
				->select('summercup.id','order_list.uid','summercup.nick_name','summercup.card','summercup.name','company','address','zip','summercup.mobile','summercup.email','cause','summercup.status','order_list.addtime','order_list.updatetime','summercup.year')
				->where('order_list.status','=',2)
				->where('order_list.goods_id','=',2);
		
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('summercup.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('summercup.name','like','%'.$data['name'].'%');
		}
		$data['email']=Input::get('email');
		if(!empty($data['email'])){
			$conn->where('summercup.email','=',$data['email']);
		}
		$data['mobile']=Input::get('mobile');
		if(!empty($data['mobile'])){
			$conn->where('summercup.mobile','=',$data['mobile']);
		}
		
		$data['sdate']=Input::get('sdate');
		if(!empty($data['sdate'])){
			$conn->where('order_list.addtime','>=',strtotime($data['sdate']));
		}
		
		$data['edate']=Input::get('edate');
		if(!empty($data['edate'])){
			$conn->where('order_list.addtime','<',strtotime($data['edate']));
		}
		
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('summercup.nick_name','like','%'.$data['nick_name'].'%');
		}
		
		$list = $conn->orderBy('order_list.addtime','desc')->get();
		
		$ids=array();
		$data=array();
		foreach($list as $v){
			//array('ID','用户ID','真名','用户昵称','身份证号','移动电话','电子邮箱','单位名称','地址','邮编','申请入会理由','申请时间','通过时间','审核状态');
			$tmp=array();
			$tmp[]=$v['id'];
			$tmp[]=$v['uid'];
			$tmp[]=$v['name'];
			$tmp[]=$v['nick_name'];
			$tmp[]=$v['card'];
			$tmp[]=$v['mobile'];
			$tmp[]=$v['email'];
			$tmp[]=$v['company'];
			$tmp[]=$v['address'];
			$tmp[]=$v['zip'];
			$tmp[]=date("Y-m-d H:i",$v['addtime']);
			$tmp[]=!empty($v['updatetime'])?date("Y-m-d",$v['updatetime']):'-';
			$tmp[]='支付成功';
			$data[$v['uid']]=$tmp;
			
			if(empty($v['card'])){
				$ids[]=$v['uid'];
			}
		}
		//补充查询
		$sql="select * from league where uid in('".implode("','",$ids)."')";
		$rlt = DB::select($sql);
		foreach($rlt as $v){
			if(!empty($v['id'])){$data[$v['uid']][0]=$v['id'];}
			if(!empty($v['name'])){$data[$v['uid']][2]=$v['name'];}
			if(!empty($v['nick_name'])){$data[$v['uid']][3]=$v['nick_name'];}
			if(!empty($v['card'])){$data[$v['uid']][4]=$v['card'];}
			if(!empty($v['mobile'])){$data[$v['uid']][5]=$v['mobile'];}
			if(!empty($v['email'])){$data[$v['uid']][6]=$v['email'];}
			if(!empty($v['company'])){$data[$v['uid']][7]=$v['company'];}
			if(!empty($v['address'])){$data[$v['uid']][8]=$v['address'];}
			if(!empty($v['zip'])){$data[$v['uid']][9]=$v['zip'];}
			if(!empty($v['addtime'])){$data[$v['uid']][10]=date("Y-m-d",$v['addtime']);}
		}
		//print_r($data);exit;
		//生成xls文件==========================================
		require_once ("../app/ext/PHPExcel.php");
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
		  
		//设置当前表
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('夏青杯报名列表');		
		//设置第一行内容
		$sheetTitle=array('ID','用户ID','真名','用户昵称','身份证号','移动电话','电子邮箱','单位名称','地址','邮编','申请时间','缴费时间','缴费状态');
		$cNum=0;
		foreach($sheetTitle as $val){
		  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
		  $cNum++;
		}
		
		$rNum=2;
		foreach($data as $val){
		  $cNum=0;
		  foreach($val as $row){
			  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
			  $cNum++;
		  }
		  $rNum++;
		}
		  
		$outputFileName = "summcuplist.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		echo "<a href='http://www.weinidushi.com.cn/upload/".$outputFileName."'>下载</a>";
	}

	/**
	*	添加夏青杯认证用户
	*	@author:wang.hongli
	*	@since:2015/05/16
	**/
	public function addSumUser()
	{
		$uid = Input::get('lastdata');
		if(empty($uid))
		{
			return Redirect::to('admin/defaultError')->with('message', '添加夏青杯认证用户失败'); 
		}
		$adminSumerCup = new AdminSumerCup;
		$rs = $adminSumerCup->addSumUser($uid);
		if($rs === 'exists')
		{
			return Redirect::to('admin/defaultError')->with('message', '该用户已经存在'); 
		}
		elseif(!$rs)
		{
			return Redirect::to('admin/defaultError')->with('message', '添加夏青杯认证用户失败'); 
		}
		$pageSize = 20;
		$data = $adminSumerCup->getSummCupUList($pageSize);
		return View::make('competition.adminsumercup')->with('rs',$data);
	}

	/**
	*	后台添加的夏青杯用户列表
	*	@author:wang.hongli
	*	@since:2015/05/16
	*	@param:type 1 朗诵会 2夏青杯
	**/
	public function getAdmAddUser()
	{
		$type = Input::get('type') ? Input::get('type') : 2;
		$pageSize = 20;
		$adminSumerCup = new AdminSumerCup;
		$data = $adminSumerCup->getAdmAddUser($type,$pageSize);

		return View::make('competition.getadmadduser')->with('rs',$data['rs'])->with('userinfo',$data['userinfo'])->with('type',$type);
	}

	/**
	*	后台添加诵读联盟用户列表
	*	@author:wang.hongli
	*	@since:2015/05/15
	**/
	public function addLeagueUser()
	{
		$uid = Input::get('lastdata');
		if(empty($uid))
		{
			return Redirect::to('admin/defaultError')->with('message', '添加诵读联盟认证用户失败');
		}
		$adminSumerCup = new AdminSumerCup;
		$rs = $adminSumerCup->addLeagueUser($uid);
		if($rs === 'exists')
		{
			return Redirect::to('admin/defaultError')->with('message', '该用户已经存在');
		}
		elseif($rs === false)
		{
			return Redirect::to('admin/defaultError')->with('message', '添加诵读联盟认证用户失败');
		}
		else
		{
			return Redirect::to('admin/defaultError')->with('message', '添加诵读联盟认证用户成功');
		}
	}
	
	
	//诗经控制器
	public function raceShiList(){
		$list = DB::table('race_shijing')
				 ->leftJoin('poem','race_shijing.poem_id','=','poem.id')
				 ->select('race_shijing.id','race_shijing.poem_id','poem.name','poem.downnum','poem.readername','poem.writername')
				 ->orderBy('race_shijing.id','desc')
				 ->get();
		return View::make('competition.raceshilist')->with('list',$list);
	}
	
	//添加诗经
	public function addShi(){
		$poem_id=(int)$_GET['poem_id'];
		$rlt=DB::table('race_shijing')->insert(array('poem_id' => $poem_id));
		echo $rlt ? 1 : 0;
	}
	
	//删除诗经
	public function delShi(){
		$id=(int)$_GET["id"];
		$rlt = DB::table('race_shijing')->where('id','=',$id)->delete();
		echo $rlt ? 1 : 0;
	}
	/**
	* 赛事报名列表
	* @author:wang.hongli
	* @modify:2016/06/20
	*/
	public function matchUsersList(){
		$data = array();
		$pagesize = 20;
		$order_conn = DB::table('order_list');
		$user_match_conn = DB::table('user_match');
		$user_match_flag = 0;
		$user_match_uids = array();
		$user_uids = array();
		//所有的商品
		$all_goods=array();
		$all_competition=array();
		$all_goods_id = array();
		// $sql="select id,name,competition_id from goods where id>2 and id != 16 order by id desc";
		$rlt = DB::table('goods')->where('flag',0)->where('id','>',2)->where('id','<>',16)->orderBy('id','desc')->get(array('id','name','competition_id'));
		// $rlt=DB::select($sql);
		foreach($rlt as $v){
			$all_goods[$v['id']]=$v['name'];
			$all_competition[$v['id']]=$v['competition_id'];
			$all_goods_id[] = $v['id'];
		}
		$order_conn->whereIn('goods_id',$all_goods_id)->where('status',2);

		$search['goods_id'] = Input::has('goods_id') ? intval(Input::get('goods_id')) : '';
		if(!empty($search['goods_id'])){
			$order_conn->where('goods_id',$search['goods_id']);
		}
		$search['uid'] = Input::has('uid') ? intval(Input::get('uid')) : '';
		if(!empty($search['uid'])) {
			$order_conn->where('uid',$search['uid']);
		}
		$search['name'] = Input::has('name') ? Input::get('name') : '';
		if(!empty($search['name'])){
			$user_match_conn->where('name',$search['name']);
			$user_match_flag = 1;
		}
		$search['email'] = Input::has('email') ? Input::get('email'):'';
		if(!empty($search['email'])){
			$user_match_conn->where('email',$search['email']);
			$user_match_flag = 1;
		}
		$search['mobile']=Input::has('mobile') ? Input::get('mobile') : '';
		if(!empty($search['mobile'])){
			$user_match_conn->where('mobile',$search['mobile']);
			$user_match_flag = 1;
		}
		$search['invitationcode']=Input::has('invitationcode') ? Input::get('invitationcode') : '';
		if(!empty($search['invitationcode'])){
			$order_conn->where('invitationcode','=',$search['invitationcode']);
		}

		$search['sdate']=Input::has('sdate') ? Input::get('sdate') : '';
		if(!empty($search['sdate'])){
			$order_conn->where('addtime','>=',strtotime($search['sdate']));
		}
		
		$search['edate']=Input::has('edate') ? Input::get('edate') : '';
		if(!empty($search['edate'])){
			$order_conn->where('addtime','<',strtotime($search['edate']));
		}
		
		$search['nick_name']=Input::has('nick_name') ? Input::get('nick_name') : '';
		if(!empty($search['nick_name'])){
			$user_match_conn->where('nick_name',$search['nick_name']);
			$user_match_flag = 1;
		}
		//城市
		$search['province_id']=Input::has('province_id') ? Input::get('province_id') : '';
		if(!empty($search['province_id'])){
			$user_match_conn->where('province_id','=',$search['province_id']);
			$user_match_flag = 1;
		}
		$search['city_id']=Input::has('city_id') ? Input::get('city_id') : '';
		if(!empty($search['city_id']) && $search['city_id'] != '选择'){
			$user_match_conn->where('city_id','=',$search['city_id']);
			$user_match_flag = 1;
		}
		$search['area_id']=Input::has('area_id') ? Input::get('area_id') : '';
		if(!empty($search['area_id']) && $search['area_id'] != '选择'){
			$user_match_conn->where('area_id','=',$search['area_id']);
			$user_match_flag = 1;
		}
		$search['age'] = Input::has('age') ? Input::get('age') : '';
		if(!empty($search['age'])){
			$user_match_conn->where('age','<',16)->where('age','>',0);
			$user_match_flag = 1;
		}
		$data['allprovince']=ApiCity::getAllProvince();
		$data['allcity']=ApiCity::getAllCity();
		$data['allarea']=ApiCity::getAllArea();

		if($user_match_flag==1){
			$tmp_match_user = $user_match_conn->get();
			if(!empty($tmp_match_user)){
				foreach($tmp_match_user as $k=>$v){
					$user_match_uids[$v['uid']] = $v;
				}
			}
		}
		$data['user_match_uids'] = $user_match_uids;
		
		$last_uids = array_keys($user_match_uids);
		if(!empty($last_uids)){
			$last_uids = array_unique($last_uids);
			$order_conn->whereIn('uid',$last_uids);
		}
		if($user_match_flag==1 && empty($last_uids)){
			return Redirect::to('/admin/defaultError')->with('message','结果集为空');
		}
		$data['total']=$order_conn->count();
		$userList = $order_conn->orderBy('addtime','desc')->paginate($pagesize);
		if(!empty($userList)){
			$tmp_uids = array();
			foreach($userList as $k=>$v){
				$tmp_uids[] = $v['uid'];
			}
			if(!empty($tmp_uids)){
				$tmp_match_user = DB::table('user_match')->whereIn('uid',$tmp_uids)->orderBy('id','desc')->get();
			}else{
				$tmp_match_user = array();
			}
			if(!empty($tmp_match_user)){
				foreach($tmp_match_user as $k=>$v){
					$user_match_uids[$v['uid']] = $v;
				}
				$data['user_match_uids'] = $user_match_uids;
			}
		}
		return View::make('competition.matchuserslist')->with('userList',$userList)->with('data',$data)->with('all_goods',$all_goods)->with('all_competition',$all_competition)->with('search',$search);

	}
	
	public function matchUsersListXls()
	{
		$data = array();
		$conn = DB::table('order_list')
				->select('user_match.id','user_match.gender','user_match.uid','user_match.nick_name','user_match.card','user_match.name','company','address','zip','user_match.mobile','user_match.email','cause','user_match.status','order_list.addtime','order_list.goods_id','user_match.update_time','user.nick','order_list.invitationcode','user_match.province','user_match.city','user_match.area','user_match.province_id','user_match.city_id','user_match.area_id')
				->leftJoin('user_match','user_match.uid','=','order_list.uid')
				->leftJoin('user','user.id','=','order_list.uid')
				->where('order_list.status','=',2);
		
		$data['goods_id']=Input::get('goods_id');
		if(!empty($data['goods_id'])){
			$conn->where('order_list.goods_id','=',$data['goods_id']);
		}else{
			$conn->where('order_list.goods_id','>',2);//排除夏青杯和朗诵会
		}
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('user_match.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('user_match.name','like','%'.$data['name'].'%');
		}
		$data['email']=Input::get('email');
		if(!empty($data['email'])){
			$conn->where('user_match.email','=',$data['email']);
		}
		$data['mobile']=Input::get('mobile');
		if(!empty($data['mobile'])){
			$conn->where('user_match.mobile','=',$data['mobile']);
		}
		$data['invitationcode']=Input::get('invitationcode');
		if(!empty($data['invitationcode'])){
			$conn->where('order_list.invitationcode','=',$data['invitationcode']);
		}
		
		$data['sdate']=Input::get('sdate');
		if(!empty($data['sdate'])){
			$conn->where('order_list.addtime','>=',strtotime($data['sdate']));
		}
		
		$data['edate']=Input::get('edate');
		if(!empty($data['edate'])){
			$conn->where('order_list.addtime','<',strtotime($data['edate']));
		}
		
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		$data['province_id'] = Input::get('province_id');
		if(!empty($data['province_id']) && $data['province_id'] != '选择'){
			$conn->where('user_match.province_id',$data['province_id']);
		}
		$data['city_id'] = Input::get('city_id');
		if(!empty($data['city_id']) && $data['city_id'] != '选择'){
			$conn->where('user_match.city_id',$data['city_id']);
		}
		$data['area_id'] = Input::get('area_id');
		if(!empty($data['area_id']) && $data['area_id'] != '选择'){
			$conn->where('user_match.area_id',$data['area_id']);
		}
		$data['age'] = !empty(Input::get('age')) ? Input::get('age') :'';
		if(!empty($data['age'])){
			$conn->where('user_match.age','<',16)->where('user_match.age','>',0);
		}
		$data['total']=$conn->count();
		$userList = $conn->orderBy('order_list.addtime','desc')->get();
		//所有的商品
		$all_goods=array();
		$all_competition=array();
		$sql="select id,name,competition_id from goods order by id desc";
		$rlt=DB::select($sql);
		foreach($rlt as $v){
			$all_goods[$v['id']]=$v['name'];
			$all_competition[$v['id']]=$v['competition_id'];
		}
		
		//如果指定产品，则显示作品数量
		$user_num=array();
		if(!empty($data['goods_id'])){
			$uids=array();
			foreach($userList as $v){
				$uids[$v['uid']]=$v['uid'];
			}
			$competitionid=isset($all_competition[$data['goods_id']])?$all_competition[$data['goods_id']]:0;
			$sql="select a.uid,count(a.opusid) as num from competition_opus_rel a
				LEFT JOIN opus o on o.id=a.opusid
				where 1 and a.competitionid=".$competitionid." and o.isdel=0 and a.uid in(".implode(",",$uids).") group by a.uid ";
			$rlt = DB::select($sql);
			foreach($rlt as $v){
				$user_num[$v['uid']]=$v['num'];
			}
		}
		
		//城市
		$allprovince=array();
		$_rlt=ApiCity::getProvince();
		foreach($_rlt as $v){
			$allprovince[$v["id"]]=$v['name'];
		}
		
		$allcity=array();
		$_rlt=ApiCity::getCity();
		foreach($_rlt as $k=>$v){
			foreach($v as $key=>$val){
				$allcity[$val["id"]]=$val['name'];
			}
		}
		$allarea=array();
		$_rlt=ApiCity::getArea();
		foreach($_rlt as $k=>$v){
			foreach($v as $key=>$val){
				$allarea[$val["id"]]=$val['name'];
			}
		}
		
		$data=array();
		foreach($userList as $v){
			//array('ID','用户ID','真名','用户昵称','性别','身份证号','移动电话','电子邮箱','单位名称','地址','邮编','参加项目','申请时间','缴费时间','缴费状态');
			$tmp=array();
			$tmp[]=$v['id'];
			$tmp[]=$v['uid'];
			$tmp[]=$v['name'];
			$tmp[]=$v['nick'];
			$tmp[]=$v['gender']==1?"男":"女";
			$tmp[]=$v['card'];
			$tmp[]=$v['mobile'];
			$tmp[]=$v['email'];
			$tmp[]=$v['company'];
			$tmp[]=$v['address'];
			$tmp[]=$v['zip'];
			$tmp[]=!empty($all_goods[$v['goods_id']]) ? $all_goods[$v['goods_id']] : 0;
			$tmp[]=date("Y-m-d H:i",$v['addtime']);
			$tmp[]=!empty($v['update_time'])?date("Y-m-d",$v['update_time']):'-';
			$tmp[]='支付成功';
			$tmp[]=$v['invitationcode'];
			$tmp[]=isset($allprovince[$v['province_id']])?$allprovince[$v['province_id']]:'';
			$tmp[]=isset($allcity[$v['city_id']])?$allcity[$v['city_id']]:'';
			$tmp[]=isset($allarea[$v['area_id']])?$allarea[$v['area_id']]:'';
			$data[]=$tmp;
			
		}
		
		//print_r($data);exit;
		//生成xls文件==========================================
		require_once ("../app/ext/PHPExcel.php");
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
		  
		//设置当前表
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('比赛报名列表');		
		//设置第一行内容
		$sheetTitle=array('ID','用户ID','真名','用户昵称','性别','身份证号','移动电话','电子邮箱','单位名称','地址','邮编','参加项目','申请时间','缴费时间','缴费状态','邀请码','省份','城市','县区','作品数量');
		$cNum=0;
		foreach($sheetTitle as $val){
		  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
		  $cNum++;
		}
		
		$rNum=2;
		foreach($data as $val){
		  $cNum=0;
		  foreach($val as $row){
			  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
			  $cNum++;
		  }
		  //作品数量
		  $_num=isset($user_num[$val[1]])?$user_num[$val[1]]:0;
		  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$_num);
		  
		  $rNum++;
		}
		  
		$outputFileName = "matchuserlist.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		echo "<a href='/upload/".$outputFileName."'>下载</a>";
		
	}
	
	/*
	* 诵读会作品
	*/
	public function songOpusList(){
		$all_matchs=array();
		$sql="select id,name from competitionlist where pid in(1,2,3) and id!=22 order by id desc";
		$rlt=DB::select($sql);
		foreach($rlt as $v){
			$all_matchs[$v['id']]=$v['name'];
		}
		$data = array();
		$conn = DB::table('competition_opus_rel')
				->leftJoin('opus','competition_opus_rel.opusid','=','opus.id')
				->leftJoin('user','competition_opus_rel.uid','=','user.id')
				->select('opus.id','opus.uid','opus.name','opus.lnum','opus.praisenum','opus.repostnum','opus.commentnum','opus.downnum','opus.opustime','opus.addtime','user.nick','user.gender','opus.url','opus.isread','competition_opus_rel.competitionid')
				->where('opus.isdel','=',0);
		
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('competition_opus_rel.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('opus.name','like','%'.$data['name'].'%');
		}
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		$data['competitionid']=Input::get('competitionid');
		if(!empty($data['competitionid'])){
			$conn->where('competition_opus_rel.competitionid','=',$data['competitionid']);
		}else{
			$conn->whereIn('competition_opus_rel.competitionid',array_keys($all_matchs));
		}
		
	
		$data['total']=$conn->count();
		$relList = $conn->orderBy('competition_opus_rel.opusid','desc')->paginate(20);
		
		return View::make('competition.songopuslist')->with('relList',$relList)->with('data',$data)->with('all_matchs',$all_matchs);
	}
	/*
	* 收费赛事作品
	*/
	public function matchOpusList(){
		$all_matchs=array();
		$sql="select id,name from competitionlist where pid in(4,5) order by id desc";
		$rlt=DB::select($sql);
		foreach($rlt as $v){
			$all_matchs[$v['id']]=$v['name'];
		}
		$data = array();
		$conn = DB::table('competition_opus_rel')
				->leftJoin('opus','competition_opus_rel.opusid','=','opus.id')
				->leftJoin('user','competition_opus_rel.uid','=','user.id')
				->select('opus.id','opus.uid','opus.name','opus.lnum','opus.praisenum','opus.repostnum','opus.commentnum','opus.downnum','opus.opustime','opus.addtime','user.nick','user.gender','opus.url','opus.isread','competition_opus_rel.competitionid')
				->where('opus.isdel','=',0);
		
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('competition_opus_rel.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('opus.name','like','%'.$data['name'].'%');
		}
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		$data['competitionid']=Input::get('competitionid');
		if(!empty($data['competitionid'])){
			$conn->where('competition_opus_rel.competitionid','=',$data['competitionid']);
		}else{
			$conn->whereIn('competition_opus_rel.competitionid',array_keys($all_matchs));
		}
		
	
		$data['total']=$conn->count();
		$relList = $conn->orderBy('competition_opus_rel.opusid','desc')->paginate(20);
		
		return View::make('competition.matchopuslist')->with('relList',$relList)->with('data',$data)->with('all_matchs',$all_matchs);
	}
	public function matchOpusListXls(){
		$all_matchs=array();
		$sql="select id,name from competitionlist where pid=4 and id!=22 order by id desc";
		$rlt=DB::select($sql);
		foreach($rlt as $v){
			$all_matchs[$v['id']]=$v['name'];
		}
		$data = array();
		$conn = DB::table('competition_opus_rel')
				->leftJoin('opus','competition_opus_rel.opusid','=','opus.id')
				->leftJoin('user','competition_opus_rel.uid','=','user.id')
				->select('opus.id','opus.uid','opus.name','opus.lnum','opus.praisenum','opus.repostnum','opus.commentnum','opus.downnum','opus.opustime','opus.addtime','user.nick','opus.url','competition_opus_rel.competitionid')
				->where('opus.isdel','=',0);
		
		$data['uid']=Input::get('uid');
		if(!empty($data['uid'])){
			$conn->where('competition_opus_rel.uid','=',$data['uid']);
		}
		$data['name']=Input::get('name');
		if(!empty($data['name'])){
			$conn->where('opus.name','like','%'.$data['name'].'%');
		}
		$data['nick_name']=Input::get('nick_name');
		if(!empty($data['nick_name'])){
			$conn->where('user.nick','like','%'.$data['nick_name'].'%');
		}
		$data['competitionid']=Input::get('competitionid');
		if(!empty($data['competitionid'])){
			$conn->where('competition_opus_rel.competitionid','=',$data['competitionid']);
		}else{
			$conn->whereIn('competition_opus_rel.competitionid',array_keys($all_matchs));
		}
		
	
		$data['total']=$conn->count();
		$list = $conn->orderBy('competition_opus_rel.opusid','desc')->get();
		
		$data=array();
		foreach($list as $v){
			//array('作品ID','作品名称','用户ID','用户昵称','赛事名称','收听数','赞数','转发数','评论数','下载数','时长','发布时间','作品url地址');			
			$tmp=array();
			$tmp[]=$v['id'];
			$tmp[]=$v['name'];
			$tmp[]=$v['uid'];
			$tmp[]=$v['nick'];
			$tmp[]=$all_matchs[$v['competitionid']];
			$tmp[]=$v['lnum'];
			$tmp[]=$v['praisenum'];
			$tmp[]=$v['repostnum'];
			$tmp[]=$v['commentnum'];
			$tmp[]=$v['downnum'];
			$tmp[]=$v['opustime'];
			$tmp[]=date("Y-m-d H:i",$v['addtime']);
			$tmp[]="http://www.weinidushi.com.cn".$v['url'];
			$data[]=$tmp;
		}
		//生成xls文件==========================================
		require_once ("../app/ext/PHPExcel.php");
		$excel=new PHPExcel();
		$objWriter = new PHPExcel_Writer_Excel5($excel);
		//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式
		  
		//设置当前表
		$excel->setActiveSheetIndex(0);
		$sheet=$excel->getActiveSheet();
		$sheet->setTitle('夏青杯作品列表');		
		//设置第一行内容
		$sheetTitle=array('作品ID','作品名称','用户ID','用户昵称','赛事名称','收听数','赞数','转发数','评论数','下载数','时长','发布时间','作品url地址');	
		$cNum=0;
		foreach($sheetTitle as $val){
		  $sheet->setCellValueByColumnAndRow($cNum,1,$val);
		  $cNum++;
		}
		
		$rNum=2;
		foreach($data as $val){
		  $cNum=0;
		  foreach($val as $row){
			  $sheet->setCellValueByColumnAndRow($cNum,$rNum," ".$row);
			  $cNum++;
		  }
		  $rNum++;
		}
		  
		$outputFileName = "matchopuslist.xls";
		$file='upload/'.$outputFileName;
		$objWriter->save($file);
		echo "<a href='http://www.weinidushi.com.cn/upload/".$outputFileName."'>下载</a>";
		
	}
	
	//赛事线下缴费
	public function matchFreeAdd(){
		$competitionid = (int)Input::get('competitionid');
		$goods = DB::table('goods')->where('competition_id','=',$competitionid)->first();
		$uids=array();
		$userinfo=array();
		if(!empty($goods)){
			$sql = "select u.uid,u.type,u.update_time from user_permission u where u.type='".$goods['competition_id']."' 
			and u.uid not in(select uid from order_list where STATUS=2 and goods_id='".$goods['id']."') order by u.update_time desc";
			$rlt = DB::select($sql);
			foreach($rlt as $v){
				$uids[$v['uid']]=$v['uid'];
			}
			//查询昵称
			$sql="select id,nick from user where id in('".implode("','",$uids)."')";
			$rlt = DB::select($sql);
			foreach($rlt as $v){
				$userinfo[$v['id']]=$v['nick'];
			}
			
		}
		
		//所有的商品
		$all_goods=array();
		$sql="select id,name,competition_id from goods where id>2 order by id desc";
		$rlt=DB::select($sql);
		foreach($rlt as $v){
			$all_goods[$v['competition_id']]=$v['name'];
		}

		return View::make('competition.matchfreeadd')->with('uids',$uids)->with('userinfo',$userinfo)->with('competitionid',$competitionid)->with('all_goods',$all_goods);
	}
	
	/*
	* 获取城市
	*/
	public function getCity(){
		$province_id=(int)$_GET["province_id"];
		$data=ApiCity::getCity($province_id);
		echo json_encode($data);
	}
	
	/*
	* 获取县区
	*/
	public function getArea(){
		$city_id=(int)$_GET["city_id"];
		$data=ApiCity::getArea($city_id);
		echo json_encode($data);
	}
	
}
