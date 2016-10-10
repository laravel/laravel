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
// module.audio.flac.php                                       //
// module for analyzing FLAC and OggFLAC audio files           //
// dependencies: module.audio.ogg.php                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.audio.ogg.php', __FILE__, true);

/**
* @tutorial http://flac.sourceforge.net/format.html
*/
class getid3_flac extends getid3_handler
{
	const syncword = 'fLaC';

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);
		$StreamMarker = $this->fread(4);
		if ($StreamMarker != self::syncword) {
			return $this->error('Expecting "'.getid3_lib::PrintHexBytes(self::syncword).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($StreamMarker).'"');
		}
		$info['fileformat']            = 'flac';
		$info['audio']['dataformat']   = 'flac';
		$info['audio']['bitrate_mode'] = 'vbr';
		$info['audio']['lossless']     = true;

		// parse flac container
		return $this->parseMETAdata();
	}

	public function parseMETAdata() {
		$info = &$this->getid3->info;
		do {
			$BlockOffset   = $this->ftell();
			$BlockHeader   = $this->fread(4);
			$LBFBT         = getid3_lib::BigEndian2Int(substr($BlockHeader, 0, 1));
			$LastBlockFlag = (bool) ($LBFBT & 0x80);
			$BlockType     =        ($LBFBT & 0x7F);
			$BlockLength   = getid3_lib::BigEndian2Int(substr($BlockHeader, 1, 3));
			$BlockTypeText = self::metaBlockTypeLookup($BlockType);

			if (($BlockOffset + 4 + $BlockLength) > $info['avdataend']) {
				$this->error('METADATA_BLOCK_HEADER.BLOCK_TYPE ('.$BlockTypeText.') at offset '.$BlockOffset.' extends beyond end of file');
				break;
			}
			if ($BlockLength < 1) {
				$this->error('METADATA_BLOCK_HEADER.BLOCK_LENGTH ('.$BlockLength.') at offset '.$BlockOffset.' is invalid');
				break;
			}

			$info['flac'][$BlockTypeText]['raw'] = array();
			$BlockTypeText_raw = &$info['flac'][$BlockTypeText]['raw'];

			$BlockTypeText_raw['offset']          = $BlockOffset;
			$BlockTypeText_raw['last_meta_block'] = $LastBlockFlag;
			$BlockTypeText_raw['block_type']      = $BlockType;
			$BlockTypeText_raw['block_type_text'] = $BlockTypeText;
			$BlockTypeText_raw['block_length']    = $BlockLength;
			if ($BlockTypeText_raw['block_type'] != 0x06) { // do not read attachment data automatically
				$BlockTypeText_raw['block_data']  = $this->fread($BlockLength);
			}

			switch ($BlockTypeText) {
				case 'STREAMINFO':     // 0x00
					if (!$this->parseSTREAMINFO($BlockTypeText_raw['block_data'])) {
						return false;
					}
					break;

				case 'PADDING':        // 0x01
					unset($info['flac']['PADDING']); // ignore
					break;

				case 'APPLICATION':    // 0x02
					if (!$this->parseAPPLICATION($BlockTypeText_raw['block_data'])) {
						return false;
					}
					break;

				case 'SEEKTABLE':      // 0x03
					if (!$this->parseSEEKTABLE($BlockTypeText_raw['block_data'])) {
						return false;
					}
					break;

				case 'VORBIS_COMMENT': // 0x04
					if (!$this->parseVORBIS_COMMENT($BlockTypeText_raw['block_data'])) {
						return false;
					}
					break;

				case 'CUESHEET':       // 0x05
					if (!$this->parseCUESHEET($BlockTypeText_raw['block_data'])) {
						return false;
					}
					break;

				case 'PICTURE':        // 0x06
					if (!$this->parsePICTURE()) {
						return false;
					}
					break;

				default:
					$this->warning('Unhandled METADATA_BLOCK_HEADER.BLOCK_TYPE ('.$BlockType.') at offset '.$BlockOffset);
			}

			unset($info['flac'][$BlockTypeText]['raw']);
			$info['avdataoffset'] = $this->ftell();
		}
		while ($LastBlockFlag === false);

		// handle tags
		if (!empty($info['flac']['VORBIS_COMMENT']['comments'])) {
			$info['flac']['comments'] = $info['flac']['VORBIS_COMMENT']['comments'];
		}
		if (!empty($info['flac']['VORBIS_COMMENT']['vendor'])) {
			$info['audio']['encoder'] = str_replace('reference ', '', $info['flac']['VORBIS_COMMENT']['vendor']);
		}

		// copy attachments to 'comments' array if nesesary
		if (isset($info['flac']['PICTURE']) && ($this->getid3->option_save_attachments !== getID3::ATTACHMENTS_NONE)) {
			foreach ($info['flac']['PICTURE'] as $entry) {
				if (!empty($entry['data'])) {
					if (!isset($info['flac']['comments']['picture'])) {
						$info['flac']['comments']['picture'] = array();
					}
					$comments_picture_data = array();
					foreach (array('data', 'image_mime', 'image_width', 'image_height', 'imagetype', 'picturetype', 'description', 'datalength') as $picture_key) {
						if (isset($entry[$picture_key])) {
							$comments_picture_data[$picture_key] = $entry[$picture_key];
						}
					}
					$info['flac']['comments']['picture'][] = $comments_picture_data;
					unset($comments_picture_data);
				}
			}
		}

		if (isset($info['flac']['STREAMINFO'])) {
			if (!$this->isDependencyFor('matroska')) {
				$info['flac']['compressed_audio_bytes'] = $info['avdataend'] - $info['avdataoffset'];
			}
			$info['flac']['uncompressed_audio_bytes'] = $info['flac']['STREAMINFO']['samples_stream'] * $info['flac']['STREAMINFO']['channels'] * ($info['flac']['STREAMINFO']['bits_per_sample'] / 8);
			if ($info['flac']['uncompressed_audio_bytes'] == 0) {
				return $this->error('Corrupt FLAC file: uncompressed_audio_bytes == zero');
			}
			if (!empty($info['flac']['compressed_audio_bytes'])) {
				$info['flac']['compression_ratio'] = $info['flac']['compressed_audio_bytes'] / $info['flac']['uncompressed_audio_bytes'];
			}
		}

		// set md5_data_source - built into flac 0.5+
		if (isset($info['flac']['STREAMINFO']['audio_signature'])) {

			if ($info['flac']['STREAMINFO']['audio_signature'] === str_repeat("\x00", 16)) {
                $this->warning('FLAC STREAMINFO.audio_signature is null (known issue with libOggFLAC)');
			}
			else {
				$info['md5_data_source'] = '';
				$md5 = $info['flac']['STREAMINFO']['audio_signature'];
				for ($i = 0; $i < strlen($md5); $i++) {
					$info['md5_data_source'] .= str_pad(dechex(ord($md5[$i])), 2, '00', STR_PAD_LEFT);
				}
				if (!preg_match('/^[0-9a-f]{32}$/', $info['md5_data_source'])) {
					unset($info['md5_data_source']);
				}
			}
		}

		if (isset($info['flac']['STREAMINFO']['bits_per_sample'])) {
			$info['audio']['bits_per_sample'] = $info['flac']['STREAMINFO']['bits_per_sample'];
			if ($info['audio']['bits_per_sample'] == 8) {
				// special case
				// must invert sign bit on all data bytes before MD5'ing to match FLAC's calculated value
				// MD5sum calculates on unsigned bytes, but FLAC calculated MD5 on 8-bit audio data as signed
				$this->warning('FLAC calculates MD5 data strangely on 8-bit audio, so the stored md5_data_source value will not match the decoded WAV file');
			}
		}

		return true;
	}

	private function parseSTREAMINFO($BlockData) {
		$info = &$this->getid3->info;

		$info['flac']['STREAMINFO'] = array();
		$streaminfo = &$info['flac']['STREAMINFO'];

		$streaminfo['min_block_size']  = getid3_lib::BigEndian2Int(substr($BlockData, 0, 2));
		$streaminfo['max_block_size']  = getid3_lib::BigEndian2Int(substr($BlockData, 2, 2));
		$streaminfo['min_frame_size']  = getid3_lib::BigEndian2Int(substr($BlockData, 4, 3));
		$streaminfo['max_frame_size']  = getid3_lib::BigEndian2Int(substr($BlockData, 7, 3));

		$SRCSBSS                       = getid3_lib::BigEndian2Bin(substr($BlockData, 10, 8));
		$streaminfo['sample_rate']     = getid3_lib::Bin2Dec(substr($SRCSBSS,  0, 20));
		$streaminfo['channels']        = getid3_lib::Bin2Dec(substr($SRCSBSS, 20,  3)) + 1;
		$streaminfo['bits_per_sample'] = getid3_lib::Bin2Dec(substr($SRCSBSS, 23,  5)) + 1;
		$streaminfo['samples_stream']  = getid3_lib::Bin2Dec(substr($SRCSBSS, 28, 36));

		$streaminfo['audio_signature'] = substr($BlockData, 18, 16);

		if (!empty($streaminfo['sample_rate'])) {

			$info['audio']['bitrate_mode']    = 'vbr';
			$info['audio']['sample_rate']     = $streaminfo['sample_rate'];
			$info['audio']['channels']        = $streaminfo['channels'];
			$info['audio']['bits_per_sample'] = $streaminfo['bits_per_sample'];
			$info['playtime_seconds']         = $streaminfo['samples_stream'] / $streaminfo['sample_rate'];
			if ($info['playtime_seconds'] > 0) {
				if (!$this->isDependencyFor('matroska')) {
					$info['audio']['bitrate'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];
				}
				else {
					$this->warning('Cannot determine audio bitrate because total stream size is unknown');
				}
			}

		} else {
			return $this->error('Corrupt METAdata block: STREAMINFO');
		}

		return true;
	}

	private function parseAPPLICATION($BlockData) {
		$info = &$this->getid3->info;

		$ApplicationID = getid3_lib::BigEndian2Int(substr($BlockData, 0, 4));
		$info['flac']['APPLICATION'][$ApplicationID]['name'] = self::applicationIDLookup($ApplicationID);
		$info['flac']['APPLICATION'][$ApplicationID]['data'] = substr($BlockData, 4);

		return true;
	}

	private function parseSEEKTABLE($BlockData) {
		$info = &$this->getid3->info;

		$offset = 0;
		$BlockLength = strlen($BlockData);
		$placeholderpattern = str_repeat("\xFF", 8);
		while ($offset < $BlockLength) {
			$SampleNumberString = substr($BlockData, $offset, 8);
			$offset += 8;
			if ($SampleNumberString == $placeholderpattern) {

				// placeholder point
				getid3_lib::safe_inc($info['flac']['SEEKTABLE']['placeholders'], 1);
				$offset += 10;

			} else {

				$SampleNumber                                        = getid3_lib::BigEndian2Int($SampleNumberString);
				$info['flac']['SEEKTABLE'][$SampleNumber]['offset']  = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 8));
				$offset += 8;
				$info['flac']['SEEKTABLE'][$SampleNumber]['samples'] = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 2));
				$offset += 2;

			}
		}

		return true;
	}

	private function parseVORBIS_COMMENT($BlockData) {
		$info = &$this->getid3->info;

		$getid3_ogg = new getid3_ogg($this->getid3);
		if ($this->isDependencyFor('matroska')) {
			$getid3_ogg->setStringMode($this->data_string);
		}
		$getid3_ogg->ParseVorbisComments();
		if (isset($info['ogg'])) {
			unset($info['ogg']['comments_raw']);
			$info['flac']['VORBIS_COMMENT'] = $info['ogg'];
			unset($info['ogg']);
		}

		unset($getid3_ogg);

		return true;
	}

	private function parseCUESHEET($BlockData) {
		$info = &$this->getid3->info;
		$offset = 0;
		$info['flac']['CUESHEET']['media_catalog_number'] =                              trim(substr($BlockData, $offset, 128), "\0");
		$offset += 128;
		$info['flac']['CUESHEET']['lead_in_samples']      =         getid3_lib::BigEndian2Int(substr($BlockData, $offset, 8));
		$offset += 8;
		$info['flac']['CUESHEET']['flags']['is_cd']       = (bool) (getid3_lib::BigEndian2Int(substr($BlockData, $offset, 1)) & 0x80);
		$offset += 1;

		$offset += 258; // reserved

		$info['flac']['CUESHEET']['number_tracks']        =         getid3_lib::BigEndian2Int(substr($BlockData, $offset, 1));
		$offset += 1;

		for ($track = 0; $track < $info['flac']['CUESHEET']['number_tracks']; $track++) {
			$TrackSampleOffset = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 8));
			$offset += 8;
			$TrackNumber       = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 1));
			$offset += 1;

			$info['flac']['CUESHEET']['tracks'][$TrackNumber]['sample_offset']         = $TrackSampleOffset;

			$info['flac']['CUESHEET']['tracks'][$TrackNumber]['isrc']                  =                           substr($BlockData, $offset, 12);
			$offset += 12;

			$TrackFlagsRaw                                                             = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 1));
			$offset += 1;
			$info['flac']['CUESHEET']['tracks'][$TrackNumber]['flags']['is_audio']     = (bool) ($TrackFlagsRaw & 0x80);
			$info['flac']['CUESHEET']['tracks'][$TrackNumber]['flags']['pre_emphasis'] = (bool) ($TrackFlagsRaw & 0x40);

			$offset += 13; // reserved

			$info['flac']['CUESHEET']['tracks'][$TrackNumber]['index_points']          = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 1));
			$offset += 1;

			for ($index = 0; $index < $info['flac']['CUESHEET']['tracks'][$TrackNumber]['index_points']; $index++) {
				$IndexSampleOffset = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 8));
				$offset += 8;
				$IndexNumber       = getid3_lib::BigEndian2Int(substr($BlockData, $offset, 1));
				$offset += 1;

				$offset += 3; // reserved

				$info['flac']['CUESHEET']['tracks'][$TrackNumber]['indexes'][$IndexNumber] = $IndexSampleOffset;
			}
		}

		return true;
	}

	/**
	* Parse METADATA_BLOCK_PICTURE flac structure and extract attachment
	* External usage: audio.ogg
	*/
	public function parsePICTURE() {
		$info = &$this->getid3->info;

		$picture['typeid']         = getid3_lib::BigEndian2Int($this->fread(4));
		$picture['picturetype']    = self::pictureTypeLookup($picture['typeid']);
		$picture['image_mime']     = $this->fread(getid3_lib::BigEndian2Int($this->fread(4)));
		$descr_length              = getid3_lib::BigEndian2Int($this->fread(4));
		if ($descr_length) {
			$picture['description'] = $this->fread($descr_length);
		}
		$picture['image_width']    = getid3_lib::BigEndian2Int($this->fread(4));
		$picture['image_height']   = getid3_lib::BigEndian2Int($this->fread(4));
		$picture['color_depth']    = getid3_lib::BigEndian2Int($this->fread(4));
		$picture['colors_indexed'] = getid3_lib::BigEndian2Int($this->fread(4));
		$picture['datalength']     = getid3_lib::BigEndian2Int($this->fread(4));

		if ($picture['image_mime'] == '-->') {
			$picture['data'] = $this->fread($picture['datalength']);
		} else {
			$picture['data'] = $this->saveAttachment(
				str_replace('/', '_', $picture['picturetype']).'_'.$this->ftell(),
				$this->ftell(),
				$picture['datalength'],
				$picture['image_mime']);
		}

		$info['flac']['PICTURE'][] = $picture;

		return true;
	}

	public static function metaBlockTypeLookup($blocktype) {
		static $lookup = array(
			0 => 'STREAMINFO',
			1 => 'PADDING',
			2 => 'APPLICATION',
			3 => 'SEEKTABLE',
			4 => 'VORBIS_COMMENT',
			5 => 'CUESHEET',
			6 => 'PICTURE',
		);
		return (isset($lookup[$blocktype]) ? $lookup[$blocktype] : 'reserved');
	}

	public static function applicationIDLookup($applicationid) {
		// http://flac.sourceforge.net/id.html
		static $lookup = array(
			0x41544348 => 'FlacFile',                                                                           // "ATCH"
			0x42534F4C => 'beSolo',                                                                             // "BSOL"
			0x42554753 => 'Bugs Player',                                                                        // "BUGS"
			0x43756573 => 'GoldWave cue points (specification)',                                                // "Cues"
			0x46696361 => 'CUE Splitter',                                                                       // "Fica"
			0x46746F6C => 'flac-tools',                                                                         // "Ftol"
			0x4D4F5442 => 'MOTB MetaCzar',                                                                      // "MOTB"
			0x4D505345 => 'MP3 Stream Editor',                                                                  // "MPSE"
			0x4D754D4C => 'MusicML: Music Metadata Language',                                                   // "MuML"
			0x52494646 => 'Sound Devices RIFF chunk storage',                                                   // "RIFF"
			0x5346464C => 'Sound Font FLAC',                                                                    // "SFFL"
			0x534F4E59 => 'Sony Creative Software',                                                             // "SONY"
			0x5351455A => 'flacsqueeze',                                                                        // "SQEZ"
			0x54745776 => 'TwistedWave',                                                                        // "TtWv"
			0x55495453 => 'UITS Embedding tools',                                                               // "UITS"
			0x61696666 => 'FLAC AIFF chunk storage',                                                            // "aiff"
			0x696D6167 => 'flac-image application for storing arbitrary files in APPLICATION metadata blocks',  // "imag"
			0x7065656D => 'Parseable Embedded Extensible Metadata (specification)',                             // "peem"
			0x71667374 => 'QFLAC Studio',                                                                       // "qfst"
			0x72696666 => 'FLAC RIFF chunk storage',                                                            // "riff"
			0x74756E65 => 'TagTuner',                                                                           // "tune"
			0x78626174 => 'XBAT',                                                                               // "xbat"
			0x786D6364 => 'xmcd',                                                                               // "xmcd"
		);
		return (isset($lookup[$applicationid]) ? $lookup[$applicationid] : 'reserved');
	}

	public static function pictureTypeLookup($type_id) {
		static $lookup = array (
			 0 => 'Other',
			 1 => '32x32 pixels \'file icon\' (PNG only)',
			 2 => 'Other file icon',
			 3 => 'Cover (front)',
			 4 => 'Cover (back)',
			 5 => 'Leaflet page',
			 6 => 'Media (e.g. label side of CD)',
			 7 => 'Lead artist/lead performer/soloist',
			 8 => 'Artist/performer',
			 9 => 'Conductor',
			10 => 'Band/Orchestra',
			11 => 'Composer',
			12 => 'Lyricist/text writer',
			13 => 'Recording Location',
			14 => 'During recording',
			15 => 'During performance',
			16 => 'Movie/video screen capture',
			17 => 'A bright coloured fish',
			18 => 'Illustration',
			19 => 'Band/artist logotype',
			20 => 'Publisher/Studio logotype',
		);
		return (isset($lookup[$type_id]) ? $lookup[$type_id] : 'reserved');
	}

}
