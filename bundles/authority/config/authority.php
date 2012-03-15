<?php
return array(

	/*
	|--------------------------------------------------------------------------
	| Initialize User Permissions Based On Roles
	|--------------------------------------------------------------------------
	|
	| This closure is called by the Authority\Ability class' "initialize" method
	|
	*/

	'initialize' => function($user)
	{
		if ( ! $user) return false;

		Authority::action_alias('manage', array('create', 'read', 'update', 'delete'));

		if($user->has_role('member'))
		{
			Authority::allow('edit', 'page', function($page) use ($user) {
				return $user->id == $page->user_id;
			});

			Authority::allow('create', 'page', function() use ($user) {
				return DB::table('users')->where_id($user->id)->where('credits', '>', 0)->first();
			});

			Authority::deny('create', 'page', function() use ($user) {
				return $user->id == 1;
			});
		}

		if($user->has_role('admin'))
		{
			Authority::allow('manage', 'all');
		}

	}

);