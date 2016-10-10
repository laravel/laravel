<?php
/*
$Id: nusoapmime.php,v 1.13 2010/04/26 20:15:08 snichol Exp $

NuSOAP - Web Services Toolkit for PHP

Copyright (c) 2002 NuSphere Corporation

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

The NuSOAP project home is:
http://sourceforge.net/projects/nusoap/

The primary support for NuSOAP is the mailing list:
nusoap-general@lists.sourceforge.net

If you have any questions or comments, please email:

Dietrich Ayala
dietrich@ganx4.com
http://dietrich.ganx4.com/nusoap

NuSphere Corporation
http://www.nusphere.com

*/

/*require_once('nusoap.php');*/
/* PEAR Mail_MIME library */
require_once('Mail/mimeDecode.php');
require_once('Mail/mimePart.php');

/**
* nusoap_client_mime client supporting MIME attachments defined at
* http://www.w3.org/TR/SOAP-attachments.  It depends on the PEAR Mail_MIME library.
*
* @author   Scott Nichol <snichol@users.sourceforge.net>
* @author	Thanks to Guillaume and Henning Reich for posting great attachment code to the mail list
* @version  $Id: nusoapmime.php,v 1.13 2010/04/26 20:15:08 snichol Exp $
* @access   public
*/
class nusoap_client_mime extends nusoap_client {
	/**
	 * @var array Each array element in the return is an associative array with keys
	 * data, filename, contenttype, cid
	 * @access private
	 */
	var $requestAttachments = array();
	/**
	 * @var array Each array element in the return is an associative array with keys
	 * data, filename, contenttype, cid
	 * @access private
	 */
	var $responseAttachments;
	/**
	 * @var string
	 * @access private
	 */
	var $mimeContentType;
	
	/**
	* adds a MIME attachment to the current request.
	*
	* If the $data parameter contains an empty string, this method will read
	* the contents of the file named by the $filename parameter.
	*
	* If the $cid parameter is false, this method will generate the cid.
	*
	* @param string $data The data of the attachment
	* @param string $filename The filename of the attachment (default is empty string)
	* @param string $contenttype The MIME Content-Type of the attachment (default is application/octet-stream)
	* @param string $cid The content-id (cid) of the attachment (default is false)
	* @return string The content-id (cid) of the attachment
	* @access public
	*/
	function addAttachment($data, $filename = '', $contenttype = 'application/octet-stream', $cid = false) {
		if (! $cid) {
			$cid = md5(uniqid(time()));
		}

		$info['data'] = $data;
		$info['filename'] = $filename;
		$info['contenttype'] = $contenttype;
		$info['cid'] = $cid;
		
		$this->requestAttachments[] = $info;

		return $cid;
	}

	/**
	* clears the MIME attachments for the current request.
	*
	* @access public
	*/
	function clearAttachments() {
		$this->requestAttachments = array();
	}

	/**
	* gets the MIME attachments from the current response.
	*
	* Each array element in the return is an associative array with keys
	* data, filename, contenttype, cid.  These keys correspond to the parameters
	* for addAttachment.
	*
	* @return array The attachments.
	* @access public
	*/
	function getAttachments() {
		return $this->responseAttachments;
	}

	/**
	* gets the HTTP body for the current request.
	*
	* @param string $soapmsg The SOAP payload
	* @return string The HTTP body, which includes the SOAP payload
	* @access private
	*/
	function getHTTPBody($soapmsg) {
		if (count($this->requestAttachments) > 0) {
			$params['content_type'] = 'multipart/related; type="text/xml"';
			$mimeMessage = new Mail_mimePart('', $params);
			unset($params);

			$params['content_type'] = 'text/xml';
			$params['encoding']     = '8bit';
			$params['charset']      = $this->soap_defencoding;
			$mimeMessage->addSubpart($soapmsg, $params);
			
			foreach ($this->requestAttachments as $att) {
				unset($params);

				$params['content_type'] = $att['contenttype'];
				$params['encoding']     = 'base64';
				$params['disposition']  = 'attachment';
				$params['dfilename']    = $att['filename'];
				$params['cid']          = $att['cid'];

				if ($att['data'] == '' && $att['filename'] <> '') {
					if ($fd = fopen($att['filename'], 'rb')) {
						$data = fread($fd, filesize($att['filename']));
						fclose($fd);
					} else {
						$data = '';
					}
					$mimeMessage->addSubpart($data, $params);
				} else {
					$mimeMessage->addSubpart($att['data'], $params);
				}
			}

			$output = $mimeMessage->encode();
			$mimeHeaders = $output['headers'];
	
			foreach ($mimeHeaders as $k => $v) {
				$this->debug("MIME header $k: $v");
				if (strtolower($k) == 'content-type') {
					// PHP header() seems to strip leading whitespace starting
					// the second line, so force everything to one line
					$this->mimeContentType = str_replace("\r\n", " ", $v);
				}
			}
	
			return $output['body'];
		}

		return parent::getHTTPBody($soapmsg);
	}
	
