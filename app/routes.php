<?php
Form::macro('foo', function() {});  // this line triggers the error in the test

Route::get('form-test', function()
{
        return View::make('form-test');
});
