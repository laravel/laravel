<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>500 - Internal Server Error</title>

	<link href="http://fonts.googleapis.com/css?family=Quattrocento&amp;v1" rel="stylesheet" type="text/css" media="all" />
	<link href="http://fonts.googleapis.com/css?family=Ubuntu&amp;v1" rel="stylesheet" type="text/css" media="all" />
	<link href="http://fonts.googleapis.com/css?family=Lobster+Two&amp;v1" rel="stylesheet" type="text/css" media="all" />

	<style type="text/css">
		body {
			background-color: #eee;
			color: #6d6d6d;
			font-family: 'Ubuntu';
			font-size: 16px;
		}

		a {
			color: #7089b3;
			font-weight: bold;
			text-decoration: none;
		}

		h1.laravel {
			font-family: 'Lobster Two', Helvetica, serif;				
			font-size: 60px;
			margin: 0 0 15px -10px;
			padding: 0;
			text-shadow: -1px 1px 1px #fff;
		}

		h2 {
			font-family: 'Quattrocento', serif;
			font-size: 30px;
			margin: 30px 0 0 0;
			padding: 0;
			text-shadow: -1px 1px 1px #fff;
		}

		p {
			margin: 10px 0 0 0;
			line-height: 25px;
		}

		#header {
			margin: 0 auto;
			margin-bottom: 15px;
			margin-top: 20px;
			width: 80%;
		}

		#wrapper {
			background-color: #fff;
			border-radius: 10px;
			margin: 0 auto;
			padding: 10px;
			width: 80%;
		}

		#wrapper h2:first-of-type {
			margin-top: 0;
		}
	</style>
</head>
<body>
	<div id="header">
		<?php
			$messages = array('Whoops!', 'Oh no!', 'Ouch!');
			$message = $messages[mt_rand(0, 2)];
		?>

		<h1 class="laravel"><?php echo $message; ?></h1>
	</div>

	<div id="wrapper">
		<?php
			$apologies = array("It's not your fault.", "Don't give up on us.", "We're really sorry.");
			$apology = $apologies[mt_rand(0, 2)];
		?>

		<h2><?php echo $apology; ?></h2>

		<p>Something failed while we were handling your request. Would you like go to our <a href="<?php echo System\Config::get('application.url'); ?>">home page</a> instead?</p>
	</div>
</body>
</html>