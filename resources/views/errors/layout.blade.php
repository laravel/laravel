@extends('layouts/base')

@section('head')
	@include('layouts/partials/meta', [
		'stylesheet' => '/compiled/css/error.css',
	])

	@include('layouts/partials/tracking')
@endsection

@section('app:before')
	@include('layouts/partials/tracking', ['body' => true])
@endsection

@section('app')
	<div id="app" class="site-wrapper">
		<main class="error-message">
			<div class="logo placeholder placeholder--logo">
				<img src="/static/img/branding/logo.svg" alt="Engage logo">
			</div>

			@yield('content')
		</main>
	</div>
@endsection
