<?php
require_once('UmengNotification.php');

abstract class IOSNotification extends UmengNotification {
	// The array for payload, please see API doc for more information
	protected $iosPayload = array(
								"aps"       =>  array(
													"alert"					=>  NULL
													//"badge"				=>  xx,
													//"sound"				=>	"xx",
													//"content-available"	=>	xx	
												)
			        			//"key1"	=>	"value1",
			        			//"key2"	=>	"value2"
							);

	// Keys can be set in the aps level
	protected $APS_KEYS    = array("alert", "badge", "sound", "content-available");

	function __construct() {
		parent::__construct();
		$this->data["payload"] = $this->iosPayload;
	}

	// Set key/value for $data array, for the keys which can be set please see $DATA_KEYS, $PAYLOAD_KEYS, $BODY_KEYS, $POLICY_KEYS
	function setPredefinedKeyValue($key, $value) {
		if (!is_string($key))
			throw new Exception("key should be a string!");

		if (in_array($key, $this->DATA_KEYS)) {
			$this->data[$key] = $value;
		} else if (in_array($key, $this->APS_KEYS)) {
			$this->data["payload"]["aps"][$key] = $value;
		} else if (in_array($key, $this->POLICY_KEYS)) {
			$this->data["policy"][$key] = $value;
		} else {
			if ($key == "payload" || $key == "policy" || $key == "aps") {
				throw new Exception("You don't need to set value for ${key} , just set values for the sub keys in it.");
			} else {
				throw new Exception("Unknown key: ${key}");
			}
		}
	}

	// Set extra key/value for Android notification
	function setCustomizedField($key, $value) {
		if (!is_string($key))
			throw new Exception("key should be a string!");
		$this->data["payload"][$key] = $value;
	}
}