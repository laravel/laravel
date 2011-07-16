<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Laravel - <?php echo $severity; ?></title>
 
	<link href="http://fonts.googleapis.com/css?family=Quattrocento&amp;v1" rel="stylesheet" type="text/css" media="all" />
	<link href="http://fonts.googleapis.com/css?family=Ubuntu&amp;v1" rel="stylesheet" type="text/css" media="all" />
	<link href="http://fonts.googleapis.com/css?family=Lobster+Two&amp;v1" rel="stylesheet" type="text/css" media="all" />

	<style type="text/css">
		body {
			background-color: #eee;
			color: #6d6d6d;
			font-family: 'Ubuntu';
			font-size: 15px;
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

		pre {
			font-size: 12px;
		}

		pre.context {
			margin: 0; padding: 0;
		}

		pre.highlight {
			font-weight: bold;
			color: #990000;
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
		<h1 class="laravel"><?php echo $severity; ?></h1>
	</div>

	<div id="wrapper"> 
		<h2>Message:</h2>

		<p><?php echo $message; ?> in <strong><?php echo basename($file); ?></strong> on line <strong><?php echo $line; ?></strong>.</p>

		<h2>Stack Trace:</h2>

		<pre><?php echo $trace; ?></pre>

		<h2>Snapshot:</h2>

		<p>
		<?php if (count($contexts) > 0): ?>

			<?php foreach($contexts as $num => $context): ?>
				<pre class="context <?php echo ($line == $num) ? 'highlight' : ''; ?>"><?php echo htmlentities($num.': '.$context); ?></pre>
			<?php endforeach; ?>

		<?php else: ?>
			Snapshot Unavailable.
		<?php endif; ?>
		</p>
	</div> 
</body> 
</html>