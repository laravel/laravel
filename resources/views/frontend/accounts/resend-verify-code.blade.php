@extends('resend-verify-code', [
	'model' => [
		'action' => route('frontend.echo', [
			'json' => [
				'message' => trans('accounts.verification.resent'),
			],
		]),
	],
])
