<?php

if ( ! class_exists('Resolve_Controller'))
{
	class Resolve_Controller extends \Laravel\Controller {
		private $var;
		
		public function __construct($var)
		{
			$this->var = $var;
		}
		
		public function index()
		{
			return 'resolve/index/' . $this->var;
		}
	}
	
	IoC::container()->register('controllers.resolve', function($c)
	{
		return new Resolve_Controller('variable');
	});
}