<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Laravel - Test Report</title>

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
			font-size: 35px;
			color: #6d6d6d;
			margin: 0 0 10px 0;
			text-shadow: 1px 1px #000;
		}

		h2 {
			font-size: 25px;
			color: #6d6d6d;
			text-shadow: 1px 1px #000;
		}

		h3 {
			font-size: 20px;
			color: #6d6d6d;
			text-shadow: 1px 1px #000;
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

		div.basic {
			background-color: #eee;
		}

		div.passed {
			background-color: #d8f5cf;
		}

		div.failed {
			background-color: #ffebe8;
		}
	</style>
</head> 
<body>
	<div id="wrapper"> 
		<h1>Test Report</h1>

		<h2>Passed <?php echo $passed; ?> / <?php echo $total; ?> Tests</h2>

		<?php foreach ($results as $suite => $results): ?>
			<h3><?php echo $suite; ?></h3>

			<?php foreach ($results as $result): ?>

				<div class="content <?php echo ($result['result']) ? 'passed' : 'failed'; ?>">
					<strong><?php echo ($result['result']) ? 'Passed' : 'Failed'; ?>:</strong> <?php echo $result['name']; ?>
				</div>

			<?php endforeach; ?>
		<?php endforeach; ?>
	</div> 
</body> 
</html>