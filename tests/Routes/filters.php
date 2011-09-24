<?php

return array(
	'before_filter' => function()
	{
		return 'filtered before';
	},
	
	'after_filter' => function($response)
	{
		$response->content = 'filtered after';
		
		return $response;
	},
	
	'after_filter2' => function($response)
	{
		$response->content .= ' filtered after2';
		
		return $response;
	},
);