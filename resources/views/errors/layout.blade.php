<!doctype html>
<html>
<head>
	@include('layouts/partials/meta', [
		'stylesheet' => '/compiled/css/error.css'
	])

	@include('layouts/partials/gtm', [
		'placement' => 'head'
	])
</head>
<body>
	@include('layouts/partials/gtm', [
		'placement' => 'body'
	])

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
