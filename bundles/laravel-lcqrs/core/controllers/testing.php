<?php

use LCQRS\Send;
use LCQRS\Libraries\UUID;

use App\AggregateRoots\Account;
use App\Entities\Role;

use App\Commands\CreateRole;
use App\Commands\CreateAccount;
use App\Commands\UpdateAccount;
use App\Commands\AssignRolesToAccount;
use App\Commands\UnassignRolesFromAccount;

class LCQRS_Testing_Controller extends Controller {
	
	public function action_index()
	{
		$uuid = 'f0f709a1-d1a9-40c4-a0c8-8d11a7f4e113'; //UUID::generate();
		$admin_role_uuid = UUID::generate();
		$agent_role_uuid = UUID::generate();

		Send::command(new CreateRole($admin_role_uuid, array('key' => 'admin')));
		Send::command(new CreateRole($agent_role_uuid, array('key' => 'agent')));

		$account = Send::command(new CreateAccount($uuid, array('first_name' => 'Koen', 'last_name' => 'Smeets')));
		$account = Send::command(new UpdateAccount($uuid, array('last_name' => 'Schmeets')));
		$account = Send::command(new AssignRolesToAccount($uuid, array('role_uuids' => array($admin_role_uuid, $agent_role_uuid))));
		$account = Send::command(new UnassignRolesFromAccount($uuid, array('role_uuids' => array($agent_role_uuid))));
	
		var_dump(new Account($uuid));
		//var_dump(new Role('c955189b-60db-4c46-b115-003204d9ddc3'));
	}

}