<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
    // Include the two files that we need.
    require 'rest/Validator.php';
    require 'validators/SpecialChecks.php';

    // Register the `something' validator.
    Validator::extend('something', 'SpecialChecks@checkSomething');

    // Create some dummy data.
    $data = [
        'field1' => 'a value',
    ];

    // And use the custom `something' validation rule.
    $rules = [
        'field1' => 'something',
    ];

    // When we create the our validator instance in accordance to its contract.
    $validator = new Acme\Validator(App::make('translator'), $data, $rules);

    // Then Laravel will fail to honour the extension that we declared above:
    //
    // 'Method [validateSomething] does not exist.'
    //
    var_dump($validator->passes());

    // When we use the default validator instance, then we will get
    // the following error:
    /*
      $validator = Validator::make($data, $rules);
      var_dump($validator->passes());
    */
});
