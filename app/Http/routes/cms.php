<?php

app()->router->group(['prefix' => 'admin'], function() {
	app()->router->get('/', function() {
		return 'this is the admin home page';
	});
});