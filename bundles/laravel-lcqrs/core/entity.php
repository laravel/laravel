<?php namespace LCQRS;

use Exception;
use Laravel\IoC;
use Laravel\Str;
use Laravel\Event;

class Entity {

	public $attributes = array();

	public function __construct($uuid = null, $load_from_history = true)
	{
		if(is_null($uuid)) return $this;

		$this->attributes['uuid'] = $uuid;
		if($load_from_history)
		{
			$segments = explode('\\', get_class($this));
			$aggregateroot_name = array_pop($segments);
	
			$eventstore = IoC::resolve('EventStore');
			$events = $eventstore->get($this->attributes['uuid'], $aggregateroot_name);
			$this->load_from_history($events);
		}
	}

	public function apply_event($event, $add = true)
	{
		if($add)
		{
			$eventstore = IoC::resolve('EventStore');
			$eventstore->put($event->attributes['uuid'], $this->get_aggregate_name(), $event);
		}
		
		$apply_method = $this->to_apply_method($event);
		if(method_exists(get_called_class(), $apply_method))
		{
			return $this->$apply_method($event);
		}
	}

	protected function to_apply_method($event)
	{
		$segments = explode('\\', get_class($event));
		$event_name = array_pop($segments);
		$underscored_event_name = uncamelcase($event_name);

		return 'apply_'.$underscored_event_name;
	}

	protected function get_aggregate_name()
	{
		$segments = explode('\\', get_class($this));
					
		return array_pop($segments);
	}

	public function load_from_history($events)
	{
		foreach($events as $event)
		{
			$this->apply_event($event, false);
		}
	}

}