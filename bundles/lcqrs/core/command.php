<?php namespace CQRS;

use Laravel\Message;

class Command {
	
	public static function fire($command)
	{
		Message::pub('command', serialize($command));
	}

}
