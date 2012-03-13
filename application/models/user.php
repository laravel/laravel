<?php

class User extends Model {
	
	public static $timestamps = true;

    public function roles()
    {
        return $this->has_and_belongs_to_many('Role');
    }

    public static function has_role($key)
    {
        foreach(Auth::user()->roles as $role)
        {
            if($role->key == $key)
            {
                return true;
            }
        }

        return false;
    }

    public static function has_any_role($keys)
    {
        if( ! is_array($keys))
        {
            $keys = func_get_args();
        }

        foreach(Auth::user()->roles as $role)
        {
            if(in_array($role->key, $keys))
            {
                return true;
            }
        }

        return false;
    }
    
}