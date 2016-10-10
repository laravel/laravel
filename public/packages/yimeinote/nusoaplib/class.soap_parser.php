<?php




/**
*
* nusoap_parser class parses SOAP XML messages into native PHP values
*
* @author   Dietrich Ayala <dietrich@ganx4.com>
* @author   Scott Nichol <snichol@users.sourceforge.net>
* @version  $Id: class.soap_parser.php,v 1.42 2010/04/26 20:15:08 snichol Exp $
* @access   public
*/
class nusoap_parser extends nusoap_base {

	var $xml = '';
	var $xml_encoding = '';
	var $method = '';
	var $root_struct = '';
	var $root_struct_name = '';
	var $root_struct_namespace = '';
	var $root_header = '';
    var $document = '';			// incoming SOAP body (text)
	// determines where in the message we are (envelope,header,body,method)
	var $status = '';
	var $position = 0;
	var $depth = 0;
	var $default_namespace = '';
	var $namespaces = array();
	var $message = array();
    var $parent = '';
	var $fault = false;
	var $fault_code = '';
	var $fault_str = '';
	var $fault_detail = '';
	var $depth_array = array();
	var $debug_flag = true;
	var $soapresponse = NULL;	// parsed SOAP Body
	var $soapheader = NULL;		// parsed SOAP Header
	var $responseHeaders = '';	// incoming SOAP headers (text)
	var $body_position = 0;
	// for multiref parsing:
	// array of id => pos
	var $ids = array();
	// array of id => hrefs => pos
	var $multirefs = array();
	// toggle for auto-decoding element content
	var $decode_utf8 = true;

