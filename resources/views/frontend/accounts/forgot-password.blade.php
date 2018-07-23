@extends('app/accounts/forgot-password', [
	'model' => [
		'action' => route('frontend.echo', [
			'json' => [
				'messsage' => trans('accounts.passwords.sent'),
			],
		]),
	],
])
