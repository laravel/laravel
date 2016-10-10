<?php




/**
* transport class for sending/receiving data via HTTP and HTTPS
* NOTE: PHP must be compiled with the CURL extension for HTTPS support
*
* @author   Dietrich Ayala <dietrich@ganx4.com>
* @author   Scott Nichol <snichol@users.sourceforge.net>
* @version  $Id: class.soap_transport_http.php,v 1.68 2010/04/26 20:15:08 snichol Exp $
* @access public
*/
class soap_transport_http extends nusoap_base {

	var $url = '';
	var $uri = '';
	var $digest_uri = '';
	var $scheme = '';
	var $host = '';
	var $port = '';
	var $path = '';
	var $request_method = 'POST';
	var $protocol_version = '1.0';
	var $encoding = '';
	var $outgoing_headers = array();
	var $incoming_headers = array();
	var $incoming_cookies = array();
	var $outgoing_payload = '';
	var $incoming_payload = '';
	var $response_status_line;	// HTTP response status line
	var $useSOAPAction = true;
	var $persistentConnection = false;
	var $ch = false;	// cURL handle
	var $ch_options = array();	// cURL custom options
	var $use_curl = false;		// force cURL use
	var $proxy = null;			// proxy information (associative array)
	var $username = '';
	var $password = '';
	var $authtype = '';
	var $digestRequest = array();
	var $certRequest = array();	// keys must be cainfofile (optional), sslcertfile, sslkeyfile, passphrase, certpassword (optional), verifypeer (optional), verifyhost (optional)
								// cainfofile: certificate authority file, e.g. '$pathToPemFiles/rootca.pem'
								// sslcertfile: SSL certificate file, e.g. '$pathToPemFiles/mycert.pem'
								// sslkeyfile: SSL key file, e.g. '$pathToPemFiles/mykey.pem'
								// passphrase: SSL key password/passphrase
								// certpassword: SSL certificate password
								// verifypeer: default is 1
								// verifyhost: default is 1

	/**
	* constructor
	*
	* @param string $url The URL to which to connect
	* @param array $curl_options User-specified cURL options
	* @param boolean $use_curl Whether to try to force cURL use
	* @access public
	*/
	function soap_transport_http($url, $curl_options = NULL, $use_curl = false){
		parent::nusoap_base();
		$this->debug("ctor url=$url use_curl=$use_curl curl_options:");
		$this->appendDebug($this->varDump($curl_options));
		$this->setURL($url);
		if (is_array($curl_options)) {
			$this->ch_options = $curl_options;
		}
		$this->use_curl = $use_curl;
		preg_match('/\$Revisio' . 'n: ([^ ]+)/', $this->revision, $rev);
		$this->setHeader('User-Agent', $this->title.'/'.$this->version.' ('.$rev[1].')');
	}

	/**
	* sets a cURL option
	*
	* @param	mixed $option The cURL option (always integer?)
	* @param	mixed $value The cURL option value
	* @access   private
	*/
	function setCurlOption($option, $value) {
		$this->debug("setCurlOption option=$option, value=");
		$this->appendDebug($this->varDump($value));
		curl_setopt($this->ch, $option, $value);
	}

	/**
	* sets an HTTP header
	*
	* @param string $name The name of the header
	* @param string $value The value of the header
	* @access private
	*/
	function setHeader($name, $value) {
		$this->outgoing_headers[$name] = $value;
		$this->debug("set header $name: $value");
	}

	/**
	* unsets an HTTP header
	*
	* @param string $name The name of the header
	* @access private
	*/
	function unsetHeader($name) {
		if (isset($this->outgoing_headers[$name])) {
			$this->debug("unset header $name");
			unset($this->outgoing_headers[$name]);
		}
	}

	/**
	* sets the URL to which to connect
	*
	* @param string $url The URL to which to connect
	* @access private
	*/
	function setURL($url) {
		$this->url = $url;

		$u = parse_url($url);
		foreach($u as $k => $v){
			$this->debug("parsed URL $k = $v");
			$this->$k = $v;
		}
		
		// add any GET params to path
		if(isset($u['query']) && $u['query'] != ''){
            $this->path .= '?' . $u['query'];
		}
		
		// set default port
		if(!isset($u['port'])){
			if($u['scheme'] == 'https'){
				$this->port = 443;
			} else {
				$this->port = 80;
			}
		}
		
		$this->uri = $this->path;
		$this->digest_uri = $this->uri;
		
		// build headers
		if (!isset($u['port'])) {
			$this->setHeader('Host', $this->host);
		} else {
			$this->setHeader('Host', $this->host.':'.$this->port);
		}

		if (isset($u['user']) && $u['user'] != '') {
			$this->setCredentials(urldecode($u['user']), isset($u['pass']) ? urldecode($u['pass']) : '');
		}
	}

	/**
	* gets the I/O method to use
	*
	* @return	string	I/O method to use (socket|curl|unknown)
	* @access	private
	*/
	function io_method() {
		if ($this->use_curl || ($this->scheme == 'https') || ($this->scheme == 'http' && $this->authtype == 'ntlm') || ($this->scheme == 'http' && is_array($this->proxy) && $this->proxy['authtype'] == 'ntlm'))
			return 'curl';
		if (($this->scheme == 'http' || $this->scheme == 'ssl') && $this->authtype != 'ntlm' && (!is_array($this->proxy) || $this->proxy['authtype'] != 'ntlm'))
			return 'socket';
		return 'unknown';
	}