	/**
	* gets the HTTP content type for the current request.
	*
	* Note: getHTTPBody must be called before this.
	*
	* @return string the HTTP content type for the current request.
	* @access private
	*/
	function getHTTPContentType() {
		if (count($this->requestAttachments) > 0) {
			return $this->mimeContentType;
		}
		return parent::getHTTPContentType();
	}
	
	/**
	* gets the HTTP content type charset for the current request.
	* returns false for non-text content types.
	*
	* Note: getHTTPBody must be called before this.
	*
	* @return string the HTTP content type charset for the current request.
	* @access private
	*/
	function getHTTPContentTypeCharset() {
		if (count($this->requestAttachments) > 0) {
			return false;
		}
		return parent::getHTTPContentTypeCharset();
	}

	/**
	* processes SOAP message returned from server
	*
	* @param	array	$headers	The HTTP headers
	* @param	string	$data		unprocessed response data from server
	* @return	mixed	value of the message, decoded into a PHP type
	* @access   private
	*/
    function parseResponse($headers, $data) {
		$this->debug('Entering parseResponse() for payload of length ' . strlen($data) . ' and type of ' . $headers['content-type']);
		$this->responseAttachments = array();
		if (strstr($headers['content-type'], 'multipart/related')) {
			$this->debug('Decode multipart/related');
			$input = '';
			foreach ($headers as $k => $v) {
				$input .= "$k: $v\r\n";
			}
			$params['input'] = $input . "\r\n" . $data;
			$params['include_bodies'] = true;
			$params['decode_bodies'] = true;
			$params['decode_headers'] = true;
			
			$structure = Mail_mimeDecode::decode($params);

			foreach ($structure->parts as $part) {
				if (!isset($part->disposition) && (strstr($part->headers['content-type'], 'text/xml'))) {
					$this->debug('Have root part of type ' . $part->headers['content-type']);
					$root = $part->body;
					$return = parent::parseResponse($part->headers, $part->body);
				} else {
					$this->debug('Have an attachment of type ' . $part->headers['content-type']);
					$info['data'] = $part->body;
					$info['filename'] = isset($part->d_parameters['filename']) ? $part->d_parameters['filename'] : '';
					$info['contenttype'] = $part->headers['content-type'];
					$info['cid'] = $part->headers['content-id'];
					$this->responseAttachments[] = $info;
				}
			}
		
			if (isset($return)) {
				$this->responseData = $root;
				return $return;
			}
			
			$this->setError('No root part found in multipart/related content');
			return '';
		}
		$this->debug('Not multipart/related');
		return parent::parseResponse($headers, $data);
	}
}

/*
 *	For backwards compatiblity, define soapclientmime unless the PHP SOAP extension is loaded.
 */
if (!extension_loaded('soap')) {
	class soapclientmime extends nusoap_client_mime {
	}
}

/**
* nusoap_server_mime server supporting MIME attachments defined at
* http://www.w3.org/TR/SOAP-attachments.  It depends on the PEAR Mail_MIME library.
*
* @author   Scott Nichol <snichol@users.sourceforge.net>
* @author	Thanks to Guillaume and Henning Reich for posting great attachment code to the mail list
* @version  $Id: nusoapmime.php,v 1.13 2010/04/26 20:15:08 snichol Exp $
* @access   public
*/
class nusoap_server_mime extends nusoap_server {
	/**
	 * @var array Each array element in the return is an associative array with keys
	 * data, filename, contenttype, cid
	 * @access private
	 */
	var $requestAttachments = array();
	/**
	 * @var array Each array element in the return is an associative array with keys
	 * data, filename, contenttype, cid
	 * @access private
	 */
	var $responseAttachments;
	/**
	 * @var string
	 * @access private
	 */
	var $mimeContentType;
	
	/**
	* adds a MIME attachment to the current response.
	*
	* If the $data parameter contains an empty string, this method will read
	* the contents of the file named by the $filename parameter.
	*
	* If the $cid parameter is false, this method will generate the cid.
	*
	* @param string $data The data of the attachment
	* @param string $filename The filename of the attachment (default is empty string)
	* @param string $contenttype The MIME Content-Type of the attachment (default is application/octet-stream)
	* @param string $cid The content-id (cid) of the attachment (default is false)
	* @return string The content-id (cid) of the attachment
	* @access public
	*/
	function addAttachment($data, $filename = '', $contenttype = 'application/octet-stream', $cid = false) {
		if (! $cid) {
			$cid = md5(uniqid(time()));
		}

		$info['data'] = $data;
		$info['filename'] = $filename;
		$info['contenttype'] = $contenttype;
		$info['cid'] = $cid;
		
		$this->responseAttachments[] = $info;

		return $cid;
	}

