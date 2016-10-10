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
// module.audio.la.php                                         //
// module for analyzing BONK audio files                       //
// dependencies: module.tag.id3v2.php (optional)               //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_bonk extends getid3_handler
{
	public function Analyze() {
		$info = &$this->getid3->info;

		// shortcut
		$info['bonk'] = array();
		$thisfile_bonk        = &$info['bonk'];

		$thisfile_bonk['dataoffset'] = $info['avdataoffset'];
		$thisfile_bonk['dataend']    = $info['avdataend'];

		if (!getid3_lib::intValueSupported($thisfile_bonk['dataend'])) {

			$info['warning'][] = 'Unable to parse BONK file from end (v0.6+ preferred method) because PHP filesystem functions only support up to '.round(PHP_INT_MAX / 1073741824).'GB';

		} else {

			// scan-from-end method, for v0.6 and higher
			$this->fseek($thisfile_bonk['dataend'] - 8);
			$PossibleBonkTag = $this->fread(8);
			while ($this->BonkIsValidTagName(substr($PossibleBonkTag, 4, 4), true)) {
				$BonkTagSize = getid3_lib::LittleEndian2Int(substr($PossibleBonkTag, 0, 4));
				$this->fseek(0 - $BonkTagSize, SEEK_CUR);
				$BonkTagOffset = $this->ftell();
				$TagHeaderTest = $this->fread(5);
				if (($TagHeaderTest{0} != "\x00") || (substr($PossibleBonkTag, 4, 4) != strtolower(substr($PossibleBonkTag, 4, 4)))) {
					$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes("\x00".strtoupper(substr($PossibleBonkTag, 4, 4))).'" at offset '.$BonkTagOffset.', found "'.getid3_lib::PrintHexBytes($TagHeaderTest).'"';
					return false;
				}
				$BonkTagName = substr($TagHeaderTest, 1, 4);

				$thisfile_bonk[$BonkTagName]['size']   = $BonkTagSize;
				$thisfile_bonk[$BonkTagName]['offset'] = $BonkTagOffset;
				$this->HandleBonkTags($BonkTagName);
				$NextTagEndOffset = $BonkTagOffset - 8;
				if ($NextTagEndOffset < $thisfile_bonk['dataoffset']) {
					if (empty($info['audio']['encoder'])) {
						$info['audio']['encoder'] = 'Extended BONK v0.9+';
					}
					return true;
				}
				$this->fseek($NextTagEndOffset);
				$PossibleBonkTag = $this->fread(8);
			}

		}

		// seek-from-beginning method for v0.4 and v0.5
		if (empty($thisfile_bonk['BONK'])) {
			$this->fseek($thisfile_bonk['dataoffset']);
			do {
				$TagHeaderTest = $this->fread(5);
				switch ($TagHeaderTest) {
					case "\x00".'BONK':
						if (empty($info['audio']['encoder'])) {
							$info['audio']['encoder'] = 'BONK v0.4';
						}
						break;

					case "\x00".'INFO':
						$info['audio']['encoder'] = 'Extended BONK v0.5';
						break;

					default:
						break 2;
				}
				$BonkTagName = substr($TagHeaderTest, 1, 4);
				$thisfile_bonk[$BonkTagName]['size']   = $thisfile_bonk['dataend'] - $thisfile_bonk['dataoffset'];
				$thisfile_bonk[$BonkTagName]['offset'] = $thisfile_bonk['dataoffset'];
				$this->HandleBonkTags($BonkTagName);

			} while (true);
		}

		// parse META block for v0.6 - v0.8
		if (empty($thisfile_bonk['INFO']) && isset($thisfile_bonk['META']['tags']['info'])) {
			$this->fseek($thisfile_bonk['META']['tags']['info']);
			$TagHeaderTest = $this->fread(5);
			if ($TagHeaderTest == "\x00".'INFO') {
				$info['audio']['encoder'] = 'Extended BONK v0.6 - v0.8';

				$BonkTagName = substr($TagHeaderTest, 1, 4);
				$thisfile_bonk[$BonkTagName]['size']   = $thisfile_bonk['dataend'] - $thisfile_bonk['dataoffset'];
				$thisfile_bonk[$BonkTagName]['offset'] = $thisfile_bonk['dataoffset'];
				$this->HandleBonkTags($BonkTagName);
			}
		}

		if (empty($info['audio']['encoder'])) {
			$info['audio']['encoder'] = 'Extended BONK v0.9+';
		}
		if (empty($thisfile_bonk['BONK'])) {
			unset($info['bonk']);
		}
		return true;

	}

	public function HandleBonkTags($BonkTagName) {
		$info = &$this->getid3->info;
		switch ($BonkTagName) {
			case 'BONK':
				// shortcut
				$thisfile_bonk_BONK = &$info['bonk']['BONK'];

				$BonkData = "\x00".'BONK'.$this->fread(17);
				$thisfile_bonk_BONK['version']            =        getid3_lib::LittleEndian2Int(substr($BonkData,  5, 1));
				$thisfile_bonk_BONK['number_samples']     =        getid3_lib::LittleEndian2Int(substr($BonkData,  6, 4));
				$thisfile_bonk_BONK['sample_rate']        =        getid3_lib::LittleEndian2Int(substr($BonkData, 10, 4));

				$thisfile_bonk_BONK['channels']           =        getid3_lib::LittleEndian2Int(substr($BonkData, 14, 1));
				$thisfile_bonk_BONK['lossless']           = (bool) getid3_lib::LittleEndian2Int(substr($BonkData, 15, 1));
				$thisfile_bonk_BONK['joint_stereo']       = (bool) getid3_lib::LittleEndian2Int(substr($BonkData, 16, 1));
				$thisfile_bonk_BONK['number_taps']        =        getid3_lib::LittleEndian2Int(substr($BonkData, 17, 2));
				$thisfile_bonk_BONK['downsampling_ratio'] =        getid3_lib::LittleEndian2Int(substr($BonkData, 19, 1));
				$thisfile_bonk_BONK['samples_per_packet'] =        getid3_lib::LittleEndian2Int(substr($BonkData, 20, 2));

				$info['avdataoffset'] = $thisfile_bonk_BONK['offset'] + 5 + 17;
				$info['avdataend']    = $thisfile_bonk_BONK['offset'] + $thisfile_bonk_BONK['size'];

				$info['fileformat']               = 'bonk';
				$info['audio']['dataformat']      = 'bonk';
				$info['audio']['bitrate_mode']    = 'vbr'; // assumed
				$info['audio']['channels']        = $thisfile_bonk_BONK['channels'];
				$info['audio']['sample_rate']     = $thisfile_bonk_BONK['sample_rate'];
				$info['audio']['channelmode']     = ($thisfile_bonk_BONK['joint_stereo'] ? 'joint stereo' : 'stereo');
				$info['audio']['lossless']        = $thisfile_bonk_BONK['lossless'];
				$info['audio']['codec']           = 'bonk';

				$info['playtime_seconds'] = $thisfile_bonk_BONK['number_samples'] / ($thisfile_bonk_BONK['sample_rate'] * $thisfile_bonk_BONK['channels']);
				if ($info['playtime_seconds'] > 0) {
					$info['audio']['bitrate'] = (($info['bonk']['dataend'] - $info['bonk']['dataoffset']) * 8) / $info['playtime_seconds'];
				}
				break;

			case 'INFO':
				// shortcut
				$thisfile_bonk_INFO = &$info['bonk']['INFO'];

				$thisfile_bonk_INFO['version'] = getid3_lib::LittleEndian2Int($this->fread(1));
				$thisfile_bonk_INFO['entries_count'] = 0;
				$NextInfoDataPair = $this->fread(5);
				if (!$this->BonkIsValidTagName(substr($NextInfoDataPair, 1, 4))) {
					while (!feof($this->getid3->fp)) {
						//$CurrentSeekInfo['offset']  = getid3_lib::LittleEndian2Int(substr($NextInfoDataPair, 0, 4));
						//$CurrentSeekInfo['nextbit'] = getid3_lib::LittleEndian2Int(substr($NextInfoDataPair, 4, 1));
						//$thisfile_bonk_INFO[] = $CurrentSeekInfo;

						$NextInfoDataPair = $this->fread(5);
						if ($this->BonkIsValidTagName(substr($NextInfoDataPair, 1, 4))) {
							$this->fseek(-5, SEEK_CUR);
							break;
						}
						$thisfile_bonk_INFO['entries_count']++;
					}
				}
				break;

			case 'META':
				$BonkData = "\x00".'META'.$this->fread($info['bonk']['META']['size'] - 5);
				$info['bonk']['META']['version'] = getid3_lib::LittleEndian2Int(substr($BonkData,  5, 1));

				$MetaTagEntries = floor(((strlen($BonkData) - 8) - 6) / 8); // BonkData - xxxxmeta - ØMETA
				$offset = 6;
				for ($i = 0; $i < $MetaTagEntries; $i++) {
					$MetaEntryTagName   =                              substr($BonkData, $offset, 4);
					$offset += 4;
					$MetaEntryTagOffset = getid3_lib::LittleEndian2Int(substr($BonkData, $offset, 4));
					$offset += 4;
					$info['bonk']['META']['tags'][$MetaEntryTagName] = $MetaEntryTagOffset;
				}
				break;

			case ' ID3':
				$info['audio']['encoder'] = 'Extended BONK v0.9+';

				// ID3v2 checking is optional
				if (class_exists('getid3_id3v2')) {
					$getid3_temp = new getID3();
					$getid3_temp->openfile($this->getid3->filename);
					$getid3_id3v2 = new getid3_id3v2($getid3_temp);
					$getid3_id3v2->StartingOffset = $info['bonk'][' ID3']['offset'] + 2;
					$info['bonk'][' ID3']['valid'] = $getid3_id3v2->Analyze();
					if ($info['bonk'][' ID3']['valid']) {
						$info['id3v2'] = $getid3_temp->info['id3v2'];
					}
					unset($getid3_temp, $getid3_id3v2);
				}
				break;

			default:
				$info['warning'][] = 'Unexpected Bonk tag "'.$BonkTagName.'" at offset '.$info['bonk'][$BonkTagName]['offset'];
				break;

		}
	}

	public static function BonkIsValidTagName($PossibleBonkTag, $ignorecase=false) {
		static $BonkIsValidTagName = array('BONK', 'INFO', ' ID3', 'META');
		foreach ($BonkIsValidTagName as $validtagname) {
			if ($validtagname == $PossibleBonkTag) {
				return true;
			} elseif ($ignorecase && (strtolower($validtagname) == strtolower($PossibleBonkTag))) {
				return true;
			}
		}
		return false;
	}

}
