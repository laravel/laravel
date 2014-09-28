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
	ob_start();

	$user = new User;
	echo "At start, \$user->born is: "; var_dump($user->born);

	$born = \Carbon\Carbon::parse('1983-01-01 12:00:00 UTC');
	echo "New UTC Carbon date: {$born->toRssString()}\n";

	echo "Change its timezone to America/Vancouver\n";
	$born->timezone('America/Vancouver');
	echo "Vancouver Carbon date: {$born->toRssString()}\n";

	echo "Now set \$user->born to this\n";
	$user->born = $born;

	echo "Now retrieve \$user->born: {$user->born->toRssString()}\n";

	echo "We should see 12 noon +0000, but we see 4am +0000\n";

	$response = Response::make(ob_get_clean(), 200);
	$response->header('Content-Type', 'text/plain');
	return $response;
});
