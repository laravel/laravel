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
// write.apetag.php                                            //
// module for writing APE tags                                 //
// dependencies: module.tag.apetag.php                         //
//                                                            ///
/////////////////////////////////////////////////////////////////


getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.tag.apetag.php', __FILE__, true);

class getid3_write_apetag
{

	public $filename;
	public $tag_data;
	public $always_preserve_replaygain = true;    // ReplayGain / MP3gain tags will be copied from old tag even if not passed in data
	public $warnings                   = array(); // any non-critical errors will be stored here
	public $errors                     = array(); // any critical errors will be stored here

	public function getid3_write_apetag() {
		return true;
	}

	public function WriteAPEtag() {
		// NOTE: All data passed to this function must be UTF-8 format

		$getID3 = new getID3;
		$ThisFileInfo = $getID3->analyze($this->filename);

		if (isset($ThisFileInfo['ape']['tag_offset_start']) && isset($ThisFileInfo['lyrics3']['tag_offset_end'])) {
			if ($ThisFileInfo['ape']['tag_offset_start'] >= $ThisFileInfo['lyrics3']['tag_offset_end']) {
				// Current APE tag between Lyrics3 and ID3v1/EOF
				// This break Lyrics3 functionality
				if (!$this->DeleteAPEtag()) {
					return false;
				}
				$ThisFileInfo = $getID3->analyze($this->filename);
			}
		}

		if ($this->always_preserve_replaygain) {
			$ReplayGainTagsToPreserve = array('mp3gain_minmax', 'mp3gain_album_minmax', 'mp3gain_undo', 'replaygain_track_peak', 'replaygain_track_gain', 'replaygain_album_peak', 'replaygain_album_gain');
			foreach ($ReplayGainTagsToPreserve as $rg_key) {
				if (isset($ThisFileInfo['ape']['items'][strtolower($rg_key)]['data'][0]) && !isset($this->tag_data[strtoupper($rg_key)][0])) {
					$this->tag_data[strtoupper($rg_key)][0] = $ThisFileInfo['ape']['items'][strtolower($rg_key)]['data'][0];
				}
			}
		}

		if ($APEtag = $this->GenerateAPEtag()) {
			if (is_writable($this->filename) && is_file($this->filename) && ($fp = fopen($this->filename, 'a+b'))) {
				$oldignoreuserabort = ignore_user_abort(true);
				flock($fp, LOCK_EX);

				$PostAPEdataOffset = $ThisFileInfo['avdataend'];
				if (isset($ThisFileInfo['ape']['tag_offset_end'])) {
					$PostAPEdataOffset = max($PostAPEdataOffset, $ThisFileInfo['ape']['tag_offset_end']);
				}
				if (isset($ThisFileInfo['lyrics3']['tag_offset_start'])) {
					$PostAPEdataOffset = max($PostAPEdataOffset, $ThisFileInfo['lyrics3']['tag_offset_start']);
				}
				fseek($fp, $PostAPEdataOffset);
				$PostAPEdata = '';
				if ($ThisFileInfo['filesize'] > $PostAPEdataOffset) {
					$PostAPEdata = fread($fp, $ThisFileInfo['filesize'] - $PostAPEdataOffset);
				}

				fseek($fp, $PostAPEdataOffset);
				if (isset($ThisFileInfo['ape']['tag_offset_start'])) {
					fseek($fp, $ThisFileInfo['ape']['tag_offset_start']);
				}
				ftruncate($fp, ftell($fp));
				fwrite($fp, $APEtag, strlen($APEtag));
				if (!empty($PostAPEdata)) {
					fwrite($fp, $PostAPEdata, strlen($PostAPEdata));
				}
				flock($fp, LOCK_UN);
				fclose($fp);
				ignore_user_abort($oldignoreuserabort);
				return true;
			}
		}
		return false;
	}

