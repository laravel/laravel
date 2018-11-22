@extends('app/accounts/forgot-password', [
	'model' => [
		'login_url' => route('frontend.show', 'accounts/login'),
		'register_url' => route('frontend.show', 'accounts/register'),
		'action' => route('frontend.echo', [
			'json' => [
				'message' => trans('accounts.passwords.sent'),
			],
		]),
	],
])
