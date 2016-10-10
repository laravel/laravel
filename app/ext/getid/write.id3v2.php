<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
///                                                            //
// write.id3v2.php                                             //
// module for writing ID3v2 tags                               //
// dependencies: module.tag.id3v2.php                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.tag.id3v2.php', __FILE__, true);

class getid3_write_id3v2
{
	public $filename;
	public $tag_data;
	public $fread_buffer_size           = 32768;    // read buffer size in bytes
	public $paddedlength                = 4096;     // minimum length of ID3v2 tag in bytes
	public $majorversion                = 3;        // ID3v2 major version (2, 3 (recommended), 4)
	public $minorversion                = 0;        // ID3v2 minor version - always 0
	public $merge_existing_data         = false;    // if true, merge new data with existing tags; if false, delete old tag data and only write new tags
	public $id3v2_default_encodingid    = 0;        // default text encoding (ISO-8859-1) if not explicitly passed
	public $id3v2_use_unsynchronisation = false;    // the specs say it should be TRUE, but most other ID3v2-aware programs are broken if unsynchronization is used, so by default don't use it.
	public $warnings                    = array();  // any non-critical errors will be stored here
	public $errors                      = array();  // any critical errors will be stored here

	public function getid3_write_id3v2() {
		return true;
	}

	public function WriteID3v2() {
		// File MUST be writeable - CHMOD(646) at least. It's best if the
		// directory is also writeable, because that method is both faster and less susceptible to errors.

		if (!empty($this->filename) && (is_writeable($this->filename) || (!file_exists($this->filename) && is_writeable(dirname($this->filename))))) {
			// Initialize getID3 engine
			$getID3 = new getID3;
			$OldThisFileInfo = $getID3->analyze($this->filename);
			if (!getid3_lib::intValueSupported($OldThisFileInfo['filesize'])) {
				$this->errors[] = 'Unable to write ID3v2 because file is larger than '.round(PHP_INT_MAX / 1073741824).'GB';
				fclose($fp_source);
				return false;
			}
			if ($this->merge_existing_data) {
				// merge with existing data
				if (!empty($OldThisFileInfo['id3v2'])) {
					$this->tag_data = $this->array_join_merge($OldThisFileInfo['id3v2'], $this->tag_data);
				}
			}
			$this->paddedlength = (isset($OldThisFileInfo['id3v2']['headerlength']) ? max($OldThisFileInfo['id3v2']['headerlength'], $this->paddedlength) : $this->paddedlength);

			if ($NewID3v2Tag = $this->GenerateID3v2Tag()) {

				if (file_exists($this->filename) && is_writeable($this->filename) && isset($OldThisFileInfo['id3v2']['headerlength']) && ($OldThisFileInfo['id3v2']['headerlength'] == strlen($NewID3v2Tag))) {

					// best and fastest method - insert-overwrite existing tag (padded to length of old tag if neccesary)
					if (file_exists($this->filename)) {

						if (is_readable($this->filename) && is_writable($this->filename) && is_file($this->filename) && ($fp = fopen($this->filename, 'r+b'))) {
							rewind($fp);
							fwrite($fp, $NewID3v2Tag, strlen($NewID3v2Tag));
							fclose($fp);
						} else {
							$this->errors[] = 'Could not fopen("'.$this->filename.'", "r+b")';
						}

					} else {

						if (is_writable($this->filename) && is_file($this->filename) && ($fp = fopen($this->filename, 'wb'))) {
							rewind($fp);
							fwrite($fp, $NewID3v2Tag, strlen($NewID3v2Tag));
							fclose($fp);
						} else {
							$this->errors[] = 'Could not fopen("'.$this->filename.'", "wb")';
						}

					}

				} else {

					if ($tempfilename = tempnam(GETID3_TEMP_DIR, 'getID3')) {
						if (is_readable($this->filename) && is_file($this->filename) && ($fp_source = fopen($this->filename, 'rb'))) {
							if (is_writable($tempfilename) && is_file($tempfilename) && ($fp_temp = fopen($tempfilename, 'wb'))) {

								fwrite($fp_temp, $NewID3v2Tag, strlen($NewID3v2Tag));

								rewind($fp_source);
								if (!empty($OldThisFileInfo['avdataoffset'])) {
									fseek($fp_source, $OldThisFileInfo['avdataoffset']);
								}

								while ($buffer = fread($fp_source, $this->fread_buffer_size)) {
									fwrite($fp_temp, $buffer, strlen($buffer));
								}

								fclose($fp_temp);
								fclose($fp_source);
								copy($tempfilename, $this->filename);
								unlink($tempfilename);
								return true;

							} else {
								$this->errors[] = 'Could not fopen("'.$tempfilename.'", "wb")';
							}
							fclose($fp_source);

						} else {
							$this->errors[] = 'Could not fopen("'.$this->filename.'", "rb")';
						}
					}
					return false;

				}

			} else {

				$this->errors[] = '$this->GenerateID3v2Tag() failed';

			}

			if (!empty($this->errors)) {
				return false;
			}
			return true;
		} else {
			$this->errors[] = 'WriteID3v2() failed: !is_writeable('.$this->filename.')';
		}
		return false;
	}

	public function RemoveID3v2() {
		// File MUST be writeable - CHMOD(646) at least. It's best if the
		// directory is also writeable, because that method is both faster and less susceptible to errors.
		if (is_writeable(dirname($this->filename))) {

			// preferred method - only one copying operation, minimal chance of corrupting
			// original file if script is interrupted, but required directory to be writeable
			if (is_readable($this->filename) && is_file($this->filename) && ($fp_source = fopen($this->filename, 'rb'))) {

				// Initialize getID3 engine
				$getID3 = new getID3;
				$OldThisFileInfo = $getID3->analyze($this->filename);
				if (!getid3_lib::intValueSupported($OldThisFileInfo['filesize'])) {
					$this->errors[] = 'Unable to remove ID3v2 because file is larger than '.round(PHP_INT_MAX / 1073741824).'GB';
					fclose($fp_source);
					return false;
				}
				rewind($fp_source);
				if ($OldThisFileInfo['avdataoffset'] !== false) {
					fseek($fp_source, $OldThisFileInfo['avdataoffset']);
				}
				if (is_writable($this->filename) && is_file($this->filename) && ($fp_temp = fopen($this->filename.'getid3tmp', 'w+b'))) {
					while ($buffer = fread($fp_source, $this->fread_buffer_size)) {
						fwrite($fp_temp, $buffer, strlen($buffer));
					}
					fclose($fp_temp);
				} else {
					$this->errors[] = 'Could not fopen("'.$this->filename.'getid3tmp", "w+b")';
				}
				fclose($fp_source);
			} else {
				$this->errors[] = 'Could not fopen("'.$this->filename.'", "rb")';
			}
			if (file_exists($this->filename)) {
				unlink($this->filename);
			}
			rename($this->filename.'getid3tmp', $this->filename);

		} elseif (is_writable($this->filename)) {

			// less desirable alternate method - double-copies the file, overwrites original file
			// and could corrupt source file if the script is interrupted or an error occurs.
			if (is_readable($this->filename) && is_file($this->filename) && ($fp_source = fopen($this->filename, 'rb'))) {

				// Initialize getID3 engine
				$getID3 = new getID3;
				$OldThisFileInfo = $getID3->analyze($this->filename);
				if (!getid3_lib::intValueSupported($OldThisFileInfo['filesize'])) {
					$this->errors[] = 'Unable to remove ID3v2 because file is larger than '.round(PHP_INT_MAX / 1073741824).'GB';
					fclose($fp_source);
					return false;
				}
				rewind($fp_source);
				if ($OldThisFileInfo['avdataoffset'] !== false) {
					fseek($fp_source, $OldThisFileInfo['avdataoffset']);
				}
				if ($fp_temp = tmpfile()) {
					while ($buffer = fread($fp_source, $this->fread_buffer_size)) {
						fwrite($fp_temp, $buffer, strlen($buffer));
					}
					fclose($fp_source);
					if (is_writable($this->filename) && is_file($this->filename) && ($fp_source = fopen($this->filename, 'wb'))) {
						rewind($fp_temp);
						while ($buffer = fread($fp_temp, $this->fread_buffer_size)) {
							fwrite($fp_source, $buffer, strlen($buffer));
						}
						fseek($fp_temp, -128, SEEK_END);
						fclose($fp_source);
					} else {
						$this->errors[] = 'Could not fopen("'.$this->filename.'", "wb")';
					}
					fclose($fp_temp);
				} else {
					$this->errors[] = 'Could not create tmpfile()';
				}
			} else {
				$this->errors[] = 'Could not fopen("'.$this->filename.'", "rb")';
			}

		} else {

			$this->errors[] = 'Directory and file both not writeable';

		}

		if (!empty($this->errors)) {
			return false;
		}
		return true;
	}


	public function GenerateID3v2TagFlags($flags) {
		switch ($this->majorversion) {
			case 4:
				// %abcd0000
				$flag  = (!empty($flags['unsynchronisation']) ? '1' : '0'); // a - Unsynchronisation
				$flag .= (!empty($flags['extendedheader']   ) ? '1' : '0'); // b - Extended header
				$flag .= (!empty($flags['experimental']     ) ? '1' : '0'); // c - Experimental indicator
				$flag .= (!empty($flags['footer']           ) ? '1' : '0'); // d - Footer present
				$flag .= '0000';
				break;

			case 3:
				// %abc00000
				$flag  = (!empty($flags['unsynchronisation']) ? '1' : '0'); // a - Unsynchronisation
				$flag .= (!empty($flags['extendedheader']   ) ? '1' : '0'); // b - Extended header
				$flag .= (!empty($flags['experimental']     ) ? '1' : '0'); // c - Experimental indicator
				$flag .= '00000';
				break;

			case 2:
				// %ab000000
				$flag  = (!empty($flags['unsynchronisation']) ? '1' : '0'); // a - Unsynchronisation
				$flag .= (!empty($flags['compression']      ) ? '1' : '0'); // b - Compression
				$flag .= '000000';
				break;

			default:
				return false;
				break;
		}
		return chr(bindec($flag));
	}


	public function GenerateID3v2FrameFlags($TagAlter=false, $FileAlter=false, $ReadOnly=false, $Compression=false, $Encryption=false, $GroupingIdentity=false, $Unsynchronisation=false, $DataLengthIndicator=false) {
		switch ($this->majorversion) {
			case 4:
				// %0abc0000 %0h00kmnp
				$flag1  = '0';
				$flag1 .= $TagAlter  ? '1' : '0'; // a - Tag alter preservation (true == discard)
				$flag1 .= $FileAlter ? '1' : '0'; // b - File alter preservation (true == discard)
				$flag1 .= $ReadOnly  ? '1' : '0'; // c - Read only (true == read only)
				$flag1 .= '0000';

				$flag2  = '0';
				$flag2 .= $GroupingIdentity    ? '1' : '0'; // h - Grouping identity (true == contains group information)
				$flag2 .= '00';
				$flag2 .= $Compression         ? '1' : '0'; // k - Compression (true == compressed)
				$flag2 .= $Encryption          ? '1' : '0'; // m - Encryption (true == encrypted)
				$flag2 .= $Unsynchronisation   ? '1' : '0'; // n - Unsynchronisation (true == unsynchronised)
				$flag2 .= $DataLengthIndicator ? '1' : '0'; // p - Data length indicator (true == data length indicator added)
				break;

			case 3:
				// %abc00000 %ijk00000
				$flag1  = $TagAlter  ? '1' : '0';  // a - Tag alter preservation (true == discard)
				$flag1 .= $FileAlter ? '1' : '0';  // b - File alter preservation (true == discard)
				$flag1 .= $ReadOnly  ? '1' : '0';  // c - Read only (true == read only)
				$flag1 .= '00000';

				$flag2  = $Compression      ? '1' : '0';      // i - Compression (true == compressed)
				$flag2 .= $Encryption       ? '1' : '0';      // j - Encryption (true == encrypted)
				$flag2 .= $GroupingIdentity ? '1' : '0';      // k - Grouping identity (true == contains group information)
				$flag2 .= '00000';
				break;

			default:
				return false;
				break;

		}
		return chr(bindec($flag1)).chr(bindec($flag2));
	}

