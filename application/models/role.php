<?php

class Role extends Model {
	
    public function users()
    {
        return $this->has_and_belongs_to_many('User');
    }
	
}