@extends('app/accounts/forgot-password', [
	'model' => [
		'action' => route('frontend.echo', [
			'json' => [
				'message' => trans('accounts.passwords.sent'),
			],
		]),
	],
])
