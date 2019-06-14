@extends('layouts/base')

@section('head')
	@parent

	@include('layouts/partials/tracking')
@endsection

@section('app:before')
	@include('layouts/partials/tracking', ['body' => true])

	@include('layouts/partials/outdated-browser')
@endsection

@section('app')
	@include('layouts/partials/site-header')

	@yield('content')

	@include('layouts/partials/site-footer')
@endsection
