{{--

	|-----------------------------------------------------------------|
	|  Warning: limited functionality should be added to this layout  |
	|  It can render if the app is 'critically' impaired.             |
	|-----------------------------------------------------------------|

--}}
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>@yield('title')</title>

	<link href="/compiled/css/app.css" rel="stylesheet">
</head>
<body>
	<main class="flex items-center min-h-screen p-3 text-center">
		<div class="e-copy m-auto max-w-copy">
			@yield('message')
		</div>
	</main>
</body>
</html>
