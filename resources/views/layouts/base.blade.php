<!doctype html>
<html>
<head>
	@yield('head')
</head>
<body>
	@yield('app:before')

	<div id="app" class="relative" v-cloak>
		@yield('app')
	</div>

	@section('app:after')
		<script src="https://polyfill.io/v3/polyfill.min.js?features={{ implode('%2C', ['Array.from', 'Promise', 'IntersectionObserver', 'Element.prototype.matches', 'Element.prototype.classList', 'Array.prototype.includes', 'Array.prototype.find']) }}"></script>
		<script src="{{ mix('/compiled/js/app.js') }}" async></script>
	@show
</body>
</html>
