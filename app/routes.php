<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	var_dump('ALL CATEGORIES');
	var_dump(Category::all()->toArray());

	var_dump('A CHILDREN');
	var_dump(Category::find(1)->children->toArray());

	var_dump('B CHILDREN');
	var_dump(Category::find(2)->children->toArray());

	var_dump('CATEGORIES WITH AT LEAST ONE CHILDREN');
	var_dump(Category::has('children')->get()->toArray());

	$queries = DB::getQueryLog();
	var_dump(end($queries));

	var_dump('This should be the correct query');
	var_dump("select * from 'categories' where (select count(*) from 'categories' as tableAlias where tableAlias.'parent_id' = 'categories'.'id') >= 1");
});
