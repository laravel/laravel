@extends('layouts/base')

@section('app')
	@include('layouts/partials/site-header')

	<main id="site-content">
		@yield('content')
	</main>

	@include('layouts/partials/site-footer')
@endsection
