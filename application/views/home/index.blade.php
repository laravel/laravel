<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Laravel: A Framework For Web Artisans</title>
	<meta name="viewport" content="width=device-width">
	{{ HTML::style('laravel/css/style.css') }}
</head>
<body onload="prettyPrint()">
	<div class="wrapper">
		<header>
			<h1>Laravel</h1>
			<h2>A Framework For Web Artisans</h2>

			<p class="intro-text">
				You have successfully installed the Laravel framework. Laravel is a simple framework
				that helps web artisans create beautiful, creative applications using elegant, expressive
				syntax. You'll love using it.
			</p>
		</header>
		<div role="main" class="main">
			<div class="home">
				<h3>Learn the terrain.</h3>

				<p>
					You've landed yourself on our default home page. The route that
					is generating this page lives at:
				</p>

				<pre>{{ path('app') }}routes.php</pre>

				<p>And the view sitting before you can be found at:</p>

				<pre>{{ path('app') }}views/home/index.php</pre>

				<h3>Read the docs.</h3>

				<p>
					The docs are now included with the source package, you can {{ HTML::link('docs', 'read them offline here') }}.
				</p>

				<h3>Create something beautiful.</h3>

				<p>
					Now that you're up and running, it's time to start creating!
					Here are some links to help you get started:
				</p>

				<ul class="out-links">
					<li><a href="http://laravel.com">Official Website</a></li>
					<li><a href="http://forums.laravel.com">Laravel Forums</a></li>
					<li><a href="http://github.com/laravel/laravel">GitHub Repository</a></li>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>
