<?php namespace Jackzz\AggregateRoots;

use CQRS\AggregateRoot;

use Jackzz\Entities\Role;

use Jackzz\Events\RoleCreated;
use Jackzz\Events\AccountCreated;
use Jackzz\Events\AccountUpdated;
use Jackzz\Events\RolesAssignedToAccount;
use Jackzz\Events\RolesUnAssignedFromAccount;

class Account extends AggregateRoot {

	public function create($attributes)
	{
		$this->apply_event(new AccountCreated($attributes));
	}

	public function update($attributes)
	{
		$this->apply_event(new AccountUpdated($attributes));
	}

	public function assign_roles($attributes)
	{
		$this->apply_event(new RolesAssignedToAccount($attributes));
	}

	public function unassign_roles($attributes)
	{
		$this->apply_event(new RolesUnAssignedFromAccount($attributes));
	}



	protected function apply_account_created(AccountCreated $event)
	{
		$this->attributes = $event->attributes;
	}

	protected function apply_account_updated(AccountUpdated $event)
	{
		$this->attributes = array_merge($this->attributes, $event->attributes);
	}

	protected function apply_roles_assigned_to_account(RolesAssignedToAccount $event)
	{
		foreach($event->attributes['role_uuids'] as $role_uuid)
		{
			$this->attributes['roles'][$role_uuid] = new Role($role_uuid);
		}
	}

	protected function apply_roles_unassigned_from_account(RolesUnAssignedFromAccount $event)
	{
		foreach($event->attributes['role_uuids'] as $role_uuid)
		{
			unset($this->attributes['roles'][$role_uuid]);
		}
	}

}