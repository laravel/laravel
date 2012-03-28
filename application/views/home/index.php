<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Laravel - A Framework For Web Artisans</title>

		<style>
			@import url(http://fonts.googleapis.com/css?family=Ubuntu);

			body {
				background: #eee;
				color: #6d6d6d;
				font: normal normal normal 14px/1.253 Ubuntu, sans-serif;
				margin: 0 0 25px 0;
				min-width: 800px;
				padding: 0;
			}

			#main {
				background-clip: padding-box;
				background-color: #fff;
				border:1px solid #ccc;
				border-radius: 5px;
				box-shadow: 0 0 10px #cdcdcd;
				margin: 25px auto 0;
				padding: 30px;
				width: 700px;
				position: relative;
			}

			#main h1 {
				font-family: 'Ubuntu';
				font-size: 38px;
				letter-spacing: 2px;
				margin: 0 0 10px 0;
				padding: 0;
			}

			#main h2 {
				color: #999;
				font-size: 18px;
				letter-spacing: 3px;
				margin: 0 0 25px 0;
				padding: 0 0 0 0;
			}

			#main h3 {
				color: #999;
				margin-top: 24px;
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
				background-color: #333;
				border-left: 1px solid #d8d8d8;
				border-top: 1px solid #d8d8d8;
				border-radius: 5px;
				color: #eee;
				padding: 10px;
			}

			#main div.warning {
				background-color: #feefb3;
				border: 1px solid;
				border-radius: 5px;
				color: #9f6000;
				padding: 10px;
			}

			#main ul {
				margin: 10px 0;
				padding: 0 30px;
			}

			#main li {
				margin: 5px 0;
			}
		</style>
	</head>
	<body>
		<?php Anbu::render(); ?>
		<div id="main">
			<h1>Welcome To Laravel</h1>

			<h2>A Framework For Web Artisans</h2>

			<p>
				You have successfully installed the Laravel framework. Laravel is a simple framework
				that helps web artisans create beautiful, creative applications using elegant, expressive
				syntax. You'll love using it.
			</p>

			<h3>Learn the terrain.</h3>

			<p>
				You've landed yourself on our default home page. The route that
				is generating this page lives at:
			</p>

			<pre><code>APP_PATH/routes.php</code></pre>

			<p>And the view sitting before you can be found at:</p>

			<pre><code>APP_PATH/views/home/index.php</code></pre>

			<h3>Create something beautiful.</h3>

			<p>
				Now that you're up and running, it's time to start creating!
				Here are some links to help you get started:
			</p>

			<ul>
				<li><a href="http://laravel.com">Official Website</a></li>
				<li><a href="http://forums.laravel.com">Laravel Forums</a></li>
				<li><a href="http://github.com/laravel/laravel">GitHub Repository</a></li>
			</ul>
		</div>
	</body>
</html>