	/**
	* establish an HTTP connection
	*
	* @param    integer $timeout set connection timeout in seconds
	* @param	integer $response_timeout set response timeout in seconds
	* @return	boolean true if connected, false if not
	* @access   private
	*/
	function connect($connection_timeout=0,$response_timeout=30){
	  	// For PHP 4.3 with OpenSSL, change https scheme to ssl, then treat like
	  	// "regular" socket.
	  	// TODO: disabled for now because OpenSSL must be *compiled* in (not just
	  	//       loaded), and until PHP5 stream_get_wrappers is not available.
//	  	if ($this->scheme == 'https') {
//		  	if (version_compare(phpversion(), '4.3.0') >= 0) {
//		  		if (extension_loaded('openssl')) {
//		  			$this->scheme = 'ssl';
//		  			$this->debug('Using SSL over OpenSSL');
//		  		}
//		  	}
//		}
		$this->debug("connect connection_timeout $connection_timeout, response_timeout $response_timeout, scheme $this->scheme, host $this->host, port $this->port");
	  if ($this->io_method() == 'socket') {
		if (!is_array($this->proxy)) {
			$host = $this->host;
			$port = $this->port;
		} else {
			$host = $this->proxy['host'];
			$port = $this->proxy['port'];
		}

		// use persistent connection
		if($this->persistentConnection && isset($this->fp) && is_resource($this->fp)){
			if (!feof($this->fp)) {
				$this->debug('Re-use persistent connection');
				return true;
			}
			fclose($this->fp);
			$this->debug('Closed persistent connection at EOF');
		}

		// munge host if using OpenSSL
		if ($this->scheme == 'ssl') {
			$host = 'ssl://' . $host;
		}
		$this->debug('calling fsockopen with host ' . $host . ' connection_timeout ' . $connection_timeout);

		// open socket
		if($connection_timeout > 0){
			$this->fp = @fsockopen( $host, $this->port, $this->errno, $this->error_str, $connection_timeout);
		} else {
			$this->fp = @fsockopen( $host, $this->port, $this->errno, $this->error_str);
		}
		
		// test pointer
		if(!$this->fp) {
			$msg = 'Couldn\'t open socket connection to server ' . $this->url;
			if ($this->errno) {
				$msg .= ', Error ('.$this->errno.'): '.$this->error_str;
			} else {
				$msg .= ' prior to connect().  This is often a problem looking up the host name.';
			}
			$this->debug($msg);
			$this->setError($msg);
			return false;
		}
		
		// set response timeout
		$this->debug('set response timeout to ' . $response_timeout);
		socket_set_timeout( $this->fp, $response_timeout);

		$this->debug('socket connected');
		return true;
	  } else if ($this->io_method() == 'curl') {
		if (!extension_loaded('curl')) {
//			$this->setError('cURL Extension, or OpenSSL extension w/ PHP version >= 4.3 is required for HTTPS');
			$this->setError('The PHP cURL Extension is required for HTTPS or NLTM.  You will need to re-build or update your PHP to include cURL or change php.ini to load the PHP cURL extension.');
			return false;
		}
		// Avoid warnings when PHP does not have these options
		if (defined('CURLOPT_CONNECTIONTIMEOUT'))
			$CURLOPT_CONNECTIONTIMEOUT = CURLOPT_CONNECTIONTIMEOUT;
		else
			$CURLOPT_CONNECTIONTIMEOUT = 78;
		if (defined('CURLOPT_HTTPAUTH'))
			$CURLOPT_HTTPAUTH = CURLOPT_HTTPAUTH;
		else
			$CURLOPT_HTTPAUTH = 107;
		if (defined('CURLOPT_PROXYAUTH'))
			$CURLOPT_PROXYAUTH = CURLOPT_PROXYAUTH;
		else
			$CURLOPT_PROXYAUTH = 111;
		if (defined('CURLAUTH_BASIC'))
			$CURLAUTH_BASIC = CURLAUTH_BASIC;
		else
			$CURLAUTH_BASIC = 1;
		if (defined('CURLAUTH_DIGEST'))
			$CURLAUTH_DIGEST = CURLAUTH_DIGEST;
		else
			$CURLAUTH_DIGEST = 2;
		if (defined('CURLAUTH_NTLM'))
			$CURLAUTH_NTLM = CURLAUTH_NTLM;
		else
			$CURLAUTH_NTLM = 8;

		$this->debug('connect using cURL');
		// init CURL
		$this->ch = curl_init();
		// set url
		$hostURL = ($this->port != '') ? "$this->scheme://$this->host:$this->port" : "$this->scheme://$this->host";
		// add path
		$hostURL .= $this->path;
		$this->setCurlOption(CURLOPT_URL, $hostURL);
		// follow location headers (re-directs)
		if (ini_get('safe_mode') || ini_get('open_basedir')) {
			$this->debug('safe_mode or open_basedir set, so do not set CURLOPT_FOLLOWLOCATION');
			$this->debug('safe_mode = ');
			$this->appendDebug($this->varDump(ini_get('safe_mode')));
			$this->debug('open_basedir = ');
			$this->appendDebug($this->varDump(ini_get('open_basedir')));
		} else {
			$this->setCurlOption(CURLOPT_FOLLOWLOCATION, 1);
		}
		// ask for headers in the response output
		$this->setCurlOption(CURLOPT_HEADER, 1);
		// ask for the response output as the return value
		$this->setCurlOption(CURLOPT_RETURNTRANSFER, 1);
		// encode
		// We manage this ourselves through headers and encoding
//		if(function_exists('gzuncompress')){
//			$this->setCurlOption(CURLOPT_ENCODING, 'deflate');
//		}
		// persistent connection
		if ($this->persistentConnection) {
			// I believe the following comment is now bogus, having applied to
			// the code when it used CURLOPT_CUSTOMREQUEST to send the request.
			// The way we send data, we cannot use persistent connections, since
			// there will be some "junk" at the end of our request.
			//$this->setCurlOption(CURL_HTTP_VERSION_1_1, true);
			$this->persistentConnection = false;
			$this->setHeader('Connection', 'close');
		}
		// set timeouts
		if ($connection_timeout != 0) {
			$this->setCurlOption($CURLOPT_CONNECTIONTIMEOUT, $connection_timeout);
		}
		if ($response_timeout != 0) {
			$this->setCurlOption(CURLOPT_TIMEOUT, $response_timeout);
		}

		if ($this->scheme == 'https') {
			$this->debug('set cURL SSL verify options');
			// recent versions of cURL turn on peer/host checking by default,
			// while PHP binaries are not compiled with a default location for the
			// CA cert bundle, so disable peer/host checking.
			//$this->setCurlOption(CURLOPT_CAINFO, 'f:\php-4.3.2-win32\extensions\curl-ca-bundle.crt');		
			$this->setCurlOption(CURLOPT_SSL_VERIFYPEER, 0);
			$this->setCurlOption(CURLOPT_SSL_VERIFYHOST, 0);
	
			// support client certificates (thanks Tobias Boes, Doug Anarino, Eryan Ariobowo)
			if ($this->authtype == 'certificate') {
				$this->debug('set cURL certificate options');
				if (isset($this->certRequest['cainfofile'])) {
					$this->setCurlOption(CURLOPT_CAINFO, $this->certRequest['cainfofile']);
				}
				if (isset($this->certRequest['verifypeer'])) {
					$this->setCurlOption(CURLOPT_SSL_VERIFYPEER, $this->certRequest['verifypeer']);
				} else {
					$this->setCurlOption(CURLOPT_SSL_VERIFYPEER, 1);
				}
				if (isset($this->certRequest['verifyhost'])) {
					$this->setCurlOption(CURLOPT_SSL_VERIFYHOST, $this->certRequest['verifyhost']);
				} else {
					$this->setCurlOption(CURLOPT_SSL_VERIFYHOST, 1);
				}
				if (isset($this->certRequest['sslcertfile'])) {
					$this->setCurlOption(CURLOPT_SSLCERT, $this->certRequest['sslcertfile']);
				}
				if (isset($this->certRequest['sslkeyfile'])) {
					$this->setCurlOption(CURLOPT_SSLKEY, $this->certRequest['sslkeyfile']);
				}
				if (isset($this->certRequest['passphrase'])) {
					$this->setCurlOption(CURLOPT_SSLKEYPASSWD, $this->certRequest['passphrase']);
				}
				if (isset($this->certRequest['certpassword'])) {
					$this->setCurlOption(CURLOPT_SSLCERTPASSWD, $this->certRequest['certpassword']);
				}
			}
		}
		if ($this->authtype && ($this->authtype != 'certificate')) {
			if ($this->username) {
				$this->debug('set cURL username/password');
				$this->setCurlOption(CURLOPT_USERPWD, "$this->username:$this->password");
			}
			if ($this->authtype == 'basic') {
				$this->debug('set cURL for Basic authentication');
				$this->setCurlOption($CURLOPT_HTTPAUTH, $CURLAUTH_BASIC);
			}
			if ($this->authtype == 'digest') {
				$this->debug('set cURL for digest authentication');
				$this->setCurlOption($CURLOPT_HTTPAUTH, $CURLAUTH_DIGEST);
			}
			if ($this->authtype == 'ntlm') {
				$this->debug('set cURL for NTLM authentication');
				$this->setCurlOption($CURLOPT_HTTPAUTH, $CURLAUTH_NTLM);
			}
		}
		if (is_array($this->proxy)) {
			$this->debug('set cURL proxy options');
			if ($this->proxy['port'] != '') {
				$this->setCurlOption(CURLOPT_PROXY, $this->proxy['host'].':'.$this->proxy['port']);
			} else {
				$this->setCurlOption(CURLOPT_PROXY, $this->proxy['host']);
			}
			if ($this->proxy['username'] || $this->proxy['password']) {
				$this->debug('set cURL proxy authentication options');
				$this->setCurlOption(CURLOPT_PROXYUSERPWD, $this->proxy['username'].':'.$this->proxy['password']);
				if ($this->proxy['authtype'] == 'basic') {
					$this->setCurlOption($CURLOPT_PROXYAUTH, $CURLAUTH_BASIC);
				}
				if ($this->proxy['authtype'] == 'ntlm') {
					$this->setCurlOption($CURLOPT_PROXYAUTH, $CURLAUTH_NTLM);
				}
			}
		}
		$this->debug('cURL connection set up');
		return true;
	  } else {
		$this->setError('Unknown scheme ' . $this->scheme);
		$this->debug('Unknown scheme ' . $this->scheme);
		return false;
	  }
	}

