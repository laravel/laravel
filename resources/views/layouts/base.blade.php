<!doctype html>
<html>
<head>
	@section('head')
		@include('layouts/partials/meta', [
			'stylesheet' => '/compiled/css/app.css',
		])

		@include('layouts/partials/tracking')
	@show
</head>
<body>
	@section('app:before')
		@include('layouts/partials/tracking', ['body' => true])

		@include('layouts/partials/accessibility-skip-links')

		@include('layouts/partials/outdated-browser')
	@show

	<div id="app" v-cloak>
		@yield('app')
	</div>

	@section('app:after')
		<script>
			window.app = @json($page['js']);
		</script>

		<script src="https://polyfill.io/v3/polyfill.min.js?features={{ implode('%2C', ['Array.from', 'Promise', 'IntersectionObserver', 'Element.prototype.matches', 'Element.prototype.classList', 'Array.prototype.includes', 'Array.prototype.find']) }}"></script>
		<script src="{{ mix('/compiled/js/app.js') }}" async></script>
	@show
</body>
</html>
