<?php



/**
 *
 * Based on the Domain51_Testing_Selenium class available at
 * http://domain51.googlecode.com/svn/Domain51/trunk/
 *
 * @author Travis Swicegood <development [at] domain51 [dot] com>
 *
 */
class SimpleSeleniumRemoteControl
{
	private $_browser = '';
	private $_browserUrl = '';
	private $_host = 'localhost';
	private $_port = 4444;
	private $_timeout = 30000;
	private $_sessionId = null;

	private $_commandMap = array(
		'bool' => array(
			'verify', 
			'verifyTextPresent', 
			'verifyTextNotPresent',
			'verifyValue'
		),
		'string' => array(
			'getNewBrowserSession',
		),
	);

	public function __construct($browser, $browserUrl, $host = 'localhost', $port = 4444, $timeout = 30000) {
		$this->_browser = $browser;
		$this->_browserUrl = $browserUrl;
		$this->_host = $host;
		$this->_port = $port;
		$this->_timeout = $timeout;
	}

	public function sessionIdParser($response) {
		return substr($response, 3);
	}
	
	public function start() {
		$response = $this->cmd('getNewBrowserSession', array($this->_browser, $this->_browserUrl));
		$this->_sessionId = $this->sessionIdParser($response);
	}

	public function stop() {
		$this->cmd('testComplete');
		$this->_sessionId = null;
	}

	public function __call($method, $arguments) {
		$response = $this->cmd($method, $arguments);
		
		foreach ($this->_commandMap as $type => $commands) {
			if (!in_array($method, $commands)) {
				continue;
				$type = null;
			}
			break;
		}

		switch ($type) {
			case 'bool' :
				return substr($response, 0, 2) == 'OK' ? true : false;
				break;

			case 'string' :
			default:
				return $response;
		}
	}
	
	private function _server() {
		return "http://{$this->_host}:{$this->_port}/selenium-server/driver/";
	}

    public function buildUrlCmd($method, $arguments = array()) {
        $params = array(
            'cmd=' . urlencode($method),
        );
        $i = 1;
        foreach ($arguments as $param) {
            $params[] = $i++ . '=' . urlencode(trim($param));
        }
        if (isset($this->_sessionId)) {
            $params[] = 'sessionId=' . $this->_sessionId;
        }

        return $this->_server()."?".implode('&', $params);
    }

	public function cmd($method, $arguments = array()) {
          $url = $this->buildUrlCmd($method, $arguments);
          $response = $this->_sendRequest($url);
          return $response;
	}

	public function isUp() {
        return (bool)@fsockopen($this->_host, $this->_port, $errno, $errstr, 30);
	}
	
	private function _initCurl($url) {
        if (!function_exists('curl_init')) {
            throw new Exception('this code currently requires the curl extension');
        }
        if (!$ch = curl_init($url)) {
            throw new Exception('Unable to setup curl');
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, floor($this->_timeout));
		return $ch;	
	}
	
	private function _sendRequest($url) {
        $ch = $this->_initCurl($url);
        $result = curl_exec($ch);
        if (($errno = curl_errno($ch)) != 0) {
            throw new Exception('Curl returned non-null errno ' . $errno . ':' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
	}
}
