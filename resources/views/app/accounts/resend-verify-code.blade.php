@extends('layouts/app')

@section('content')

	<resend-verify-code-form
		action="{{ $model['action'] }}"
	></resend-verify-code-form>

@endsection
