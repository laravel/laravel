@extends('layouts/app')

@section('content')

	<forgot-password-form
		action="{{ $model['action'] }}"
		email="{{ $model['email'] ?? '' }}"
		login-url="{{ $model['login_url'] }}"
		register-url="{{ $model['register_url'] }}"
	></forgot-password-form>

@endsection
