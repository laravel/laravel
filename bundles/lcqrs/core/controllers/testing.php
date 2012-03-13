<?php

use CQRS\Command;
use CQRS\Libraries\UUID;

use Jackzz\AggregateRoots\Account;
use Jackzz\Entities\Role;

use Jackzz\Commands\CreateRole;
use Jackzz\Commands\CreateAccount;
use Jackzz\Commands\UpdateAccount;
use Jackzz\Commands\AssignRolesToAccount;
use Jackzz\Commands\UnassignRolesFromAccount;

class Jackzz_Testing_Controller extends Controller {
	
	public function action_index()
	{
		$uuid = 'f0f709a1-d1a9-40c4-a0c8-8d11a7f4e113'; //UUID::generate();
		$admin_role_uuid = UUID::generate();
		$agent_role_uuid = UUID::generate();

		Command::fire(new CreateRole($admin_role_uuid, array('key' => 'admin')));
		Command::fire(new CreateRole($agent_role_uuid, array('key' => 'agent')));

		$account = Command::fire(new CreateAccount($uuid, array('first_name' => 'Koen', 'last_name' => 'Smeets')));
		$account = Command::fire(new UpdateAccount($uuid, array('last_name' => 'Schmeets')));
		$account = Command::fire(new AssignRolesToAccount($uuid, array('role_uuids' => array($admin_role_uuid, $agent_role_uuid))));
		$account = Command::fire(new UnassignRolesFromAccount($uuid, array('role_uuids' => array($agent_role_uuid))));
	
		var_dump(new Account($uuid));
		//var_dump(new Role('c955189b-60db-4c46-b115-003204d9ddc3'));
	}

}