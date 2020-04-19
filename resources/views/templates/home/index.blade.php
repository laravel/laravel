{{-- @description Main homepage of the site --}}

@extends('app/home/home', [
    'model' => [
        'user' => _use('people.person'),
        'friends' => [
            _use('people.person'),
            _use('people.person'),
            _use('people.person'),
        ],
    ],
])
