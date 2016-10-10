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
// module.tag.apetag.php                                       //
// module for analyzing APE tags                               //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

class getid3_apetag extends getid3_handler
{
	public $inline_attachments = true; // true: return full data for all attachments; false: return no data for all attachments; integer: return data for attachments <= than this; string: save as file to this directory
	public $overrideendoffset  = 0;

	public function Analyze() {
		$info = &$this->getid3->info;

		if (!getid3_lib::intValueSupported($info['filesize'])) {
			$info['warning'][] = 'Unable to check for APEtags because file is larger than '.round(PHP_INT_MAX / 1073741824).'GB';
			return false;
		}

		$id3v1tagsize     = 128;
		$apetagheadersize = 32;
		$lyrics3tagsize   = 10;

		if ($this->overrideendoffset == 0) {

			$this->fseek(0 - $id3v1tagsize - $apetagheadersize - $lyrics3tagsize, SEEK_END);
			$APEfooterID3v1 = $this->fread($id3v1tagsize + $apetagheadersize + $lyrics3tagsize);

			//if (preg_match('/APETAGEX.{24}TAG.{125}$/i', $APEfooterID3v1)) {
			if (substr($APEfooterID3v1, strlen($APEfooterID3v1) - $id3v1tagsize - $apetagheadersize, 8) == 'APETAGEX') {

				// APE tag found before ID3v1
				$info['ape']['tag_offset_end'] = $info['filesize'] - $id3v1tagsize;

			//} elseif (preg_match('/APETAGEX.{24}$/i', $APEfooterID3v1)) {
			} elseif (substr($APEfooterID3v1, strlen($APEfooterID3v1) - $apetagheadersize, 8) == 'APETAGEX') {

				// APE tag found, no ID3v1
				$info['ape']['tag_offset_end'] = $info['filesize'];

			}

		} else {

			$this->fseek($this->overrideendoffset - $apetagheadersize);
			if ($this->fread(8) == 'APETAGEX') {
				$info['ape']['tag_offset_end'] = $this->overrideendoffset;
			}

		}
		if (!isset($info['ape']['tag_offset_end'])) {

			// APE tag not found
			unset($info['ape']);
			return false;

		}

		// shortcut
		$thisfile_ape = &$info['ape'];

		$this->fseek($thisfile_ape['tag_offset_end'] - $apetagheadersize);
		$APEfooterData = $this->fread(32);
		if (!($thisfile_ape['footer'] = $this->parseAPEheaderFooter($APEfooterData))) {
			$info['error'][] = 'Error parsing APE footer at offset '.$thisfile_ape['tag_offset_end'];
			return false;
		}

		if (isset($thisfile_ape['footer']['flags']['header']) && $thisfile_ape['footer']['flags']['header']) {
			$this->fseek($thisfile_ape['tag_offset_end'] - $thisfile_ape['footer']['raw']['tagsize'] - $apetagheadersize);
			$thisfile_ape['tag_offset_start'] = $this->ftell();
			$APEtagData = $this->fread($thisfile_ape['footer']['raw']['tagsize'] + $apetagheadersize);
		} else {
			$thisfile_ape['tag_offset_start'] = $thisfile_ape['tag_offset_end'] - $thisfile_ape['footer']['raw']['tagsize'];
			$this->fseek($thisfile_ape['tag_offset_start']);
			$APEtagData = $this->fread($thisfile_ape['footer']['raw']['tagsize']);
		}
		$info['avdataend'] = $thisfile_ape['tag_offset_start'];

		if (isset($info['id3v1']['tag_offset_start']) && ($info['id3v1']['tag_offset_start'] < $thisfile_ape['tag_offset_end'])) {
			$info['warning'][] = 'ID3v1 tag information ignored since it appears to be a false synch in APEtag data';
			unset($info['id3v1']);
			foreach ($info['warning'] as $key => $value) {
				if ($value == 'Some ID3v1 fields do not use NULL characters for padding') {
					unset($info['warning'][$key]);
					sort($info['warning']);
					break;
				}
			}
		}

		$offset = 0;
		if (isset($thisfile_ape['footer']['flags']['header']) && $thisfile_ape['footer']['flags']['header']) {
			if ($thisfile_ape['header'] = $this->parseAPEheaderFooter(substr($APEtagData, 0, $apetagheadersize))) {
				$offset += $apetagheadersize;
			} else {
				$info['error'][] = 'Error parsing APE header at offset '.$thisfile_ape['tag_offset_start'];
				return false;
			}
		}

		// shortcut
		$info['replay_gain'] = array();
		$thisfile_replaygain = &$info['replay_gain'];

		for ($i = 0; $i < $thisfile_ape['footer']['raw']['tag_items']; $i++) {
			$value_size = getid3_lib::LittleEndian2Int(substr($APEtagData, $offset, 4));
			$offset += 4;
			$item_flags = getid3_lib::LittleEndian2Int(substr($APEtagData, $offset, 4));
			$offset += 4;
			if (strstr(substr($APEtagData, $offset), "\x00") === false) {
				$info['error'][] = 'Cannot find null-byte (0x00) seperator between ItemKey #'.$i.' and value. ItemKey starts '.$offset.' bytes into the APE tag, at file offset '.($thisfile_ape['tag_offset_start'] + $offset);
				return false;
			}
			$ItemKeyLength = strpos($APEtagData, "\x00", $offset) - $offset;
			$item_key      = strtolower(substr($APEtagData, $offset, $ItemKeyLength));

			// shortcut
			$thisfile_ape['items'][$item_key] = array();
			$thisfile_ape_items_current = &$thisfile_ape['items'][$item_key];

			$thisfile_ape_items_current['offset'] = $thisfile_ape['tag_offset_start'] + $offset;

			$offset += ($ItemKeyLength + 1); // skip 0x00 terminator
			$thisfile_ape_items_current['data'] = substr($APEtagData, $offset, $value_size);
			$offset += $value_size;

			$thisfile_ape_items_current['flags'] = $this->parseAPEtagFlags($item_flags);
			switch ($thisfile_ape_items_current['flags']['item_contents_raw']) {
				case 0: // UTF-8
				case 2: // Locator (URL, filename, etc), UTF-8 encoded
					$thisfile_ape_items_current['data'] = explode("\x00", $thisfile_ape_items_current['data']);
					break;

				case 1:  // binary data
				default:
					break;
			}

			switch (strtolower($item_key)) {
				// http://wiki.hydrogenaud.io/index.php?title=ReplayGain#MP3Gain
				case 'replaygain_track_gain':
					if (preg_match('#^[\\-\\+][0-9\\.,]{8}$#', $thisfile_ape_items_current['data'][0])) {
						$thisfile_replaygain['track']['adjustment'] = (float) str_replace(',', '.', $thisfile_ape_items_current['data'][0]); // float casting will see "0,95" as zero!
						$thisfile_replaygain['track']['originator'] = 'unspecified';
					} else {
						$info['warning'][] = 'MP3gainTrackGain value in APEtag appears invalid: "'.$thisfile_ape_items_current['data'][0].'"';
					}
					break;

				case 'replaygain_track_peak':
					if (preg_match('#^[0-9\\.,]{8}$#', $thisfile_ape_items_current['data'][0])) {
						$thisfile_replaygain['track']['peak']       = (float) str_replace(',', '.', $thisfile_ape_items_current['data'][0]); // float casting will see "0,95" as zero!
						$thisfile_replaygain['track']['originator'] = 'unspecified';
						if ($thisfile_replaygain['track']['peak'] <= 0) {
							$info['warning'][] = 'ReplayGain Track peak from APEtag appears invalid: '.$thisfile_replaygain['track']['peak'].' (original value = "'.$thisfile_ape_items_current['data'][0].'")';
						}
					} else {
						$info['warning'][] = 'MP3gainTrackPeak value in APEtag appears invalid: "'.$thisfile_ape_items_current['data'][0].'"';
					}
					break;

				case 'replaygain_album_gain':
					if (preg_match('#^[\\-\\+][0-9\\.,]{8}$#', $thisfile_ape_items_current['data'][0])) {
						$thisfile_replaygain['album']['adjustment'] = (float) str_replace(',', '.', $thisfile_ape_items_current['data'][0]); // float casting will see "0,95" as zero!
						$thisfile_replaygain['album']['originator'] = 'unspecified';
					} else {
						$info['warning'][] = 'MP3gainAlbumGain value in APEtag appears invalid: "'.$thisfile_ape_items_current['data'][0].'"';
					}
					break;

				case 'replaygain_album_peak':
					if (preg_match('#^[0-9\\.,]{8}$#', $thisfile_ape_items_current['data'][0])) {
						$thisfile_replaygain['album']['peak']       = (float) str_replace(',', '.', $thisfile_ape_items_current['data'][0]); // float casting will see "0,95" as zero!
						$thisfile_replaygain['album']['originator'] = 'unspecified';
						if ($thisfile_replaygain['album']['peak'] <= 0) {
							$info['warning'][] = 'ReplayGain Album peak from APEtag appears invalid: '.$thisfile_replaygain['album']['peak'].' (original value = "'.$thisfile_ape_items_current['data'][0].'")';
						}
					} else {
						$info['warning'][] = 'MP3gainAlbumPeak value in APEtag appears invalid: "'.$thisfile_ape_items_current['data'][0].'"';
					}
					break;

				case 'mp3gain_undo':
					if (preg_match('#^[\\-\\+][0-9]{3},[\\-\\+][0-9]{3},[NW]$#', $thisfile_ape_items_current['data'][0])) {
						list($mp3gain_undo_left, $mp3gain_undo_right, $mp3gain_undo_wrap) = explode(',', $thisfile_ape_items_current['data'][0]);
						$thisfile_replaygain['mp3gain']['undo_left']  = intval($mp3gain_undo_left);
						$thisfile_replaygain['mp3gain']['undo_right'] = intval($mp3gain_undo_right);
						$thisfile_replaygain['mp3gain']['undo_wrap']  = (($mp3gain_undo_wrap == 'Y') ? true : false);
					} else {
						$info['warning'][] = 'MP3gainUndo value in APEtag appears invalid: "'.$thisfile_ape_items_current['data'][0].'"';
					}
					break;

				case 'mp3gain_minmax':
					if (preg_match('#^[0-9]{3},[0-9]{3}$#', $thisfile_ape_items_current['data'][0])) {
						list($mp3gain_globalgain_min, $mp3gain_globalgain_max) = explode(',', $thisfile_ape_items_current['data'][0]);
						$thisfile_replaygain['mp3gain']['globalgain_track_min'] = intval($mp3gain_globalgain_min);
						$thisfile_replaygain['mp3gain']['globalgain_track_max'] = intval($mp3gain_globalgain_max);
					} else {
						$info['warning'][] = 'MP3gainMinMax value in APEtag appears invalid: "'.$thisfile_ape_items_current['data'][0].'"';
					}
					break;

				case 'mp3gain_album_minmax':
					if (preg_match('#^[0-9]{3},[0-9]{3}$#', $thisfile_ape_items_current['data'][0])) {
						list($mp3gain_globalgain_album_min, $mp3gain_globalgain_album_max) = explode(',', $thisfile_ape_items_current['data'][0]);
						$thisfile_replaygain['mp3gain']['globalgain_album_min'] = intval($mp3gain_globalgain_album_min);
						$thisfile_replaygain['mp3gain']['globalgain_album_max'] = intval($mp3gain_globalgain_album_max);
					} else {
						$info['warning'][] = 'MP3gainAlbumMinMax value in APEtag appears invalid: "'.$thisfile_ape_items_current['data'][0].'"';
					}
					break;

				case 'tracknumber':
					if (is_array($thisfile_ape_items_current['data'])) {
						foreach ($thisfile_ape_items_current['data'] as $comment) {
							$thisfile_ape['comments']['track'][] = $comment;
						}
					}
					break;

				case 'cover art (artist)':
				case 'cover art (back)':
				case 'cover art (band logo)':
				case 'cover art (band)':
				case 'cover art (colored fish)':
				case 'cover art (composer)':
				case 'cover art (conductor)':
				case 'cover art (front)':
				case 'cover art (icon)':
				case 'cover art (illustration)':
				case 'cover art (lead)':
				case 'cover art (leaflet)':
				case 'cover art (lyricist)':
				case 'cover art (media)':
				case 'cover art (movie scene)':
				case 'cover art (other icon)':
				case 'cover art (other)':
				case 'cover art (performance)':
				case 'cover art (publisher logo)':
				case 'cover art (recording)':
				case 'cover art (studio)':
					// list of possible cover arts from http://taglib-sharp.sourcearchive.com/documentation/2.0.3.0-2/Ape_2Tag_8cs-source.html
					if (is_array($thisfile_ape_items_current['data'])) {
						$info['warning'][] = 'APEtag "'.$item_key.'" should be flagged as Binary data, but was incorrectly flagged as UTF-8';
						$thisfile_ape_items_current['data'] = implode("\x00", $thisfile_ape_items_current['data']);
					}
					list($thisfile_ape_items_current['filename'], $thisfile_ape_items_current['data']) = explode("\x00", $thisfile_ape_items_current['data'], 2);
					$thisfile_ape_items_current['data_offset'] = $thisfile_ape_items_current['offset'] + strlen($thisfile_ape_items_current['filename']."\x00");
					$thisfile_ape_items_current['data_length'] = strlen($thisfile_ape_items_current['data']);

					$thisfile_ape_items_current['image_mime'] = '';
					$imageinfo = array();
					$imagechunkcheck = getid3_lib::GetDataImageSize($thisfile_ape_items_current['data'], $imageinfo);
					$thisfile_ape_items_current['image_mime'] = image_type_to_mime_type($imagechunkcheck[2]);

					do {
						if ($this->inline_attachments === false) {
							// skip entirely
							unset($thisfile_ape_items_current['data']);
							break;
						}
						if ($this->inline_attachments === true) {
							// great
						} elseif (is_int($this->inline_attachments)) {
							if ($this->inline_attachments < $thisfile_ape_items_current['data_length']) {
								// too big, skip
								$info['warning'][] = 'attachment at '.$thisfile_ape_items_current['offset'].' is too large to process inline ('.number_format($thisfile_ape_items_current['data_length']).' bytes)';
								unset($thisfile_ape_items_current['data']);
								break;
							}
						} elseif (is_string($this->inline_attachments)) {
							$this->inline_attachments = rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $this->inline_attachments), DIRECTORY_SEPARATOR);
							if (!is_dir($this->inline_attachments) || !is_writable($this->inline_attachments)) {
								// cannot write, skip
								$info['warning'][] = 'attachment at '.$thisfile_ape_items_current['offset'].' cannot be saved to "'.$this->inline_attachments.'" (not writable)';
								unset($thisfile_ape_items_current['data']);
								break;
							}
						}
						// if we get this far, must be OK
						if (is_string($this->inline_attachments)) {
							$destination_filename = $this->inline_attachments.DIRECTORY_SEPARATOR.md5($info['filenamepath']).'_'.$thisfile_ape_items_current['data_offset'];
							if (!file_exists($destination_filename) || is_writable($destination_filename)) {
								file_put_contents($destination_filename, $thisfile_ape_items_current['data']);
							} else {
								$info['warning'][] = 'attachment at '.$thisfile_ape_items_current['offset'].' cannot be saved to "'.$destination_filename.'" (not writable)';
							}
							$thisfile_ape_items_current['data_filename'] = $destination_filename;
							unset($thisfile_ape_items_current['data']);
						} else {
							if (!isset($info['ape']['comments']['picture'])) {
								$info['ape']['comments']['picture'] = array();
							}
							$comments_picture_data = array();
							foreach (array('data', 'image_mime', 'image_width', 'image_height', 'imagetype', 'picturetype', 'description', 'datalength') as $picture_key) {
								if (isset($thisfile_ape_items_current[$picture_key])) {
									$comments_picture_data[$picture_key] = $thisfile_ape_items_current[$picture_key];
								}
							}
							$info['ape']['comments']['picture'][] = $comments_picture_data;
							unset($comments_picture_data);
						}
					} while (false);
					break;

				default:
					if (is_array($thisfile_ape_items_current['data'])) {
						foreach ($thisfile_ape_items_current['data'] as $comment) {
							$thisfile_ape['comments'][strtolower($item_key)][] = $comment;
						}
					}
					break;
			}

		}
		if (empty($thisfile_replaygain)) {
			unset($info['replay_gain']);
		}
		return true;
	}

	public function parseAPEheaderFooter($APEheaderFooterData) {
		// http://www.uni-jena.de/~pfk/mpp/sv8/apeheader.html

		// shortcut
		$headerfooterinfo['raw'] = array();
		$headerfooterinfo_raw = &$headerfooterinfo['raw'];

		$headerfooterinfo_raw['footer_tag']   =                  substr($APEheaderFooterData,  0, 8);
		if ($headerfooterinfo_raw['footer_tag'] != 'APETAGEX') {
			return false;
		}
		$headerfooterinfo_raw['version']      = getid3_lib::LittleEndian2Int(substr($APEheaderFooterData,  8, 4));
		$headerfooterinfo_raw['tagsize']      = getid3_lib::LittleEndian2Int(substr($APEheaderFooterData, 12, 4));
		$headerfooterinfo_raw['tag_items']    = getid3_lib::LittleEndian2Int(substr($APEheaderFooterData, 16, 4));
		$headerfooterinfo_raw['global_flags'] = getid3_lib::LittleEndian2Int(substr($APEheaderFooterData, 20, 4));
		$headerfooterinfo_raw['reserved']     =                              substr($APEheaderFooterData, 24, 8);

		$headerfooterinfo['tag_version']         = $headerfooterinfo_raw['version'] / 1000;
		if ($headerfooterinfo['tag_version'] >= 2) {
			$headerfooterinfo['flags'] = $this->parseAPEtagFlags($headerfooterinfo_raw['global_flags']);
		}
		return $headerfooterinfo;
	}

	public function parseAPEtagFlags($rawflagint) {
		// "Note: APE Tags 1.0 do not use any of the APE Tag flags.
		// All are set to zero on creation and ignored on reading."
		// http://wiki.hydrogenaud.io/index.php?title=Ape_Tags_Flags
		$flags['header']            = (bool) ($rawflagint & 0x80000000);
		$flags['footer']            = (bool) ($rawflagint & 0x40000000);
		$flags['this_is_header']    = (bool) ($rawflagint & 0x20000000);
		$flags['item_contents_raw'] =        ($rawflagint & 0x00000006) >> 1;
		$flags['read_only']         = (bool) ($rawflagint & 0x00000001);

		$flags['item_contents']     = $this->APEcontentTypeFlagLookup($flags['item_contents_raw']);

		return $flags;
	}

	public function APEcontentTypeFlagLookup($contenttypeid) {
		static $APEcontentTypeFlagLookup = array(
			0 => 'utf-8',
			1 => 'binary',
			2 => 'external',
			3 => 'reserved'
		);
		return (isset($APEcontentTypeFlagLookup[$contenttypeid]) ? $APEcontentTypeFlagLookup[$contenttypeid] : 'invalid');
	}

	public function APEtagItemIsUTF8Lookup($itemkey) {
		static $APEtagItemIsUTF8Lookup = array(
			'title',
			'subtitle',
			'artist',
			'album',
			'debut album',
			'publisher',
			'conductor',
			'track',
			'composer',
			'comment',
			'copyright',
			'publicationright',
			'file',
			'year',
			'record date',
			'record location',
			'genre',
			'media',
			'related',
			'isrc',
			'abstract',
			'language',
			'bibliography'
		);
		return in_array(strtolower($itemkey), $APEtagItemIsUTF8Lookup);
	}

}
