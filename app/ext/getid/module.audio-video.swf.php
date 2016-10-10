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
// module.audio-video.swf.php                                  //
// module for analyzing Shockwave Flash files                  //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_swf extends getid3_handler
{
	public $ReturnAllTagData = false;

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat']          = 'swf';
		$info['video']['dataformat'] = 'swf';

		// http://www.openswf.org/spec/SWFfileformat.html

		$this->fseek($info['avdataoffset']);

		$SWFfileData = $this->fread($info['avdataend'] - $info['avdataoffset']); // 8 + 2 + 2 + max(9) bytes NOT including Frame_Size RECT data

		$info['swf']['header']['signature']  = substr($SWFfileData, 0, 3);
		switch ($info['swf']['header']['signature']) {
			case 'FWS':
				$info['swf']['header']['compressed'] = false;
				break;

			case 'CWS':
				$info['swf']['header']['compressed'] = true;
				break;

			default:
				$info['error'][] = 'Expecting "FWS" or "CWS" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($info['swf']['header']['signature']).'"';
				unset($info['swf']);
				unset($info['fileformat']);
				return false;
				break;
		}
		$info['swf']['header']['version'] = getid3_lib::LittleEndian2Int(substr($SWFfileData, 3, 1));
		$info['swf']['header']['length']  = getid3_lib::LittleEndian2Int(substr($SWFfileData, 4, 4));

		if ($info['swf']['header']['compressed']) {
			$SWFHead     = substr($SWFfileData, 0, 8);
			$SWFfileData = substr($SWFfileData, 8);
			if ($decompressed = @gzuncompress($SWFfileData)) {
				$SWFfileData = $SWFHead.$decompressed;
			} else {
				$info['error'][] = 'Error decompressing compressed SWF data ('.strlen($SWFfileData).' bytes compressed, should be '.($info['swf']['header']['length'] - 8).' bytes uncompressed)';
				return false;
			}
		}

		$FrameSizeBitsPerValue = (ord(substr($SWFfileData, 8, 1)) & 0xF8) >> 3;
		$FrameSizeDataLength   = ceil((5 + (4 * $FrameSizeBitsPerValue)) / 8);
		$FrameSizeDataString   = str_pad(decbin(ord(substr($SWFfileData, 8, 1)) & 0x07), 3, '0', STR_PAD_LEFT);
		for ($i = 1; $i < $FrameSizeDataLength; $i++) {
			$FrameSizeDataString .= str_pad(decbin(ord(substr($SWFfileData, 8 + $i, 1))), 8, '0', STR_PAD_LEFT);
		}
		list($X1, $X2, $Y1, $Y2) = explode("\n", wordwrap($FrameSizeDataString, $FrameSizeBitsPerValue, "\n", 1));
		$info['swf']['header']['frame_width']  = getid3_lib::Bin2Dec($X2);
		$info['swf']['header']['frame_height'] = getid3_lib::Bin2Dec($Y2);

		// http://www-lehre.informatik.uni-osnabrueck.de/~fbstark/diplom/docs/swf/Flash_Uncovered.htm
		// Next in the header is the frame rate, which is kind of weird.
		// It is supposed to be stored as a 16bit integer, but the first byte
		// (or last depending on how you look at it) is completely ignored.
		// Example: 0x000C  ->  0x0C  ->  12     So the frame rate is 12 fps.

		// Byte at (8 + $FrameSizeDataLength) is always zero and ignored
		$info['swf']['header']['frame_rate']  = getid3_lib::LittleEndian2Int(substr($SWFfileData,  9 + $FrameSizeDataLength, 1));
		$info['swf']['header']['frame_count'] = getid3_lib::LittleEndian2Int(substr($SWFfileData, 10 + $FrameSizeDataLength, 2));

		$info['video']['frame_rate']         = $info['swf']['header']['frame_rate'];
		$info['video']['resolution_x']       = intval(round($info['swf']['header']['frame_width']  / 20));
		$info['video']['resolution_y']       = intval(round($info['swf']['header']['frame_height'] / 20));
		$info['video']['pixel_aspect_ratio'] = (float) 1;

		if (($info['swf']['header']['frame_count'] > 0) && ($info['swf']['header']['frame_rate'] > 0)) {
			$info['playtime_seconds'] = $info['swf']['header']['frame_count'] / $info['swf']['header']['frame_rate'];
		}
//echo __LINE__.'='.number_format(microtime(true) - $start_time, 3).'<br>';


		// SWF tags

		$CurrentOffset = 12 + $FrameSizeDataLength;
		$SWFdataLength = strlen($SWFfileData);

		while ($CurrentOffset < $SWFdataLength) {
//echo __LINE__.'='.number_format(microtime(true) - $start_time, 3).'<br>';

			$TagIDTagLength = getid3_lib::LittleEndian2Int(substr($SWFfileData, $CurrentOffset, 2));
			$TagID     = ($TagIDTagLength & 0xFFFC) >> 6;
			$TagLength = ($TagIDTagLength & 0x003F);
			$CurrentOffset += 2;
			if ($TagLength == 0x3F) {
				$TagLength = getid3_lib::LittleEndian2Int(substr($SWFfileData, $CurrentOffset, 4));
				$CurrentOffset += 4;
			}

			unset($TagData);
			$TagData['offset'] = $CurrentOffset;
			$TagData['size']   = $TagLength;
			$TagData['id']     = $TagID;
			$TagData['data']   = substr($SWFfileData, $CurrentOffset, $TagLength);
			switch ($TagID) {
				case 0: // end of movie
					break 2;

				case 9: // Set background color
					//$info['swf']['tags'][] = $TagData;
					$info['swf']['bgcolor'] = strtoupper(str_pad(dechex(getid3_lib::BigEndian2Int($TagData['data'])), 6, '0', STR_PAD_LEFT));
					break;

				default:
					if ($this->ReturnAllTagData) {
						$info['swf']['tags'][] = $TagData;
					}
					break;
			}

			$CurrentOffset += $TagLength;
		}

		return true;
	}

}
