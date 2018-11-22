@extends('app/accounts/register', [
	'model' => [
		'login_url' => route('frontend.show', 'accounts/login'),
		'action' => route('frontend.echo', [
			'redirect' => route('home.show'),
		]),
	],
])
