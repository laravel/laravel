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
// write.id3v1.php                                             //
// module for writing ID3v1 tags                               //
// dependencies: module.tag.id3v1.php                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.tag.id3v1.php', __FILE__, true);

class getid3_write_id3v1
{
	public $filename;
	public $filesize;
	public $tag_data;
	public $warnings = array(); // any non-critical errors will be stored here
	public $errors   = array(); // any critical errors will be stored here

	public function getid3_write_id3v1() {
		return true;
	}

	public function WriteID3v1() {
		// File MUST be writeable - CHMOD(646) at least
		if (!empty($this->filename) && is_readable($this->filename) && is_writable($this->filename) && is_file($this->filename)) {
			$this->setRealFileSize();
			if (($this->filesize <= 0) || !getid3_lib::intValueSupported($this->filesize)) {
				$this->errors[] = 'Unable to WriteID3v1('.$this->filename.') because filesize ('.$this->filesize.') is larger than '.round(PHP_INT_MAX / 1073741824).'GB';
				return false;
			}
			if ($fp_source = fopen($this->filename, 'r+b')) {
				fseek($fp_source, -128, SEEK_END);
				if (fread($fp_source, 3) == 'TAG') {
					fseek($fp_source, -128, SEEK_END); // overwrite existing ID3v1 tag
				} else {
					fseek($fp_source, 0, SEEK_END);    // append new ID3v1 tag
				}
				$this->tag_data['track'] = (isset($this->tag_data['track']) ? $this->tag_data['track'] : (isset($this->tag_data['track_number']) ? $this->tag_data['track_number'] : (isset($this->tag_data['tracknumber']) ? $this->tag_data['tracknumber'] : '')));

				$new_id3v1_tag_data = getid3_id3v1::GenerateID3v1Tag(
														(isset($this->tag_data['title']  ) ? $this->tag_data['title']   : ''),
														(isset($this->tag_data['artist'] ) ? $this->tag_data['artist']  : ''),
														(isset($this->tag_data['album']  ) ? $this->tag_data['album']   : ''),
														(isset($this->tag_data['year']   ) ? $this->tag_data['year']    : ''),
														(isset($this->tag_data['genreid']) ? $this->tag_data['genreid'] : ''),
														(isset($this->tag_data['comment']) ? $this->tag_data['comment'] : ''),
														(isset($this->tag_data['track']  ) ? $this->tag_data['track']   : ''));
				fwrite($fp_source, $new_id3v1_tag_data, 128);
				fclose($fp_source);
				return true;

			} else {
				$this->errors[] = 'Could not fopen('.$this->filename.', "r+b")';
				return false;
			}
		}
		$this->errors[] = 'File is not writeable: '.$this->filename;
		return false;
	}

	public function FixID3v1Padding() {
		// ID3v1 data is supposed to be padded with NULL characters, but some taggers incorrectly use spaces
		// This function rewrites the ID3v1 tag with correct padding

		// Initialize getID3 engine
		$getID3 = new getID3;
		$getID3->option_tag_id3v2  = false;
		$getID3->option_tag_apetag = false;
		$getID3->option_tags_html  = false;
		$getID3->option_extra_info = false;
		$getID3->option_tag_id3v1  = true;
		$ThisFileInfo = $getID3->analyze($this->filename);
		if (isset($ThisFileInfo['tags']['id3v1'])) {
			foreach ($ThisFileInfo['tags']['id3v1'] as $key => $value) {
				$id3v1data[$key] = implode(',', $value);
			}
			$this->tag_data = $id3v1data;
			return $this->WriteID3v1();
		}
		return false;
	}

	public function RemoveID3v1() {
		// File MUST be writeable - CHMOD(646) at least
		if (!empty($this->filename) && is_readable($this->filename) && is_writable($this->filename) && is_file($this->filename)) {
			$this->setRealFileSize();
			if (($this->filesize <= 0) || !getid3_lib::intValueSupported($this->filesize)) {
				$this->errors[] = 'Unable to RemoveID3v1('.$this->filename.') because filesize ('.$this->filesize.') is larger than '.round(PHP_INT_MAX / 1073741824).'GB';
				return false;
			}
			if ($fp_source = fopen($this->filename, 'r+b')) {

				fseek($fp_source, -128, SEEK_END);
				if (fread($fp_source, 3) == 'TAG') {
					ftruncate($fp_source, $this->filesize - 128);
				} else {
					// no ID3v1 tag to begin with - do nothing
				}
				fclose($fp_source);
				return true;

			} else {
				$this->errors[] = 'Could not fopen('.$this->filename.', "r+b")';
			}
		} else {
			$this->errors[] = $this->filename.' is not writeable';
		}
		return false;
	}

	public function setRealFileSize() {
		if (PHP_INT_MAX > 2147483647) {
			$this->filesize = filesize($this->filename);
			return true;
		}
		// 32-bit PHP will not return correct values for filesize() if file is >=2GB
		// but getID3->analyze() has workarounds to get actual filesize
		$getID3 = new getID3;
		$getID3->option_tag_id3v1  = false;
		$getID3->option_tag_id3v2  = false;
		$getID3->option_tag_apetag = false;
		$getID3->option_tags_html  = false;
		$getID3->option_extra_info = false;
		$ThisFileInfo = $getID3->analyze($this->filename);
		$this->filesize = $ThisFileInfo['filesize'];
		return true;
	}

}
