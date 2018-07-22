@extends('app/accounts/password-reset', [
	'model' => [
		'action' => route('frontend.echo', [
			'json' => [
				'messsage' => trans('accounts.passwords.sent'),
			],
		]),
	],
])
