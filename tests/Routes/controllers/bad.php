<?php

if ( ! class_exists('Bad_Controller'))
{
	class Bad_Controller extends \Laravel\Controller {
		
	}
	
	IoC::container()->register('controllers.bad', function($c)
	{
		new Bad_Controller; // doesn't return
	});
}