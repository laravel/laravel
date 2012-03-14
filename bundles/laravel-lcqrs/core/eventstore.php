<?php namespace LCQRS;

use Laravel\Database as DB;

class EventStore {

	public static function put($aggregateroot_uuid, $aggregateroot_name, $event)
	{
		DB::table('events')->insert(array('aggregateroot_uuid' => $aggregateroot_uuid, 'aggregateroot_name' => $aggregateroot_name, 'event' => serialize($event)));
	}

	public static function get($aggregateroot_uuid, $aggregateroot_name)
	{
		$events = array();

		$rows = DB::table('events')->where_aggregateroot_uuid($aggregateroot_uuid)->where_aggregateroot_name($aggregateroot_name)->order_by('id', 'ASC')->get();
		foreach($rows as $row)
		{
			$events[] = unserialize($row->event);
		}

		return $events;
	}

}