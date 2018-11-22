@extends('app/accounts/login', [
	'model' => [
		'register_url' => route('frontend.show', 'accounts/register'),
		'forgot_password_url' => route('frontend.show', 'accounts/forgot-password'),
		'action' => route('frontend.echo', [
			'json' => [
				'redirect' => route('frontend.show', 'home/home'),
			],
		]),
	],
])
