<?php
Route::controller('lcqrs::testing');

Autoloader::namespaces(array(
	'CQRS' => __DIR__.DS.'core'
));

IoC::singleton('EventStore', function()
{
    return new CQRS\EventStore;
});

CQRS\Message::sub('command', function($message) {
	$command = unserialize($message);
	$segments = explode('\\', get_class($command));
	$command_handler = array_pop($segments);
	$command_handler = 'Jackzz\\CommandHandlers\\'.$command_handler;
	new $command_handler($command);
});