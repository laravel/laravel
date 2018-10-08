@extends('layouts/app')

@section('content')

	<register-form
		action="{{ $model['action'] }}"
		email="{{ $model['email'] ?? '' }}"
		login-url="{{ $model['login_url'] }}"
	></register-form>

@endsection
