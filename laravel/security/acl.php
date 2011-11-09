<?php

namespace Laravel\Security;

use Laravel\Config;
use Laravel\Lang;
use Laravel\Request;
use Laravel\Database\Manager;

/**
 * Access Control List class.
 *
 * The purpose of this class is to provide access control to RESTful
 * URIs. The database setup script can be found in the config file
 * named acl.php.
 *
 * @author Amir Khawaja <khawaja.amir@gmail.com>
 */
class Acl
{
    /**
     * @static
     * @throws \Exception
     * @param string $role
     * @param int $user_id
     * @return bool
     */
    public static function is_user_in_role($role, $user_id = -1)
    {
        $acl_conf = Config::get('acl');

        if (is_null($acl_conf)) {
            throw new \Exception(Lang::line('acl.config_file_missing')->get());
        }

        $role = Manager::table($acl_conf['table_role'])
                ->join($acl_conf['table_user_role'],
                       $acl_conf['table_user_role'] . '.role_id', '=',
                       $acl_conf['table_role'] . '.id')
                ->join($acl_conf['table_user'],
                       $acl_conf['table_user'] . '.id', '=',
                       $acl_conf['table_user_role'] . '.user_id')
                ->where($acl_conf['table_role'] . '.name', '=', $role)
                ->where($acl_conf['table_user'] . '.id', '=', $user_id)
                ->count();

        return (!is_null($role) && $role > 0);
    }

    /**
     * Check to see if a Role has permission to access a Resource.
     * @static
     * @throws \Exception
     * @param string $user_id
     * @param null|string $resource
     * @return bool
     */
    public static function has_permission($user_id, $resource = null)
    {
        $acl_conf = Config::get('acl');

        if (is_null($acl_conf)) {
            throw new \Exception(Lang::line('acl.config_file_missing')->get());
        }

        if (is_null($resource)) {
            $resource = Request::route()->key;
        }

        if (is_null($user_id) || !is_numeric($user_id)) {
            throw new \Exception(Lang::line('user_id_missing'));
        }

        $perm = Manager::table($acl_conf['table_role'])
                ->join($acl_conf['table_role_resource'],
                       $acl_conf['table_role_resource'] . '.role_id', '=',
                       $acl_conf['table_role'] . '.id')
                ->join($acl_conf['table_resource'],
                       $acl_conf['table_resource'] . '.id', '=',
                       $acl_conf['table_role_resource'] . '.resource_id')
                ->join($acl_conf['table_user_role'],
                       $acl_conf['table_user_role'] . '.role_id', '=',
                       $acl_conf['table_role'] . '.id')
                ->join($acl_conf['table_user'],
                       $acl_conf['table_user'] . '.id', '=',
                       $acl_conf['table_user_role'] . '.user_id')
                ->where($acl_conf['table_user'] . '.id', '=', $user_id)
                ->where($acl_conf['table_resource'] . '.uri', '=', $resource)
                ->count();

        return (!is_null($perm) && $perm > 0);
    }

    /**
     * Get the Roles of a given User.
     * @static
     * @throws \Exception
     * @param $user_id
     * @return null|object
     */
    public static function roles_for_user($user_id)
    {
        $acl_conf = Config::get('acl');

        if (is_null($acl_conf)) {
            throw new \Exception(Lang::line('acl.config_file_missing')->get());
        }

        if (is_null($user_id) || !is_numeric($user_id)) {
            throw new \Exception(Lang::line('acl.user_id_missing')->get());
        }

        $roles = Manager::table($acl_conf['table_role'])
                ->join($acl_conf['table_user_role'],
                       $acl_conf['table_user_role'] . '.role_id', '=',
                       $acl_conf['table_role'] . '.id')
                ->join($acl_conf['table_user'],
                       $acl_conf['table_user'] . '.id', '=',
                       $acl_conf['table_user_role'] . '.user_id')
                ->where($acl_conf['table_user'] . '.id', '=', $user_id)
                ->get();

        return is_null($roles) ? null : $roles;
    }
}
