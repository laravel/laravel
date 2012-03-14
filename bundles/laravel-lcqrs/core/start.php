<?php
Route::controller('lcqrs::testing');

Autoloader::namespaces(array(
	'LCQRS' => __DIR__
));

IoC::singleton('EventStore', function()
{
    return new LCQRS\EventStore;
});