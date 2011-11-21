<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Error 500 - Internal Server Error</title>

		<style>
			<?php echo file_get_contents(PUBLIC_PATH.'css/laravel.css'); ?>
		</style>
	</head>
	<body>
		<div id="main">
			<img src="http://laravel.com/img/splash/error.png" class="marker">

			<?php $messages = array('Ouch.', 'Oh no!', 'Whoops!'); ?>

			<h1><?php echo $messages[mt_rand(0, 2)]; ?></h1>

			<h2>Server Error: 500 (Internal Server Error)</h2>

			<h3>What does this mean?</h3>

			<p>
				Something went wrong on our servers while we were processing your request.
				We're really sorry about this, and will work hard to get this resolved as
				soon as possible.
			</p>

			<p>
				Perhaps you would like to go to our <?php echo HTML::link('/', 'home page'); ?>?
			</p>
		</div>
	</body>
</html>