	/**
	* sends the SOAP request and gets the SOAP response via HTTP[S]
	*
	* @param    string $data message data
	* @param    integer $timeout set connection timeout in seconds
	* @param	integer $response_timeout set response timeout in seconds
	* @param	array $cookies cookies to send
	* @return	string data
	* @access   public
	*/
	function send($data, $timeout=0, $response_timeout=30, $cookies=NULL) {
		
		$this->debug('entered send() with data of length: '.strlen($data));

		$this->tryagain = true;
		$tries = 0;
		while ($this->tryagain) {
			$this->tryagain = false;
			if ($tries++ < 2) {
				// make connnection
				if (!$this->connect($timeout, $response_timeout)){
					return false;
				}
				
				// send request
				if (!$this->sendRequest($data, $cookies)){
					return false;
				}
				
				// get response
				$respdata = $this->getResponse();
			} else {
				$this->setError("Too many tries to get an OK response ($this->response_status_line)");
			}
		}		
		$this->debug('end of send()');
		return $respdata;
	}


	/**
	* sends the SOAP request and gets the SOAP response via HTTPS using CURL
	*
	* @param    string $data message data
	* @param    integer $timeout set connection timeout in seconds
	* @param	integer $response_timeout set response timeout in seconds
	* @param	array $cookies cookies to send
	* @return	string data
	* @access   public
	* @deprecated
	*/
	function sendHTTPS($data, $timeout=0, $response_timeout=30, $cookies) {
		return $this->send($data, $timeout, $response_timeout, $cookies);
	}
	
