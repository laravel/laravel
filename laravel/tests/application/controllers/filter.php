<?php

class Filter_Controller extends Controller {

	public function __construct()
	{
		Filter::register('test-all-before', function() { $_SERVER['test-all-before'] = true; });
		Filter::register('test-all-after', function() { $_SERVER['test-all-after'] = true; });
		Filter::register('test-profile-before', function() { $_SERVER['test-profile-before'] = true; });
		Filter::register('test-except', function() { $_SERVER['test-except'] = true; });
		Filter::register('test-on-post', function() { $_SERVER['test-on-post'] = true; });
		Filter::register('test-on-get-put', function() { $_SERVER['test-on-get-put'] = true; });
		Filter::register('test-before-filter', function() { return 'Filtered!'; });
		Filter::register('test-param', function($var1, $var2) { return $var1.$var2; });
		Filter::register('test-multi-1', function() { $_SERVER['test-multi-1'] = true; });
		Filter::register('test-multi-2', function() { $_SERVER['test-multi-2'] = true; });

		$this->filter('before', 'test-all-before');
		$this->filter('after', 'test-all-after');
		$this->filter('before', 'test-profile-before')->only(array('profile'));
		$this->filter('before', 'test-except')->except(array('index', 'profile'));
		$this->filter('before', 'test-on-post')->on(array('post'));
		$this->filter('before', 'test-on-get-put')->on(array('get', 'put'));
		$this->filter('before', 'test-before-filter')->only('login');
		$this->filter('after', 'test-before-filter')->only('logout');
		$this->filter('before', 'test-param:1,2')->only('edit');
		$this->filter('before', 'test-multi-1|test-multi-2')->only('save');
	}

	public function action_index()
	{
		return __FUNCTION__;
	}

	public function action_profile()
	{
		return __FUNCTION__;
	}

	public function action_show()
	{
		return __FUNCTION__;
	}

	public function action_edit()
	{
		return __FUNCTION__;
	}

	public function action_save()
	{
		return __FUNCTION__;
	}

	public function action_login()
	{
		return __FUNCTION__;
	}

	public function action_logout()
	{
		return __FUNCTION__;
	}

}