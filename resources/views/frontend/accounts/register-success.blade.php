@extends('app/accounts/register', [
	'model' => [
		'action' => route('frontend.echo', [
			'redirect' => route('page/home'),
		]),
	],
])
