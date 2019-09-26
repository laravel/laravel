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
	<main class="m-auto max-w-copy p-3 text-center">
		<div class="m-auto w-48">
			<placeholder class="pt-logo">
				<img src="/static/img/branding/logo.svg" alt="Engage logo">
			</placeholder>
		</div>

		<div class="e-copy mt-4">
			@yield('content')
		</div>
	</main>
@endsection