	/**
	* if authenticating, set user credentials here
	*
	* @param    string $username
	* @param    string $password
	* @param	string $authtype (basic|digest|certificate|ntlm)
	* @param	array $digestRequest (keys must be nonce, nc, realm, qop)
	* @param	array $certRequest (keys must be cainfofile (optional), sslcertfile, sslkeyfile, passphrase, certpassword (optional), verifypeer (optional), verifyhost (optional): see corresponding options in cURL docs)
	* @access   public
	*/
	function setCredentials($username, $password, $authtype = 'basic', $digestRequest = array(), $certRequest = array()) {
		$this->debug("setCredentials username=$username authtype=$authtype digestRequest=");
		$this->appendDebug($this->varDump($digestRequest));
		$this->debug("certRequest=");
		$this->appendDebug($this->varDump($certRequest));
		// cf. RFC 2617
		if ($authtype == 'basic') {
			$this->setHeader('Authorization', 'Basic '.base64_encode(str_replace(':','',$username).':'.$password));
		} elseif ($authtype == 'digest') {
			if (isset($digestRequest['nonce'])) {
				$digestRequest['nc'] = isset($digestRequest['nc']) ? $digestRequest['nc']++ : 1;
				
				// calculate the Digest hashes (calculate code based on digest implementation found at: http://www.rassoc.com/gregr/weblog/stories/2002/07/09/webServicesSecurityHttpDigestAuthenticationWithoutActiveDirectory.html)
	
				// A1 = unq(username-value) ":" unq(realm-value) ":" passwd
				$A1 = $username. ':' . (isset($digestRequest['realm']) ? $digestRequest['realm'] : '') . ':' . $password;
	
				// H(A1) = MD5(A1)
				$HA1 = md5($A1);
	
				// A2 = Method ":" digest-uri-value
				$A2 = $this->request_method . ':' . $this->digest_uri;
	
				// H(A2)
				$HA2 =  md5($A2);
	
				// KD(secret, data) = H(concat(secret, ":", data))
				// if qop == auth:
				// request-digest  = <"> < KD ( H(A1),     unq(nonce-value)
				//                              ":" nc-value
				//                              ":" unq(cnonce-value)
				//                              ":" unq(qop-value)
				//                              ":" H(A2)
				//                            ) <">
				// if qop is missing,
				// request-digest  = <"> < KD ( H(A1), unq(nonce-value) ":" H(A2) ) > <">
	
				$unhashedDigest = '';
				$nonce = isset($digestRequest['nonce']) ? $digestRequest['nonce'] : '';
				$cnonce = $nonce;
				if ($digestRequest['qop'] != '') {
					$unhashedDigest = $HA1 . ':' . $nonce . ':' . sprintf("%08d", $digestRequest['nc']) . ':' . $cnonce . ':' . $digestRequest['qop'] . ':' . $HA2;
				} else {
					$unhashedDigest = $HA1 . ':' . $nonce . ':' . $HA2;
				}
	
				$hashedDigest = md5($unhashedDigest);
	
				$opaque = '';	
				if (isset($digestRequest['opaque'])) {
					$opaque = ', opaque="' . $digestRequest['opaque'] . '"';
				}

				$this->setHeader('Authorization', 'Digest username="' . $username . '", realm="' . $digestRequest['realm'] . '", nonce="' . $nonce . '", uri="' . $this->digest_uri . $opaque . '", cnonce="' . $cnonce . '", nc=' . sprintf("%08x", $digestRequest['nc']) . ', qop="' . $digestRequest['qop'] . '", response="' . $hashedDigest . '"');
			}
		} elseif ($authtype == 'certificate') {
			$this->certRequest = $certRequest;
			$this->debug('Authorization header not set for certificate');
		} elseif ($authtype == 'ntlm') {
			// do nothing
			$this->debug('Authorization header not set for ntlm');
		}
		$this->username = $username;
		$this->password = $password;
		$this->authtype = $authtype;
		$this->digestRequest = $digestRequest;
	}
	
	/**
	* set the soapaction value
	*
	* @param    string $soapaction
	* @access   public
	*/
	function setSOAPAction($soapaction) {
		$this->setHeader('SOAPAction', '"' . $soapaction . '"');
	}
	
	/**
	* use http encoding
	*
	* @param    string $enc encoding style. supported values: gzip, deflate, or both
	* @access   public
	*/
	function setEncoding($enc='gzip, deflate') {
		if (function_exists('gzdeflate')) {
			$this->protocol_version = '1.1';
			$this->setHeader('Accept-Encoding', $enc);
			if (!isset($this->outgoing_headers['Connection'])) {
				$this->setHeader('Connection', 'close');
				$this->persistentConnection = false;
			}
			// deprecated as of PHP 5.3.0
			//set_magic_quotes_runtime(0);
			$this->encoding = $enc;
		}
	}
	
	/**
	* set proxy info here
	*
	* @param    string $proxyhost use an empty string to remove proxy
	* @param    string $proxyport
	* @param	string $proxyusername
	* @param	string $proxypassword
	* @param	string $proxyauthtype (basic|ntlm)
	* @access   public
	*/
	function setProxy($proxyhost, $proxyport, $proxyusername = '', $proxypassword = '', $proxyauthtype = 'basic') {
		if ($proxyhost) {
			$this->proxy = array(
				'host' => $proxyhost,
				'port' => $proxyport,
				'username' => $proxyusername,
				'password' => $proxypassword,
				'authtype' => $proxyauthtype
			);
			if ($proxyusername != '' && $proxypassword != '' && $proxyauthtype = 'basic') {
				$this->setHeader('Proxy-Authorization', ' Basic '.base64_encode($proxyusername.':'.$proxypassword));
			}
		} else {
			$this->debug('remove proxy');
			$proxy = null;
			unsetHeader('Proxy-Authorization');
		}
	}
	

	/**
	 * Test if the given string starts with a header that is to be skipped.
	 * Skippable headers result from chunked transfer and proxy requests.
	 *
	 * @param	string $data The string to check.
	 * @returns	boolean	Whether a skippable header was found.
	 * @access	private
	 */
	function isSkippableCurlHeader(&$data) {
		$skipHeaders = array(	'HTTP/1.1 100',
								'HTTP/1.0 301',
								'HTTP/1.1 301',
								'HTTP/1.0 302',
								'HTTP/1.1 302',
								'HTTP/1.0 401',
								'HTTP/1.1 401',
								'HTTP/1.0 200 Connection established');
		foreach ($skipHeaders as $hd) {
			$prefix = substr($data, 0, strlen($hd));
			if ($prefix == $hd) return true;
		}

		return false;
	}

