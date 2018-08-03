@extends('app/accounts/password-reset', [
	'model' => [
		'action' => route('frontend.echo', [
			'json' => [
				'message' => trans('accounts.passwords.sent'),
			],
		]),
	],
])