	/**
	* clears the MIME attachments for the current response.
	*
	* @access public
	*/
	function clearAttachments() {
		$this->responseAttachments = array();
	}

	/**
	* gets the MIME attachments from the current request.
	*
	* Each array element in the return is an associative array with keys
	* data, filename, contenttype, cid.  These keys correspond to the parameters
	* for addAttachment.
	*
	* @return array The attachments.
	* @access public
	*/
	function getAttachments() {
		return $this->requestAttachments;
	}

	/**
	* gets the HTTP body for the current response.
	*
	* @param string $soapmsg The SOAP payload
	* @return string The HTTP body, which includes the SOAP payload
	* @access private
	*/
	function getHTTPBody($soapmsg) {
		if (count($this->responseAttachments) > 0) {
			$params['content_type'] = 'multipart/related; type="text/xml"';
			$mimeMessage = new Mail_mimePart('', $params);
			unset($params);

			$params['content_type'] = 'text/xml';
			$params['encoding']     = '8bit';
			$params['charset']      = $this->soap_defencoding;
			$mimeMessage->addSubpart($soapmsg, $params);
			
			foreach ($this->responseAttachments as $att) {
				unset($params);

				$params['content_type'] = $att['contenttype'];
				$params['encoding']     = 'base64';
				$params['disposition']  = 'attachment';
				$params['dfilename']    = $att['filename'];
				$params['cid']          = $att['cid'];

				if ($att['data'] == '' && $att['filename'] <> '') {
					if ($fd = fopen($att['filename'], 'rb')) {
						$data = fread($fd, filesize($att['filename']));
						fclose($fd);
					} else {
						$data = '';
					}
					$mimeMessage->addSubpart($data, $params);
				} else {
					$mimeMessage->addSubpart($att['data'], $params);
				}
			}

			$output = $mimeMessage->encode();
			$mimeHeaders = $output['headers'];
	
			foreach ($mimeHeaders as $k => $v) {
				$this->debug("MIME header $k: $v");
				if (strtolower($k) == 'content-type') {
					// PHP header() seems to strip leading whitespace starting
					// the second line, so force everything to one line
					$this->mimeContentType = str_replace("\r\n", " ", $v);
				}
			}
	
			return $output['body'];
		}

		return parent::getHTTPBody($soapmsg);
	}
	
	/**
	* gets the HTTP content type for the current response.
	*
	* Note: getHTTPBody must be called before this.
	*
	* @return string the HTTP content type for the current response.
	* @access private
	*/
	function getHTTPContentType() {
		if (count($this->responseAttachments) > 0) {
			return $this->mimeContentType;
		}
		return parent::getHTTPContentType();
	}
	
	/**
	* gets the HTTP content type charset for the current response.
	* returns false for non-text content types.
	*
	* Note: getHTTPBody must be called before this.
	*
	* @return string the HTTP content type charset for the current response.
	* @access private
	*/
	function getHTTPContentTypeCharset() {
		if (count($this->responseAttachments) > 0) {
			return false;
		}
		return parent::getHTTPContentTypeCharset();
	}

	/**
	* processes SOAP message received from client
	*
	* @param	array	$headers	The HTTP headers
	* @param	string	$data		unprocessed request data from client
	* @return	mixed	value of the message, decoded into a PHP type
	* @access   private
	*/
    function parseRequest($headers, $data) {
		$this->debug('Entering parseRequest() for payload of length ' . strlen($data) . ' and type of ' . $headers['content-type']);
		$this->requestAttachments = array();
		if (strstr($headers['content-type'], 'multipart/related')) {
			$this->debug('Decode multipart/related');
			$input = '';
			foreach ($headers as $k => $v) {
				$input .= "$k: $v\r\n";
			}
			$params['input'] = $input . "\r\n" . $data;
			$params['include_bodies'] = true;
			$params['decode_bodies'] = true;
			$params['decode_headers'] = true;
			
			$structure = Mail_mimeDecode::decode($params);

			foreach ($structure->parts as $part) {
				if (!isset($part->disposition) && (strstr($part->headers['content-type'], 'text/xml'))) {
					$this->debug('Have root part of type ' . $part->headers['content-type']);
					$return = parent::parseRequest($part->headers, $part->body);
				} else {
					$this->debug('Have an attachment of type ' . $part->headers['content-type']);
					$info['data'] = $part->body;
					$info['filename'] = isset($part->d_parameters['filename']) ? $part->d_parameters['filename'] : '';
					$info['contenttype'] = $part->headers['content-type'];
					$info['cid'] = $part->headers['content-id'];
					$this->requestAttachments[] = $info;
				}
			}
		
			if (isset($return)) {
				return $return;
			}
			
			$this->setError('No root part found in multipart/related content');
			return;
		}
		$this->debug('Not multipart/related');
		return parent::parseRequest($headers, $data);
	}
}

/*
 *	For backwards compatiblity
 */
class nusoapservermime extends nusoap_server_mime {
}

?>
