<?php 
/**
*	敏感词管理
*	@author:wang.hongli
*	@since:2015/01/04
**/	
class AdminSensitiveWord extends AdminCommon
{

	/**
	*	将敏感词导入数据库
	*	@author:wang.hongli
	*	@since:2016/01/04
	*/
	public function import_word_to_db()
	{
		$path = public_path();
		$c = file_get_contents($path.'/sensertive.txt');
		$c_str = str_replace("\r\n", '', $c);
		$c_a = explode('|1', $c_str);

		$db_dbdata = DB::table('sensitive_word')->lists('word');

		if(!empty($c_a))
		{
			foreach($c_a as $k=>$v)
			{
				if(in_array($v, $db_dbdata))
				{
					continue;
				}
				$sql = "insert into sensitive_word (`word`) values('{$v}')";	
				DB::insert($sql);
			}
		}
	}

	/**
	*	将数据库中的敏感词导入tree
	*	@author:wang.hongli
	*	@since:2016/01/04
	**/
	public function import_word_to_tree()
	{
		header("Content-type:text/html;charset=utf-8");
		$path = public_path();
		$db_dbdata = DB::table('sensitive_word')->lists('word');
		if(empty($db_dbdata))
		{
			return;
		}
		$resTrie = trie_filter_new(); //create an empty trie tree
		foreach($db_dbdata as $k=>$v)
		{
			if(empty($v))
			{
				continue;
			}
			trie_filter_store($resTrie, $v);
		}
		trie_filter_save($resTrie, $path.'/blackword.tree');
	}
	
}
