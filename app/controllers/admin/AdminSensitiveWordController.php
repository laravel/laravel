<?php 
/**
*	敏感词控制器
*	@author:wang.hongli
*	@since:2015/01/04
*/
class AdminSensitiveWordController extends BaseController
{

	/**
	*	@将文件中的禁用词语导入数据库
	*	@author:wang.hongli
	*	@since:2015/01/04
	**/
	public function import_word_to_db()
	{
		$adminSensitive = new AdminSensitiveWord();
		$adminSensitive->import_word_to_db();
		return;
	}

	/**
	*	将数据库中的数据导入tree文件
	*	@author:wang.hongli
	*	@since:2016/01/04
	**/
	public function import_word_to_tree()
	{
		$adminSensitive = new AdminSensitiveWord();
		$adminSensitive->import_word_to_tree();
		return;
	}

	/**
	*	禁用词语列表
	*	@author:wang.hongli
	*	@since:2016/01/05
	*/
	public function adminSensitiveWord()
	{
		$pageSize = 50;
		$words = DB::table('sensitive_word')->select('id','word')->orderBy('id','desc')->paginate($pageSize);
		return View::make('sensitiveword.sensitiveWord')->with('words',$words);
	}

	/**
	*	删除禁用词语
	*	@author:wang.hongli
	*	@since:2016/01/05
	**/
	public function admDelSenWord()
	{
		$wordid = intval(Input::get('wordid'));
		if(empty($wordid))
		{
			echo 'error';
			return;
		}
		
		if(!DB::table('sensitive_word')->where('id','=',$wordid)->delete())
		{
			echo 'error';
			return;
		}
		echo 1;
		return;
	}

	/**
	*	添加敏感词
	*	@author:wang.hongli
	*	@since:2016/01/05
	**/
	public function addSensitiveWord()
	{
		$keyWord = htmlspecialchars(Input::get('keyWord'));
		if(empty($keyWord))
		{
			echo 'error';
			return;
		}
		$db_dbdata = DB::table('sensitive_word')->lists('word');
		if(in_array($keyWord, $db_dbdata))
		{
			echo 'error';
			return;
		}
		if(!DB::table('sensitive_word')->insert(array('word'=>$keyWord)))
		{
			echo 'error';
			return;
		}
		echo 1;
		return;
	}


}