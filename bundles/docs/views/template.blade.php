<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Laravel: A Framework For Web Artisans</title>
	<meta name="viewport" content="width=device-width">
	{{ Asset::styles(); }}
	{{ Asset::scripts(); }}
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
			<aside class="sidebar">
				{{ $sidebar }}
			</aside>
			<div class="content">
				@yield('content')
			</div>
		</div>
	</div>
	{{ HTML::script('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js') }}
	{{ Asset::container('footer')->scripts(); }}
</body>
</html>
