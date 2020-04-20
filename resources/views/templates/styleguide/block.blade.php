@extends('app/styleguide', [
	'model' => Config::get(implode('.', [
		'styleguide',
		'sections',
		Request::query('section'),
		'blocks',
		Request::query('block'),
		'previews',
		Request::query('preview'),
	])),
])
