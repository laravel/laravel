<?php
$I = new FunctionalTester($scenario);
$I->wantTo('make sure that the form token is set');

$I->amOnPage('/form-test');
$token = $I->grabValueFrom('input[name="_token"]');
$I->assertNotEmpty($token);
 
