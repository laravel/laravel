<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Laravel - Uncaught Exception</title>

		<style>
			@import url(http://fonts.googleapis.com/css?family=Ubuntu);

			body {
				background:#eee;
				color: #6d6d6d;
				font: normal normal normal 14px/1.253 Ubuntu, sans-serif;
				margin:0;
				min-width:1000px;
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
				width: 900px;
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

			#main pre {
				font-size: 12px;
				background-color: #f0f0f0;
				border-left: 1px solid #d8d8d8;
				border-top: 1px solid #d8d8d8;
				border-radius: 5px;
				padding: 10px;
				white-space: pre-wrap;
			}
		</style>
	</head>
	<body>
		<div id="main">
			<h1><?php echo $severity; ?></h1>

			<h3>Message</h3>

			<pre><?php echo $message; ?></pre>

			<h3>Stack Trace</h3>

			<pre><?php echo $exception->getTraceAsString(); ?></pre>

			<h3>Snapshot</h3>

			<?php
				$lines = array();

				foreach (File::snapshot($exception->getFile(), $exception->getLine()) as $num => $context)
				{
					$lines[] = $num.': '.$context;
				}
			?>

			<pre><?php echo htmlentities(implode("\n", $lines)); ?></pre>
		</div>
	</body>
</html>