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
// module.audio.vqf.php                                        //
// module for analyzing VQF audio files                        //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_vqf extends getid3_handler
{
	public function Analyze() {
		$info = &$this->getid3->info;

		// based loosely on code from TTwinVQ by Jurgen Faul <jfaulØgmx*de>
		// http://jfaul.de/atl  or  http://j-faul.virtualave.net/atl/atl.html

		$info['fileformat']            = 'vqf';
		$info['audio']['dataformat']   = 'vqf';
		$info['audio']['bitrate_mode'] = 'cbr';
		$info['audio']['lossless']     = false;

		// shortcut
		$info['vqf']['raw'] = array();
		$thisfile_vqf               = &$info['vqf'];
		$thisfile_vqf_raw           = &$thisfile_vqf['raw'];

		$this->fseek($info['avdataoffset']);
		$VQFheaderData = $this->fread(16);

		$offset = 0;
		$thisfile_vqf_raw['header_tag'] = substr($VQFheaderData, $offset, 4);
		$magic = 'TWIN';
		if ($thisfile_vqf_raw['header_tag'] != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($thisfile_vqf_raw['header_tag']).'"';
			unset($info['vqf']);
			unset($info['fileformat']);
			return false;
		}
		$offset += 4;
		$thisfile_vqf_raw['version'] =                           substr($VQFheaderData, $offset, 8);
		$offset += 8;
		$thisfile_vqf_raw['size']    = getid3_lib::BigEndian2Int(substr($VQFheaderData, $offset, 4));
		$offset += 4;

		while ($this->ftell() < $info['avdataend']) {

			$ChunkBaseOffset = $this->ftell();
			$chunkoffset = 0;
			$ChunkData = $this->fread(8);
			$ChunkName = substr($ChunkData, $chunkoffset, 4);
			if ($ChunkName == 'DATA') {
				$info['avdataoffset'] = $ChunkBaseOffset;
				break;
			}
			$chunkoffset += 4;
			$ChunkSize = getid3_lib::BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
			$chunkoffset += 4;
			if ($ChunkSize > ($info['avdataend'] - $this->ftell())) {
				$info['error'][] = 'Invalid chunk size ('.$ChunkSize.') for chunk "'.$ChunkName.'" at offset '.$ChunkBaseOffset;
				break;
			}
			if ($ChunkSize > 0) {
				$ChunkData .= $this->fread($ChunkSize);
			}

			switch ($ChunkName) {
				case 'COMM':
					// shortcut
					$thisfile_vqf['COMM'] = array();
					$thisfile_vqf_COMM    = &$thisfile_vqf['COMM'];

					$thisfile_vqf_COMM['channel_mode']   = getid3_lib::BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
					$chunkoffset += 4;
					$thisfile_vqf_COMM['bitrate']        = getid3_lib::BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
					$chunkoffset += 4;
					$thisfile_vqf_COMM['sample_rate']    = getid3_lib::BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
					$chunkoffset += 4;
					$thisfile_vqf_COMM['security_level'] = getid3_lib::BigEndian2Int(substr($ChunkData, $chunkoffset, 4));
					$chunkoffset += 4;

					$info['audio']['channels']        = $thisfile_vqf_COMM['channel_mode'] + 1;
					$info['audio']['sample_rate']     = $this->VQFchannelFrequencyLookup($thisfile_vqf_COMM['sample_rate']);
					$info['audio']['bitrate']         = $thisfile_vqf_COMM['bitrate'] * 1000;
					$info['audio']['encoder_options'] = 'CBR' . ceil($info['audio']['bitrate']/1000);

					if ($info['audio']['bitrate'] == 0) {
						$info['error'][] = 'Corrupt VQF file: bitrate_audio == zero';
						return false;
					}
					break;

				case 'NAME':
				case 'AUTH':
				case '(c) ':
				case 'FILE':
				case 'COMT':
				case 'ALBM':
					$thisfile_vqf['comments'][$this->VQFcommentNiceNameLookup($ChunkName)][] = trim(substr($ChunkData, 8));
					break;

				case 'DSIZ':
					$thisfile_vqf['DSIZ'] = getid3_lib::BigEndian2Int(substr($ChunkData, 8, 4));
					break;

				default:
					$info['warning'][] = 'Unhandled chunk type "'.$ChunkName.'" at offset '.$ChunkBaseOffset;
					break;
			}
		}

		$info['playtime_seconds'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['audio']['bitrate'];

		if (isset($thisfile_vqf['DSIZ']) && (($thisfile_vqf['DSIZ'] != ($info['avdataend'] - $info['avdataoffset'] - strlen('DATA'))))) {
			switch ($thisfile_vqf['DSIZ']) {
				case 0:
				case 1:
					$info['warning'][] = 'Invalid DSIZ value "'.$thisfile_vqf['DSIZ'].'". This is known to happen with VQF files encoded by Ahead Nero, and seems to be its way of saying this is TwinVQF v'.($thisfile_vqf['DSIZ'] + 1).'.0';
					$info['audio']['encoder'] = 'Ahead Nero';
					break;

				default:
					$info['warning'][] = 'Probable corrupted file - should be '.$thisfile_vqf['DSIZ'].' bytes, actually '.($info['avdataend'] - $info['avdataoffset'] - strlen('DATA'));
					break;
			}
		}

		return true;
	}

	public function VQFchannelFrequencyLookup($frequencyid) {
		static $VQFchannelFrequencyLookup = array(
			11 => 11025,
			22 => 22050,
			44 => 44100
		);
		return (isset($VQFchannelFrequencyLookup[$frequencyid]) ? $VQFchannelFrequencyLookup[$frequencyid] : $frequencyid * 1000);
	}

	public function VQFcommentNiceNameLookup($shortname) {
		static $VQFcommentNiceNameLookup = array(
			'NAME' => 'title',
			'AUTH' => 'artist',
			'(c) ' => 'copyright',
			'FILE' => 'filename',
			'COMT' => 'comment',
			'ALBM' => 'album'
		);
		return (isset($VQFcommentNiceNameLookup[$shortname]) ? $VQFcommentNiceNameLookup[$shortname] : $shortname);
	}

}
