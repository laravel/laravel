@extends('layouts/app')

@section('content')

	<forgot-password-form
		action="{{ $model['action'] }}"
		email="{{ $model['email'] or '' }}"
	></forgot-password-form>

@endsection
