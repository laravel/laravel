<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// module.audio.nsv.php                                        //
// module for analyzing Nullsoft NSV files                     //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_nsv extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);
		$NSVheader = $this->fread(4);

		switch ($NSVheader) {
			case 'NSVs':
				if ($this->getNSVsHeaderFilepointer(0)) {
					$info['fileformat']          = 'nsv';
					$info['audio']['dataformat'] = 'nsv';
					$info['video']['dataformat'] = 'nsv';
					$info['audio']['lossless']   = false;
					$info['video']['lossless']   = false;
				}
				break;

			case 'NSVf':
				if ($this->getNSVfHeaderFilepointer(0)) {
					$info['fileformat']          = 'nsv';
					$info['audio']['dataformat'] = 'nsv';
					$info['video']['dataformat'] = 'nsv';
					$info['audio']['lossless']   = false;
					$info['video']['lossless']   = false;
					$this->getNSVsHeaderFilepointer($info['nsv']['NSVf']['header_length']);
				}
				break;

			default:
				$info['error'][] = 'Expecting "NSVs" or "NSVf" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($NSVheader).'"';
				return false;
				break;
		}

		if (!isset($info['nsv']['NSVf'])) {
			$info['warning'][] = 'NSVf header not present - cannot calculate playtime or bitrate';
		}

		return true;
	}

	public function getNSVsHeaderFilepointer($fileoffset) {
		$info = &$this->getid3->info;
		$this->fseek($fileoffset);
		$NSVsheader = $this->fread(28);
		$offset = 0;

		$info['nsv']['NSVs']['identifier']      =                  substr($NSVsheader, $offset, 4);
		$offset += 4;

		if ($info['nsv']['NSVs']['identifier'] != 'NSVs') {
			$info['error'][] = 'expected "NSVs" at offset ('.$fileoffset.'), found "'.$info['nsv']['NSVs']['identifier'].'" instead';
			unset($info['nsv']['NSVs']);
			return false;
		}

		$info['nsv']['NSVs']['offset']          = $fileoffset;

		$info['nsv']['NSVs']['video_codec']     =                              substr($NSVsheader, $offset, 4);
		$offset += 4;
		$info['nsv']['NSVs']['audio_codec']     =                              substr($NSVsheader, $offset, 4);
		$offset += 4;
		$info['nsv']['NSVs']['resolution_x']    = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 2));
		$offset += 2;
		$info['nsv']['NSVs']['resolution_y']    = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 2));
		$offset += 2;

		$info['nsv']['NSVs']['framerate_index'] = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;
		//$info['nsv']['NSVs']['unknown1b']       = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;
		//$info['nsv']['NSVs']['unknown1c']       = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;
		//$info['nsv']['NSVs']['unknown1d']       = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;
		//$info['nsv']['NSVs']['unknown2a']       = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;
		//$info['nsv']['NSVs']['unknown2b']       = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;
		//$info['nsv']['NSVs']['unknown2c']       = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;
		//$info['nsv']['NSVs']['unknown2d']       = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
		$offset += 1;

		switch ($info['nsv']['NSVs']['audio_codec']) {
			case 'PCM ':
				$info['nsv']['NSVs']['bits_channel'] = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
				$offset += 1;
				$info['nsv']['NSVs']['channels']     = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 1));
				$offset += 1;
				$info['nsv']['NSVs']['sample_rate']  = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 2));
				$offset += 2;

				$info['audio']['sample_rate']        = $info['nsv']['NSVs']['sample_rate'];
				break;

			case 'MP3 ':
			case 'NONE':
			default:
				//$info['nsv']['NSVs']['unknown3']     = getid3_lib::LittleEndian2Int(substr($NSVsheader, $offset, 4));
				$offset += 4;
				break;
		}

		$info['video']['resolution_x']       = $info['nsv']['NSVs']['resolution_x'];
		$info['video']['resolution_y']       = $info['nsv']['NSVs']['resolution_y'];
		$info['nsv']['NSVs']['frame_rate']   = $this->NSVframerateLookup($info['nsv']['NSVs']['framerate_index']);
		$info['video']['frame_rate']         = $info['nsv']['NSVs']['frame_rate'];
		$info['video']['bits_per_sample']    = 24;
		$info['video']['pixel_aspect_ratio'] = (float) 1;

		return true;
	}

	public function getNSVfHeaderFilepointer($fileoffset, $getTOCoffsets=false) {
		$info = &$this->getid3->info;
		$this->fseek($fileoffset);
		$NSVfheader = $this->fread(28);
		$offset = 0;

		$info['nsv']['NSVf']['identifier']    =                  substr($NSVfheader, $offset, 4);
		$offset += 4;

		if ($info['nsv']['NSVf']['identifier'] != 'NSVf') {
			$info['error'][] = 'expected "NSVf" at offset ('.$fileoffset.'), found "'.$info['nsv']['NSVf']['identifier'].'" instead';
			unset($info['nsv']['NSVf']);
			return false;
		}

		$info['nsv']['NSVs']['offset']        = $fileoffset;

		$info['nsv']['NSVf']['header_length'] = getid3_lib::LittleEndian2Int(substr($NSVfheader, $offset, 4));
		$offset += 4;
		$info['nsv']['NSVf']['file_size']     = getid3_lib::LittleEndian2Int(substr($NSVfheader, $offset, 4));
		$offset += 4;

		if ($info['nsv']['NSVf']['file_size'] > $info['avdataend']) {
			$info['warning'][] = 'truncated file - NSVf header indicates '.$info['nsv']['NSVf']['file_size'].' bytes, file actually '.$info['avdataend'].' bytes';
		}

		$info['nsv']['NSVf']['playtime_ms']   = getid3_lib::LittleEndian2Int(substr($NSVfheader, $offset, 4));
		$offset += 4;
		$info['nsv']['NSVf']['meta_size']     = getid3_lib::LittleEndian2Int(substr($NSVfheader, $offset, 4));
		$offset += 4;
		$info['nsv']['NSVf']['TOC_entries_1'] = getid3_lib::LittleEndian2Int(substr($NSVfheader, $offset, 4));
		$offset += 4;
		$info['nsv']['NSVf']['TOC_entries_2'] = getid3_lib::LittleEndian2Int(substr($NSVfheader, $offset, 4));
		$offset += 4;

		if ($info['nsv']['NSVf']['playtime_ms'] == 0) {
			$info['error'][] = 'Corrupt NSV file: NSVf.playtime_ms == zero';
			return false;
		}

		$NSVfheader .= $this->fread($info['nsv']['NSVf']['meta_size'] + (4 * $info['nsv']['NSVf']['TOC_entries_1']) + (4 * $info['nsv']['NSVf']['TOC_entries_2']));
		$NSVfheaderlength = strlen($NSVfheader);
		$info['nsv']['NSVf']['metadata']      =                  substr($NSVfheader, $offset, $info['nsv']['NSVf']['meta_size']);
		$offset += $info['nsv']['NSVf']['meta_size'];

		if ($getTOCoffsets) {
			$TOCcounter = 0;
			while ($TOCcounter < $info['nsv']['NSVf']['TOC_entries_1']) {
				if ($TOCcounter < $info['nsv']['NSVf']['TOC_entries_1']) {
					$info['nsv']['NSVf']['TOC_1'][$TOCcounter] = getid3_lib::LittleEndian2Int(substr($NSVfheader, $offset, 4));
					$offset += 4;
					$TOCcounter++;
				}
			}
		}

		if (trim($info['nsv']['NSVf']['metadata']) != '') {
			$info['nsv']['NSVf']['metadata'] = str_replace('`', "\x01", $info['nsv']['NSVf']['metadata']);
			$CommentPairArray = explode("\x01".' ', $info['nsv']['NSVf']['metadata']);
			foreach ($CommentPairArray as $CommentPair) {
				if (strstr($CommentPair, '='."\x01")) {
					list($key, $value) = explode('='."\x01", $CommentPair, 2);
					$info['nsv']['comments'][strtolower($key)][] = trim(str_replace("\x01", '', $value));
				}
			}
		}

		$info['playtime_seconds'] = $info['nsv']['NSVf']['playtime_ms'] / 1000;
		$info['bitrate']          = ($info['nsv']['NSVf']['file_size'] * 8) / $info['playtime_seconds'];

		return true;
	}


	public static function NSVframerateLookup($framerateindex) {
		if ($framerateindex <= 127) {
			return (float) $framerateindex;
		}
		static $NSVframerateLookup = array();
		if (empty($NSVframerateLookup)) {
			$NSVframerateLookup[129] = (float) 29.970;
			$NSVframerateLookup[131] = (float) 23.976;
			$NSVframerateLookup[133] = (float) 14.985;
			$NSVframerateLookup[197] = (float) 59.940;
			$NSVframerateLookup[199] = (float) 47.952;
		}
		return (isset($NSVframerateLookup[$framerateindex]) ? $NSVframerateLookup[$framerateindex] : false);
	}

}