	/**
	* constructor that actually does the parsing
	*
	* @param    string $xml SOAP message
	* @param    string $encoding character encoding scheme of message
	* @param    string $method method for which XML is parsed (unused?)
	* @param    string $decode_utf8 whether to decode UTF-8 to ISO-8859-1
	* @access   public
	*/
	function nusoap_parser($xml,$encoding='UTF-8',$method='',$decode_utf8=true){
		parent::nusoap_base();
		$this->xml = $xml;
		$this->xml_encoding = $encoding;
		$this->method = $method;
		$this->decode_utf8 = $decode_utf8;

		// Check whether content has been read.
		if(!empty($xml)){
			// Check XML encoding
			$pos_xml = strpos($xml, '<?xml');
			if ($pos_xml !== FALSE) {
				$xml_decl = substr($xml, $pos_xml, strpos($xml, '?>', $pos_xml + 2) - $pos_xml + 1);
				if (preg_match("/encoding=[\"']([^\"']*)[\"']/", $xml_decl, $res)) {
					$xml_encoding = $res[1];
					if (strtoupper($xml_encoding) != $encoding) {
						$err = "Charset from HTTP Content-Type '" . $encoding . "' does not match encoding from XML declaration '" . $xml_encoding . "'";
						$this->debug($err);
						if ($encoding != 'ISO-8859-1' || strtoupper($xml_encoding) != 'UTF-8') {
							$this->setError($err);
							return;
						}
						// when HTTP says ISO-8859-1 (the default) and XML says UTF-8 (the typical), assume the other endpoint is just sloppy and proceed
					} else {
						$this->debug('Charset from HTTP Content-Type matches encoding from XML declaration');
					}
				} else {
					$this->debug('No encoding specified in XML declaration');
				}
			} else {
				$this->debug('No XML declaration');
			}
			$this->debug('Entering nusoap_parser(), length='.strlen($xml).', encoding='.$encoding);
			// Create an XML parser - why not xml_parser_create_ns?
			$this->parser = xml_parser_create($this->xml_encoding);
			// Set the options for parsing the XML data.
			//xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
			xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $this->xml_encoding);
			// Set the object for the parser.
			xml_set_object($this->parser, $this);
			// Set the element handlers for the parser.
			xml_set_element_handler($this->parser, 'start_element','end_element');
			xml_set_character_data_handler($this->parser,'character_data');

			// Parse the XML file.
			if(!xml_parse($this->parser,$xml,true)){
			    // Display an error message.
			    $err = sprintf('XML error parsing SOAP payload on line %d: %s',
			    xml_get_current_line_number($this->parser),
			    xml_error_string(xml_get_error_code($this->parser)));
				$this->debug($err);
				$this->debug("XML payload:\n" . $xml);
				$this->setError($err);
			} else {
				$this->debug('in nusoap_parser ctor, message:');
				$this->appendDebug($this->varDump($this->message));
				$this->debug('parsed successfully, found root struct: '.$this->root_struct.' of name '.$this->root_struct_name);
				// get final value
				$this->soapresponse = $this->message[$this->root_struct]['result'];
				// get header value
				if($this->root_header != '' && isset($this->message[$this->root_header]['result'])){
					$this->soapheader = $this->message[$this->root_header]['result'];
				}
				// resolve hrefs/ids
				if(sizeof($this->multirefs) > 0){
					foreach($this->multirefs as $id => $hrefs){
						$this->debug('resolving multirefs for id: '.$id);
						$idVal = $this->buildVal($this->ids[$id]);
						if (is_array($idVal) && isset($idVal['!id'])) {
							unset($idVal['!id']);
						}
						foreach($hrefs as $refPos => $ref){
							$this->debug('resolving href at pos '.$refPos);
							$this->multirefs[$id][$refPos] = $idVal;
						}
					}
				}
			}
			xml_parser_free($this->parser);
		} else {
			$this->debug('xml was empty, didn\'t parse!');
			$this->setError('xml was empty, didn\'t parse!');
		}
	}

	/**
	* start-element handler
	*
	* @param    resource $parser XML parser object
	* @param    string $name element name
	* @param    array $attrs associative array of attributes
	* @access   private
	*/
	function start_element($parser, $name, $attrs) {
		// position in a total number of elements, starting from 0
		// update class level pos
		$pos = $this->position++;
		// and set mine
		$this->message[$pos] = array('pos' => $pos,'children'=>'','cdata'=>'');
		// depth = how many levels removed from root?
		// set mine as current global depth and increment global depth value
		$this->message[$pos]['depth'] = $this->depth++;

		// else add self as child to whoever the current parent is
		if($pos != 0){
			$this->message[$this->parent]['children'] .= '|'.$pos;
		}
		// set my parent
		$this->message[$pos]['parent'] = $this->parent;
		// set self as current parent
		$this->parent = $pos;
		// set self as current value for this depth
		$this->depth_array[$this->depth] = $pos;
		// get element prefix
		if(strpos($name,':')){
			// get ns prefix
			$prefix = substr($name,0,strpos($name,':'));
			// get unqualified name
			$name = substr(strstr($name,':'),1);
		}
		// set status
		if ($name == 'Envelope' && $this->status == '') {
			$this->status = 'envelope';
		} elseif ($name == 'Header' && $this->status == 'envelope') {
			$this->root_header = $pos;
			$this->status = 'header';
		} elseif ($name == 'Body' && $this->status == 'envelope'){
			$this->status = 'body';
			$this->body_position = $pos;
		// set method
		} elseif($this->status == 'body' && $pos == ($this->body_position+1)) {
			$this->status = 'method';
			$this->root_struct_name = $name;
			$this->root_struct = $pos;
			$this->message[$pos]['type'] = 'struct';
			$this->debug("found root struct $this->root_struct_name, pos $this->root_struct");
		}
		// set my status
		$this->message[$pos]['status'] = $this->status;
		// set name
		$this->message[$pos]['name'] = htmlspecialchars($name);
		// set attrs
		$this->message[$pos]['attrs'] = $attrs;

		// loop through atts, logging ns and type declarations
        $attstr = '';
		foreach($attrs as $key => $value){
        	$key_prefix = $this->getPrefix($key);
			$key_localpart = $this->getLocalPart($key);
			// if ns declarations, add to class level array of valid namespaces
            if($key_prefix == 'xmlns'){
				if(preg_match('/^http:\/\/www.w3.org\/[0-9]{4}\/XMLSchema$/',$value)){
					$this->XMLSchemaVersion = $value;
					$this->namespaces['xsd'] = $this->XMLSchemaVersion;
					$this->namespaces['xsi'] = $this->XMLSchemaVersion.'-instance';
				}
                $this->namespaces[$key_localpart] = $value;
				// set method namespace
				if($name == $this->root_struct_name){
					$this->methodNamespace = $value;
				}
			// if it's a type declaration, set type
        } elseif($key_localpart == 'type'){
        		if (isset($this->message[$pos]['type']) && $this->message[$pos]['type'] == 'array') {
        			// do nothing: already processed arrayType
        		} else {
	            	$value_prefix = $this->getPrefix($value);
	                $value_localpart = $this->getLocalPart($value);
					$this->message[$pos]['type'] = $value_localpart;
					$this->message[$pos]['typePrefix'] = $value_prefix;
	                if(isset($this->namespaces[$value_prefix])){
	                	$this->message[$pos]['type_namespace'] = $this->namespaces[$value_prefix];
	                } else if(isset($attrs['xmlns:'.$value_prefix])) {
						$this->message[$pos]['type_namespace'] = $attrs['xmlns:'.$value_prefix];
	                }
					// should do something here with the namespace of specified type?
				}
			} elseif($key_localpart == 'arrayType'){
				$this->message[$pos]['type'] = 'array';
				/* do arrayType ereg here
				[1]    arrayTypeValue    ::=    atype asize
				[2]    atype    ::=    QName rank*
				[3]    rank    ::=    '[' (',')* ']'
				[4]    asize    ::=    '[' length~ ']'
				[5]    length    ::=    nextDimension* Digit+
				[6]    nextDimension    ::=    Digit+ ','
				*/
				$expr = '/([A-Za-z0-9_]+):([A-Za-z]+[A-Za-z0-9_]+)\[([0-9]+),?([0-9]*)\]/';
				if(preg_match($expr,$value,$regs)){
					$this->message[$pos]['typePrefix'] = $regs[1];
					$this->message[$pos]['arrayTypePrefix'] = $regs[1];
	                if (isset($this->namespaces[$regs[1]])) {
	                	$this->message[$pos]['arrayTypeNamespace'] = $this->namespaces[$regs[1]];
	                } else if (isset($attrs['xmlns:'.$regs[1]])) {
						$this->message[$pos]['arrayTypeNamespace'] = $attrs['xmlns:'.$regs[1]];
	                }
					$this->message[$pos]['arrayType'] = $regs[2];
					$this->message[$pos]['arraySize'] = $regs[3];
					$this->message[$pos]['arrayCols'] = $regs[4];
				}
			// specifies nil value (or not)
			} elseif ($key_localpart == 'nil'){
				$this->message[$pos]['nil'] = ($value == 'true' || $value == '1');
			// some other attribute
			} elseif ($key != 'href' && $key != 'xmlns' && $key_localpart != 'encodingStyle' && $key_localpart != 'root') {
				$this->message[$pos]['xattrs']['!' . $key] = $value;
			}

			if ($key == 'xmlns') {
				$this->default_namespace = $value;
			}
			// log id
			if($key == 'id'){
				$this->ids[$value] = $pos;
			}
			// root
			if($key_localpart == 'root' && $value == 1){
				$this->status = 'method';
				$this->root_struct_name = $name;
				$this->root_struct = $pos;
				$this->debug("found root struct $this->root_struct_name, pos $pos");
			}
            // for doclit
            $attstr .= " $key=\"$value\"";
		}
        // get namespace - must be done after namespace atts are processed
		if(isset($prefix)){
			$this->message[$pos]['namespace'] = $this->namespaces[$prefix];
			$this->default_namespace = $this->namespaces[$prefix];
		} else {
			$this->message[$pos]['namespace'] = $this->default_namespace;
		}
        if($this->status == 'header'){
        	if ($this->root_header != $pos) {
	        	$this->responseHeaders .= "<" . (isset($prefix) ? $prefix . ':' : '') . "$name$attstr>";
	        }
        } elseif($this->root_struct_name != ''){
        	$this->document .= "<" . (isset($prefix) ? $prefix . ':' : '') . "$name$attstr>";
        }
	}

	/**
	* end-element handler
	*
	* @param    resource $parser XML parser object
	* @param    string $name element name
	* @access   private
	*/
	function end_element($parser, $name) {
		// position of current element is equal to the last value left in depth_array for my depth
		$pos = $this->depth_array[$this->depth--];

        // get element prefix
		if(strpos($name,':')){
			// get ns prefix
			$prefix = substr($name,0,strpos($name,':'));
			// get unqualified name
			$name = substr(strstr($name,':'),1);
		}
		
		// build to native type
		if(isset($this->body_position) && $pos > $this->body_position){
			// deal w/ multirefs
			if(isset($this->message[$pos]['attrs']['href'])){
				// get id
				$id = substr($this->message[$pos]['attrs']['href'],1);
				// add placeholder to href array
				$this->multirefs[$id][$pos] = 'placeholder';
				// add set a reference to it as the result value
				$this->message[$pos]['result'] =& $this->multirefs[$id][$pos];
            // build complexType values
			} elseif($this->message[$pos]['children'] != ''){
				// if result has already been generated (struct/array)
				if(!isset($this->message[$pos]['result'])){
					$this->message[$pos]['result'] = $this->buildVal($pos);
				}
			// build complexType values of attributes and possibly simpleContent
			} elseif (isset($this->message[$pos]['xattrs'])) {
				if (isset($this->message[$pos]['nil']) && $this->message[$pos]['nil']) {
					$this->message[$pos]['xattrs']['!'] = null;
				} elseif (isset($this->message[$pos]['cdata']) && trim($this->message[$pos]['cdata']) != '') {
	            	if (isset($this->message[$pos]['type'])) {
						$this->message[$pos]['xattrs']['!'] = $this->decodeSimple($this->message[$pos]['cdata'], $this->message[$pos]['type'], isset($this->message[$pos]['type_namespace']) ? $this->message[$pos]['type_namespace'] : '');
					} else {
						$parent = $this->message[$pos]['parent'];
						if (isset($this->message[$parent]['type']) && ($this->message[$parent]['type'] == 'array') && isset($this->message[$parent]['arrayType'])) {
							$this->message[$pos]['xattrs']['!'] = $this->decodeSimple($this->message[$pos]['cdata'], $this->message[$parent]['arrayType'], isset($this->message[$parent]['arrayTypeNamespace']) ? $this->message[$parent]['arrayTypeNamespace'] : '');
						} else {
							$this->message[$pos]['xattrs']['!'] = $this->message[$pos]['cdata'];
						}
					}
				}
				$this->message[$pos]['result'] = $this->message[$pos]['xattrs'];
			// set value of simpleType (or nil complexType)
			} else {
            	//$this->debug('adding data for scalar value '.$this->message[$pos]['name'].' of value '.$this->message[$pos]['cdata']);
				if (isset($this->message[$pos]['nil']) && $this->message[$pos]['nil']) {
					$this->message[$pos]['xattrs']['!'] = null;
				} elseif (isset($this->message[$pos]['type'])) {
					$this->message[$pos]['result'] = $this->decodeSimple($this->message[$pos]['cdata'], $this->message[$pos]['type'], isset($this->message[$pos]['type_namespace']) ? $this->message[$pos]['type_namespace'] : '');
				} else {
					$parent = $this->message[$pos]['parent'];
					if (isset($this->message[$parent]['type']) && ($this->message[$parent]['type'] == 'array') && isset($this->message[$parent]['arrayType'])) {
						$this->message[$pos]['result'] = $this->decodeSimple($this->message[$pos]['cdata'], $this->message[$parent]['arrayType'], isset($this->message[$parent]['arrayTypeNamespace']) ? $this->message[$parent]['arrayTypeNamespace'] : '');
					} else {
						$this->message[$pos]['result'] = $this->message[$pos]['cdata'];
					}
				}

				/* add value to parent's result, if parent is struct/array
				$parent = $this->message[$pos]['parent'];
				if($this->message[$parent]['type'] != 'map'){
					if(strtolower($this->message[$parent]['type']) == 'array'){
						$this->message[$parent]['result'][] = $this->message[$pos]['result'];
					} else {
						$this->message[$parent]['result'][$this->message[$pos]['name']] = $this->message[$pos]['result'];
					}
				}
				*/
			}
		}
		
        // for doclit
        if($this->status == 'header'){
        	if ($this->root_header != $pos) {
	        	$this->responseHeaders .= "</" . (isset($prefix) ? $prefix . ':' : '') . "$name>";
	        }
        } elseif($pos >= $this->root_struct){
        	$this->document .= "</" . (isset($prefix) ? $prefix . ':' : '') . "$name>";
        }
		// switch status
		if ($pos == $this->root_struct){
			$this->status = 'body';
			$this->root_struct_namespace = $this->message[$pos]['namespace'];
		} elseif ($pos == $this->root_header) {
			$this->status = 'envelope';
		} elseif ($name == 'Body' && $this->status == 'body') {
			$this->status = 'envelope';
		} elseif ($name == 'Header' && $this->status == 'header') { // will never happen
			$this->status = 'envelope';
		} elseif ($name == 'Envelope' && $this->status == 'envelope') {
			$this->status = '';
		}
		// set parent back to my parent
		$this->parent = $this->message[$pos]['parent'];
	}

	/**
	* element content handler
	*
	* @param    resource $parser XML parser object
	* @param    string $data element content
	* @access   private
	*/
	function character_data($parser, $data){
		$pos = $this->depth_array[$this->depth];
		if ($this->xml_encoding=='UTF-8'){
			// TODO: add an option to disable this for folks who want
			// raw UTF-8 that, e.g., might not map to iso-8859-1
			// TODO: this can also be handled with xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, "ISO-8859-1");
			if($this->decode_utf8){
				$data = utf8_decode($data);
			}
		}
        $this->message[$pos]['cdata'] .= $data;
        // for doclit
        if($this->status == 'header'){
        	$this->responseHeaders .= $data;
        } else {
        	$this->document .= $data;
        }
	}

	/**
	* get the parsed message (SOAP Body)
	*
	* @return	mixed
	* @access   public
	* @deprecated	use get_soapbody instead
	*/
	function get_response(){
		return $this->soapresponse;
	}

	/**
	* get the parsed SOAP Body (NULL if there was none)
	*
	* @return	mixed
	* @access   public
	*/
	function get_soapbody(){
		return $this->soapresponse;
	}

	/**
	* get the parsed SOAP Header (NULL if there was none)
	*
	* @return	mixed
	* @access   public
	*/
	function get_soapheader(){
		return $this->soapheader;
	}

	/**
	* get the unparsed SOAP Header
	*
	* @return	string XML or empty if no Header
	* @access   public
	*/
	function getHeaders(){
	    return $this->responseHeaders;
	}

	/**
	* decodes simple types into PHP variables
	*
	* @param    string $value value to decode
	* @param    string $type XML type to decode
	* @param    string $typens XML type namespace to decode
	* @return	mixed PHP value
	* @access   private
	*/
	function decodeSimple($value, $type, $typens) {
		// TODO: use the namespace!
		if ((!isset($type)) || $type == 'string' || $type == 'long' || $type == 'unsignedLong') {
			return (string) $value;
		}
		if ($type == 'int' || $type == 'integer' || $type == 'short' || $type == 'byte') {
			return (int) $value;
		}
		if ($type == 'float' || $type == 'double' || $type == 'decimal') {
			return (double) $value;
		}
		if ($type == 'boolean') {
			if (strtolower($value) == 'false' || strtolower($value) == 'f') {
				return false;
			}
			return (boolean) $value;
		}
		if ($type == 'base64' || $type == 'base64Binary') {
			$this->debug('Decode base64 value');
			return base64_decode($value);
		}
		// obscure numeric types
		if ($type == 'nonPositiveInteger' || $type == 'negativeInteger'
			|| $type == 'nonNegativeInteger' || $type == 'positiveInteger'
			|| $type == 'unsignedInt'
			|| $type == 'unsignedShort' || $type == 'unsignedByte') {
			return (int) $value;
		}
		// bogus: parser treats array with no elements as a simple type
		if ($type == 'array') {
			return array();
		}
		// everything else
		return (string) $value;
	}

	/**
	* builds response structures for compound values (arrays/structs)
	* and scalars
	*
	* @param    integer $pos position in node tree
	* @return	mixed	PHP value
	* @access   private
	*/
	function buildVal($pos){
		if(!isset($this->message[$pos]['type'])){
			$this->message[$pos]['type'] = '';
		}
		$this->debug('in buildVal() for '.$this->message[$pos]['name']."(pos $pos) of type ".$this->message[$pos]['type']);
		// if there are children...
		if($this->message[$pos]['children'] != ''){
			$this->debug('in buildVal, there are children');
			$children = explode('|',$this->message[$pos]['children']);
			array_shift($children); // knock off empty
			// md array
			if(isset($this->message[$pos]['arrayCols']) && $this->message[$pos]['arrayCols'] != ''){
            	$r=0; // rowcount
            	$c=0; // colcount
            	foreach($children as $child_pos){
					$this->debug("in buildVal, got an MD array element: $r, $c");
					$params[$r][] = $this->message[$child_pos]['result'];
				    $c++;
				    if($c == $this->message[$pos]['arrayCols']){
				    	$c = 0;
						$r++;
				    }
                }
            // array
			} elseif($this->message[$pos]['type'] == 'array' || $this->message[$pos]['type'] == 'Array'){
                $this->debug('in buildVal, adding array '.$this->message[$pos]['name']);
                foreach($children as $child_pos){
                	$params[] = &$this->message[$child_pos]['result'];
                }
            // apache Map type: java hashtable
            } elseif($this->message[$pos]['type'] == 'Map' && $this->message[$pos]['type_namespace'] == 'http://xml.apache.org/xml-soap'){
                $this->debug('in buildVal, Java Map '.$this->message[$pos]['name']);
                foreach($children as $child_pos){
                	$kv = explode("|",$this->message[$child_pos]['children']);
                   	$params[$this->message[$kv[1]]['result']] = &$this->message[$kv[2]]['result'];
                }
            // generic compound type
            //} elseif($this->message[$pos]['type'] == 'SOAPStruct' || $this->message[$pos]['type'] == 'struct') {
		    } else {
	    		// Apache Vector type: treat as an array
                $this->debug('in buildVal, adding Java Vector or generic compound type '.$this->message[$pos]['name']);
				if ($this->message[$pos]['type'] == 'Vector' && $this->message[$pos]['type_namespace'] == 'http://xml.apache.org/xml-soap') {
					$notstruct = 1;
				} else {
					$notstruct = 0;
	            }
            	//
            	foreach($children as $child_pos){
            		if($notstruct){
            			$params[] = &$this->message[$child_pos]['result'];
            		} else {
            			if (isset($params[$this->message[$child_pos]['name']])) {
            				// de-serialize repeated element name into an array
            				if ((!is_array($params[$this->message[$child_pos]['name']])) || (!isset($params[$this->message[$child_pos]['name']][0]))) {
            					$params[$this->message[$child_pos]['name']] = array($params[$this->message[$child_pos]['name']]);
            				}
            				$params[$this->message[$child_pos]['name']][] = &$this->message[$child_pos]['result'];
            			} else {
					    	$params[$this->message[$child_pos]['name']] = &$this->message[$child_pos]['result'];
					    }
                	}
                }
			}
			if (isset($this->message[$pos]['xattrs'])) {
                $this->debug('in buildVal, handling attributes');
				foreach ($this->message[$pos]['xattrs'] as $n => $v) {
					$params[$n] = $v;
				}
			}
			// handle simpleContent
			if (isset($this->message[$pos]['cdata']) && trim($this->message[$pos]['cdata']) != '') {
                $this->debug('in buildVal, handling simpleContent');
            	if (isset($this->message[$pos]['type'])) {
					$params['!'] = $this->decodeSimple($this->message[$pos]['cdata'], $this->message[$pos]['type'], isset($this->message[$pos]['type_namespace']) ? $this->message[$pos]['type_namespace'] : '');
				} else {
					$parent = $this->message[$pos]['parent'];
					if (isset($this->message[$parent]['type']) && ($this->message[$parent]['type'] == 'array') && isset($this->message[$parent]['arrayType'])) {
						$params['!'] = $this->decodeSimple($this->message[$pos]['cdata'], $this->message[$parent]['arrayType'], isset($this->message[$parent]['arrayTypeNamespace']) ? $this->message[$parent]['arrayTypeNamespace'] : '');
					} else {
						$params['!'] = $this->message[$pos]['cdata'];
					}
				}
			}
			$ret = is_array($params) ? $params : array();
			$this->debug('in buildVal, return:');
			$this->appendDebug($this->varDump($ret));
			return $ret;
		} else {
        	$this->debug('in buildVal, no children, building scalar');
			$cdata = isset($this->message[$pos]['cdata']) ? $this->message[$pos]['cdata'] : '';
        	if (isset($this->message[$pos]['type'])) {
				$ret = $this->decodeSimple($cdata, $this->message[$pos]['type'], isset($this->message[$pos]['type_namespace']) ? $this->message[$pos]['type_namespace'] : '');
				$this->debug("in buildVal, return: $ret");
				return $ret;
			}
			$parent = $this->message[$pos]['parent'];
			if (isset($this->message[$parent]['type']) && ($this->message[$parent]['type'] == 'array') && isset($this->message[$parent]['arrayType'])) {
				$ret = $this->decodeSimple($cdata, $this->message[$parent]['arrayType'], isset($this->message[$parent]['arrayTypeNamespace']) ? $this->message[$parent]['arrayTypeNamespace'] : '');
				$this->debug("in buildVal, return: $ret");
				return $ret;
			}
           	$ret = $this->message[$pos]['cdata'];
			$this->debug("in buildVal, return: $ret");
           	return $ret;
		}
	}
}

/**
 * Backward compatibility
 */
class soap_parser extends nusoap_parser {
}


?>