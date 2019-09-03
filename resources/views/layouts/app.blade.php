@extends('layouts/base')

@section('head')
	@include('layouts/partials/meta', [
		'stylesheet' => '/compiled/css/app.css',
	])

	@include('layouts/partials/tracking')
@endsection

@section('app:before')
	@include('layouts/partials/tracking', ['body' => true])

	@include('layouts/partials/outdated-browser')
@endsection

@section('app')
	@include('layouts/partials/site-header')

	<main class="relative z-site-content">
		<div class="container">
			@yield('content')
		</div>
	</main>

	@include('layouts/partials/site-footer')
@endsection
