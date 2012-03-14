<?php namespace LCQRS;

use LCQRS\Message;

class Send {
	
	public static function command($command)
	{
		Message::pub('command', serialize($command));
	}

	public static function event($command)
	{
		Message::pub('event', serialize($command));
	}

}
