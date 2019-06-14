@extends('layouts/base')

@section('head')
	@parent

	@include('layouts/partials/gtm', [
		'placement' => 'head',
	])
@endsection

@section('app:before')
	@include('layouts/partials/gtm', [
		'placement' => 'body',
	])

	@include('layouts/partials/outdated-browser')
@endsection

@section('app')
	@include('layouts/partials/site-header')

	@yield('content')

	@include('layouts/partials/site-footer')
@endsection
