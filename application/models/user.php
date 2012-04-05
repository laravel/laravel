<?php

class User extends Model {
	
	public static $timestamps = true;

    public function roles()
    {
        return $this->has_and_belongs_to_many('Role');
    }

    public static function has_role($key)
    {
        return is_null($this->roles()->where_name($key)->first());
    }

    public static function has_any_role($keys)
    {
        $keys = (array) $keys;

        return is_null($this->roles()->where('name', 'IN', $keys)->first());
    }
    
}