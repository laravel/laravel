@extends('app/accounts/password-reset', [
	'model' => [
		'token' => '',
		'action' => route('frontend.echo', [
			'json' => [
				'message' => trans('accounts.passwords.sent'),
			],
		]),
	],
])
