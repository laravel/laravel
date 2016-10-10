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
// write.lyrics3.php                                           //
// module for writing Lyrics3 tags                             //
// dependencies: module.tag.lyrics3.php                        //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_write_lyrics3
{
	public $filename;
	public $tag_data;
	//public $lyrics3_version = 2;       // 1 or 2
	public $warnings        = array(); // any non-critical errors will be stored here
	public $errors          = array(); // any critical errors will be stored here

	public function getid3_write_lyrics3() {
		return true;
	}

	public function WriteLyrics3() {
		$this->errors[] = 'WriteLyrics3() not yet functional - cannot write Lyrics3';
		return false;
	}
	public function DeleteLyrics3() {
		// Initialize getID3 engine
		$getID3 = new getID3;
		$ThisFileInfo = $getID3->analyze($this->filename);
		if (isset($ThisFileInfo['lyrics3']['tag_offset_start']) && isset($ThisFileInfo['lyrics3']['tag_offset_end'])) {
			if (is_readable($this->filename) && is_writable($this->filename) && is_file($this->filename) && ($fp = fopen($this->filename, 'a+b'))) {

				flock($fp, LOCK_EX);
				$oldignoreuserabort = ignore_user_abort(true);

				fseek($fp, $ThisFileInfo['lyrics3']['tag_offset_end']);
				$DataAfterLyrics3 = '';
				if ($ThisFileInfo['filesize'] > $ThisFileInfo['lyrics3']['tag_offset_end']) {
					$DataAfterLyrics3 = fread($fp, $ThisFileInfo['filesize'] - $ThisFileInfo['lyrics3']['tag_offset_end']);
				}

				ftruncate($fp, $ThisFileInfo['lyrics3']['tag_offset_start']);

				if (!empty($DataAfterLyrics3)) {
					fseek($fp, $ThisFileInfo['lyrics3']['tag_offset_start']);
					fwrite($fp, $DataAfterLyrics3, strlen($DataAfterLyrics3));
				}

				flock($fp, LOCK_UN);
				fclose($fp);
				ignore_user_abort($oldignoreuserabort);

				return true;

			} else {
				$this->errors[] = 'Cannot fopen('.$this->filename.', "a+b")';
				return false;
			}
		}
		// no Lyrics3 present
		return true;
	}

}
