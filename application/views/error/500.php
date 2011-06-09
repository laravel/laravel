<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>500 - Internal Server Error</title>

	<link href='http://fonts.googleapis.com/css?family=Ubuntu&amp;subset=latin' rel='stylesheet' type='text/css'>

	<style type="text/css">
		body {
			background-color: #fff;
			margin: 45px 0 0 0;
			font-family: 'Ubuntu', sans-serif;
			font-size: 16px;
			color: #3f3f3f;
		}

		h1 {
			font-size: 40px;
			margin: 0 0 10px 0;
		}

		a {
			color: #000;
		}

		#wrapper {
			width: 740px;
			margin: 0 auto;
		}

		#content {
			padding: 10px 10px 10px 10px;
			background-color: #ffebe8;
			border: 1px solid #dd3c10;
			border-radius: 10px;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<?php
			$messages = array('Whoops!', 'Oh no!', 'Ouch!');
			$message = $messages[mt_rand(0, 2)];
		?>

		<h1><?php echo $message; ?></h1>

		<div id="content">
			An error occured while we were processing your request.
			<br /><br />
			Would you like go to our <a href="<?php echo System\Config::get('application.url'); ?>">home page</a> instead?
		</div>
	</div>
</body>
</html>