<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Error 404 - Not Found</title>

		<style>
			@import url(http://fonts.googleapis.com/css?family=Ubuntu);

			body {
				background:#eee;
				color: #6d6d6d;
				font: normal normal normal 16px/1.253 Ubuntu, sans-serif;
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

			#main h2,h3 {
				margin-top: 25px;
				padding: 0 0 0 0;
			}

			#main h3 {
				font-size: 18px;
			}

			#main p {
				line-height: 25px;
				margin: 10px 0;
			}
		</style>
	</head>
	<body>
		<div id="main">
			<?php $messages = array('We need a map.', 'I think we\'re lost.', 'We took a wrong turn.'); ?>

			<h1><?php echo $messages[mt_rand(0, 2)]; ?></h1>

			<p>We're really sorry, but we couldn't find the resource you requested.</p>

			<p>Perhaps you would like to go to our <?php echo HTML::link('/', 'home page'); ?> instead?</p>
		</div>
	</body>
</html>