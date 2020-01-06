<!doctype html>
<html>
<head>
	@include('layouts/partials/meta')

	@include('layouts/partials/tracking')
</head>
<body>
	@include('layouts/partials/tracking', ['body' => true])

	@include('layouts/partials/outdated-browser')

	<div id="app" class="flex flex-col relative w-full min-h-full" v-cloak>
		@yield('app')
	</div>

	@section('app:after')
		<script src="https://polyfill.io/v3/polyfill.min.js?features={{ implode('%2C', ['Array.from', 'Promise', 'IntersectionObserver', 'Element.prototype.matches', 'Element.prototype.classList', 'Array.prototype.includes', 'Array.prototype.find']) }}"></script>
		<script src="{{ mix('/compiled/js/app.js') }}" async></script>
	@show
</body>
</html>
