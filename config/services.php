<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => [
		'domain' => env('MAILGUN_DOMAIN', ''),
		'secret' => env('MAILGUN_SECRET', ''),
	],

	'mandrill' => [
		'secret' => env('MANDRILL_SECRET', ''),
	],

	'ses' => [
		'key'    => env('SES_KEY', ''),
		'secret' => env('SES_SECRET', ''),
		'region' => env('SES_REGION', 'us-east-1'),
	],

	'stripe' => [
		'model'  => env('STRIPE_MODEL', 'User'),
		'secret' => env('STRIPE_SECRET', ''),
	],

];
