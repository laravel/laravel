<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Application Title -->
	<title>Laravel Application</title>

	<!-- Bootstrap CSS -->
	<link href="/css/app.css" rel="stylesheet">
	<link href="/css/vendor/font-awesome.css" rel="stylesheet">

	<!-- Web Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<!-- Static navbar -->
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">Laravel</a>
			</div>

			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="/">Home</a></li>
				</ul>

				@if (Auth::check())
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
                				<img src="https://www.gravatar.com/avatar/{{{ md5(strtolower(Auth::user()->email)) }}}?s=35" height="35" width="35" class="navbar-avatar">
								{{ Auth::user()->name }} <b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="/auth/logout"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
							</ul>
						</li>
					</ul>
				@else
					<ul class="nav navbar-nav navbar-right">
						<li><a href="/auth/login"><i class="fa fa-btn fa-sign-in"></i>Login</a></li>
						<li><a href="/auth/register"><i class="fa fa-btn fa-user"></i>Register</a></li>
					</ul>
				@endif
			</div>
		</div>
	</nav>

	@yield('content')

	<!-- Bootstrap JavaScript -->
	<script src="/js/vendor/jquery.js"></script>
	<script src="/js/vendor/bootstrap.js"></script>
</body>
</html>