	/**
	* decode a string that is encoded w/ "chunked' transfer encoding
 	* as defined in RFC2068 19.4.6
	*
	* @param    string $buffer
	* @param    string $lb
	* @returns	string
	* @access   public
	* @deprecated
	*/
	function decodeChunked($buffer, $lb){
		// length := 0
		$length = 0;
		$new = '';
		
		// read chunk-size, chunk-extension (if any) and CRLF
		// get the position of the linebreak
		$chunkend = strpos($buffer, $lb);
		if ($chunkend == FALSE) {
			$this->debug('no linebreak found in decodeChunked');
			return $new;
		}
		$temp = substr($buffer,0,$chunkend);
		$chunk_size = hexdec( trim($temp) );
		$chunkstart = $chunkend + strlen($lb);
		// while (chunk-size > 0) {
		while ($chunk_size > 0) {
			$this->debug("chunkstart: $chunkstart chunk_size: $chunk_size");
			$chunkend = strpos( $buffer, $lb, $chunkstart + $chunk_size);
		  	
			// Just in case we got a broken connection
		  	if ($chunkend == FALSE) {
		  	    $chunk = substr($buffer,$chunkstart);
				// append chunk-data to entity-body
		    	$new .= $chunk;
		  	    $length += strlen($chunk);
		  	    break;
			}
			
		  	// read chunk-data and CRLF
		  	$chunk = substr($buffer,$chunkstart,$chunkend-$chunkstart);
		  	// append chunk-data to entity-body
		  	$new .= $chunk;
		  	// length := length + chunk-size
		  	$length += strlen($chunk);
		  	// read chunk-size and CRLF
		  	$chunkstart = $chunkend + strlen($lb);
			
		  	$chunkend = strpos($buffer, $lb, $chunkstart) + strlen($lb);
			if ($chunkend == FALSE) {
				break; //Just in case we got a broken connection
			}
			$temp = substr($buffer,$chunkstart,$chunkend-$chunkstart);
			$chunk_size = hexdec( trim($temp) );
			$chunkstart = $chunkend;
		}
		return $new;
	}
	
	/**
	 * Writes the payload, including HTTP headers, to $this->outgoing_payload.
	 *
	 * @param	string $data HTTP body
	 * @param	string $cookie_str data for HTTP Cookie header
	 * @return	void
	 * @access	private
	 */
	function buildPayload($data, $cookie_str = '') {
		// Note: for cURL connections, $this->outgoing_payload is ignored,
		// as is the Content-Length header, but these are still created as
		// debugging guides.

		// add content-length header
		if ($this->request_method != 'GET') {
			$this->setHeader('Content-Length', strlen($data));
		}

		// start building outgoing payload:
		if ($this->proxy) {
			$uri = $this->url;
		} else {
			$uri = $this->uri;
		}
		$req = "$this->request_method $uri HTTP/$this->protocol_version";
		$this->debug("HTTP request: $req");
		$this->outgoing_payload = "$req\r\n";

		// loop thru headers, serializing
		foreach($this->outgoing_headers as $k => $v){
			$hdr = $k.': '.$v;
			$this->debug("HTTP header: $hdr");
			$this->outgoing_payload .= "$hdr\r\n";
		}

		// add any cookies
		if ($cookie_str != '') {
			$hdr = 'Cookie: '.$cookie_str;
			$this->debug("HTTP header: $hdr");
			$this->outgoing_payload .= "$hdr\r\n";
		}

		// header/body separator
		$this->outgoing_payload .= "\r\n";
		
		// add data
		$this->outgoing_payload .= $data;
	}

	/**
	* sends the SOAP request via HTTP[S]
	*
	* @param    string $data message data
	* @param	array $cookies cookies to send
	* @return	boolean	true if OK, false if problem
	* @access   private
	*/
	function sendRequest($data, $cookies = NULL) {
		// build cookie string
		$cookie_str = $this->getCookiesForRequest($cookies, (($this->scheme == 'ssl') || ($this->scheme == 'https')));

		// build payload
		$this->buildPayload($data, $cookie_str);

	  if ($this->io_method() == 'socket') {
		// send payload
		if(!fputs($this->fp, $this->outgoing_payload, strlen($this->outgoing_payload))) {
			$this->setError('couldn\'t write message data to socket');
			$this->debug('couldn\'t write message data to socket');
			return false;
		}
		$this->debug('wrote data to socket, length = ' . strlen($this->outgoing_payload));
		return true;
	  } else if ($this->io_method() == 'curl') {
		// set payload
		// cURL does say this should only be the verb, and in fact it
		// turns out that the URI and HTTP version are appended to this, which
		// some servers refuse to work with (so we no longer use this method!)
		//$this->setCurlOption(CURLOPT_CUSTOMREQUEST, $this->outgoing_payload);
		$curl_headers = array();
		foreach($this->outgoing_headers as $k => $v){
			if ($k == 'Connection' || $k == 'Content-Length' || $k == 'Host' || $k == 'Authorization' || $k == 'Proxy-Authorization') {
				$this->debug("Skip cURL header $k: $v");
			} else {
				$curl_headers[] = "$k: $v";
			}
		}
		if ($cookie_str != '') {
			$curl_headers[] = 'Cookie: ' . $cookie_str;
		}
		$this->setCurlOption(CURLOPT_HTTPHEADER, $curl_headers);
		$this->debug('set cURL HTTP headers');
		if ($this->request_method == "POST") {
	  		$this->setCurlOption(CURLOPT_POST, 1);
	  		$this->setCurlOption(CURLOPT_POSTFIELDS, $data);
			$this->debug('set cURL POST data');
	  	} else {
	  	}
		// insert custom user-set cURL options
		foreach ($this->ch_options as $key => $val) {
			$this->setCurlOption($key, $val);
		}

		$this->debug('set cURL payload');
		return true;
	  }
	}

