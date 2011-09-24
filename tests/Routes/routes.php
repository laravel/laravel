<?php

/**
 * routes for test units
 */

return array(
	/**
	 * wildcard test
	 */
	'GET /test/wildcard/(:num)/(:any)' => function($id, $name)
	{
		return $id . '/' . $name;
	},
	
	/**
	 * regex wildcard
	 */
	'GET /test/wildcard/([a-z]{3}[0-9]{3})' => function($id)
	{
		return $id;
	},
	
	/**
	 * wildcard with optional parameter
	 */
	'GET /test/optwildcard/(:any?)' => function($value = '')
	{
		return $value;
	},
	
	/**
	 * direct path test
	 */
	'GET /test/direct' => function()
	{
		return 'direct';
	},
	
	/**
	 * multiple routes in one
	 */
	'GET /test/multi, GET /test/altmulti' => function()
	{
		return 'multi test';
	},
	
	/**
	 * post request
	 */
	'POST /test/postrequest' => function()
	{
		return 'POST request';
	},
	
	/**
	 * PUT request
	 */
	'PUT /test/putrequest' => function()
	{
		return 'PUT request';
	},
	
	/**
	 * before filter
	 */
	'GET /test/filter/before' => array('before' => 'before_filter', function()
	{
		return 'not filtered';
	}),
	
	/**
	 * after filter
	 */
	'GET /test/filter/after' => array('after' => 'after_filter', function()
	{
		return 'not filtered';
	}),
	
	/**
	 * multiple filters
	 */
	'GET /test/filter/multi' => array('after' => 'after_filter, after_filter2', function()
	{
		return 'not filtered';
	}),
);