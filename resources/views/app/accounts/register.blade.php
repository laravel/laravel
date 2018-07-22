@extends('layouts/app')

@section('content')

	<register-form
		action="{{ $model['action'] }}"
		email="{{ $model['email'] or '' }}"
		forgot-password-url="{{ $model['forgot_password_url'] }}"
	></register-form>

@endsection
