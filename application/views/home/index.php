<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"> 
	<title>Welcome To Laravel!</title> 
 
	<link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css" media="all" /> 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>

	<style type="text/css">
		body {
			background-color: #fff; 
			font-family: 'Ubuntu', sans-serif; 
			font-size: 16px;
			color: #3f3f3f;
		}
 
		h1 {
			font-size: 40px;
			color: #6d6d6d;
			margin: 0 0 10px 0;
			text-shadow: 1px 1px #000;
		}		
 
		a {
			color: #000; 
		}
 
		#wrapper {
			width: 740px;
		}
 
		#content {
			padding: 10px 10px 10px 10px;
			background-color: #eee;
			border-radius: 10px;
		}

		#footer {
			font-size: 12px;
			padding-top: 10px;
			text-align: right;
		}
	</style>

	<script type="text/javascript"> 
		$(document).ready(function(){
			$(window).resize(function(){
				$('#wrapper').css({
					position:'absolute', 
					left: ($(window).width() - $('#wrapper').outerWidth()) / 2, 
					top: ($(window).height() - $('#wrapper').outerHeight()) / 3
				});
		 	});
		 
			$(window).resize();
		});		
	</script>
</head> 
<body>
	<div id="wrapper">
		<h1>Laravel</h1> 
 
		<div id="content"> 
			You have successfully installed Laravel.
			<?php Cache::driver('adslkadsl'); ?>
			<br /><br />

			Perhaps you would like to <a href="http://laravel.com/docs">peruse the documentation</a> or <a href="http://github.com/taylorotwell/laravel">contribute on GitHub</a>?
		</div>

		<div id="footer">
			<?php echo Benchmark::memory(); ?>mb &middot; <?php echo Benchmark::check('laravel'); ?>ms
		</div>
	</div> 
</body> 
</html>