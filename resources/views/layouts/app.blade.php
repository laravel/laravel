<!doctype html>
<html>
<head>
	@include('layouts/partials/meta', [
		'stylesheet' => '/compiled/css/app.css'
	])

	@include('layouts/partials/gtm', [
		'placement' => 'head'
	])
</head>
<body>
	@include('layouts/partials/gtm', [
		'placement' => 'body'
	])

	@include('layouts/partials/outdated-browser')

	<div id="app" class="site-wrapper">
		@include('layouts/partials/site-header')

		@yield('content')

		@include('layouts/partials/site-footer')
	</div>

	<script src="https://polyfill.io/v3/polyfill.min.js?features=Array.from%2CPromise%2CIntersectionObserver%2CElement.prototype.matches%2CElement.prototype.classList%2CArray.prototype.find"></script>
	<script src="{{ mix('/compiled/js/app.js') }}" async></script>
</body>
</html>
