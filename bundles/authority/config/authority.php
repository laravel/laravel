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
        // The initialize method (this Closure function) will be ran on every page load when the bundle get's started.
        // A User Object will be passed into this method and is available via $user
        // The $user variable is a instantiated User Object (application/models/user.php)

        // First, let's group together some "Actions" so we can later give a user access to multiple actions at once
        Authority::action_alias('manage', array('create', 'read', 'update', 'delete'));
        Authority::action_alias('moderate', array('update', 'delete'));

        // If a user doesn't have any roles, we don't have to give him permissions so we can stop right here.
        if(count($user->roles) == 0) return false;

        if($user->has_role('admin'))
        {
            // The logged in user is an admin, we allow him to perform manage actions (create, read, update, delete) on "all" "Resources".
            Authority::allow('manage', 'all');

            // Let's say we want to "Deny" the admin from adding accounts if his age is below 21 (i don't mean to discriminate ;) 
            // Since we have the User object, and it has an "age" property, we can make a simple if statement.
            if($user->age < 21)
            {
                // Too young! we "deny" the user to create users, i'm sorry...
                Authority::deny('create', 'User');
            }

            // Let's make it a little harder, we don't want the admin to be able to delete his own User account, but has to be allowed to delete other Users.
            // We only know that the "Resource" is a User, But we don't know the User id, we can send that information to the Rule Closure, in the Closure below, the argument is called $that_user.
            // We also pass in the logged in user, since the Closure is outside of the scope where this comment is in.
            Authority::deny('delete', 'User', function ($that_user) use ($user)
            {
                // If the id of the User that we are trying to delete is equal to our logged in user, we return true, meaning the Deny Rule will be set.
                return $that_user->id == $user->id;
            });
        }

        if($user->has_role('store_owner'))
        {
            // What if the logged in User has the role "store_owner", let's allow the user to manage his own store
            Authority::allow('manage', 'Store', function($store) use ($user)
            {
                return is_null(DB::table('stores')->where_id($store->id)->where_user_id($user->id)->first());
            });

            // We can also allow "Actions" on certain "Resources" by results we get from somewhere else, look closely at the next example
            foreach(DB::table('permissions')->where_user_id($user->id)->get() as $permission)
            {
                if($permission->type == 'allow')
                {
                    Authority::allow($permission->action, $permission->resource);
                }
                else
                {
                    Authority::deny($permission->action, $permission->resource);    
                }
            }
        }
    }

);