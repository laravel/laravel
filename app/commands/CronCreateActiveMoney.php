<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CronCreateActiveMoney extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:CronCreateActiveMoney';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "生成更新本月报表(每天更新数据)";

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
		$now= date("Y-m",time());
		$rs=DB::table('Active_money')->where('time',$now)->first();
		//每月第一次插入日期信息
		if(!$rs){
			DB::table('Active_money')->insert(array('time'=>$now,'alluser'=>0,'user_buy'=>0,'buy_per'=>0,'money'=>0,'addtime'=>time()));
		}else{
			$day=date("Y-m-d",time());
			$endtime=strtotime($day." 00:00:00"); 
			$starttime=strtotime($now."-01 00:00:00"); 
			$sql="select count(*) as user_buy,sum(total_price) as money from order_list where status=2 and  updatetime >=$starttime and updatetime <$endtime";
    	 	$order= DB::select($sql);
			$sql1="select count(*) as num from user where addtime <$endtime";
            $alluser=DB::select($sql1);
			DB::table('Active_money')->where('time',$now)->update(array('alluser'=>$alluser[0]['num'],"user_buy"=>$order[0]['user_buy'],"money"=>$order[0]['money']));
		}
}
