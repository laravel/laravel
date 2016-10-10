<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportNavOpus extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'user:importnavopus';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
		//导入时候打开
		return false;
		ini_set('memory_limit',-1);
		$categoryids = DB::table('navigation')->where('isdel',0)->lists('id');
		$opus_tmp_info = DB::table('opus')->select('id','uid','lnum','praisenum','repostnum','commentnum')->get();
		$opus_info = array();
		foreach($opus_tmp_info as $k=>$v){
			$opus_info[$v['id']] = $v;
		}
		unset($opus_tmp_info);
		
		if(empty($categoryids)) return;
		$url = public_path();
		//opus category 表文件
		$url = public_path();
		$nav_opus_table_id_txt_handle = fopen($url.'/importdata/nav_opus_table_id.txt','a+');
		foreach($categoryids as $k=>$v){
			$tmp_rs = DB::table('navopusrel')->where('categoryid',$v)->get();
			if(empty($tmp_rs)) continue;
			$tmp_table_id = $v%10;
			$tmp_table = 'nav_opus_'.$tmp_table_id;
			//单个分类下作品文件
			$nav_opus_txt = $url.'/importdata/'.$tmp_table.'.txt';
			$nav_opus_handle = fopen($nav_opus_txt, 'a+');
			foreach($tmp_rs as $key=>$value){
				//插入nav_opus_table_id
				$str = $value['opusid'].'	'.$tmp_table_id."\n";
				fwrite($nav_opus_table_id_txt_handle, $str);
				if(empty($opus_info[$value['opusid']])) continue;
				$uid = $opus_info[$value['opusid']]['uid'];
				$commentnum = $opus_info[$value['opusid']]['commentnum'];
				$lnum = $opus_info[$value['opusid']]['lnum'];
				$repostnum = $opus_info[$value['opusid']]['repostnum'];
				$praisenum = $opus_info[$value['opusid']]['praisenum'];
				
				$str2 = '0'.'	'.$uid.'	'.$value['opusid'].'	'.$value['categoryid'].'	'.$commentnum.'	'.$lnum.'	'.$repostnum.'	'.$praisenum.'	'.$value['addtime'].'	'.$value['poemid']."\n";
				fwrite($nav_opus_handle, $str2);
			}
			fclose($nav_opus_handle);
			unset($nav_opus_handle);
			unset($tmp_rs);
		}
		fclose($nav_opus_table_id_txt_handle);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
// 	protected function getArguments()
// 	{
// 		return array(
// 			array('example', InputArgument::REQUIRED, 'An example argument.'),
// 		);
// 	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
