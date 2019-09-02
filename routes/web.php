<?php

Route::get('/')
    ->uses('Home\HomeController')
    ->name('home.show');
