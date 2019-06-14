<!doctype html>
<html>
<head>
	@include('layouts/partials/meta', [
		'stylesheet' => '/compiled/css/error.css'
	])

	@include('layouts/partials/tracking')
</head>
<body>
	@include('layouts/partials/tracking', ['body' => true])

	<div id="app" class="site-wrapper">
		<main class="error-message">
			<div class="logo placeholder placeholder--logo">
				<img src="/static/img/branding/logo.svg" alt="Engage logo">
			</div>

			@yield('content')
		</main>
	</div>
</body>
</html>
