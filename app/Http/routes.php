<?php

/**
 * This is the home route
 */
$router->get('{handled?}', ['as' => 'home', function($handled = null)
{
	saveToSession('NO Exception', 'counter0');

	if ($handled === 'washandled')
	{
		$handled = 'An exception was handled in the last request!';
	}
	else
	{
		$handled = '';
	}

	return '<!doctype html>
				<html lang="en">
				<body>
					<div class="welcome">
						<h1><a href="/">Home</a>: '. (Session::get('NO Exception') ?: 'whoops!, sessions are not working at all') .'</h1>
						<h1><a href="/e1">Exception 1</a>: ' . (Session::get('Exception1') ?: 'click it, or maybe it\'s not working?') . '</h1>
						<h1><a href="/e2">Exception 2</a>: ' . (Session::get('Exception2') ?: 'click it, or maybe it\'s not working?') . '</h1>
						<br><br>
						<h2 style="color:red">'.$handled.'</h2>
					</div>
				</body>
				</html>
		';
}]);


/**
 *  Laravel 5 handles correctly this exception, but it doesn't save the Session
 *  Because the save() method is not called during shutdown.
 */

class Exception1 extends Exception {}

$router->get('e1', ['as' => 'e1', function ()
{
	throw new Exception1('e1');

	return Redirect::home();
}]);

App::make('exception')->error(function(Exception1 $exception, $code)
{
	saveToSession('Exception1', 'counter1');

	return Redirect::to('/washandled');
});

/**
 * This one is exactly the same, but I'm using Session::save() and it works.
 *
 */

class Exception2 extends Exception {}

App::make('exception')->error(function(Exception2 $exception, $code)
{
	saveToSession('Exception2', 'counter2');

	Session::save();

	return Redirect::to('/washandled');
});

$router->get('e2', ['as' => 'e2', function ()
{
	throw new Exception2('e2');

	return Redirect::to('/washandled');
}]);

function saveToSession($name, $counter)
{
	$times = Session::get($counter) ?: 0;

	$times++;

	Session::put($counter, $times);

	Session::put($name, "yep, sessions are working fine in this case - ".$times);
}
