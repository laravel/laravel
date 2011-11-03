<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title><?php echo $severity; ?></title>

		<style>
			<?php echo file_get_contents(PUBLIC_PATH.'css/laravel.css'); ?>
		</style>

		<style>
			pre {
				word-wrap: break-word;
			}
		</style>
	</head>
	<body>
		<div id="main">
			<img src="http://laravel.com/img/splash/error.png" class="marker">

			<h1><?php echo $severity; ?></h1>

			<h3>Error Message:</h3>

			<pre><?php echo $message; ?></pre>

			<h3>Stack Trace:</h3>

			<?php
				$search  = array(APP_PATH, SYS_PATH);

				$replace = array('APP_PATH/', 'SYS_PATH/');

				$trace   = str_replace($search, $replace, $exception->getTraceAsString());
			?>

			<pre style="word-wrap: break-word;"><?php echo $trace; ?></pre>
		</div>
	</body>
</html>