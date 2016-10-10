<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronCreateBillBord extends Command {
	/**
	*	作品大赛主播大赛
	*	生成周榜，月榜，年榜
	*	@author:wang.hongli
	*	@since:2014/11/16
	*	每周第一天，每月第一天，每年第一天执行 -120秒
	*	主播大赛周榜，月榜，年榜
	**/
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:cronCreateBillBord';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '作品大赛主播大赛';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$type = $this->argument('type');
		$flag = $this->argument('flag');
		$auth = $this->argument('auth');
		if($auth != 'shoushan@2013'){
			die('error');
		}
		$addTime = time()-3600; //添加时间 inserttime
		$data = [
			'type'=>$type,
			'inserttime'=>$addTime,
			'flag'=>$flag
		];
		$sid = DB::table('subbillnav')->insertGetId($data);
		//存储路径
		$path = public_path().'/upload/billbord/';
		//作品
		if(empty($flag)){
			switch($type){
				case 1:
					$sql = "select id,uid,commentnum,poemid,name,url,lyricurl,type,firstchar,praisenum,lnum-preweeknum as totalNum,repostnum,addtime,opustime,writer  from opus order by totalNum desc,repostnum desc,lnum desc limit 100";
					$sql2 = "update opus set preweeknum = lnum";
					break;
				case 2:
					$sql = "select id,uid,commentnum,poemid,name,url,lyricurl,type,firstchar,praisenum,lnum-premonthnum as totalNum,repostnum,addtime,opustime,writer from opus order by totalNum desc,repostnum desc,lnum desc limit 100";
					$sql2 = "update opus set premonthnum = lnum";
					break;
				case 3:
					$sql = "select id,uid,commentnum,poemid,name,url,lyricurl,type,firstchar,praisenum,lnum-preyearnum as totalNum,repostnum,addtime,opustime, writer from opus order by totalNum desc,repostnum desc,lnum desc limit 100";
					$sql2 = "update opus set preyearnum = lnum";
					break;
			}
			$tmp_rs = DB::select($sql);
			$uids = [];
			if(!empty($tmp_rs)){
				foreach($tmp_rs as $k=>$v){
					$uids[$v['uid']] = $v['uid'];
				}
				//select user info
				$tmp_user_info = DB::table('user')->whereIn('id',$uids)->where('isdel','<>',1)->get(array('id','nick','gender','grade','sportrait','authtype','isleague'));
				if(empty($tmp_user_info)) {
					die('error');
				}
				$user_info = [];
				foreach($tmp_user_info as $k=>$v){
					$user_info[$v['id']] = $v;
				}
				// 整合数组
				$data = [];
				foreach($tmp_rs as $k=>$v){
					if(empty($user_info[$v['uid']])) continue;
					$tmp_arr = [];
					$users = $user_info[$v['uid']];
					$tmp_arr['id'] = $v['id'];
					$tmp_arr['uid'] = $v['uid'];
					$tmp_arr['commentnum'] = $v['commentnum'];
					$tmp_arr['poemid']  = $v['poemid'];
					$tmp_arr['name'] = $v['name'];
					$tmp_arr['writername'] = $v['writer'];
					$tmp_arr['url'] = $v['url'];
					$tmp_arr['lyricurl'] = $v['lyricurl'];
					$tmp_arr['type'] = $v['type'];
					$tmp_arr['firstchar'] = $v['firstchar'];
					$tmp_arr['totalNum'] = $v['totalNum'];
					$tmp_arr['repostnum'] = $v['repostnum'];
					$tmp_arr['addtime'] = $v['addtime'];
					$tmp_arr['opustime'] = $v['opustime'];
					$tmp_arr['nick'] = $users['nick'];
					$tmp_arr['gender'] = $users['gender'];
					$tmp_arr['grade'] = $users['grade'];
					$tmp_arr['sportrait'] = $users['sportrait'];
					$tmp_arr['authtype'] = $users['authtype'];
					$tmp_arr['isleague'] = $users['isleague'];
					$data[] = $tmp_arr;
				}
			}
		//主播
		}else{
			switch($type){
				case 1:
					$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-preweeknum as totalNum ,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit 98";
					$sql2 = "update user set preweeknum = praisenum";
					break;
				case 2:
					$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-premonthnum as totalNum,  fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit 98";
					$sql2 = "update user set premonthnum = praisenum";
					break;
				case 3:
					$sql = "select id,nick,phone,gender,lnum,repostnum,attention,praisenum-preyearnum as totalNum,fans,opusnum,grade,sportrait,portrait,albums,signature,authtype,opusname,isedit,issex,user.addtime,bgpic from  user where user.isdel != 1 order by totalNum desc,user.lnum desc,user.repostnum desc limit 98";
					$sql2 = "update user set preyearnum = praisenum";
					break;
			}
			$data = DB::select($sql);
			
		}
		$this->gendDiploma($data,$addTime,$flag,$type);
		if(!empty($data))
		{
			$filePath = $path.$sid.'.txt';
			$data = serialize($data);
			file_put_contents($filePath, $data);
			//将前周，月，年，作品，主播赞数清零
			DB::update($sql2);
		}

	}

	/**
	* 生成奖状 
	* @author:wang.hongli
	* @since:2016/06/26
	**/
	protected function gendDiploma($data=array(),$addTime=0,$flag=0,$type=0){
		if(empty($data)) return;
		$diploma = [];
		$last_data = array_slice($data, 0,20);
		$i = 1;
		foreach($data as $k=>$v){
			if(empty($v['uid'])) continue;
			if($i>10){
				break;
			}
			$tmp_diploma = [
				'sort'=>$i,
				'flag'=>$flag,
				'type'=>$type,
				'addtime'=>$addTime,
			];
			if(empty($flag)){
				$tmp_diploma['uid'] = $v['uid'];
			}else{
				$tmp_diploma[]['uid'] = $v['id'];
			}
			$diploma[] = $tmp_diploma;
			$i++;

		}
		DB::table('diploma')->insert($diploma);
	}

	/**
	 * Get the console command arguments.
	 * type 1周2月3年
	 * flag 0 作品 1 主播
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['type',InputArgument::REQUIRED, 'week month year'],
			['flag',InputArgument::REQUIRED,'opus user'],
			['auth',InputArgument::REQUIRED,'auth'],
		];
	}
}
