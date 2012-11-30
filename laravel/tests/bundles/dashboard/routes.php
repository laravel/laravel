<?php

Route::get('dashboard', array('as' => 'dashboard', function()
{
	//
}));

Route::controller('dashboard::panel');