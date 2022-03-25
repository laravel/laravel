<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
	@show

	<div id="app" v-cloak>
		@yield('app')
	</div>

	@section('app:after')
		<script src="{{ mix('/compiled/js/app.js') }}" async></script>
	@show
</body>
</html>
