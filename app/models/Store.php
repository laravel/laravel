<?php

class Store extends Eloquent {

	public $timestamps = false;

	public function getDates() {
		return array('opened_at');
	}

}


