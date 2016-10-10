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
// module.audio.tta.php                                        //
// module for analyzing TTA Audio files                        //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_tta extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat']            = 'tta';
		$info['audio']['dataformat']   = 'tta';
		$info['audio']['lossless']     = true;
		$info['audio']['bitrate_mode'] = 'vbr';

		$this->fseek($info['avdataoffset']);
		$ttaheader = $this->fread(26);

		$info['tta']['magic'] = substr($ttaheader, 0, 3);
		$magic = 'TTA';
		if ($info['tta']['magic'] != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($info['tta']['magic']).'"';
			unset($info['fileformat']);
			unset($info['audio']);
			unset($info['tta']);
			return false;
		}

		switch ($ttaheader{3}) {
			case "\x01": // TTA v1.x
			case "\x02": // TTA v1.x
			case "\x03": // TTA v1.x
				// "It was the demo-version of the TTA encoder. There is no released format with such header. TTA encoder v1 is not supported about a year."
				$info['tta']['major_version'] = 1;
				$info['avdataoffset'] += 16;

				$info['tta']['compression_level']   = ord($ttaheader{3});
				$info['tta']['channels']            = getid3_lib::LittleEndian2Int(substr($ttaheader,  4,  2));
				$info['tta']['bits_per_sample']     = getid3_lib::LittleEndian2Int(substr($ttaheader,  6,  2));
				$info['tta']['sample_rate']         = getid3_lib::LittleEndian2Int(substr($ttaheader,  8,  4));
				$info['tta']['samples_per_channel'] = getid3_lib::LittleEndian2Int(substr($ttaheader, 12,  4));

				$info['audio']['encoder_options']   = '-e'.$info['tta']['compression_level'];
				$info['playtime_seconds']           = $info['tta']['samples_per_channel'] / $info['tta']['sample_rate'];
				break;

			case '2': // TTA v2.x
				// "I have hurried to release the TTA 2.0 encoder. Format documentation is removed from our site. This format still in development. Please wait the TTA2 format, encoder v4."
				$info['tta']['major_version'] = 2;
				$info['avdataoffset'] += 20;

				$info['tta']['compression_level']   = getid3_lib::LittleEndian2Int(substr($ttaheader,  4,  2));
				$info['tta']['audio_format']        = getid3_lib::LittleEndian2Int(substr($ttaheader,  6,  2));
				$info['tta']['channels']            = getid3_lib::LittleEndian2Int(substr($ttaheader,  8,  2));
				$info['tta']['bits_per_sample']     = getid3_lib::LittleEndian2Int(substr($ttaheader, 10,  2));
				$info['tta']['sample_rate']         = getid3_lib::LittleEndian2Int(substr($ttaheader, 12,  4));
				$info['tta']['data_length']         = getid3_lib::LittleEndian2Int(substr($ttaheader, 16,  4));

				$info['audio']['encoder_options']   = '-e'.$info['tta']['compression_level'];
				$info['playtime_seconds']           = $info['tta']['data_length'] / $info['tta']['sample_rate'];
				break;

			case '1': // TTA v3.x
				// "This is a first stable release of the TTA format. It will be supported by the encoders v3 or higher."
				$info['tta']['major_version'] = 3;
				$info['avdataoffset'] += 26;

				$info['tta']['audio_format']        = getid3_lib::LittleEndian2Int(substr($ttaheader,  4,  2)); // getid3_riff::wFormatTagLookup()
				$info['tta']['channels']            = getid3_lib::LittleEndian2Int(substr($ttaheader,  6,  2));
				$info['tta']['bits_per_sample']     = getid3_lib::LittleEndian2Int(substr($ttaheader,  8,  2));
				$info['tta']['sample_rate']         = getid3_lib::LittleEndian2Int(substr($ttaheader, 10,  4));
				$info['tta']['data_length']         = getid3_lib::LittleEndian2Int(substr($ttaheader, 14,  4));
				$info['tta']['crc32_footer']        =                              substr($ttaheader, 18,  4);
				$info['tta']['seek_point']          = getid3_lib::LittleEndian2Int(substr($ttaheader, 22,  4));

				$info['playtime_seconds']           = $info['tta']['data_length'] / $info['tta']['sample_rate'];
				break;

			default:
				$info['error'][] = 'This version of getID3() ['.$this->getid3->version().'] only knows how to handle TTA v1 and v2 - it may not work correctly with this file which appears to be TTA v'.$ttaheader{3};
				return false;
				break;
		}

		$info['audio']['encoder']         = 'TTA v'.$info['tta']['major_version'];
		$info['audio']['bits_per_sample'] = $info['tta']['bits_per_sample'];
		$info['audio']['sample_rate']     = $info['tta']['sample_rate'];
		$info['audio']['channels']        = $info['tta']['channels'];
		$info['audio']['bitrate']         = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];

		return true;
	}

}
