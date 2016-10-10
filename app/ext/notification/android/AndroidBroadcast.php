<?php
require_once(dirname(__FILE__) . '/../AndroidNotification.php');

class AndroidBroadcast extends AndroidNotification {
	function  __construct() {
		parent::__construct();
		$this->data["type"] = "broadcast";
	}
}