<?php namespace Jackzz\Entities;

use CQRS\Entity;
use Jackzz\Events\RoleCreated;

class Role extends Entity {

	public function create($attributes)
	{
		$this->apply_event(new RoleCreated($attributes));
	}



	protected function apply_role_created(RoleCreated $event)
	{
		$this->attributes = $event->attributes;
	}

}