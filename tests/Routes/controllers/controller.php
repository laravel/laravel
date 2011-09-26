<?php

if ( ! class_exists('Controller_Controller'))
{
	class Controller_Controller extends \Laravel\Controller {
		public function index()
		{
			return 'controller/index';
		}
		
		public function action($param = '', $param2 = '')
		{
			return 'controller/action' . ((!empty($param)) ? '/' . $param : '') . ((!empty($param2)) ? '/' . $param2 : '');
		}
		
		public function view() {
			return $this->view->make('home.index');
		}
		
		public function custom() {
			return $this->custom;
		}
		
		public function notvar() {
			return $this->notvar;
		}
	}
	
	IoC::container()->register('custom', function($c)
	{
		return 'custom';
	});
}