	/**
	* gets the SOAP response via HTTP[S]
	*
	* @return	string the response (also sets member variables like incoming_payload)
	* @access   private
	*/
	function getResponse(){
		$this->incoming_payload = '';
	    
	  if ($this->io_method() == 'socket') {
	    // loop until headers have been retrieved
	    $data = '';
	    while (!isset($lb)){

			// We might EOF during header read.
			if(feof($this->fp)) {
				$this->incoming_payload = $data;
				$this->debug('found no headers before EOF after length ' . strlen($data));
				$this->debug("received before EOF:\n" . $data);
				$this->setError('server failed to send headers');
				return false;
			}

			$tmp = fgets($this->fp, 256);
			$tmplen = strlen($tmp);
			$this->debug("read line of $tmplen bytes: " . trim($tmp));

			if ($tmplen == 0) {
				$this->incoming_payload = $data;
				$this->debug('socket read of headers timed out after length ' . strlen($data));
				$this->debug("read before timeout: " . $data);
				$this->setError('socket read of headers timed out');
				return false;
			}

			$data .= $tmp;
			$pos = strpos($data,"\r\n\r\n");
			if($pos > 1){
				$lb = "\r\n";
			} else {
				$pos = strpos($data,"\n\n");
				if($pos > 1){
					$lb = "\n";
				}
			}
			// remove 100 headers
			if (isset($lb) && preg_match('/^HTTP\/1.1 100/',$data)) {
				unset($lb);
				$data = '';
			}//
		}
		// store header data
		$this->incoming_payload .= $data;
		$this->debug('found end of headers after length ' . strlen($data));
		// process headers
		$header_data = trim(substr($data,0,$pos));
		$header_array = explode($lb,$header_data);
		$this->incoming_headers = array();
		$this->incoming_cookies = array();
		foreach($header_array as $header_line){
			$arr = explode(':',$header_line, 2);
			if(count($arr) > 1){
				$header_name = strtolower(trim($arr[0]));
				$this->incoming_headers[$header_name] = trim($arr[1]);
				if ($header_name == 'set-cookie') {
					// TODO: allow multiple cookies from parseCookie
					$cookie = $this->parseCookie(trim($arr[1]));
					if ($cookie) {
						$this->incoming_cookies[] = $cookie;
						$this->debug('found cookie: ' . $cookie['name'] . ' = ' . $cookie['value']);
					} else {
						$this->debug('did not find cookie in ' . trim($arr[1]));
					}
    			}
			} else if (isset($header_name)) {
				// append continuation line to previous header
				$this->incoming_headers[$header_name] .= $lb . ' ' . $header_line;
			}
		}
		
		// loop until msg has been received
		if (isset($this->incoming_headers['transfer-encoding']) && strtolower($this->incoming_headers['transfer-encoding']) == 'chunked') {
			$content_length =  2147483647;	// ignore any content-length header
			$chunked = true;
			$this->debug("want to read chunked content");
		} elseif (isset($this->incoming_headers['content-length'])) {
			$content_length = $this->incoming_headers['content-length'];
			$chunked = false;
			$this->debug("want to read content of length $content_length");
		} else {
			$content_length =  2147483647;
			$chunked = false;
			$this->debug("want to read content to EOF");
		}
		$data = '';
		do {
			if ($chunked) {
				$tmp = fgets($this->fp, 256);
				$tmplen = strlen($tmp);
				$this->debug("read chunk line of $tmplen bytes");
				if ($tmplen == 0) {
					$this->incoming_payload = $data;
					$this->debug('socket read of chunk length timed out after length ' . strlen($data));
					$this->debug("read before timeout:\n" . $data);
					$this->setError('socket read of chunk length timed out');
					return false;
				}
				$content_length = hexdec(trim($tmp));
				$this->debug("chunk length $content_length");
			}
			$strlen = 0;
		    while (($strlen < $content_length) && (!feof($this->fp))) {
		    	$readlen = min(8192, $content_length - $strlen);
				$tmp = fread($this->fp, $readlen);
				$tmplen = strlen($tmp);
				$this->debug("read buffer of $tmplen bytes");
				if (($tmplen == 0) && (!feof($this->fp))) {
					$this->incoming_payload = $data;
					$this->debug('socket read of body timed out after length ' . strlen($data));
					$this->debug("read before timeout:\n" . $data);
					$this->setError('socket read of body timed out');
					return false;
				}
				$strlen += $tmplen;
				$data .= $tmp;
			}
			if ($chunked && ($content_length > 0)) {
				$tmp = fgets($this->fp, 256);
				$tmplen = strlen($tmp);
				$this->debug("read chunk terminator of $tmplen bytes");
				if ($tmplen == 0) {
					$this->incoming_payload = $data;
					$this->debug('socket read of chunk terminator timed out after length ' . strlen($data));
					$this->debug("read before timeout:\n" . $data);
					$this->setError('socket read of chunk terminator timed out');
					return false;
				}
			}
		} while ($chunked && ($content_length > 0) && (!feof($this->fp)));
		if (feof($this->fp)) {
			$this->debug('read to EOF');
		}
		$this->debug('read body of length ' . strlen($data));
		$this->incoming_payload .= $data;
		$this->debug('received a total of '.strlen($this->incoming_payload).' bytes of data from server');
		
		// close filepointer
		if(
			(isset($this->incoming_headers['connection']) && strtolower($this->incoming_headers['connection']) == 'close') || 
			(! $this->persistentConnection) || feof($this->fp)){
			fclose($this->fp);
			$this->fp = false;
			$this->debug('closed socket');
		}
		
		// connection was closed unexpectedly
		if($this->incoming_payload == ''){
			$this->setError('no response from server');
			return false;
		}
		
		// decode transfer-encoding
//		if(isset($this->incoming_headers['transfer-encoding']) && strtolower($this->incoming_headers['transfer-encoding']) == 'chunked'){
//			if(!$data = $this->decodeChunked($data, $lb)){
//				$this->setError('Decoding of chunked data failed');
//				return false;
//			}
			//print "<pre>\nde-chunked:\n---------------\n$data\n\n---------------\n</pre>";
			// set decoded payload
//			$this->incoming_payload = $header_data.$lb.$lb.$data;
//		}
	
	  } else if ($this->io_method() == 'curl') {
		// send and receive
		$this->debug('send and receive with cURL');
		$this->incoming_payload = curl_exec($this->ch);
		$data = $this->incoming_payload;

        $cErr = curl_error($this->ch);
		if ($cErr != '') {
        	$err = 'cURL ERROR: '.curl_errno($this->ch).': '.$cErr.'<br>';
        	// TODO: there is a PHP bug that can cause this to SEGV for CURLINFO_CONTENT_TYPE
			foreach(curl_getinfo($this->ch) as $k => $v){
				$err .= "$k: $v<br>";
			}
			$this->debug($err);
			$this->setError($err);
			curl_close($this->ch);
	    	return false;
		} else {
			//echo '<pre>';
			//var_dump(curl_getinfo($this->ch));
			//echo '</pre>';
		}
		// close curl
		$this->debug('No cURL error, closing cURL');
		curl_close($this->ch);
		
		// try removing skippable headers
		$savedata = $data;
		while ($this->isSkippableCurlHeader($data)) {
			$this->debug("Found HTTP header to skip");
			if ($pos = strpos($data,"\r\n\r\n")) {
				$data = ltrim(substr($data,$pos));
			} elseif($pos = strpos($data,"\n\n") ) {
				$data = ltrim(substr($data,$pos));
			}
		}

		if ($data == '') {
			// have nothing left; just remove 100 header(s)
			$data = $savedata;
			while (preg_match('/^HTTP\/1.1 100/',$data)) {
				if ($pos = strpos($data,"\r\n\r\n")) {
					$data = ltrim(substr($data,$pos));
				} elseif($pos = strpos($data,"\n\n") ) {
					$data = ltrim(substr($data,$pos));
				}
			}
		}
		
		// separate content from HTTP headers
		if ($pos = strpos($data,"\r\n\r\n")) {
			$lb = "\r\n";
		} elseif( $pos = strpos($data,"\n\n")) {
			$lb = "\n";
		} else {
			$this->debug('no proper separation of headers and document');
			$this->setError('no proper separation of headers and document');
			return false;
		}
		$header_data = trim(substr($data,0,$pos));
		$header_array = explode($lb,$header_data);
		$data = ltrim(substr($data,$pos));
		$this->debug('found proper separation of headers and document');
		$this->debug('cleaned data, stringlen: '.strlen($data));
		// clean headers
		foreach ($header_array as $header_line) {
			$arr = explode(':',$header_line,2);
			if(count($arr) > 1){
				$header_name = strtolower(trim($arr[0]));
				$this->incoming_headers[$header_name] = trim($arr[1]);
				if ($header_name == 'set-cookie') {
					// TODO: allow multiple cookies from parseCookie
					$cookie = $this->parseCookie(trim($arr[1]));
					if ($cookie) {
						$this->incoming_cookies[] = $cookie;
						$this->debug('found cookie: ' . $cookie['name'] . ' = ' . $cookie['value']);
					} else {
						$this->debug('did not find cookie in ' . trim($arr[1]));
					}
    			}
			} else if (isset($header_name)) {
				// append continuation line to previous header
				$this->incoming_headers[$header_name] .= $lb . ' ' . $header_line;
			}
		}
	  }

		$this->response_status_line = $header_array[0];
		$arr = explode(' ', $this->response_status_line, 3);
		$http_version = $arr[0];
		$http_status = intval($arr[1]);
		$http_reason = count($arr) > 2 ? $arr[2] : '';

 		// see if we need to resend the request with http digest authentication
 		if (isset($this->incoming_headers['location']) && ($http_status == 301 || $http_status == 302)) {
 			$this->debug("Got $http_status $http_reason with Location: " . $this->incoming_headers['location']);
 			$this->setURL($this->incoming_headers['location']);
			$this->tryagain = true;
			return false;
		}

 		// see if we need to resend the request with http digest authentication
 		if (isset($this->incoming_headers['www-authenticate']) && $http_status == 401) {
 			$this->debug("Got 401 $http_reason with WWW-Authenticate: " . $this->incoming_headers['www-authenticate']);
 			if (strstr($this->incoming_headers['www-authenticate'], "Digest ")) {
 				$this->debug('Server wants digest authentication');
 				// remove "Digest " from our elements
 				$digestString = str_replace('Digest ', '', $this->incoming_headers['www-authenticate']);
 				
 				// parse elements into array
 				$digestElements = explode(',', $digestString);
 				foreach ($digestElements as $val) {
 					$tempElement = explode('=', trim($val), 2);
 					$digestRequest[$tempElement[0]] = str_replace("\"", '', $tempElement[1]);
 				}

				// should have (at least) qop, realm, nonce
 				if (isset($digestRequest['nonce'])) {
 					$this->setCredentials($this->username, $this->password, 'digest', $digestRequest);
 					$this->tryagain = true;
 					return false;
 				}
 			}
			$this->debug('HTTP authentication failed');
			$this->setError('HTTP authentication failed');
			return false;
 		}
		
		if (
			($http_status >= 300 && $http_status <= 307) ||
			($http_status >= 400 && $http_status <= 417) ||
			($http_status >= 501 && $http_status <= 505)
		   ) {
			$this->setError("Unsupported HTTP response status $http_status $http_reason (soapclient->response has contents of the response)");
			return false;
		}

		// decode content-encoding
		if(isset($this->incoming_headers['content-encoding']) && $this->incoming_headers['content-encoding'] != ''){
			if(strtolower($this->incoming_headers['content-encoding']) == 'deflate' || strtolower($this->incoming_headers['content-encoding']) == 'gzip'){
    			// if decoding works, use it. else assume data wasn't gzencoded
    			if(function_exists('gzinflate')){
					//$timer->setMarker('starting decoding of gzip/deflated content');
					// IIS 5 requires gzinflate instead of gzuncompress (similar to IE 5 and gzdeflate v. gzcompress)
					// this means there are no Zlib headers, although there should be
					$this->debug('The gzinflate function exists');
					$datalen = strlen($data);
					if ($this->incoming_headers['content-encoding'] == 'deflate') {
						if ($degzdata = @gzinflate($data)) {
	    					$data = $degzdata;
	    					$this->debug('The payload has been inflated to ' . strlen($data) . ' bytes');
	    					if (strlen($data) < $datalen) {
	    						// test for the case that the payload has been compressed twice
		    					$this->debug('The inflated payload is smaller than the gzipped one; try again');
								if ($degzdata = @gzinflate($data)) {
			    					$data = $degzdata;
			    					$this->debug('The payload has been inflated again to ' . strlen($data) . ' bytes');
								}
	    					}
	    				} else {
	    					$this->debug('Error using gzinflate to inflate the payload');
	    					$this->setError('Error using gzinflate to inflate the payload');
	    				}
					} elseif ($this->incoming_headers['content-encoding'] == 'gzip') {
						if ($degzdata = @gzinflate(substr($data, 10))) {	// do our best
							$data = $degzdata;
	    					$this->debug('The payload has been un-gzipped to ' . strlen($data) . ' bytes');
	    					if (strlen($data) < $datalen) {
	    						// test for the case that the payload has been compressed twice
		    					$this->debug('The un-gzipped payload is smaller than the gzipped one; try again');
								if ($degzdata = @gzinflate(substr($data, 10))) {
			    					$data = $degzdata;
			    					$this->debug('The payload has been un-gzipped again to ' . strlen($data) . ' bytes');
								}
	    					}
	    				} else {
	    					$this->debug('Error using gzinflate to un-gzip the payload');
							$this->setError('Error using gzinflate to un-gzip the payload');
	    				}
					}
					//$timer->setMarker('finished decoding of gzip/deflated content');
					//print "<xmp>\nde-inflated:\n---------------\n$data\n-------------\n</xmp>";
					// set decoded payload
					$this->incoming_payload = $header_data.$lb.$lb.$data;
    			} else {
					$this->debug('The server sent compressed data. Your php install must have the Zlib extension compiled in to support this.');
					$this->setError('The server sent compressed data. Your php install must have the Zlib extension compiled in to support this.');
				}
			} else {
				$this->debug('Unsupported Content-Encoding ' . $this->incoming_headers['content-encoding']);
				$this->setError('Unsupported Content-Encoding ' . $this->incoming_headers['content-encoding']);
			}
		} else {
			$this->debug('No Content-Encoding header');
		}
		
		if(strlen($data) == 0){
			$this->debug('no data after headers!');
			$this->setError('no data present after HTTP headers');
			return false;
		}
		
		return $data;
	}

