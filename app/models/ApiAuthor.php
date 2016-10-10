<?php 
/**
*	用户认证
*	@author:wang.hongli
*	@since:2015/01/11
**/
class ApiAuthor extends ApiCommon
{
	protected $table = 'author';
	public $timestamps = false;
	/**
	*	添加认证
	*	@author:wang.hongli
	*	@since:2015/01/11
	**/
	public function author()
	{
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return -1;
		$uid = intval($info['id']);
		$nick = htmlspecialchars($info['nick']);
		if(empty(Input::get('realname')) || empty(Input::get('telphone')) || empty(Input::get('content')))
		{
			return -2;	
		}
		$apiAuthor = new ApiAuthor;
		$apiAuthor->uid = $uid;
		$apiAuthor->nick = $nick;
		$apiAuthor->realname = Input::get('realname');
		$apiAuthor->telphone = Input::get('telphone');
		$apiAuthor->content = htmlspecialchars(Input::get('content'));
		$apiAuthor->addtime = time();
		if(!$apiAuthor->save()) 
		{
			return -3;
		}
		return 1;
	}
}