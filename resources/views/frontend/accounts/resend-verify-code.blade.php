@extends('resend-verify-code', [
	'model' => [
		'action' => route('frontend.echo', [
			'json' => [
				'messsage' => trans('accounts.verification.resent'),
			],
		]),
	],
])
