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

	<main class="site-content font-hairline font-thin font-light font-normal font-medium font-semibold font-bold font-extrabold font-black">
		<div class="container">
			@yield('content')
		</div>
	</main>

	@include('layouts/partials/site-footer')
@endsection
