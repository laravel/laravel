@extends('layouts/app')

@section('content')

	<register-form
		action="{{ $model['action'] }}"
		email="{{ $model['email'] or '' }}"
		login-url="{{ $model['login_url'] }}"
	></register-form>

@endsection
