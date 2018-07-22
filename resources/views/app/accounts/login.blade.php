@extends('layouts/app')

@section('content')

	<login-form
		action="{{ $model['action'] }}"
		register-url="{{ $model['register_url'] }}"
		forgot-password-url="{{ $model['forgot_password_url'] }}"
	></login-form>

@endsection
