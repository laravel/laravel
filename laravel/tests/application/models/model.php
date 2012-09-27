<?php

class Model extends Laravel\Database\Eloquent\Model {

	public function set_setter($setter)
	{
		$this->set_attribute('setter', 'setter: '.$setter);
	}

	public function get_getter()
	{
		return 'getter: '.$this->get_attribute('getter');
	}

}