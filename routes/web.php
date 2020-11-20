<?php

Route::get('/')
    ->uses('Home\HomeController@show')
    ->name('home.show');
