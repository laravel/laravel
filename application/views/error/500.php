<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Error 500 - Internal Server Error</title>

		<style>
			@import url(http://fonts.googleapis.com/css?family=Ubuntu);

			body {
				background:#eee;
				color: #6d6d6d;
				font: normal normal normal 14px/1.253 Ubuntu, sans-serif;
				margin:0;
				min-width:800px;
				padding:0;
			}

			#main {
				background-clip: padding-box;
				background-color: #fff;
				border:1px solid #ccc;
				border-radius: 5px;
				box-shadow: 0 0 10px #cdcdcd;
				margin: 50px auto 0;
				padding: 30px;
				width: 700px;
			}

			#main h1 {
				font-family: 'Ubuntu';
				font-size: 34px;
				margin: 0 0 20px 0;
				padding: 0;
			}

			#main p {
				line-height: 25px;
				margin: 10px 0;
			}
		</style>
	</head>
	<body>
		<div id="main">
			<?php $messages = array('Something bad has happened.', 'We messed up.', 'Whoops!'); ?>

			<h1><?php echo $messages[mt_rand(0, 2)]; ?></h1>

			<p>We're really sorry, but something went wrong while we were processing your request.</p>

			<p>Perhaps you would like to go to our <?php echo HTML::link('/', 'home page'); ?> instead?</p>
		</div>
	</body>
</html>