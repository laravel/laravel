<!doctype html>
<html>
<head>
	@yield('head')
</head>
<body>
	@yield('app:before')

	<div id="app" class="site-wrapper">
		@yield('app')
	</div>

	@section('app:after')
		<script src="https://polyfill.io/v3/polyfill.min.js?features=Array.from%2CPromise%2CIntersectionObserver%2CElement.prototype.matches%2CElement.prototype.classList%2CArray.prototype.find"></script>
		<script src="{{ mix('/compiled/js/app.js') }}" async></script>
	@show
</body>
</html>
