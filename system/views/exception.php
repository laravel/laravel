<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Laravel - Error</title>

	<link href='http://fonts.googleapis.com/css?family=Ubuntu&amp;subset=latin' rel='stylesheet' type='text/css'>

	<style type="text/css">
		body {
			background-color: #fff;
			font-family: 'Ubuntu', sans-serif;
			font-size: 18px;
			color: #3f3f3f;
			padding: 10px;
		}

		h1 {
			font-family: 'Ubuntu', sans-serif;
			font-size: 45px;
			color: #6d6d6d;
			margin: 0 0 10px 0;
			text-shadow: 1px 1px #000;
		}

		h3 {
			color: #6d6d6d;
			margin: 0 0 10px 0;
		}

		pre {
			font-size: 14px;
			margin: 0 0 0 0;
			padding: 0 0 0 0;
		}
 
		#wrapper {
			width: 100%;
		}
 
		div.content {
			padding: 10px 10px 10px 10px;
			background-color: #eee;
			border-radius: 10px;
			margin-bottom: 10px;
		}
	</style>
</head> 
<body>
	<div id="wrapper"> 
		<h1><?php echo $severity; ?></h1> 
 
		<div class="content">
			<h3>Message:</h3> 
			<?php echo $message; ?> in <strong><?php echo basename($file); ?></strong> on line <strong><?php echo $line; ?></strong>.
		</div>

		<div class="content">
			<h3>Stack Trace:</h3>

			<pre><?php echo $trace; ?></pre>
		</div>
	</div> 
</body> 
</html>