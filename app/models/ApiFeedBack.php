<?php 
/**
*	意见反馈
*	@author:wang.hongli
*	@since:2015/01/11
*/
class ApiFeedBack extends ApiCommon
{
	protected $table = 'feedback';
	public $timestamps = false;
	/**
	*	添加反馈
	*	@author:wang.hongli
	*	@since:2015/01/11
	*/
	public function feedBack()
	{
		$info = $this->viaCookieLogin();
		if(empty($info['id'])) return -1;
		$uid = $info['id'];
		$nick = $info['nick'];
		if(empty(Input::get('realname')) || empty(Input::get('telphone')) || empty(Input::get('content')))
		{
			return -2;
		}
		$dev = Input::get('dev','');
		$apiFeedBack = new ApiFeedBack;
		//平台标示，默认1 android 否则为ios 
		$plat_form = 1;
		if(Input::has('plat_form')){
			$plat_form = intval(Input::get('plat_form'));
		}
		$content = Input::get('content');
		//敏感词过滤
		if(my_sens_word($content))
		{
			return -4;
		}
		$apiFeedBack->plat_form = $plat_form;
		$apiFeedBack->uid = $uid;
		$apiFeedBack->nick = $nick;
		$apiFeedBack->realname = htmlspecialchars(Input::get('realname'));
		$apiFeedBack->telphone = htmlspecialchars(Input::get('telphone'));
		$apiFeedBack->content = htmlspecialchars(Input::get('content'));
		$apiFeedBack->addtime = time();
		$apiFeedBack->dev = $dev;
		if(!$apiFeedBack->save())
		{
			return -3;
		}
		return 1;
	}
}