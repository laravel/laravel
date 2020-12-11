<?php

Route::get('/')
    ->uses('Home\HomeController@index')
    ->name('homepage.index');
