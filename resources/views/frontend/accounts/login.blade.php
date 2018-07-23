@extends('app/accounts/login', [
	'model' => [
		'action' => route('frontend.echo', [
			'json' => [
				'redirect' => route('home.show'),
			],
		]),
	],
])
