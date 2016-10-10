<?php
require_once(dirname(__FILE__) . '/../AndroidNotification.php');

class AndroidFilecast extends AndroidNotification {
	function  __construct() {
		parent::__construct();
		$this->data["type"] = "filecast";
		$this->data["file_id"]  = NULL;
	}

	//return file_id if SUCCESS, else throw Exception with details.
	function uploadContents($content) {
		if ($this->data["appkey"] == NULL)
			throw new Exception("appkey should not be NULL!");
		if ($this->data["timestamp"] == NULL)
			throw new Exception("timestamp should not be NULL!");
		if (!is_string($content))
			throw new Exception("content should be a string!");

		$post = array("appkey"           => $this->data["appkey"],
					  "timestamp"        => $this->data["timestamp"], 
					  "content"          => $content
					  );
		$url = $this->host . $this->uploadPath;
		$postBody = json_encode($post);
		$sign = md5("POST" . $url . $postBody . $this->appMasterSecret);
		$url = $url . "?sign=" . $sign;
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrNo = curl_errno($ch);
        $curlErr = curl_error($ch);  
        curl_close($ch);
        print($result . "\r\n");
        if ($httpCode == "0") //time out 
        	throw new Exception("Curl error number:" . $curlErrNo . " , Curl error details:" . $curlErr . "\r\n");
        else if ($httpCode != "200") //we did send the notifition out and got a non-200 response
        	throw new Exception("http code:" . $httpCode . " details:" . $result . "\r\n");
        $returnData = json_decode($result, TRUE);
        if ($returnData["ret"] == "FAIL")
        	throw new Exception("Failed to upload file, details:" . $result . "\r\n");
        else
        	$this->data["file_id"] = $returnData["data"]["file_id"];
	}

	function getFileId() {
		if (array_key_exists("file_id", $this->data))
			return $this->data["file_id"];
		return NULL;
	}

}