	/**
	 * sets the content-type for the SOAP message to be sent
	 *
	 * @param	string $type the content type, MIME style
	 * @param	mixed $charset character set used for encoding (or false)
	 * @access	public
	 */
	function setContentType($type, $charset = false) {
		$this->setHeader('Content-Type', $type . ($charset ? '; charset=' . $charset : ''));
	}

	/**
	 * specifies that an HTTP persistent connection should be used
	 *
	 * @return	boolean whether the request was honored by this method.
	 * @access	public
	 */
	function usePersistentConnection(){
		if (isset($this->outgoing_headers['Accept-Encoding'])) {
			return false;
		}
		$this->protocol_version = '1.1';
		$this->persistentConnection = true;
		$this->setHeader('Connection', 'Keep-Alive');
		return true;
	}

	/**
	 * parse an incoming Cookie into it's parts
	 *
	 * @param	string $cookie_str content of cookie
	 * @return	array with data of that cookie
	 * @access	private
	 */
	/*
	 * TODO: allow a Set-Cookie string to be parsed into multiple cookies
	 */
	function parseCookie($cookie_str) {
		$cookie_str = str_replace('; ', ';', $cookie_str) . ';';
		$data = preg_split('/;/', $cookie_str);
		$value_str = $data[0];

		$cookie_param = 'domain=';
		$start = strpos($cookie_str, $cookie_param);
		if ($start > 0) {
			$domain = substr($cookie_str, $start + strlen($cookie_param));
			$domain = substr($domain, 0, strpos($domain, ';'));
		} else {
			$domain = '';
		}

		$cookie_param = 'expires=';
		$start = strpos($cookie_str, $cookie_param);
		if ($start > 0) {
			$expires = substr($cookie_str, $start + strlen($cookie_param));
			$expires = substr($expires, 0, strpos($expires, ';'));
		} else {
			$expires = '';
		}

		$cookie_param = 'path=';
		$start = strpos($cookie_str, $cookie_param);
		if ( $start > 0 ) {
			$path = substr($cookie_str, $start + strlen($cookie_param));
			$path = substr($path, 0, strpos($path, ';'));
		} else {
			$path = '/';
		}
						
		$cookie_param = ';secure;';
		if (strpos($cookie_str, $cookie_param) !== FALSE) {
			$secure = true;
		} else {
			$secure = false;
		}

		$sep_pos = strpos($value_str, '=');

		if ($sep_pos) {
			$name = substr($value_str, 0, $sep_pos);
			$value = substr($value_str, $sep_pos + 1);
			$cookie= array(	'name' => $name,
			                'value' => $value,
							'domain' => $domain,
							'path' => $path,
							'expires' => $expires,
							'secure' => $secure
							);		
			return $cookie;
		}
		return false;
	}
  