	public function DeleteAPEtag() {
		$getID3 = new getID3;
		$ThisFileInfo = $getID3->analyze($this->filename);
		if (isset($ThisFileInfo['ape']['tag_offset_start']) && isset($ThisFileInfo['ape']['tag_offset_end'])) {
			if (is_writable($this->filename) && is_file($this->filename) && ($fp = fopen($this->filename, 'a+b'))) {

				flock($fp, LOCK_EX);
				$oldignoreuserabort = ignore_user_abort(true);

				fseek($fp, $ThisFileInfo['ape']['tag_offset_end']);
				$DataAfterAPE = '';
				if ($ThisFileInfo['filesize'] > $ThisFileInfo['ape']['tag_offset_end']) {
					$DataAfterAPE = fread($fp, $ThisFileInfo['filesize'] - $ThisFileInfo['ape']['tag_offset_end']);
				}

				ftruncate($fp, $ThisFileInfo['ape']['tag_offset_start']);
				fseek($fp, $ThisFileInfo['ape']['tag_offset_start']);

				if (!empty($DataAfterAPE)) {
					fwrite($fp, $DataAfterAPE, strlen($DataAfterAPE));
				}

				flock($fp, LOCK_UN);
				fclose($fp);
				ignore_user_abort($oldignoreuserabort);

				return true;
			}
			return false;
		}
		return true;
	}


	public function GenerateAPEtag() {
		// NOTE: All data passed to this function must be UTF-8 format

		$items = array();
		if (!is_array($this->tag_data)) {
			return false;
		}
		foreach ($this->tag_data as $key => $arrayofvalues) {
			if (!is_array($arrayofvalues)) {
				return false;
			}

			$valuestring = '';
			foreach ($arrayofvalues as $value) {
				$valuestring .= str_replace("\x00", '', $value)."\x00";
			}
			$valuestring = rtrim($valuestring, "\x00");

			// Length of the assigned value in bytes
			$tagitem  = getid3_lib::LittleEndian2String(strlen($valuestring), 4);

			//$tagitem .= $this->GenerateAPEtagFlags(true, true, false, 0, false);
			$tagitem .= "\x00\x00\x00\x00";

			$tagitem .= $this->CleanAPEtagItemKey($key)."\x00";
			$tagitem .= $valuestring;

			$items[] = $tagitem;

		}

		return $this->GenerateAPEtagHeaderFooter($items, true).implode('', $items).$this->GenerateAPEtagHeaderFooter($items, false);
	}

	public function GenerateAPEtagHeaderFooter(&$items, $isheader=false) {
		$tagdatalength = 0;
		foreach ($items as $itemdata) {
			$tagdatalength += strlen($itemdata);
		}

		$APEheader  = 'APETAGEX';
		$APEheader .= getid3_lib::LittleEndian2String(2000, 4);
		$APEheader .= getid3_lib::LittleEndian2String(32 + $tagdatalength, 4);
		$APEheader .= getid3_lib::LittleEndian2String(count($items), 4);
		$APEheader .= $this->GenerateAPEtagFlags(true, true, $isheader, 0, false);
		$APEheader .= str_repeat("\x00", 8);

		return $APEheader;
	}

	public function GenerateAPEtagFlags($header=true, $footer=true, $isheader=false, $encodingid=0, $readonly=false) {
		$APEtagFlags = array_fill(0, 4, 0);
		if ($header) {
			$APEtagFlags[0] |= 0x80; // Tag contains a header
		}
		if (!$footer) {
			$APEtagFlags[0] |= 0x40; // Tag contains no footer
		}
		if ($isheader) {
			$APEtagFlags[0] |= 0x20; // This is the header, not the footer
		}

		// 0: Item contains text information coded in UTF-8
		// 1: Item contains binary information °)
		// 2: Item is a locator of external stored information °°)
		// 3: reserved
		$APEtagFlags[3] |= ($encodingid << 1);

		if ($readonly) {
			$APEtagFlags[3] |= 0x01; // Tag or Item is Read Only
		}

		return chr($APEtagFlags[3]).chr($APEtagFlags[2]).chr($APEtagFlags[1]).chr($APEtagFlags[0]);
	}

	public function CleanAPEtagItemKey($itemkey) {
		$itemkey = preg_replace("#[^\x20-\x7E]#i", '', $itemkey);

		// http://www.personal.uni-jena.de/~pfk/mpp/sv8/apekey.html
		switch (strtoupper($itemkey)) {
			case 'EAN/UPC':
			case 'ISBN':
			case 'LC':
			case 'ISRC':
				$itemkey = strtoupper($itemkey);
				break;

			default:
				$itemkey = ucwords($itemkey);
				break;
		}
		return $itemkey;

	}

}