	public function GenerateID3v2FrameData($frame_name, $source_data_array) {
		if (!getid3_id3v2::IsValidID3v2FrameName($frame_name, $this->majorversion)) {
			return false;
		}
		$framedata = '';

		if (($this->majorversion < 3) || ($this->majorversion > 4)) {

			$this->errors[] = 'Only ID3v2.3 and ID3v2.4 are supported in GenerateID3v2FrameData()';

		} else { // $this->majorversion 3 or 4

			switch ($frame_name) {
				case 'UFID':
					// 4.1   UFID Unique file identifier
					// Owner identifier        <text string> $00
					// Identifier              <up to 64 bytes binary data>
					if (strlen($source_data_array['data']) > 64) {
						$this->errors[] = 'Identifier not allowed to be longer than 64 bytes in '.$frame_name.' (supplied data was '.strlen($source_data_array['data']).' bytes long)';
					} else {
						$framedata .= str_replace("\x00", '', $source_data_array['ownerid'])."\x00";
						$framedata .= substr($source_data_array['data'], 0, 64); // max 64 bytes - truncate anything longer
					}
					break;

				case 'TXXX':
					// 4.2.2 TXXX User defined text information frame
					// Text encoding     $xx
					// Description       <text string according to encoding> $00 (00)
					// Value             <text string according to encoding>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'], $this->majorversion)) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= $source_data_array['description'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'WXXX':
					// 4.3.2 WXXX User defined URL link frame
					// Text encoding     $xx
					// Description       <text string according to encoding> $00 (00)
					// URL               <text string>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'], $this->majorversion)) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} elseif (!isset($source_data_array['data']) || !$this->IsValidURL($source_data_array['data'], false, false)) {
						//$this->errors[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
						// probably should be an error, need to rewrite IsValidURL() to handle other encodings
						$this->warnings[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= $source_data_array['description'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'IPLS':
					// 4.4  IPLS Involved people list (ID3v2.3 only)
					// Text encoding     $xx
					// People list strings    <textstrings>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'], $this->majorversion)) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'MCDI':
					// 4.4   MCDI Music CD identifier
					// CD TOC                <binary data>
					$framedata .= $source_data_array['data'];
					break;

				case 'ETCO':
					// 4.5   ETCO Event timing codes
					// Time stamp format    $xx
					//   Where time stamp format is:
					// $01  (32-bit value) MPEG frames from beginning of file
					// $02  (32-bit value) milliseconds from beginning of file
					//   Followed by a list of key events in the following format:
					// Type of event   $xx
					// Time stamp      $xx (xx ...)
					//   The 'Time stamp' is set to zero if directly at the beginning of the sound
					//   or after the previous event. All events MUST be sorted in chronological order.
					if (($source_data_array['timestampformat'] > 2) || ($source_data_array['timestampformat'] < 1)) {
						$this->errors[] = 'Invalid Time Stamp Format byte in '.$frame_name.' ('.$source_data_array['timestampformat'].')';
					} else {
						$framedata .= chr($source_data_array['timestampformat']);
						foreach ($source_data_array as $key => $val) {
							if (!$this->ID3v2IsValidETCOevent($val['typeid'])) {
								$this->errors[] = 'Invalid Event Type byte in '.$frame_name.' ('.$val['typeid'].')';
							} elseif (($key != 'timestampformat') && ($key != 'flags')) {
								if (($val['timestamp'] > 0) && ($previousETCOtimestamp >= $val['timestamp'])) {
									//   The 'Time stamp' is set to zero if directly at the beginning of the sound
									//   or after the previous event. All events MUST be sorted in chronological order.
									$this->errors[] = 'Out-of-order timestamp in '.$frame_name.' ('.$val['timestamp'].') for Event Type ('.$val['typeid'].')';
								} else {
									$framedata .= chr($val['typeid']);
									$framedata .= getid3_lib::BigEndian2String($val['timestamp'], 4, false);
								}
							}
						}
					}
					break;

				case 'MLLT':
					// 4.6   MLLT MPEG location lookup table
					// MPEG frames between reference  $xx xx
					// Bytes between reference        $xx xx xx
					// Milliseconds between reference $xx xx xx
					// Bits for bytes deviation       $xx
					// Bits for milliseconds dev.     $xx
					//   Then for every reference the following data is included;
					// Deviation in bytes         %xxx....
					// Deviation in milliseconds  %xxx....
					if (($source_data_array['framesbetweenreferences'] > 0) && ($source_data_array['framesbetweenreferences'] <= 65535)) {
						$framedata .= getid3_lib::BigEndian2String($source_data_array['framesbetweenreferences'], 2, false);
					} else {
						$this->errors[] = 'Invalid MPEG Frames Between References in '.$frame_name.' ('.$source_data_array['framesbetweenreferences'].')';
					}
					if (($source_data_array['bytesbetweenreferences'] > 0) && ($source_data_array['bytesbetweenreferences'] <= 16777215)) {
						$framedata .= getid3_lib::BigEndian2String($source_data_array['bytesbetweenreferences'], 3, false);
					} else {
						$this->errors[] = 'Invalid bytes Between References in '.$frame_name.' ('.$source_data_array['bytesbetweenreferences'].')';
					}
					if (($source_data_array['msbetweenreferences'] > 0) && ($source_data_array['msbetweenreferences'] <= 16777215)) {
						$framedata .= getid3_lib::BigEndian2String($source_data_array['msbetweenreferences'], 3, false);
					} else {
						$this->errors[] = 'Invalid Milliseconds Between References in '.$frame_name.' ('.$source_data_array['msbetweenreferences'].')';
					}
					if (!$this->IsWithinBitRange($source_data_array['bitsforbytesdeviation'], 8, false)) {
						if (($source_data_array['bitsforbytesdeviation'] % 4) == 0) {
							$framedata .= chr($source_data_array['bitsforbytesdeviation']);
						} else {
							$this->errors[] = 'Bits For Bytes Deviation in '.$frame_name.' ('.$source_data_array['bitsforbytesdeviation'].') must be a multiple of 4.';
						}
					} else {
						$this->errors[] = 'Invalid Bits For Bytes Deviation in '.$frame_name.' ('.$source_data_array['bitsforbytesdeviation'].')';
					}
					if (!$this->IsWithinBitRange($source_data_array['bitsformsdeviation'], 8, false)) {
						if (($source_data_array['bitsformsdeviation'] % 4) == 0) {
							$framedata .= chr($source_data_array['bitsformsdeviation']);
						} else {
							$this->errors[] = 'Bits For Milliseconds Deviation in '.$frame_name.' ('.$source_data_array['bitsforbytesdeviation'].') must be a multiple of 4.';
						}
					} else {
						$this->errors[] = 'Invalid Bits For Milliseconds Deviation in '.$frame_name.' ('.$source_data_array['bitsformsdeviation'].')';
					}
					foreach ($source_data_array as $key => $val) {
						if (($key != 'framesbetweenreferences') && ($key != 'bytesbetweenreferences') && ($key != 'msbetweenreferences') && ($key != 'bitsforbytesdeviation') && ($key != 'bitsformsdeviation') && ($key != 'flags')) {
							$unwrittenbitstream .= str_pad(getid3_lib::Dec2Bin($val['bytedeviation']), $source_data_array['bitsforbytesdeviation'], '0', STR_PAD_LEFT);
							$unwrittenbitstream .= str_pad(getid3_lib::Dec2Bin($val['msdeviation']),   $source_data_array['bitsformsdeviation'],    '0', STR_PAD_LEFT);
						}
					}
					for ($i = 0; $i < strlen($unwrittenbitstream); $i += 8) {
						$highnibble = bindec(substr($unwrittenbitstream, $i, 4)) << 4;
						$lownibble  = bindec(substr($unwrittenbitstream, $i + 4, 4));
						$framedata .= chr($highnibble & $lownibble);
					}
					break;

				case 'SYTC':
					// 4.7   SYTC Synchronised tempo codes
					// Time stamp format   $xx
					// Tempo data          <binary data>
					//   Where time stamp format is:
					// $01  (32-bit value) MPEG frames from beginning of file
					// $02  (32-bit value) milliseconds from beginning of file
					if (($source_data_array['timestampformat'] > 2) || ($source_data_array['timestampformat'] < 1)) {
						$this->errors[] = 'Invalid Time Stamp Format byte in '.$frame_name.' ('.$source_data_array['timestampformat'].')';
					} else {
						$framedata .= chr($source_data_array['timestampformat']);
						foreach ($source_data_array as $key => $val) {
							if (!$this->ID3v2IsValidETCOevent($val['typeid'])) {
								$this->errors[] = 'Invalid Event Type byte in '.$frame_name.' ('.$val['typeid'].')';
							} elseif (($key != 'timestampformat') && ($key != 'flags')) {
								if (($val['tempo'] < 0) || ($val['tempo'] > 510)) {
									$this->errors[] = 'Invalid Tempo (max = 510) in '.$frame_name.' ('.$val['tempo'].') at timestamp ('.$val['timestamp'].')';
								} else {
									if ($val['tempo'] > 255) {
										$framedata .= chr(255);
										$val['tempo'] -= 255;
									}
									$framedata .= chr($val['tempo']);
									$framedata .= getid3_lib::BigEndian2String($val['timestamp'], 4, false);
								}
							}
						}
					}
					break;

				case 'USLT':
					// 4.8   USLT Unsynchronised lyric/text transcription
					// Text encoding        $xx
					// Language             $xx xx xx
					// Content descriptor   <text string according to encoding> $00 (00)
					// Lyrics/text          <full text string according to encoding>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} elseif (getid3_id3v2::LanguageLookup($source_data_array['language'], true) == '') {
						$this->errors[] = 'Invalid Language in '.$frame_name.' ('.$source_data_array['language'].')';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= strtolower($source_data_array['language']);
						$framedata .= $source_data_array['description'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'SYLT':
					// 4.9   SYLT Synchronised lyric/text
					// Text encoding        $xx
					// Language             $xx xx xx
					// Time stamp format    $xx
					//   $01  (32-bit value) MPEG frames from beginning of file
					//   $02  (32-bit value) milliseconds from beginning of file
					// Content type         $xx
					// Content descriptor   <text string according to encoding> $00 (00)
					//   Terminated text to be synced (typically a syllable)
					//   Sync identifier (terminator to above string)   $00 (00)
					//   Time stamp                                     $xx (xx ...)
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} elseif (getid3_id3v2::LanguageLookup($source_data_array['language'], true) == '') {
						$this->errors[] = 'Invalid Language in '.$frame_name.' ('.$source_data_array['language'].')';
					} elseif (($source_data_array['timestampformat'] > 2) || ($source_data_array['timestampformat'] < 1)) {
						$this->errors[] = 'Invalid Time Stamp Format byte in '.$frame_name.' ('.$source_data_array['timestampformat'].')';
					} elseif (!$this->ID3v2IsValidSYLTtype($source_data_array['contenttypeid'])) {
						$this->errors[] = 'Invalid Content Type byte in '.$frame_name.' ('.$source_data_array['contenttypeid'].')';
					} elseif (!is_array($source_data_array['data'])) {
						$this->errors[] = 'Invalid Lyric/Timestamp data in '.$frame_name.' (must be an array)';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= strtolower($source_data_array['language']);
						$framedata .= chr($source_data_array['timestampformat']);
						$framedata .= chr($source_data_array['contenttypeid']);
						$framedata .= $source_data_array['description'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						ksort($source_data_array['data']);
						foreach ($source_data_array['data'] as $key => $val) {
							$framedata .= $val['data'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
							$framedata .= getid3_lib::BigEndian2String($val['timestamp'], 4, false);
						}
					}
					break;

				case 'COMM':
					// 4.10  COMM Comments
					// Text encoding          $xx
					// Language               $xx xx xx
					// Short content descrip. <text string according to encoding> $00 (00)
					// The actual text        <full text string according to encoding>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} elseif (getid3_id3v2::LanguageLookup($source_data_array['language'], true) == '') {
						$this->errors[] = 'Invalid Language in '.$frame_name.' ('.$source_data_array['language'].')';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= strtolower($source_data_array['language']);
						$framedata .= $source_data_array['description'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'RVA2':
					// 4.11  RVA2 Relative volume adjustment (2) (ID3v2.4+ only)
					// Identification          <text string> $00
					//   The 'identification' string is used to identify the situation and/or
					//   device where this adjustment should apply. The following is then
					//   repeated for every channel:
					// Type of channel         $xx
					// Volume adjustment       $xx xx
					// Bits representing peak  $xx
					// Peak volume             $xx (xx ...)
					$framedata .= str_replace("\x00", '', $source_data_array['description'])."\x00";
					foreach ($source_data_array as $key => $val) {
						if ($key != 'description') {
							$framedata .= chr($val['channeltypeid']);
							$framedata .= getid3_lib::BigEndian2String($val['volumeadjust'], 2, false, true); // signed 16-bit
							if (!$this->IsWithinBitRange($source_data_array['bitspeakvolume'], 8, false)) {
								$framedata .= chr($val['bitspeakvolume']);
								if ($val['bitspeakvolume'] > 0) {
									$framedata .= getid3_lib::BigEndian2String($val['peakvolume'], ceil($val['bitspeakvolume'] / 8), false, false);
								}
							} else {
								$this->errors[] = 'Invalid Bits Representing Peak Volume in '.$frame_name.' ('.$val['bitspeakvolume'].') (range = 0 to 255)';
							}
						}
					}
					break;

				case 'RVAD':
					// 4.12  RVAD Relative volume adjustment (ID3v2.3 only)
					// Increment/decrement     %00fedcba
					// Bits used for volume descr.        $xx
					// Relative volume change, right      $xx xx (xx ...) // a
					// Relative volume change, left       $xx xx (xx ...) // b
					// Peak volume right                  $xx xx (xx ...)
					// Peak volume left                   $xx xx (xx ...)
					// Relative volume change, right back $xx xx (xx ...) // c
					// Relative volume change, left back  $xx xx (xx ...) // d
					// Peak volume right back             $xx xx (xx ...)
					// Peak volume left back              $xx xx (xx ...)
					// Relative volume change, center     $xx xx (xx ...) // e
					// Peak volume center                 $xx xx (xx ...)
					// Relative volume change, bass       $xx xx (xx ...) // f
					// Peak volume bass                   $xx xx (xx ...)
					if (!$this->IsWithinBitRange($source_data_array['bitsvolume'], 8, false)) {
						$this->errors[] = 'Invalid Bits For Volume Description byte in '.$frame_name.' ('.$source_data_array['bitsvolume'].') (range = 1 to 255)';
					} else {
						$incdecflag .= '00';
						$incdecflag .= $source_data_array['incdec']['right']     ? '1' : '0';     // a - Relative volume change, right
						$incdecflag .= $source_data_array['incdec']['left']      ? '1' : '0';      // b - Relative volume change, left
						$incdecflag .= $source_data_array['incdec']['rightrear'] ? '1' : '0'; // c - Relative volume change, right back
						$incdecflag .= $source_data_array['incdec']['leftrear']  ? '1' : '0';  // d - Relative volume change, left back
						$incdecflag .= $source_data_array['incdec']['center']    ? '1' : '0';    // e - Relative volume change, center
						$incdecflag .= $source_data_array['incdec']['bass']      ? '1' : '0';      // f - Relative volume change, bass
						$framedata .= chr(bindec($incdecflag));
						$framedata .= chr($source_data_array['bitsvolume']);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['volumechange']['right'], ceil($source_data_array['bitsvolume'] / 8), false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['volumechange']['left'],  ceil($source_data_array['bitsvolume'] / 8), false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['peakvolume']['right'], ceil($source_data_array['bitsvolume'] / 8), false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['peakvolume']['left'],  ceil($source_data_array['bitsvolume'] / 8), false);
						if ($source_data_array['volumechange']['rightrear'] || $source_data_array['volumechange']['leftrear'] ||
							$source_data_array['peakvolume']['rightrear'] || $source_data_array['peakvolume']['leftrear'] ||
							$source_data_array['volumechange']['center'] || $source_data_array['peakvolume']['center'] ||
							$source_data_array['volumechange']['bass'] || $source_data_array['peakvolume']['bass']) {
								$framedata .= getid3_lib::BigEndian2String($source_data_array['volumechange']['rightrear'], ceil($source_data_array['bitsvolume']/8), false);
								$framedata .= getid3_lib::BigEndian2String($source_data_array['volumechange']['leftrear'],  ceil($source_data_array['bitsvolume']/8), false);
								$framedata .= getid3_lib::BigEndian2String($source_data_array['peakvolume']['rightrear'], ceil($source_data_array['bitsvolume']/8), false);
								$framedata .= getid3_lib::BigEndian2String($source_data_array['peakvolume']['leftrear'],  ceil($source_data_array['bitsvolume']/8), false);
						}
						if ($source_data_array['volumechange']['center'] || $source_data_array['peakvolume']['center'] ||
							$source_data_array['volumechange']['bass'] || $source_data_array['peakvolume']['bass']) {
								$framedata .= getid3_lib::BigEndian2String($source_data_array['volumechange']['center'], ceil($source_data_array['bitsvolume']/8), false);
								$framedata .= getid3_lib::BigEndian2String($source_data_array['peakvolume']['center'], ceil($source_data_array['bitsvolume']/8), false);
						}
						if ($source_data_array['volumechange']['bass'] || $source_data_array['peakvolume']['bass']) {
								$framedata .= getid3_lib::BigEndian2String($source_data_array['volumechange']['bass'], ceil($source_data_array['bitsvolume']/8), false);
								$framedata .= getid3_lib::BigEndian2String($source_data_array['peakvolume']['bass'], ceil($source_data_array['bitsvolume']/8), false);
						}
					}
					break;

				case 'EQU2':
					// 4.12  EQU2 Equalisation (2) (ID3v2.4+ only)
					// Interpolation method  $xx
					//   $00  Band
					//   $01  Linear
					// Identification        <text string> $00
					//   The following is then repeated for every adjustment point
					// Frequency          $xx xx
					// Volume adjustment  $xx xx
					if (($source_data_array['interpolationmethod'] < 0) || ($source_data_array['interpolationmethod'] > 1)) {
						$this->errors[] = 'Invalid Interpolation Method byte in '.$frame_name.' ('.$source_data_array['interpolationmethod'].') (valid = 0 or 1)';
					} else {
						$framedata .= chr($source_data_array['interpolationmethod']);
						$framedata .= str_replace("\x00", '', $source_data_array['description'])."\x00";
						foreach ($source_data_array['data'] as $key => $val) {
							$framedata .= getid3_lib::BigEndian2String(intval(round($key * 2)), 2, false);
							$framedata .= getid3_lib::BigEndian2String($val, 2, false, true); // signed 16-bit
						}
					}
					break;

				case 'EQUA':
					// 4.12  EQUA Equalisation (ID3v2.3 only)
					// Adjustment bits    $xx
					//   This is followed by 2 bytes + ('adjustment bits' rounded up to the
					//   nearest byte) for every equalisation band in the following format,
					//   giving a frequency range of 0 - 32767Hz:
					// Increment/decrement   %x (MSB of the Frequency)
					// Frequency             (lower 15 bits)
					// Adjustment            $xx (xx ...)
					if (!$this->IsWithinBitRange($source_data_array['bitsvolume'], 8, false)) {
						$this->errors[] = 'Invalid Adjustment Bits byte in '.$frame_name.' ('.$source_data_array['bitsvolume'].') (range = 1 to 255)';
					} else {
						$framedata .= chr($source_data_array['adjustmentbits']);
						foreach ($source_data_array as $key => $val) {
							if ($key != 'bitsvolume') {
								if (($key > 32767) || ($key < 0)) {
									$this->errors[] = 'Invalid Frequency in '.$frame_name.' ('.$key.') (range = 0 to 32767)';
								} else {
									if ($val >= 0) {
										// put MSB of frequency to 1 if increment, 0 if decrement
										$key |= 0x8000;
									}
									$framedata .= getid3_lib::BigEndian2String($key, 2, false);
									$framedata .= getid3_lib::BigEndian2String($val, ceil($source_data_array['adjustmentbits'] / 8), false);
								}
							}
						}
					}
					break;

				case 'RVRB':
					// 4.13  RVRB Reverb
					// Reverb left (ms)                 $xx xx
					// Reverb right (ms)                $xx xx
					// Reverb bounces, left             $xx
					// Reverb bounces, right            $xx
					// Reverb feedback, left to left    $xx
					// Reverb feedback, left to right   $xx
					// Reverb feedback, right to right  $xx
					// Reverb feedback, right to left   $xx
					// Premix left to right             $xx
					// Premix right to left             $xx
					if (!$this->IsWithinBitRange($source_data_array['left'], 16, false)) {
						$this->errors[] = 'Invalid Reverb Left in '.$frame_name.' ('.$source_data_array['left'].') (range = 0 to 65535)';
					} elseif (!$this->IsWithinBitRange($source_data_array['right'], 16, false)) {
						$this->errors[] = 'Invalid Reverb Left in '.$frame_name.' ('.$source_data_array['right'].') (range = 0 to 65535)';
					} elseif (!$this->IsWithinBitRange($source_data_array['bouncesL'], 8, false)) {
						$this->errors[] = 'Invalid Reverb Bounces, Left in '.$frame_name.' ('.$source_data_array['bouncesL'].') (range = 0 to 255)';
					} elseif (!$this->IsWithinBitRange($source_data_array['bouncesR'], 8, false)) {
						$this->errors[] = 'Invalid Reverb Bounces, Right in '.$frame_name.' ('.$source_data_array['bouncesR'].') (range = 0 to 255)';
					} elseif (!$this->IsWithinBitRange($source_data_array['feedbackLL'], 8, false)) {
						$this->errors[] = 'Invalid Reverb Feedback, Left-To-Left in '.$frame_name.' ('.$source_data_array['feedbackLL'].') (range = 0 to 255)';
					} elseif (!$this->IsWithinBitRange($source_data_array['feedbackLR'], 8, false)) {
						$this->errors[] = 'Invalid Reverb Feedback, Left-To-Right in '.$frame_name.' ('.$source_data_array['feedbackLR'].') (range = 0 to 255)';
					} elseif (!$this->IsWithinBitRange($source_data_array['feedbackRR'], 8, false)) {
						$this->errors[] = 'Invalid Reverb Feedback, Right-To-Right in '.$frame_name.' ('.$source_data_array['feedbackRR'].') (range = 0 to 255)';
					} elseif (!$this->IsWithinBitRange($source_data_array['feedbackRL'], 8, false)) {
						$this->errors[] = 'Invalid Reverb Feedback, Right-To-Left in '.$frame_name.' ('.$source_data_array['feedbackRL'].') (range = 0 to 255)';
					} elseif (!$this->IsWithinBitRange($source_data_array['premixLR'], 8, false)) {
						$this->errors[] = 'Invalid Premix, Left-To-Right in '.$frame_name.' ('.$source_data_array['premixLR'].') (range = 0 to 255)';
					} elseif (!$this->IsWithinBitRange($source_data_array['premixRL'], 8, false)) {
						$this->errors[] = 'Invalid Premix, Right-To-Left in '.$frame_name.' ('.$source_data_array['premixRL'].') (range = 0 to 255)';
					} else {
						$framedata .= getid3_lib::BigEndian2String($source_data_array['left'], 2, false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['right'], 2, false);
						$framedata .= chr($source_data_array['bouncesL']);
						$framedata .= chr($source_data_array['bouncesR']);
						$framedata .= chr($source_data_array['feedbackLL']);
						$framedata .= chr($source_data_array['feedbackLR']);
						$framedata .= chr($source_data_array['feedbackRR']);
						$framedata .= chr($source_data_array['feedbackRL']);
						$framedata .= chr($source_data_array['premixLR']);
						$framedata .= chr($source_data_array['premixRL']);
					}
					break;

				case 'APIC':
					// 4.14  APIC Attached picture
					// Text encoding      $xx
					// MIME type          <text string> $00
					// Picture type       $xx
					// Description        <text string according to encoding> $00 (00)
					// Picture data       <binary data>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} elseif (!$this->ID3v2IsValidAPICpicturetype($source_data_array['picturetypeid'])) {
						$this->errors[] = 'Invalid Picture Type byte in '.$frame_name.' ('.$source_data_array['picturetypeid'].') for ID3v2.'.$this->majorversion;
					} elseif (($this->majorversion >= 3) && (!$this->ID3v2IsValidAPICimageformat($source_data_array['mime']))) {
						$this->errors[] = 'Invalid MIME Type in '.$frame_name.' ('.$source_data_array['mime'].') for ID3v2.'.$this->majorversion;
					} elseif (($source_data_array['mime'] == '-->') && (!$this->IsValidURL($source_data_array['data'], false, false))) {
						//$this->errors[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
						// probably should be an error, need to rewrite IsValidURL() to handle other encodings
						$this->warnings[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= str_replace("\x00", '', $source_data_array['mime'])."\x00";
						$framedata .= chr($source_data_array['picturetypeid']);
						$framedata .= (!empty($source_data_array['description']) ? $source_data_array['description'] : '').getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'GEOB':
					// 4.15  GEOB General encapsulated object
					// Text encoding          $xx
					// MIME type              <text string> $00
					// Filename               <text string according to encoding> $00 (00)
					// Content description    <text string according to encoding> $00 (00)
					// Encapsulated object    <binary data>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
					} elseif (!$this->IsValidMIMEstring($source_data_array['mime'])) {
						$this->errors[] = 'Invalid MIME Type in '.$frame_name.' ('.$source_data_array['mime'].')';
					} elseif (!$source_data_array['description']) {
						$this->errors[] = 'Missing Description in '.$frame_name;
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= str_replace("\x00", '', $source_data_array['mime'])."\x00";
						$framedata .= $source_data_array['filename'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['description'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'PCNT':
					// 4.16  PCNT Play counter
					//   When the counter reaches all one's, one byte is inserted in
					//   front of the counter thus making the counter eight bits bigger
					// Counter        $xx xx xx xx (xx ...)
					$framedata .= getid3_lib::BigEndian2String($source_data_array['data'], 4, false);
					break;

				case 'POPM':
					// 4.17  POPM Popularimeter
					//   When the counter reaches all one's, one byte is inserted in
					//   front of the counter thus making the counter eight bits bigger
					// Email to user   <text string> $00
					// Rating          $xx
					// Counter         $xx xx xx xx (xx ...)
					if (!$this->IsWithinBitRange($source_data_array['rating'], 8, false)) {
						$this->errors[] = 'Invalid Rating byte in '.$frame_name.' ('.$source_data_array['rating'].') (range = 0 to 255)';
					} elseif (!IsValidEmail($source_data_array['email'])) {
						$this->errors[] = 'Invalid Email in '.$frame_name.' ('.$source_data_array['email'].')';
					} else {
						$framedata .= str_replace("\x00", '', $source_data_array['email'])."\x00";
						$framedata .= chr($source_data_array['rating']);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['data'], 4, false);
					}
					break;

				case 'RBUF':
					// 4.18  RBUF Recommended buffer size
					// Buffer size               $xx xx xx
					// Embedded info flag        %0000000x
					// Offset to next tag        $xx xx xx xx
					if (!$this->IsWithinBitRange($source_data_array['buffersize'], 24, false)) {
						$this->errors[] = 'Invalid Buffer Size in '.$frame_name;
					} elseif (!$this->IsWithinBitRange($source_data_array['nexttagoffset'], 32, false)) {
						$this->errors[] = 'Invalid Offset To Next Tag in '.$frame_name;
					} else {
						$framedata .= getid3_lib::BigEndian2String($source_data_array['buffersize'], 3, false);
						$flag .= '0000000';
						$flag .= $source_data_array['flags']['embededinfo'] ? '1' : '0';
						$framedata .= chr(bindec($flag));
						$framedata .= getid3_lib::BigEndian2String($source_data_array['nexttagoffset'], 4, false);
					}
					break;

				case 'AENC':
					// 4.19  AENC Audio encryption
					// Owner identifier   <text string> $00
					// Preview start      $xx xx
					// Preview length     $xx xx
					// Encryption info    <binary data>
					if (!$this->IsWithinBitRange($source_data_array['previewstart'], 16, false)) {
						$this->errors[] = 'Invalid Preview Start in '.$frame_name.' ('.$source_data_array['previewstart'].')';
					} elseif (!$this->IsWithinBitRange($source_data_array['previewlength'], 16, false)) {
						$this->errors[] = 'Invalid Preview Length in '.$frame_name.' ('.$source_data_array['previewlength'].')';
					} else {
						$framedata .= str_replace("\x00", '', $source_data_array['ownerid'])."\x00";
						$framedata .= getid3_lib::BigEndian2String($source_data_array['previewstart'], 2, false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['previewlength'], 2, false);
						$framedata .= $source_data_array['encryptioninfo'];
					}
					break;

				case 'LINK':
					// 4.20  LINK Linked information
					// Frame identifier               $xx xx xx xx
					// URL                            <text string> $00
					// ID and additional data         <text string(s)>
					if (!getid3_id3v2::IsValidID3v2FrameName($source_data_array['frameid'], $this->majorversion)) {
						$this->errors[] = 'Invalid Frame Identifier in '.$frame_name.' ('.$source_data_array['frameid'].')';
					} elseif (!$this->IsValidURL($source_data_array['data'], true, false)) {
						//$this->errors[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
						// probably should be an error, need to rewrite IsValidURL() to handle other encodings
						$this->warnings[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
					} elseif ((($source_data_array['frameid'] == 'AENC') || ($source_data_array['frameid'] == 'APIC') || ($source_data_array['frameid'] == 'GEOB') || ($source_data_array['frameid'] == 'TXXX')) && ($source_data_array['additionaldata'] == '')) {
						$this->errors[] = 'Content Descriptor must be specified as additional data for Frame Identifier of '.$source_data_array['frameid'].' in '.$frame_name;
					} elseif (($source_data_array['frameid'] == 'USER') && (getid3_id3v2::LanguageLookup($source_data_array['additionaldata'], true) == '')) {
						$this->errors[] = 'Language must be specified as additional data for Frame Identifier of '.$source_data_array['frameid'].' in '.$frame_name;
					} elseif (($source_data_array['frameid'] == 'PRIV') && ($source_data_array['additionaldata'] == '')) {
						$this->errors[] = 'Owner Identifier must be specified as additional data for Frame Identifier of '.$source_data_array['frameid'].' in '.$frame_name;
					} elseif ((($source_data_array['frameid'] == 'COMM') || ($source_data_array['frameid'] == 'SYLT') || ($source_data_array['frameid'] == 'USLT')) && ((getid3_id3v2::LanguageLookup(substr($source_data_array['additionaldata'], 0, 3), true) == '') || (substr($source_data_array['additionaldata'], 3) == ''))) {
						$this->errors[] = 'Language followed by Content Descriptor must be specified as additional data for Frame Identifier of '.$source_data_array['frameid'].' in '.$frame_name;
					} else {
						$framedata .= $source_data_array['frameid'];
						$framedata .= str_replace("\x00", '', $source_data_array['data'])."\x00";
						switch ($source_data_array['frameid']) {
							case 'COMM':
							case 'SYLT':
							case 'USLT':
							case 'PRIV':
							case 'USER':
							case 'AENC':
							case 'APIC':
							case 'GEOB':
							case 'TXXX':
								$framedata .= $source_data_array['additionaldata'];
								break;
							case 'ASPI':
							case 'ETCO':
							case 'EQU2':
							case 'MCID':
							case 'MLLT':
							case 'OWNE':
							case 'RVA2':
							case 'RVRB':
							case 'SYTC':
							case 'IPLS':
							case 'RVAD':
							case 'EQUA':
								// no additional data required
								break;
							case 'RBUF':
								if ($this->majorversion == 3) {
									// no additional data required
								} else {
									$this->errors[] = $source_data_array['frameid'].' is not a valid Frame Identifier in '.$frame_name.' (in ID3v2.'.$this->majorversion.')';
								}

							default:
								if ((substr($source_data_array['frameid'], 0, 1) == 'T') || (substr($source_data_array['frameid'], 0, 1) == 'W')) {
									// no additional data required
								} else {
									$this->errors[] = $source_data_array['frameid'].' is not a valid Frame Identifier in '.$frame_name.' (in ID3v2.'.$this->majorversion.')';
								}
								break;
						}
					}
					break;

				case 'POSS':
					// 4.21  POSS Position synchronisation frame (ID3v2.3+ only)
					// Time stamp format         $xx
					// Position                  $xx (xx ...)
					if (($source_data_array['timestampformat'] < 1) || ($source_data_array['timestampformat'] > 2)) {
						$this->errors[] = 'Invalid Time Stamp Format in '.$frame_name.' ('.$source_data_array['timestampformat'].') (valid = 1 or 2)';
					} elseif (!$this->IsWithinBitRange($source_data_array['position'], 32, false)) {
						$this->errors[] = 'Invalid Position in '.$frame_name.' ('.$source_data_array['position'].') (range = 0 to 4294967295)';
					} else {
						$framedata .= chr($source_data_array['timestampformat']);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['position'], 4, false);
					}
					break;

				case 'USER':
					// 4.22  USER Terms of use (ID3v2.3+ only)
					// Text encoding        $xx
					// Language             $xx xx xx
					// The actual text      <text string according to encoding>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].')';
					} elseif (getid3_id3v2::LanguageLookup($source_data_array['language'], true) == '') {
						$this->errors[] = 'Invalid Language in '.$frame_name.' ('.$source_data_array['language'].')';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= strtolower($source_data_array['language']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'OWNE':
					// 4.23  OWNE Ownership frame (ID3v2.3+ only)
					// Text encoding     $xx
					// Price paid        <text string> $00
					// Date of purch.    <text string>
					// Seller            <text string according to encoding>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].')';
					} elseif (!$this->IsANumber($source_data_array['pricepaid']['value'], false)) {
						$this->errors[] = 'Invalid Price Paid in '.$frame_name.' ('.$source_data_array['pricepaid']['value'].')';
					} elseif (!$this->IsValidDateStampString($source_data_array['purchasedate'])) {
						$this->errors[] = 'Invalid Date Of Purchase in '.$frame_name.' ('.$source_data_array['purchasedate'].') (format = YYYYMMDD)';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						$framedata .= str_replace("\x00", '', $source_data_array['pricepaid']['value'])."\x00";
						$framedata .= $source_data_array['purchasedate'];
						$framedata .= $source_data_array['seller'];
					}
					break;

				case 'COMR':
					// 4.24  COMR Commercial frame (ID3v2.3+ only)
					// Text encoding      $xx
					// Price string       <text string> $00
					// Valid until        <text string>
					// Contact URL        <text string> $00
					// Received as        $xx
					// Name of seller     <text string according to encoding> $00 (00)
					// Description        <text string according to encoding> $00 (00)
					// Picture MIME type  <string> $00
					// Seller logo        <binary data>
					$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
					if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
						$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].')';
					} elseif (!$this->IsValidDateStampString($source_data_array['pricevaliduntil'])) {
						$this->errors[] = 'Invalid Valid Until date in '.$frame_name.' ('.$source_data_array['pricevaliduntil'].') (format = YYYYMMDD)';
					} elseif (!$this->IsValidURL($source_data_array['contacturl'], false, true)) {
						$this->errors[] = 'Invalid Contact URL in '.$frame_name.' ('.$source_data_array['contacturl'].') (allowed schemes: http, https, ftp, mailto)';
					} elseif (!$this->ID3v2IsValidCOMRreceivedAs($source_data_array['receivedasid'])) {
						$this->errors[] = 'Invalid Received As byte in '.$frame_name.' ('.$source_data_array['contacturl'].') (range = 0 to 8)';
					} elseif (!$this->IsValidMIMEstring($source_data_array['mime'])) {
						$this->errors[] = 'Invalid MIME Type in '.$frame_name.' ('.$source_data_array['mime'].')';
					} else {
						$framedata .= chr($source_data_array['encodingid']);
						unset($pricestring);
						foreach ($source_data_array['price'] as $key => $val) {
							if ($this->ID3v2IsValidPriceString($key.$val['value'])) {
								$pricestrings[] = $key.$val['value'];
							} else {
								$this->errors[] = 'Invalid Price String in '.$frame_name.' ('.$key.$val['value'].')';
							}
						}
						$framedata .= implode('/', $pricestrings);
						$framedata .= $source_data_array['pricevaliduntil'];
						$framedata .= str_replace("\x00", '', $source_data_array['contacturl'])."\x00";
						$framedata .= chr($source_data_array['receivedasid']);
						$framedata .= $source_data_array['sellername'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['description'].getid3_id3v2::TextEncodingTerminatorLookup($source_data_array['encodingid']);
						$framedata .= $source_data_array['mime']."\x00";
						$framedata .= $source_data_array['logo'];
					}
					break;

				case 'ENCR':
					// 4.25  ENCR Encryption method registration (ID3v2.3+ only)
					// Owner identifier    <text string> $00
					// Method symbol       $xx
					// Encryption data     <binary data>
					if (!$this->IsWithinBitRange($source_data_array['methodsymbol'], 8, false)) {
						$this->errors[] = 'Invalid Group Symbol in '.$frame_name.' ('.$source_data_array['methodsymbol'].') (range = 0 to 255)';
					} else {
						$framedata .= str_replace("\x00", '', $source_data_array['ownerid'])."\x00";
						$framedata .= ord($source_data_array['methodsymbol']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'GRID':
					// 4.26  GRID Group identification registration (ID3v2.3+ only)
					// Owner identifier      <text string> $00
					// Group symbol          $xx
					// Group dependent data  <binary data>
					if (!$this->IsWithinBitRange($source_data_array['groupsymbol'], 8, false)) {
						$this->errors[] = 'Invalid Group Symbol in '.$frame_name.' ('.$source_data_array['groupsymbol'].') (range = 0 to 255)';
					} else {
						$framedata .= str_replace("\x00", '', $source_data_array['ownerid'])."\x00";
						$framedata .= ord($source_data_array['groupsymbol']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'PRIV':
					// 4.27  PRIV Private frame (ID3v2.3+ only)
					// Owner identifier      <text string> $00
					// The private data      <binary data>
					$framedata .= str_replace("\x00", '', $source_data_array['ownerid'])."\x00";
					$framedata .= $source_data_array['data'];
					break;

				case 'SIGN':
					// 4.28  SIGN Signature frame (ID3v2.4+ only)
					// Group symbol      $xx
					// Signature         <binary data>
					if (!$this->IsWithinBitRange($source_data_array['groupsymbol'], 8, false)) {
						$this->errors[] = 'Invalid Group Symbol in '.$frame_name.' ('.$source_data_array['groupsymbol'].') (range = 0 to 255)';
					} else {
						$framedata .= ord($source_data_array['groupsymbol']);
						$framedata .= $source_data_array['data'];
					}
					break;

				case 'SEEK':
					// 4.29  SEEK Seek frame (ID3v2.4+ only)
					// Minimum offset to next tag       $xx xx xx xx
					if (!$this->IsWithinBitRange($source_data_array['data'], 32, false)) {
						$this->errors[] = 'Invalid Minimum Offset in '.$frame_name.' ('.$source_data_array['data'].') (range = 0 to 4294967295)';
					} else {
						$framedata .= getid3_lib::BigEndian2String($source_data_array['data'], 4, false);
					}
					break;

				case 'ASPI':
					// 4.30  ASPI Audio seek point index (ID3v2.4+ only)
					// Indexed data start (S)         $xx xx xx xx
					// Indexed data length (L)        $xx xx xx xx
					// Number of index points (N)     $xx xx
					// Bits per index point (b)       $xx
					//   Then for every index point the following data is included:
					// Fraction at index (Fi)          $xx (xx)
					if (!$this->IsWithinBitRange($source_data_array['datastart'], 32, false)) {
						$this->errors[] = 'Invalid Indexed Data Start in '.$frame_name.' ('.$source_data_array['datastart'].') (range = 0 to 4294967295)';
					} elseif (!$this->IsWithinBitRange($source_data_array['datalength'], 32, false)) {
						$this->errors[] = 'Invalid Indexed Data Length in '.$frame_name.' ('.$source_data_array['datalength'].') (range = 0 to 4294967295)';
					} elseif (!$this->IsWithinBitRange($source_data_array['indexpoints'], 16, false)) {
						$this->errors[] = 'Invalid Number Of Index Points in '.$frame_name.' ('.$source_data_array['indexpoints'].') (range = 0 to 65535)';
					} elseif (!$this->IsWithinBitRange($source_data_array['bitsperpoint'], 8, false)) {
						$this->errors[] = 'Invalid Bits Per Index Point in '.$frame_name.' ('.$source_data_array['bitsperpoint'].') (range = 0 to 255)';
					} elseif ($source_data_array['indexpoints'] != count($source_data_array['indexes'])) {
						$this->errors[] = 'Number Of Index Points does not match actual supplied data in '.$frame_name;
					} else {
						$framedata .= getid3_lib::BigEndian2String($source_data_array['datastart'], 4, false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['datalength'], 4, false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['indexpoints'], 2, false);
						$framedata .= getid3_lib::BigEndian2String($source_data_array['bitsperpoint'], 1, false);
						foreach ($source_data_array['indexes'] as $key => $val) {
							$framedata .= getid3_lib::BigEndian2String($val, ceil($source_data_array['bitsperpoint'] / 8), false);
						}
					}
					break;

				case 'RGAD':
					//   RGAD Replay Gain Adjustment
					//   http://privatewww.essex.ac.uk/~djmrob/replaygain/
					// Peak Amplitude                     $xx $xx $xx $xx
					// Radio Replay Gain Adjustment        %aaabbbcd %dddddddd
					// Audiophile Replay Gain Adjustment   %aaabbbcd %dddddddd
					//   a - name code
					//   b - originator code
					//   c - sign bit
					//   d - replay gain adjustment

					if (($source_data_array['track_adjustment'] > 51) || ($source_data_array['track_adjustment'] < -51)) {
						$this->errors[] = 'Invalid Track Adjustment in '.$frame_name.' ('.$source_data_array['track_adjustment'].') (range = -51.0 to +51.0)';
					} elseif (($source_data_array['album_adjustment'] > 51) || ($source_data_array['album_adjustment'] < -51)) {
						$this->errors[] = 'Invalid Album Adjustment in '.$frame_name.' ('.$source_data_array['album_adjustment'].') (range = -51.0 to +51.0)';
					} elseif (!$this->ID3v2IsValidRGADname($source_data_array['raw']['track_name'])) {
						$this->errors[] = 'Invalid Track Name Code in '.$frame_name.' ('.$source_data_array['raw']['track_name'].') (range = 0 to 2)';
					} elseif (!$this->ID3v2IsValidRGADname($source_data_array['raw']['album_name'])) {
						$this->errors[] = 'Invalid Album Name Code in '.$frame_name.' ('.$source_data_array['raw']['album_name'].') (range = 0 to 2)';
					} elseif (!$this->ID3v2IsValidRGADoriginator($source_data_array['raw']['track_originator'])) {
						$this->errors[] = 'Invalid Track Originator Code in '.$frame_name.' ('.$source_data_array['raw']['track_originator'].') (range = 0 to 3)';
					} elseif (!$this->ID3v2IsValidRGADoriginator($source_data_array['raw']['album_originator'])) {
						$this->errors[] = 'Invalid Album Originator Code in '.$frame_name.' ('.$source_data_array['raw']['album_originator'].') (range = 0 to 3)';
					} else {
						$framedata .= getid3_lib::Float2String($source_data_array['peakamplitude'], 32);
						$framedata .= getid3_lib::RGADgainString($source_data_array['raw']['track_name'], $source_data_array['raw']['track_originator'], $source_data_array['track_adjustment']);
						$framedata .= getid3_lib::RGADgainString($source_data_array['raw']['album_name'], $source_data_array['raw']['album_originator'], $source_data_array['album_adjustment']);
					}
					break;

				default:
					if ((($this->majorversion == 2) && (strlen($frame_name) != 3)) || (($this->majorversion > 2) && (strlen($frame_name) != 4))) {
						$this->errors[] = 'Invalid frame name "'.$frame_name.'" for ID3v2.'.$this->majorversion;
					} elseif ($frame_name{0} == 'T') {
						// 4.2. T???  Text information frames
						// Text encoding                $xx
						// Information                  <text string(s) according to encoding>
						$source_data_array['encodingid'] = (isset($source_data_array['encodingid']) ? $source_data_array['encodingid'] : $this->id3v2_default_encodingid);
						if (!$this->ID3v2IsValidTextEncoding($source_data_array['encodingid'])) {
							$this->errors[] = 'Invalid Text Encoding in '.$frame_name.' ('.$source_data_array['encodingid'].') for ID3v2.'.$this->majorversion;
						} else {
							$framedata .= chr($source_data_array['encodingid']);
							$framedata .= $source_data_array['data'];
						}
					} elseif ($frame_name{0} == 'W') {
						// 4.3. W???  URL link frames
						// URL              <text string>
						if (!$this->IsValidURL($source_data_array['data'], false, false)) {
							//$this->errors[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
							// probably should be an error, need to rewrite IsValidURL() to handle other encodings
							$this->warnings[] = 'Invalid URL in '.$frame_name.' ('.$source_data_array['data'].')';
						} else {
							$framedata .= $source_data_array['data'];
						}
					} else {
						$this->errors[] = $frame_name.' not yet supported in $this->GenerateID3v2FrameData()';
					}
					break;
			}
		}
		if (!empty($this->errors)) {
			return false;
		}
		return $framedata;
	}

	public function ID3v2FrameIsAllowed($frame_name, $source_data_array) {
		static $PreviousFrames = array();

		if ($frame_name === null) {
			// if the writing functions are called multiple times, the static array needs to be
			// cleared - this can be done by calling $this->ID3v2FrameIsAllowed(null, '')
			$PreviousFrames = array();
			return true;
		}

		if ($this->majorversion == 4) {
			switch ($frame_name) {
				case 'UFID':
				case 'AENC':
				case 'ENCR':
				case 'GRID':
					if (!isset($source_data_array['ownerid'])) {
						$this->errors[] = '[ownerid] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['ownerid'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same OwnerID ('.$source_data_array['ownerid'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['ownerid'];
					}
					break;

				case 'TXXX':
				case 'WXXX':
				case 'RVA2':
				case 'EQU2':
				case 'APIC':
				case 'GEOB':
					if (!isset($source_data_array['description'])) {
						$this->errors[] = '[description] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['description'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Description ('.$source_data_array['description'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['description'];
					}
					break;

				case 'USER':
					if (!isset($source_data_array['language'])) {
						$this->errors[] = '[language] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['language'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Language ('.$source_data_array['language'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['language'];
					}
					break;

				case 'USLT':
				case 'SYLT':
				case 'COMM':
					if (!isset($source_data_array['language'])) {
						$this->errors[] = '[language] not specified for '.$frame_name;
					} elseif (!isset($source_data_array['description'])) {
						$this->errors[] = '[description] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['language'].$source_data_array['description'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Language + Description ('.$source_data_array['language'].' + '.$source_data_array['description'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['language'].$source_data_array['description'];
					}
					break;

				case 'POPM':
					if (!isset($source_data_array['email'])) {
						$this->errors[] = '[email] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['email'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Email ('.$source_data_array['email'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['email'];
					}
					break;

				case 'IPLS':
				case 'MCDI':
				case 'ETCO':
				case 'MLLT':
				case 'SYTC':
				case 'RVRB':
				case 'PCNT':
				case 'RBUF':
				case 'POSS':
				case 'OWNE':
				case 'SEEK':
				case 'ASPI':
				case 'RGAD':
					if (in_array($frame_name, $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed';
					} else {
						$PreviousFrames[] = $frame_name;
					}
					break;

				case 'LINK':
					// this isn't implemented quite right (yet) - it should check the target frame data for compliance
					// but right now it just allows one linked frame of each type, to be safe.
					if (!isset($source_data_array['frameid'])) {
						$this->errors[] = '[frameid] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['frameid'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same FrameID ('.$source_data_array['frameid'].')';
					} elseif (in_array($source_data_array['frameid'], $PreviousFrames)) {
						// no links to singleton tags
						$this->errors[] = 'Cannot specify a '.$frame_name.' tag to a singleton tag that already exists ('.$source_data_array['frameid'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['frameid']; // only one linked tag of this type
						$PreviousFrames[] = $source_data_array['frameid'];             // no non-linked singleton tags of this type
					}
					break;

				case 'COMR':
					//   There may be more than one 'commercial frame' in a tag, but no two may be identical
					// Checking isn't implemented at all (yet) - just assumes that it's OK.
					break;

				case 'PRIV':
				case 'SIGN':
					if (!isset($source_data_array['ownerid'])) {
						$this->errors[] = '[ownerid] not specified for '.$frame_name;
					} elseif (!isset($source_data_array['data'])) {
						$this->errors[] = '[data] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['ownerid'].$source_data_array['data'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same OwnerID + Data ('.$source_data_array['ownerid'].' + '.$source_data_array['data'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['ownerid'].$source_data_array['data'];
					}
					break;

				default:
					if (($frame_name{0} != 'T') && ($frame_name{0} != 'W')) {
						$this->errors[] = 'Frame not allowed in ID3v2.'.$this->majorversion.': '.$frame_name;
					}
					break;
			}

		} elseif ($this->majorversion == 3) {

			switch ($frame_name) {
				case 'UFID':
				case 'AENC':
				case 'ENCR':
				case 'GRID':
					if (!isset($source_data_array['ownerid'])) {
						$this->errors[] = '[ownerid] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['ownerid'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same OwnerID ('.$source_data_array['ownerid'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['ownerid'];
					}
					break;

				case 'TXXX':
				case 'WXXX':
				case 'APIC':
				case 'GEOB':
					if (!isset($source_data_array['description'])) {
						$this->errors[] = '[description] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['description'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Description ('.$source_data_array['description'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['description'];
					}
					break;

				case 'USER':
					if (!isset($source_data_array['language'])) {
						$this->errors[] = '[language] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['language'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Language ('.$source_data_array['language'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['language'];
					}
					break;

				case 'USLT':
				case 'SYLT':
				case 'COMM':
					if (!isset($source_data_array['language'])) {
						$this->errors[] = '[language] not specified for '.$frame_name;
					} elseif (!isset($source_data_array['description'])) {
						$this->errors[] = '[description] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['language'].$source_data_array['description'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Language + Description ('.$source_data_array['language'].' + '.$source_data_array['description'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['language'].$source_data_array['description'];
					}
					break;

				case 'POPM':
					if (!isset($source_data_array['email'])) {
						$this->errors[] = '[email] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['email'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Email ('.$source_data_array['email'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['email'];
					}
					break;

				case 'IPLS':
				case 'MCDI':
				case 'ETCO':
				case 'MLLT':
				case 'SYTC':
				case 'RVAD':
				case 'EQUA':
				case 'RVRB':
				case 'PCNT':
				case 'RBUF':
				case 'POSS':
				case 'OWNE':
				case 'RGAD':
					if (in_array($frame_name, $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed';
					} else {
						$PreviousFrames[] = $frame_name;
					}
					break;

				case 'LINK':
					// this isn't implemented quite right (yet) - it should check the target frame data for compliance
					// but right now it just allows one linked frame of each type, to be safe.
					if (!isset($source_data_array['frameid'])) {
						$this->errors[] = '[frameid] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['frameid'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same FrameID ('.$source_data_array['frameid'].')';
					} elseif (in_array($source_data_array['frameid'], $PreviousFrames)) {
						// no links to singleton tags
						$this->errors[] = 'Cannot specify a '.$frame_name.' tag to a singleton tag that already exists ('.$source_data_array['frameid'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['frameid']; // only one linked tag of this type
						$PreviousFrames[] = $source_data_array['frameid'];             // no non-linked singleton tags of this type
					}
					break;

				case 'COMR':
					//   There may be more than one 'commercial frame' in a tag, but no two may be identical
					// Checking isn't implemented at all (yet) - just assumes that it's OK.
					break;

				case 'PRIV':
					if (!isset($source_data_array['ownerid'])) {
						$this->errors[] = '[ownerid] not specified for '.$frame_name;
					} elseif (!isset($source_data_array['data'])) {
						$this->errors[] = '[data] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['ownerid'].$source_data_array['data'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same OwnerID + Data ('.$source_data_array['ownerid'].' + '.$source_data_array['data'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['ownerid'].$source_data_array['data'];
					}
					break;

				default:
					if (($frame_name{0} != 'T') && ($frame_name{0} != 'W')) {
						$this->errors[] = 'Frame not allowed in ID3v2.'.$this->majorversion.': '.$frame_name;
					}
					break;
			}

		} elseif ($this->majorversion == 2) {

			switch ($frame_name) {
				case 'UFI':
				case 'CRM':
				case 'CRA':
					if (!isset($source_data_array['ownerid'])) {
						$this->errors[] = '[ownerid] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['ownerid'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same OwnerID ('.$source_data_array['ownerid'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['ownerid'];
					}
					break;

				case 'TXX':
				case 'WXX':
				case 'PIC':
				case 'GEO':
					if (!isset($source_data_array['description'])) {
						$this->errors[] = '[description] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['description'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Description ('.$source_data_array['description'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['description'];
					}
					break;

				case 'ULT':
				case 'SLT':
				case 'COM':
					if (!isset($source_data_array['language'])) {
						$this->errors[] = '[language] not specified for '.$frame_name;
					} elseif (!isset($source_data_array['description'])) {
						$this->errors[] = '[description] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['language'].$source_data_array['description'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Language + Description ('.$source_data_array['language'].' + '.$source_data_array['description'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['language'].$source_data_array['description'];
					}
					break;

				case 'POP':
					if (!isset($source_data_array['email'])) {
						$this->errors[] = '[email] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['email'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same Email ('.$source_data_array['email'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['email'];
					}
					break;

				case 'IPL':
				case 'MCI':
				case 'ETC':
				case 'MLL':
				case 'STC':
				case 'RVA':
				case 'EQU':
				case 'REV':
				case 'CNT':
				case 'BUF':
					if (in_array($frame_name, $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed';
					} else {
						$PreviousFrames[] = $frame_name;
					}
					break;

				case 'LNK':
					// this isn't implemented quite right (yet) - it should check the target frame data for compliance
					// but right now it just allows one linked frame of each type, to be safe.
					if (!isset($source_data_array['frameid'])) {
						$this->errors[] = '[frameid] not specified for '.$frame_name;
					} elseif (in_array($frame_name.$source_data_array['frameid'], $PreviousFrames)) {
						$this->errors[] = 'Only one '.$frame_name.' tag allowed with the same FrameID ('.$source_data_array['frameid'].')';
					} elseif (in_array($source_data_array['frameid'], $PreviousFrames)) {
						// no links to singleton tags
						$this->errors[] = 'Cannot specify a '.$frame_name.' tag to a singleton tag that already exists ('.$source_data_array['frameid'].')';
					} else {
						$PreviousFrames[] = $frame_name.$source_data_array['frameid']; // only one linked tag of this type
						$PreviousFrames[] = $source_data_array['frameid'];             // no non-linked singleton tags of this type
					}
					break;

				default:
					if (($frame_name{0} != 'T') && ($frame_name{0} != 'W')) {
						$this->errors[] = 'Frame not allowed in ID3v2.'.$this->majorversion.': '.$frame_name;
					}
					break;
			}
		}

		if (!empty($this->errors)) {
			return false;
		}
		return true;
	}

	public function GenerateID3v2Tag($noerrorsonly=true) {
		$this->ID3v2FrameIsAllowed(null, ''); // clear static array in case this isn't the first call to $this->GenerateID3v2Tag()

		$tagstring = '';
		if (is_array($this->tag_data)) {
			foreach ($this->tag_data as $frame_name => $frame_rawinputdata) {
				foreach ($frame_rawinputdata as $irrelevantindex => $source_data_array) {
					if (getid3_id3v2::IsValidID3v2FrameName($frame_name, $this->majorversion)) {
						unset($frame_length);
						unset($frame_flags);
						$frame_data = false;
						if ($this->ID3v2FrameIsAllowed($frame_name, $source_data_array)) {
							if(array_key_exists('description', $source_data_array) && array_key_exists('encodingid', $source_data_array) && array_key_exists('encoding', $this->tag_data)) {
								$source_data_array['description'] = getid3_lib::iconv_fallback($this->tag_data['encoding'], $source_data_array['encoding'], $source_data_array['description']);
							}
							if ($frame_data = $this->GenerateID3v2FrameData($frame_name, $source_data_array)) {
								$FrameUnsynchronisation = false;
								if ($this->majorversion >= 4) {
									// frame-level unsynchronisation
									$unsynchdata = $frame_data;
									if ($this->id3v2_use_unsynchronisation) {
										$unsynchdata = $this->Unsynchronise($frame_data);
									}
									if (strlen($unsynchdata) != strlen($frame_data)) {
										// unsynchronisation needed
										$FrameUnsynchronisation = true;
										$frame_data = $unsynchdata;
										if (isset($TagUnsynchronisation) && $TagUnsynchronisation === false) {
											// only set to true if ALL frames are unsynchronised
										} else {
											$TagUnsynchronisation = true;
										}
									} else {
										if (isset($TagUnsynchronisation)) {
											$TagUnsynchronisation = false;
										}
									}
									unset($unsynchdata);

									$frame_length = getid3_lib::BigEndian2String(strlen($frame_data), 4, true);
								} else {
									$frame_length = getid3_lib::BigEndian2String(strlen($frame_data), 4, false);
								}
								$frame_flags  = $this->GenerateID3v2FrameFlags($this->ID3v2FrameFlagsLookupTagAlter($frame_name), $this->ID3v2FrameFlagsLookupFileAlter($frame_name), false, false, false, false, $FrameUnsynchronisation, false);
							}
						} else {
							$this->errors[] = 'Frame "'.$frame_name.'" is NOT allowed';
						}
						if ($frame_data === false) {
							$this->errors[] = '$this->GenerateID3v2FrameData() failed for "'.$frame_name.'"';
							if ($noerrorsonly) {
								return false;
							} else {
								unset($frame_name);
							}
						}
					} else {
						// ignore any invalid frame names, including 'title', 'header', etc
						$this->warnings[] = 'Ignoring invalid ID3v2 frame type: "'.$frame_name.'"';
						unset($frame_name);
						unset($frame_length);
						unset($frame_flags);
						unset($frame_data);
					}
					if (isset($frame_name) && isset($frame_length) && isset($frame_flags) && isset($frame_data)) {
						$tagstring .= $frame_name.$frame_length.$frame_flags.$frame_data;
					}
				}
			}

			if (!isset($TagUnsynchronisation)) {
				$TagUnsynchronisation = false;
			}
			if (($this->majorversion <= 3) && $this->id3v2_use_unsynchronisation) {
				// tag-level unsynchronisation
				$unsynchdata = $this->Unsynchronise($tagstring);
				if (strlen($unsynchdata) != strlen($tagstring)) {
					// unsynchronisation needed
					$TagUnsynchronisation = true;
					$tagstring = $unsynchdata;
				}
			}

			while ($this->paddedlength < (strlen($tagstring) + getid3_id3v2::ID3v2HeaderLength($this->majorversion))) {
				$this->paddedlength += 1024;
			}

			$footer = false; // ID3v2 footers not yet supported in getID3()
			if (!$footer && ($this->paddedlength > (strlen($tagstring) + getid3_id3v2::ID3v2HeaderLength($this->majorversion)))) {
				// pad up to $paddedlength bytes if unpadded tag is shorter than $paddedlength
				// "Furthermore it MUST NOT have any padding when a tag footer is added to the tag."
				if (($this->paddedlength - strlen($tagstring) - getid3_id3v2::ID3v2HeaderLength($this->majorversion)) > 0) {
					$tagstring .= str_repeat("\x00", $this->paddedlength - strlen($tagstring) - getid3_id3v2::ID3v2HeaderLength($this->majorversion));
				}
			}
			if ($this->id3v2_use_unsynchronisation && (substr($tagstring, strlen($tagstring) - 1, 1) == "\xFF")) {
				// special unsynchronisation case:
				// if last byte == $FF then appended a $00
				$TagUnsynchronisation = true;
				$tagstring .= "\x00";
			}

			$tagheader  = 'ID3';
			$tagheader .= chr($this->majorversion);
			$tagheader .= chr($this->minorversion);
			$tagheader .= $this->GenerateID3v2TagFlags(array('unsynchronisation'=>$TagUnsynchronisation));
			$tagheader .= getid3_lib::BigEndian2String(strlen($tagstring), 4, true);

			return $tagheader.$tagstring;
		}
		$this->errors[] = 'tag_data is not an array in GenerateID3v2Tag()';
		return false;
	}

	public function ID3v2IsValidPriceString($pricestring) {
		if (getid3_id3v2::LanguageLookup(substr($pricestring, 0, 3), true) == '') {
			return false;
		} elseif (!$this->IsANumber(substr($pricestring, 3), true)) {
			return false;
		}
		return true;
	}

	public function ID3v2FrameFlagsLookupTagAlter($framename) {
		// unfinished
		switch ($framename) {
			case 'RGAD':
				$allow = true;
			default:
				$allow = false;
				break;
		}
		return $allow;
	}

	public function ID3v2FrameFlagsLookupFileAlter($framename) {
		// unfinished
		switch ($framename) {
			case 'RGAD':
				return false;
				break;

			default:
				return false;
				break;
		}
	}

	public function ID3v2IsValidETCOevent($eventid) {
		if (($eventid < 0) || ($eventid > 0xFF)) {
			// outside range of 1 byte
			return false;
		} elseif (($eventid >= 0xF0) && ($eventid <= 0xFC)) {
			// reserved for future use
			return false;
		} elseif (($eventid >= 0x17) && ($eventid <= 0xDF)) {
			// reserved for future use
			return false;
		} elseif (($eventid >= 0x0E) && ($eventid <= 0x16) && ($this->majorversion == 2)) {
			// not defined in ID3v2.2
			return false;
		} elseif (($eventid >= 0x15) && ($eventid <= 0x16) && ($this->majorversion == 3)) {
			// not defined in ID3v2.3
			return false;
		}
		return true;
	}

	public function ID3v2IsValidSYLTtype($contenttype) {
		if (($contenttype >= 0) && ($contenttype <= 8) && ($this->majorversion == 4)) {
			return true;
		} elseif (($contenttype >= 0) && ($contenttype <= 6) && ($this->majorversion == 3)) {
			return true;
		}
		return false;
	}

	public function ID3v2IsValidRVA2channeltype($channeltype) {
		if (($channeltype >= 0) && ($channeltype <= 8) && ($this->majorversion == 4)) {
			return true;
		}
		return false;
	}

	public function ID3v2IsValidAPICpicturetype($picturetype) {
		if (($picturetype >= 0) && ($picturetype <= 0x14) && ($this->majorversion >= 2) && ($this->majorversion <= 4)) {
			return true;
		}
		return false;
	}

	public function ID3v2IsValidAPICimageformat($imageformat) {
		if ($imageformat == '-->') {
			return true;
		} elseif ($this->majorversion == 2) {
			if ((strlen($imageformat) == 3) && ($imageformat == strtoupper($imageformat))) {
				return true;
			}
		} elseif (($this->majorversion == 3) || ($this->majorversion == 4)) {
			if ($this->IsValidMIMEstring($imageformat)) {
				return true;
			}
		}
		return false;
	}

	public function ID3v2IsValidCOMRreceivedAs($receivedas) {
		if (($this->majorversion >= 3) && ($receivedas >= 0) && ($receivedas <= 8)) {
			return true;
		}
		return false;
	}

	public function ID3v2IsValidRGADname($RGADname) {
		if (($RGADname >= 0) && ($RGADname <= 2)) {
			return true;
		}
		return false;
	}

	public function ID3v2IsValidRGADoriginator($RGADoriginator) {
		if (($RGADoriginator >= 0) && ($RGADoriginator <= 3)) {
			return true;
		}
		return false;
	}

	public function ID3v2IsValidTextEncoding($textencodingbyte) {
		static $ID3v2IsValidTextEncoding_cache = array(
			2 => array(true, true),
			3 => array(true, true),
			4 => array(true, true, true, true));
		return isset($ID3v2IsValidTextEncoding_cache[$this->majorversion][$textencodingbyte]);
	}

	public function Unsynchronise($data) {
		// Whenever a false synchronisation is found within the tag, one zeroed
		// byte is inserted after the first false synchronisation byte. The
		// format of a correct sync that should be altered by ID3 encoders is as
		// follows:
		//      %11111111 111xxxxx
		// And should be replaced with:
		//      %11111111 00000000 111xxxxx
		// This has the side effect that all $FF 00 combinations have to be
		// altered, so they won't be affected by the decoding process. Therefore
		// all the $FF 00 combinations have to be replaced with the $FF 00 00
		// combination during the unsynchronisation.

		$data = str_replace("\xFF\x00", "\xFF\x00\x00", $data);
		$unsyncheddata = '';
		$datalength = strlen($data);
		for ($i = 0; $i < $datalength; $i++) {
			$thischar = $data{$i};
			$unsyncheddata .= $thischar;
			if ($thischar == "\xFF") {
				$nextchar = ord($data{$i + 1});
				if (($nextchar & 0xE0) == 0xE0) {
					// previous byte = 11111111, this byte = 111?????
					$unsyncheddata .= "\x00";
				}
			}
		}
		return $unsyncheddata;
	}

	public function is_hash($var) {
		// written by dev-nullØchristophe*vg
		// taken from http://www.php.net/manual/en/function.array-merge-recursive.php
		if (is_array($var)) {
			$keys = array_keys($var);
			$all_num = true;
			for ($i = 0; $i < count($keys); $i++) {
				if (is_string($keys[$i])) {
					return true;
				}
			}
		}
		return false;
	}

	public function array_join_merge($arr1, $arr2) {
		// written by dev-nullØchristophe*vg
		// taken from http://www.php.net/manual/en/function.array-merge-recursive.php
		if (is_array($arr1) && is_array($arr2)) {
			// the same -> merge
			$new_array = array();

			if ($this->is_hash($arr1) && $this->is_hash($arr2)) {
				// hashes -> merge based on keys
				$keys = array_merge(array_keys($arr1), array_keys($arr2));
				foreach ($keys as $key) {
					$new_array[$key] = $this->array_join_merge((isset($arr1[$key]) ? $arr1[$key] : ''), (isset($arr2[$key]) ? $arr2[$key] : ''));
				}
			} else {
				// two real arrays -> merge
				$new_array = array_reverse(array_unique(array_reverse(array_merge($arr1, $arr2))));
			}
			return $new_array;
		} else {
			// not the same ... take new one if defined, else the old one stays
			return $arr2 ? $arr2 : $arr1;
		}
	}

	public function IsValidMIMEstring($mimestring) {
		if ((strlen($mimestring) >= 3) && (strpos($mimestring, '/') > 0) && (strpos($mimestring, '/') < (strlen($mimestring) - 1))) {
			return true;
		}
		return false;
	}

	public function IsWithinBitRange($number, $maxbits, $signed=false) {
		if ($signed) {
			if (($number > (0 - pow(2, $maxbits - 1))) && ($number <= pow(2, $maxbits - 1))) {
				return true;
			}
		} else {
			if (($number >= 0) && ($number <= pow(2, $maxbits))) {
				return true;
			}
		}
		return false;
	}

	public function safe_parse_url($url) {
		$parts = @parse_url($url);
		$parts['scheme'] = (isset($parts['scheme']) ? $parts['scheme'] : '');
		$parts['host']   = (isset($parts['host'])   ? $parts['host']   : '');
		$parts['user']   = (isset($parts['user'])   ? $parts['user']   : '');
		$parts['pass']   = (isset($parts['pass'])   ? $parts['pass']   : '');
		$parts['path']   = (isset($parts['path'])   ? $parts['path']   : '');
		$parts['query']  = (isset($parts['query'])  ? $parts['query']  : '');
		return $parts;
	}

	public function IsValidURL($url, $allowUserPass=false) {
		if ($url == '') {
			return false;
		}
		if ($allowUserPass !== true) {
			if (strstr($url, '@')) {
				// in the format http://user:pass@example.com  or http://user@example.com
				// but could easily be somebody incorrectly entering an email address in place of a URL
				return false;
			}
		}
		if ($parts = $this->safe_parse_url($url)) {
			if (($parts['scheme'] != 'http') && ($parts['scheme'] != 'https') && ($parts['scheme'] != 'ftp') && ($parts['scheme'] != 'gopher')) {
				return false;
			} elseif (!preg_match('#^[[:alnum:]]([-.]?[0-9a-z])*\\.[a-z]{2,3}$#i', $parts['host'], $regs) && !preg_match('#^[0-9]{1,3}(\\.[0-9]{1,3}){3}$#', $parts['host'])) {
				return false;
			} elseif (!preg_match('#^([[:alnum:]-]|[\\_])*$#i', $parts['user'], $regs)) {
				return false;
			} elseif (!preg_match('#^([[:alnum:]-]|[\\_])*$#i', $parts['pass'], $regs)) {
				return false;
			} elseif (!preg_match('#^[[:alnum:]/_\\.@~-]*$#i', $parts['path'], $regs)) {
				return false;
			} elseif (!empty($parts['query']) && !preg_match('#^[[:alnum:]?&=+:;_()%\\#/,\\.-]*$#i', $parts['query'], $regs)) {
				return false;
			} else {
				return true;
			}
		}
		return false;
	}

	public static function ID3v2ShortFrameNameLookup($majorversion, $long_description) {
		$long_description = str_replace(' ', '_', strtolower(trim($long_description)));
		static $ID3v2ShortFrameNameLookup = array();
		if (empty($ID3v2ShortFrameNameLookup)) {

			// The following are unique to ID3v2.2
			$ID3v2ShortFrameNameLookup[2]['comment']                                          = 'COM';
			$ID3v2ShortFrameNameLookup[2]['album']                                            = 'TAL';
			$ID3v2ShortFrameNameLookup[2]['beats_per_minute']                                 = 'TBP';
			$ID3v2ShortFrameNameLookup[2]['composer']                                         = 'TCM';
			$ID3v2ShortFrameNameLookup[2]['genre']                                            = 'TCO';
			$ID3v2ShortFrameNameLookup[2]['itunescompilation']                                = 'TCP';
			$ID3v2ShortFrameNameLookup[2]['copyright']                                        = 'TCR';
			$ID3v2ShortFrameNameLookup[2]['encoded_by']                                       = 'TEN';
			$ID3v2ShortFrameNameLookup[2]['language']                                         = 'TLA';
			$ID3v2ShortFrameNameLookup[2]['length']                                           = 'TLE';
			$ID3v2ShortFrameNameLookup[2]['original_artist']                                  = 'TOA';
			$ID3v2ShortFrameNameLookup[2]['original_filename']                                = 'TOF';
			$ID3v2ShortFrameNameLookup[2]['original_lyricist']                                = 'TOL';
			$ID3v2ShortFrameNameLookup[2]['original_album_title']                             = 'TOT';
			$ID3v2ShortFrameNameLookup[2]['artist']                                           = 'TP1';
			$ID3v2ShortFrameNameLookup[2]['band']                                             = 'TP2';
			$ID3v2ShortFrameNameLookup[2]['conductor']                                        = 'TP3';
			$ID3v2ShortFrameNameLookup[2]['remixer']                                          = 'TP4';
			$ID3v2ShortFrameNameLookup[2]['publisher']                                        = 'TPB';
			$ID3v2ShortFrameNameLookup[2]['isrc']                                             = 'TRC';
			$ID3v2ShortFrameNameLookup[2]['tracknumber']                                      = 'TRK';
			$ID3v2ShortFrameNameLookup[2]['track_number']                                     = 'TRK';
			$ID3v2ShortFrameNameLookup[2]['size']                                             = 'TSI';
			$ID3v2ShortFrameNameLookup[2]['encoder_settings']                                 = 'TSS';
			$ID3v2ShortFrameNameLookup[2]['description']                                      = 'TT1';
			$ID3v2ShortFrameNameLookup[2]['title']                                            = 'TT2';
			$ID3v2ShortFrameNameLookup[2]['subtitle']                                         = 'TT3';
			$ID3v2ShortFrameNameLookup[2]['lyricist']                                         = 'TXT';
			$ID3v2ShortFrameNameLookup[2]['user_text']                                        = 'TXX';
			$ID3v2ShortFrameNameLookup[2]['year']                                             = 'TYE';
			$ID3v2ShortFrameNameLookup[2]['unique_file_identifier']                           = 'UFI';
			$ID3v2ShortFrameNameLookup[2]['unsynchronised_lyrics']                            = 'ULT';
			$ID3v2ShortFrameNameLookup[2]['url_file']                                         = 'WAF';
			$ID3v2ShortFrameNameLookup[2]['url_artist']                                       = 'WAR';
			$ID3v2ShortFrameNameLookup[2]['url_source']                                       = 'WAS';
			$ID3v2ShortFrameNameLookup[2]['copyright_information']                            = 'WCP';
			$ID3v2ShortFrameNameLookup[2]['url_publisher']                                    = 'WPB';
			$ID3v2ShortFrameNameLookup[2]['url_user']                                         = 'WXX';

			// The following are common to ID3v2.3 and ID3v2.4
			$ID3v2ShortFrameNameLookup[3]['audio_encryption']                                 = 'AENC';
			$ID3v2ShortFrameNameLookup[3]['attached_picture']                                 = 'APIC';
			$ID3v2ShortFrameNameLookup[3]['picture']                                          = 'APIC';
			$ID3v2ShortFrameNameLookup[3]['comment']                                          = 'COMM';
			$ID3v2ShortFrameNameLookup[3]['commercial']                                       = 'COMR';
			$ID3v2ShortFrameNameLookup[3]['encryption_method_registration']                   = 'ENCR';
			$ID3v2ShortFrameNameLookup[3]['event_timing_codes']                               = 'ETCO';
			$ID3v2ShortFrameNameLookup[3]['general_encapsulated_object']                      = 'GEOB';
			$ID3v2ShortFrameNameLookup[3]['group_identification_registration']                = 'GRID';
			$ID3v2ShortFrameNameLookup[3]['linked_information']                               = 'LINK';
			$ID3v2ShortFrameNameLookup[3]['music_cd_identifier']                              = 'MCDI';
			$ID3v2ShortFrameNameLookup[3]['mpeg_location_lookup_table']                       = 'MLLT';
			$ID3v2ShortFrameNameLookup[3]['ownership']                                        = 'OWNE';
			$ID3v2ShortFrameNameLookup[3]['play_counter']                                     = 'PCNT';
			$ID3v2ShortFrameNameLookup[3]['popularimeter']                                    = 'POPM';
			$ID3v2ShortFrameNameLookup[3]['position_synchronisation']                         = 'POSS';
			$ID3v2ShortFrameNameLookup[3]['private']                                          = 'PRIV';
			$ID3v2ShortFrameNameLookup[3]['recommended_buffer_size']                          = 'RBUF';
			$ID3v2ShortFrameNameLookup[3]['reverb']                                           = 'RVRB';
			$ID3v2ShortFrameNameLookup[3]['synchronised_lyrics']                              = 'SYLT';
			$ID3v2ShortFrameNameLookup[3]['synchronised_tempo_codes']                         = 'SYTC';
			$ID3v2ShortFrameNameLookup[3]['album']                                            = 'TALB';
			$ID3v2ShortFrameNameLookup[3]['beats_per_minute']                                 = 'TBPM';
			$ID3v2ShortFrameNameLookup[3]['itunescompilation']                                = 'TCMP';
			$ID3v2ShortFrameNameLookup[3]['composer']                                         = 'TCOM';
			$ID3v2ShortFrameNameLookup[3]['genre']                                            = 'TCON';
			$ID3v2ShortFrameNameLookup[3]['copyright']                                        = 'TCOP';
			$ID3v2ShortFrameNameLookup[3]['playlist_delay']                                   = 'TDLY';
			$ID3v2ShortFrameNameLookup[3]['encoded_by']                                       = 'TENC';
			$ID3v2ShortFrameNameLookup[3]['lyricist']                                         = 'TEXT';
			$ID3v2ShortFrameNameLookup[3]['file_type']                                        = 'TFLT';
			$ID3v2ShortFrameNameLookup[3]['content_group_description']                        = 'TIT1';
			$ID3v2ShortFrameNameLookup[3]['title']                                            = 'TIT2';
			$ID3v2ShortFrameNameLookup[3]['subtitle']                                         = 'TIT3';
			$ID3v2ShortFrameNameLookup[3]['initial_key']                                      = 'TKEY';
			$ID3v2ShortFrameNameLookup[3]['language']                                         = 'TLAN';
			$ID3v2ShortFrameNameLookup[3]['length']                                           = 'TLEN';
			$ID3v2ShortFrameNameLookup[3]['media_type']                                       = 'TMED';
			$ID3v2ShortFrameNameLookup[3]['original_album_title']                             = 'TOAL';
			$ID3v2ShortFrameNameLookup[3]['original_filename']                                = 'TOFN';
			$ID3v2ShortFrameNameLookup[3]['original_lyricist']                                = 'TOLY';
			$ID3v2ShortFrameNameLookup[3]['original_artist']                                  = 'TOPE';
			$ID3v2ShortFrameNameLookup[3]['file_owner']                                       = 'TOWN';
			$ID3v2ShortFrameNameLookup[3]['artist']                                           = 'TPE1';
			$ID3v2ShortFrameNameLookup[3]['band']                                             = 'TPE2';
			$ID3v2ShortFrameNameLookup[3]['conductor']                                        = 'TPE3';
			$ID3v2ShortFrameNameLookup[3]['remixer']                                          = 'TPE4';
			$ID3v2ShortFrameNameLookup[3]['part_of_a_set']                                    = 'TPOS';
			$ID3v2ShortFrameNameLookup[3]['publisher']                                        = 'TPUB';
			$ID3v2ShortFrameNameLookup[3]['tracknumber']                                      = 'TRCK';
			$ID3v2ShortFrameNameLookup[3]['track_number']                                     = 'TRCK';
			$ID3v2ShortFrameNameLookup[3]['internet_radio_station_name']                      = 'TRSN';
			$ID3v2ShortFrameNameLookup[3]['internet_radio_station_owner']                     = 'TRSO';
			$ID3v2ShortFrameNameLookup[3]['isrc']                                             = 'TSRC';
			$ID3v2ShortFrameNameLookup[3]['encoder_settings']                                 = 'TSSE';
			$ID3v2ShortFrameNameLookup[3]['user_text']                                        = 'TXXX';
			$ID3v2ShortFrameNameLookup[3]['unique_file_identifier']                           = 'UFID';
			$ID3v2ShortFrameNameLookup[3]['terms_of_use']                                     = 'USER';
			$ID3v2ShortFrameNameLookup[3]['unsynchronised_lyrics']                            = 'USLT';
			$ID3v2ShortFrameNameLookup[3]['commercial']                                       = 'WCOM';
			$ID3v2ShortFrameNameLookup[3]['copyright_information']                            = 'WCOP';
			$ID3v2ShortFrameNameLookup[3]['url_file']                                         = 'WOAF';
			$ID3v2ShortFrameNameLookup[3]['url_artist']                                       = 'WOAR';
			$ID3v2ShortFrameNameLookup[3]['url_source']                                       = 'WOAS';
			$ID3v2ShortFrameNameLookup[3]['url_station']                                      = 'WORS';
			$ID3v2ShortFrameNameLookup[3]['payment']                                          = 'WPAY';
			$ID3v2ShortFrameNameLookup[3]['url_publisher']                                    = 'WPUB';
			$ID3v2ShortFrameNameLookup[3]['url_user']                                         = 'WXXX';

			// The above are common to ID3v2.3 and ID3v2.4
			// so copy them to ID3v2.4 before adding specifics for 2.3 and 2.4
			$ID3v2ShortFrameNameLookup[4] = $ID3v2ShortFrameNameLookup[3];

			// The following are unique to ID3v2.3
			$ID3v2ShortFrameNameLookup[3]['equalisation']                                     = 'EQUA';
			$ID3v2ShortFrameNameLookup[3]['involved_people_list']                             = 'IPLS';
			$ID3v2ShortFrameNameLookup[3]['relative_volume_adjustment']                       = 'RVAD';
			$ID3v2ShortFrameNameLookup[3]['date']                                             = 'TDAT';
			$ID3v2ShortFrameNameLookup[3]['time']                                             = 'TIME';
			$ID3v2ShortFrameNameLookup[3]['original_release_year']                            = 'TORY';
			$ID3v2ShortFrameNameLookup[3]['recording_dates']                                  = 'TRDA';
			$ID3v2ShortFrameNameLookup[3]['size']                                             = 'TSIZ';
			$ID3v2ShortFrameNameLookup[3]['year']                                             = 'TYER';


			// The following are unique to ID3v2.4
			$ID3v2ShortFrameNameLookup[4]['audio_seek_point_index']                           = 'ASPI';
			$ID3v2ShortFrameNameLookup[4]['equalisation']                                     = 'EQU2';
			$ID3v2ShortFrameNameLookup[4]['relative_volume_adjustment']                       = 'RVA2';
			$ID3v2ShortFrameNameLookup[4]['seek']                                             = 'SEEK';
			$ID3v2ShortFrameNameLookup[4]['signature']                                        = 'SIGN';
			$ID3v2ShortFrameNameLookup[4]['encoding_time']                                    = 'TDEN';
			$ID3v2ShortFrameNameLookup[4]['original_release_time']                            = 'TDOR';
			$ID3v2ShortFrameNameLookup[4]['recording_time']                                   = 'TDRC';
			$ID3v2ShortFrameNameLookup[4]['release_time']                                     = 'TDRL';
			$ID3v2ShortFrameNameLookup[4]['tagging_time']                                     = 'TDTG';
			$ID3v2ShortFrameNameLookup[4]['involved_people_list']                             = 'TIPL';
			$ID3v2ShortFrameNameLookup[4]['musician_credits_list']                            = 'TMCL';
			$ID3v2ShortFrameNameLookup[4]['mood']                                             = 'TMOO';
			$ID3v2ShortFrameNameLookup[4]['produced_notice']                                  = 'TPRO';
			$ID3v2ShortFrameNameLookup[4]['album_sort_order']                                 = 'TSOA';
			$ID3v2ShortFrameNameLookup[4]['performer_sort_order']                             = 'TSOP';
			$ID3v2ShortFrameNameLookup[4]['title_sort_order']                                 = 'TSOT';
			$ID3v2ShortFrameNameLookup[4]['set_subtitle']                                     = 'TSST';
		}
		return (isset($ID3v2ShortFrameNameLookup[$majorversion][strtolower($long_description)]) ? $ID3v2ShortFrameNameLookup[$majorversion][strtolower($long_description)] : '');

	}

}