	/**
	 * sort out cookies for the current request
	 *
	 * @param	array $cookies array with all cookies
	 * @param	boolean $secure is the send-content secure or not?
	 * @return	string for Cookie-HTTP-Header
	 * @access	private
	 */
	function getCookiesForRequest($cookies, $secure=false) {
		$cookie_str = '';
		if ((! is_null($cookies)) && (is_array($cookies))) {
			foreach ($cookies as $cookie) {
				if (! is_array($cookie)) {
					continue;
				}
	    		$this->debug("check cookie for validity: ".$cookie['name'].'='.$cookie['value']);
				if ((isset($cookie['expires'])) && (! empty($cookie['expires']))) {
					if (strtotime($cookie['expires']) <= time()) {
						$this->debug('cookie has expired');
						continue;
					}
				}
				if ((isset($cookie['domain'])) && (! empty($cookie['domain']))) {
					$domain = preg_quote($cookie['domain']);
					if (! preg_match("'.*$domain$'i", $this->host)) {
						$this->debug('cookie has different domain');
						continue;
					}
				}
				if ((isset($cookie['path'])) && (! empty($cookie['path']))) {
					$path = preg_quote($cookie['path']);
					if (! preg_match("'^$path.*'i", $this->path)) {
						$this->debug('cookie is for a different path');
						continue;
					}
				}
				if ((! $secure) && (isset($cookie['secure'])) && ($cookie['secure'])) {
					$this->debug('cookie is secure, transport is not');
					continue;
				}
				$cookie_str .= $cookie['name'] . '=' . $cookie['value'] . '; ';
	    		$this->debug('add cookie to Cookie-String: ' . $cookie['name'] . '=' . $cookie['value']);
			}
		}
		return $cookie_str;
  }
}


?>