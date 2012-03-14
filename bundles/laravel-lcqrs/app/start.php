<?php
Autoloader::namespaces(array(
	'App' => __DIR__
));

LCQRS\Message::sub('command', function($message) {
	$command = unserialize($message);
	$segments = explode('\\', get_class($command));
	$command_handler = array_pop($segments);
	$command_handler = 'App\\CommandHandlers\\'.$command_handler;
	new $command_handler($command);
});