<?php

class Complex {

	private $realPart = 0;
	private $imaginaryPart = 0;
	private $suffix = NULL;

	public static function _parseComplex($complexNumber)
	{
		//	Test for real number, with no imaginary part
		if (is_numeric($complexNumber))
			return array( $complexNumber, 0, NULL );

		//	Fix silly human errors
		if (strpos($complexNumber,'+-') !== FALSE)
			$complexNumber = str_replace('+-','-',$complexNumber);
		if (strpos($complexNumber,'++') !== FALSE)
			$complexNumber = str_replace('++','+',$complexNumber);
		if (strpos($complexNumber,'--') !== FALSE)
			$complexNumber = str_replace('--','-',$complexNumber);

		//	Basic validation of string, to parse out real and imaginary parts, and any suffix
		$validComplex = preg_match('/^([\-\+]?(\d+\.?\d*|\d*\.?\d+)([Ee][\-\+]?[0-2]?\d{1,3})?)([\-\+]?(\d+\.?\d*|\d*\.?\d+)([Ee][\-\+]?[0-2]?\d{1,3})?)?(([\-\+]?)([ij]?))$/ui',$complexNumber,$complexParts);

		if (!$validComplex) {
			//	Neither real nor imaginary part, so test to see if we actually have a suffix
			$validComplex = preg_match('/^([\-\+]?)([ij])$/ui',$complexNumber,$complexParts);
			if (!$validComplex) {
				throw new Exception('COMPLEX: Invalid complex number');
			}
			//	We have a suffix, so set the real to 0, the imaginary to either 1 or -1 (as defined by the sign)
			$imaginary = 1;
			if ($complexParts[1] === '-') {
				$imaginary = 0 - $imaginary;
			}
			return array(0, $imaginary, $complexParts[2]);
		}

		//	If we don't have an imaginary part, identify whether it should be +1 or -1...
		if (($complexParts[4] === '') && ($complexParts[9] !== '')) {
			if ($complexParts[7] !== $complexParts[9]) {
				$complexParts[4] = 1;
				if ($complexParts[8] === '-') {
					$complexParts[4] = -1;
				}
			//	... or if we have only the real and no imaginary part (in which case our real should be the imaginary)
			} else {
				$complexParts[4] = $complexParts[1];
				$complexParts[1] = 0;
			}
		}

		//	Return real and imaginary parts and suffix as an array, and set a default suffix if user input lazily
		return array( $complexParts[1],
					  $complexParts[4],
					  !empty($complexParts[9]) ? $complexParts[9] : 'i'
					);
	}	//	function _parseComplex()


	public function __construct($realPart, $imaginaryPart = null, $suffix = 'i')
	{
		if ($imaginaryPart === null) {
			if (is_array($realPart)) {
				//	We have an array of (potentially) real and imaginary parts, and any suffix
				list ($realPart, $imaginaryPart, $suffix) = array_values($realPart) + array(0.0, 0.0, 'i');
			} elseif((is_string($realPart)) || (is_numeric($realPart))) {
				//	We've been given a string to parse to extract the real and imaginary parts, and any suffix
				list ($realPart, $imaginaryPart, $suffix) = self::_parseComplex($realPart);
			}
		}

		//	Set parsed values in our properties
		$this->realPart = (float) $realPart;
		$this->imaginaryPart = (float) $imaginaryPart;
		$this->suffix = strtolower($suffix);
	}

	public function getReal()
	{
		return $this->realPart;
	}

	public function getImaginary()
	{
		return $this->imaginaryPart;
	}

	public function getSuffix()
	{
		return $this->suffix;
	}

	public function __toString() {
		$str = "";
		if ($this->imaginaryPart != 0.0) {
			if (abs($this->imaginaryPart) != 1.0) {
				$str .= $this->imaginaryPart . $this->suffix;
			} else {
				$str .= (($this->imaginaryPart < 0.0) ? '-' : ''). $this->suffix;
			}
		}
		if ($this->realPart != 0.0) {
			if (($str) && ($this->imaginaryPart > 0.0))
				$str = "+" . $str;
			$str = $this->realPart . $str;
		}
		if (!$str)
			$str = "0.0";
		return $str;
	}

}
