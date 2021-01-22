@extends('layouts/base')

@section('app')
	@include('layouts/partials/site-header')

	<main id="site-content">
		<div class="e-container">
			@yield('content')
		</div>
	</main>

	@include('layouts/partials/site-footer')
@endsection
