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
// module.audio.aac.php                                        //
// module for analyzing AAC Audio files                        //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_aac extends getid3_handler
{
	public function Analyze() {
		$info = &$this->getid3->info;
		$this->fseek($info['avdataoffset']);
		if ($this->fread(4) == 'ADIF') {
			$this->getAACADIFheaderFilepointer();
		} else {
			$this->getAACADTSheaderFilepointer();
		}
		return true;
	}



	public function getAACADIFheaderFilepointer() {
		$info = &$this->getid3->info;
		$info['fileformat']          = 'aac';
		$info['audio']['dataformat'] = 'aac';
		$info['audio']['lossless']   = false;

		$this->fseek($info['avdataoffset']);
		$AACheader = $this->fread(1024);
		$offset    = 0;

		if (substr($AACheader, 0, 4) == 'ADIF') {

			// http://faac.sourceforge.net/wiki/index.php?page=ADIF

			// http://libmpeg.org/mpeg4/doc/w2203tfs.pdf
			// adif_header() {
			//     adif_id                                32
			//     copyright_id_present                    1
			//     if( copyright_id_present )
			//         copyright_id                       72
			//     original_copy                           1
			//     home                                    1
			//     bitstream_type                          1
			//     bitrate                                23
			//     num_program_config_elements             4
			//     for (i = 0; i < num_program_config_elements + 1; i++ ) {
			//         if( bitstream_type == '0' )
			//             adif_buffer_fullness           20
			//         program_config_element()
			//     }
			// }

			$AACheaderBitstream = getid3_lib::BigEndian2Bin($AACheader);
			$bitoffset          = 0;

			$info['aac']['header_type']                   = 'ADIF';
			$bitoffset += 32;
			$info['aac']['header']['mpeg_version']        = 4;

			$info['aac']['header']['copyright']           = (bool) (substr($AACheaderBitstream, $bitoffset, 1) == '1');
			$bitoffset += 1;
			if ($info['aac']['header']['copyright']) {
				$info['aac']['header']['copyright_id']    = getid3_lib::Bin2String(substr($AACheaderBitstream, $bitoffset, 72));
				$bitoffset += 72;
			}
			$info['aac']['header']['original_copy']       = (bool) (substr($AACheaderBitstream, $bitoffset, 1) == '1');
			$bitoffset += 1;
			$info['aac']['header']['home']                = (bool) (substr($AACheaderBitstream, $bitoffset, 1) == '1');
			$bitoffset += 1;
			$info['aac']['header']['is_vbr']              = (bool) (substr($AACheaderBitstream, $bitoffset, 1) == '1');
			$bitoffset += 1;
			if ($info['aac']['header']['is_vbr']) {
				$info['audio']['bitrate_mode']            = 'vbr';
				$info['aac']['header']['bitrate_max']     = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 23));
				$bitoffset += 23;
			} else {
				$info['audio']['bitrate_mode']            = 'cbr';
				$info['aac']['header']['bitrate']         = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 23));
				$bitoffset += 23;
				$info['audio']['bitrate']                 = $info['aac']['header']['bitrate'];
			}
			if ($info['audio']['bitrate'] == 0) {
				$info['error'][] = 'Corrupt AAC file: bitrate_audio == zero';
				return false;
			}
			$info['aac']['header']['num_program_configs'] = 1 + getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
			$bitoffset += 4;

			for ($i = 0; $i < $info['aac']['header']['num_program_configs']; $i++) {
				// http://www.audiocoding.com/wiki/index.php?page=program_config_element

				// buffer_fullness                       20

				// element_instance_tag                   4
				// object_type                            2
				// sampling_frequency_index               4
				// num_front_channel_elements             4
				// num_side_channel_elements              4
				// num_back_channel_elements              4
				// num_lfe_channel_elements               2
				// num_assoc_data_elements                3
				// num_valid_cc_elements                  4
				// mono_mixdown_present                   1
				// mono_mixdown_element_number            4   if mono_mixdown_present == 1
				// stereo_mixdown_present                 1
				// stereo_mixdown_element_number          4   if stereo_mixdown_present == 1
				// matrix_mixdown_idx_present             1
				// matrix_mixdown_idx                     2   if matrix_mixdown_idx_present == 1
				// pseudo_surround_enable                 1   if matrix_mixdown_idx_present == 1
				// for (i = 0; i < num_front_channel_elements; i++) {
				//     front_element_is_cpe[i]            1
				//     front_element_tag_select[i]        4
				// }
				// for (i = 0; i < num_side_channel_elements; i++) {
				//     side_element_is_cpe[i]             1
				//     side_element_tag_select[i]         4
				// }
				// for (i = 0; i < num_back_channel_elements; i++) {
				//     back_element_is_cpe[i]             1
				//     back_element_tag_select[i]         4
				// }
				// for (i = 0; i < num_lfe_channel_elements; i++) {
				//     lfe_element_tag_select[i]          4
				// }
				// for (i = 0; i < num_assoc_data_elements; i++) {
				//     assoc_data_element_tag_select[i]   4
				// }
				// for (i = 0; i < num_valid_cc_elements; i++) {
				//     cc_element_is_ind_sw[i]            1
				//     valid_cc_element_tag_select[i]     4
				// }
				// byte_alignment()                       VAR
				// comment_field_bytes                    8
				// for (i = 0; i < comment_field_bytes; i++) {
				//     comment_field_data[i]              8
				// }

				if (!$info['aac']['header']['is_vbr']) {
					$info['aac']['program_configs'][$i]['buffer_fullness']        = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 20));
					$bitoffset += 20;
				}
				$info['aac']['program_configs'][$i]['element_instance_tag']       = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
				$bitoffset += 4;
				$info['aac']['program_configs'][$i]['object_type']                = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 2));
				$bitoffset += 2;
				$info['aac']['program_configs'][$i]['sampling_frequency_index']   = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
				$bitoffset += 4;
				$info['aac']['program_configs'][$i]['num_front_channel_elements'] = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
				$bitoffset += 4;
				$info['aac']['program_configs'][$i]['num_side_channel_elements']  = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
				$bitoffset += 4;
				$info['aac']['program_configs'][$i]['num_back_channel_elements']  = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
				$bitoffset += 4;
				$info['aac']['program_configs'][$i]['num_lfe_channel_elements']   = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 2));
				$bitoffset += 2;
				$info['aac']['program_configs'][$i]['num_assoc_data_elements']    = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 3));
				$bitoffset += 3;
				$info['aac']['program_configs'][$i]['num_valid_cc_elements']      = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
				$bitoffset += 4;
				$info['aac']['program_configs'][$i]['mono_mixdown_present']       = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
				$bitoffset += 1;
				if ($info['aac']['program_configs'][$i]['mono_mixdown_present']) {
					$info['aac']['program_configs'][$i]['mono_mixdown_element_number']    = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}
				$info['aac']['program_configs'][$i]['stereo_mixdown_present']             = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
				$bitoffset += 1;
				if ($info['aac']['program_configs'][$i]['stereo_mixdown_present']) {
					$info['aac']['program_configs'][$i]['stereo_mixdown_element_number']  = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}
				$info['aac']['program_configs'][$i]['matrix_mixdown_idx_present']         = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
				$bitoffset += 1;
				if ($info['aac']['program_configs'][$i]['matrix_mixdown_idx_present']) {
					$info['aac']['program_configs'][$i]['matrix_mixdown_idx']             = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 2));
					$bitoffset += 2;
					$info['aac']['program_configs'][$i]['pseudo_surround_enable']         = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
					$bitoffset += 1;
				}
				for ($j = 0; $j < $info['aac']['program_configs'][$i]['num_front_channel_elements']; $j++) {
					$info['aac']['program_configs'][$i]['front_element_is_cpe'][$j]     = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
					$bitoffset += 1;
					$info['aac']['program_configs'][$i]['front_element_tag_select'][$j] = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}
				for ($j = 0; $j < $info['aac']['program_configs'][$i]['num_side_channel_elements']; $j++) {
					$info['aac']['program_configs'][$i]['side_element_is_cpe'][$j]     = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
					$bitoffset += 1;
					$info['aac']['program_configs'][$i]['side_element_tag_select'][$j] = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}
				for ($j = 0; $j < $info['aac']['program_configs'][$i]['num_back_channel_elements']; $j++) {
					$info['aac']['program_configs'][$i]['back_element_is_cpe'][$j]     = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
					$bitoffset += 1;
					$info['aac']['program_configs'][$i]['back_element_tag_select'][$j] = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}
				for ($j = 0; $j < $info['aac']['program_configs'][$i]['num_lfe_channel_elements']; $j++) {
					$info['aac']['program_configs'][$i]['lfe_element_tag_select'][$j] = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}
				for ($j = 0; $j < $info['aac']['program_configs'][$i]['num_assoc_data_elements']; $j++) {
					$info['aac']['program_configs'][$i]['assoc_data_element_tag_select'][$j] = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}
				for ($j = 0; $j < $info['aac']['program_configs'][$i]['num_valid_cc_elements']; $j++) {
					$info['aac']['program_configs'][$i]['cc_element_is_ind_sw'][$j]          = (bool) getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 1));
					$bitoffset += 1;
					$info['aac']['program_configs'][$i]['valid_cc_element_tag_select'][$j]   = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 4));
					$bitoffset += 4;
				}

				$bitoffset = ceil($bitoffset / 8) * 8;

				$info['aac']['program_configs'][$i]['comment_field_bytes'] = getid3_lib::Bin2Dec(substr($AACheaderBitstream, $bitoffset, 8));
				$bitoffset += 8;
				$info['aac']['program_configs'][$i]['comment_field']       = getid3_lib::Bin2String(substr($AACheaderBitstream, $bitoffset, 8 * $info['aac']['program_configs'][$i]['comment_field_bytes']));
				$bitoffset += 8 * $info['aac']['program_configs'][$i]['comment_field_bytes'];


				$info['aac']['header']['profile']                           = self::AACprofileLookup($info['aac']['program_configs'][$i]['object_type'], $info['aac']['header']['mpeg_version']);
				$info['aac']['program_configs'][$i]['sampling_frequency']   = self::AACsampleRateLookup($info['aac']['program_configs'][$i]['sampling_frequency_index']);
				$info['audio']['sample_rate']                               = $info['aac']['program_configs'][$i]['sampling_frequency'];
				$info['audio']['channels']                                  = self::AACchannelCountCalculate($info['aac']['program_configs'][$i]);
				if ($info['aac']['program_configs'][$i]['comment_field']) {
					$info['aac']['comments'][]                          = $info['aac']['program_configs'][$i]['comment_field'];
				}
			}
			$info['playtime_seconds'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['audio']['bitrate'];

			$info['audio']['encoder_options'] = $info['aac']['header_type'].' '.$info['aac']['header']['profile'];



			return true;

		} else {

			unset($info['fileformat']);
			unset($info['aac']);
			$info['error'][] = 'AAC-ADIF synch not found at offset '.$info['avdataoffset'].' (expected "ADIF", found "'.substr($AACheader, 0, 4).'" instead)';
			return false;

		}

	}


	public function getAACADTSheaderFilepointer($MaxFramesToScan=1000000, $ReturnExtendedInfo=false) {
		$info = &$this->getid3->info;

		// based loosely on code from AACfile by Jurgen Faul  <jfaulØgmx.de>
		// http://jfaul.de/atl  or  http://j-faul.virtualave.net/atl/atl.html


		// http://faac.sourceforge.net/wiki/index.php?page=ADTS // dead link
		// http://wiki.multimedia.cx/index.php?title=ADTS

		// * ADTS Fixed Header: these don't change from frame to frame
		// syncword                                       12    always: '111111111111'
		// ID                                              1    0: MPEG-4, 1: MPEG-2
		// MPEG layer                                      2    If you send AAC in MPEG-TS, set to 0
		// protection_absent                               1    0: CRC present; 1: no CRC
		// profile                                         2    0: AAC Main; 1: AAC LC (Low Complexity); 2: AAC SSR (Scalable Sample Rate); 3: AAC LTP (Long Term Prediction)
		// sampling_frequency_index                        4    15 not allowed
		// private_bit                                     1    usually 0
		// channel_configuration                           3
		// original/copy                                   1    0: original; 1: copy
		// home                                            1    usually 0
		// emphasis                                        2    only if ID == 0 (ie MPEG-4)  // not present in some documentation?

		// * ADTS Variable Header: these can change from frame to frame
		// copyright_identification_bit                    1
		// copyright_identification_start                  1
		// aac_frame_length                               13    length of the frame including header (in bytes)
		// adts_buffer_fullness                           11    0x7FF indicates VBR
		// no_raw_data_blocks_in_frame                     2

		// * ADTS Error check
		// crc_check                                      16    only if protection_absent == 0

		$byteoffset  = $info['avdataoffset'];
		$framenumber = 0;

		// Init bit pattern array
		static $decbin = array();

		// Populate $bindec
		for ($i = 0; $i < 256; $i++) {
			$decbin[chr($i)] = str_pad(decbin($i), 8, '0', STR_PAD_LEFT);
		}

		// used to calculate bitrate below
		$BitrateCache = array();


		while (true) {
			// breaks out when end-of-file encountered, or invalid data found,
			// or MaxFramesToScan frames have been scanned

			if (!getid3_lib::intValueSupported($byteoffset)) {
				$info['warning'][] = 'Unable to parse AAC file beyond '.$this->ftell().' (PHP does not support file operations beyond '.round(PHP_INT_MAX / 1073741824).'GB)';
				return false;
			}
			$this->fseek($byteoffset);

			// First get substring
			$substring = $this->fread(9); // header is 7 bytes (or 9 if CRC is present)
			$substringlength = strlen($substring);
			if ($substringlength != 9) {
				$info['error'][] = 'Failed to read 7 bytes at offset '.($this->ftell() - $substringlength).' (only read '.$substringlength.' bytes)';
				return false;
			}
			// this would be easier with 64-bit math, but split it up to allow for 32-bit:
			$header1 = getid3_lib::BigEndian2Int(substr($substring, 0, 2));
			$header2 = getid3_lib::BigEndian2Int(substr($substring, 2, 4));
			$header3 = getid3_lib::BigEndian2Int(substr($substring, 6, 1));

			$info['aac']['header']['raw']['syncword']          = ($header1 & 0xFFF0) >> 4;
			if ($info['aac']['header']['raw']['syncword'] != 0x0FFF) {
				$info['error'][] = 'Synch pattern (0x0FFF) not found at offset '.($this->ftell() - $substringlength).' (found 0x0'.strtoupper(dechex($info['aac']['header']['raw']['syncword'])).' instead)';
				//if ($info['fileformat'] == 'aac') {
				//	return true;
				//}
				unset($info['aac']);
				return false;
			}

			// Gather info for first frame only - this takes time to do 1000 times!
			if ($framenumber == 0) {
				$info['aac']['header_type']                      = 'ADTS';
				$info['fileformat']                              = 'aac';
				$info['audio']['dataformat']                     = 'aac';

				$info['aac']['header']['raw']['mpeg_version']      = ($header1 & 0x0008) >> 3;
				$info['aac']['header']['raw']['mpeg_layer']        = ($header1 & 0x0006) >> 1;
				$info['aac']['header']['raw']['protection_absent'] = ($header1 & 0x0001) >> 0;

				$info['aac']['header']['raw']['profile_code']      = ($header2 & 0xC0000000) >> 30;
				$info['aac']['header']['raw']['sample_rate_code']  = ($header2 & 0x3C000000) >> 26;
				$info['aac']['header']['raw']['private_stream']    = ($header2 & 0x02000000) >> 25;
				$info['aac']['header']['raw']['channels_code']     = ($header2 & 0x01C00000) >> 22;
				$info['aac']['header']['raw']['original']          = ($header2 & 0x00200000) >> 21;
				$info['aac']['header']['raw']['home']              = ($header2 & 0x00100000) >> 20;
				$info['aac']['header']['raw']['copyright_stream']  = ($header2 & 0x00080000) >> 19;
				$info['aac']['header']['raw']['copyright_start']   = ($header2 & 0x00040000) >> 18;
				$info['aac']['header']['raw']['frame_length']      = ($header2 & 0x0003FFE0) >>  5;

				$info['aac']['header']['mpeg_version']     = ($info['aac']['header']['raw']['mpeg_version']      ? 2    : 4);
				$info['aac']['header']['crc_present']      = ($info['aac']['header']['raw']['protection_absent'] ? false: true);
				$info['aac']['header']['profile']          = self::AACprofileLookup($info['aac']['header']['raw']['profile_code'], $info['aac']['header']['mpeg_version']);
				$info['aac']['header']['sample_frequency'] = self::AACsampleRateLookup($info['aac']['header']['raw']['sample_rate_code']);
				$info['aac']['header']['private']          = (bool) $info['aac']['header']['raw']['private_stream'];
				$info['aac']['header']['original']         = (bool) $info['aac']['header']['raw']['original'];
				$info['aac']['header']['home']             = (bool) $info['aac']['header']['raw']['home'];
				$info['aac']['header']['channels']         = (($info['aac']['header']['raw']['channels_code'] == 7) ? 8 : $info['aac']['header']['raw']['channels_code']);
				if ($ReturnExtendedInfo) {
					$info['aac'][$framenumber]['copyright_id_bit']   = (bool) $info['aac']['header']['raw']['copyright_stream'];
					$info['aac'][$framenumber]['copyright_id_start'] = (bool) $info['aac']['header']['raw']['copyright_start'];
				}

				if ($info['aac']['header']['raw']['mpeg_layer'] != 0) {
					$info['warning'][] = 'Layer error - expected "0", found "'.$info['aac']['header']['raw']['mpeg_layer'].'" instead';
				}
				if ($info['aac']['header']['sample_frequency'] == 0) {
					$info['error'][] = 'Corrupt AAC file: sample_frequency == zero';
					return false;
				}

				$info['audio']['sample_rate'] = $info['aac']['header']['sample_frequency'];
				$info['audio']['channels']    = $info['aac']['header']['channels'];
			}

			$FrameLength = ($header2 & 0x0003FFE0) >>  5;

			if (!isset($BitrateCache[$FrameLength])) {
				$BitrateCache[$FrameLength] = ($info['aac']['header']['sample_frequency'] / 1024) * $FrameLength * 8;
			}
			getid3_lib::safe_inc($info['aac']['bitrate_distribution'][$BitrateCache[$FrameLength]], 1);

			$info['aac'][$framenumber]['aac_frame_length']     = $FrameLength;

			$info['aac'][$framenumber]['adts_buffer_fullness'] = (($header2 & 0x0000001F) << 6) & (($header3 & 0xFC) >> 2);
			if ($info['aac'][$framenumber]['adts_buffer_fullness'] == 0x07FF) {
				$info['audio']['bitrate_mode'] = 'vbr';
			} else {
				$info['audio']['bitrate_mode'] = 'cbr';
			}
			$info['aac'][$framenumber]['num_raw_data_blocks']  = (($header3 & 0x03) >> 0);

			if ($info['aac']['header']['crc_present']) {
				//$info['aac'][$framenumber]['crc'] = getid3_lib::BigEndian2Int(substr($substring, 7, 2);
			}

			if (!$ReturnExtendedInfo) {
				unset($info['aac'][$framenumber]);
			}

			/*
			$rounded_precision = 5000;
			$info['aac']['bitrate_distribution_rounded'] = array();
			foreach ($info['aac']['bitrate_distribution'] as $bitrate => $count) {
				$rounded_bitrate = round($bitrate / $rounded_precision) * $rounded_precision;
				getid3_lib::safe_inc($info['aac']['bitrate_distribution_rounded'][$rounded_bitrate], $count);
			}
			ksort($info['aac']['bitrate_distribution_rounded']);
			*/

			$byteoffset += $FrameLength;
			if ((++$framenumber < $MaxFramesToScan) && (($byteoffset + 10) < $info['avdataend'])) {

				// keep scanning

			} else {

				$info['aac']['frames']    = $framenumber;
				$info['playtime_seconds'] = ($info['avdataend'] / $byteoffset) * (($framenumber * 1024) / $info['aac']['header']['sample_frequency']);  // (1 / % of file scanned) * (samples / (samples/sec)) = seconds
				if ($info['playtime_seconds'] == 0) {
					$info['error'][] = 'Corrupt AAC file: playtime_seconds == zero';
					return false;
				}
				$info['audio']['bitrate']    = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];
				ksort($info['aac']['bitrate_distribution']);

				$info['audio']['encoder_options'] = $info['aac']['header_type'].' '.$info['aac']['header']['profile'];

				return true;

			}
		}
		// should never get here.
	}

	public static function AACsampleRateLookup($samplerateid) {
		static $AACsampleRateLookup = array();
		if (empty($AACsampleRateLookup)) {
			$AACsampleRateLookup[0]  = 96000;
			$AACsampleRateLookup[1]  = 88200;
			$AACsampleRateLookup[2]  = 64000;
			$AACsampleRateLookup[3]  = 48000;
			$AACsampleRateLookup[4]  = 44100;
			$AACsampleRateLookup[5]  = 32000;
			$AACsampleRateLookup[6]  = 24000;
			$AACsampleRateLookup[7]  = 22050;
			$AACsampleRateLookup[8]  = 16000;
			$AACsampleRateLookup[9]  = 12000;
			$AACsampleRateLookup[10] = 11025;
			$AACsampleRateLookup[11] = 8000;
			$AACsampleRateLookup[12] = 0;
			$AACsampleRateLookup[13] = 0;
			$AACsampleRateLookup[14] = 0;
			$AACsampleRateLookup[15] = 0;
		}
		return (isset($AACsampleRateLookup[$samplerateid]) ? $AACsampleRateLookup[$samplerateid] : 'invalid');
	}

	public static function AACprofileLookup($profileid, $mpegversion) {
		static $AACprofileLookup = array();
		if (empty($AACprofileLookup)) {
			$AACprofileLookup[2][0]  = 'Main profile';
			$AACprofileLookup[2][1]  = 'Low Complexity profile (LC)';
			$AACprofileLookup[2][2]  = 'Scalable Sample Rate profile (SSR)';
			$AACprofileLookup[2][3]  = '(reserved)';
			$AACprofileLookup[4][0]  = 'AAC_MAIN';
			$AACprofileLookup[4][1]  = 'AAC_LC';
			$AACprofileLookup[4][2]  = 'AAC_SSR';
			$AACprofileLookup[4][3]  = 'AAC_LTP';
		}
		return (isset($AACprofileLookup[$mpegversion][$profileid]) ? $AACprofileLookup[$mpegversion][$profileid] : 'invalid');
	}

	public static function AACchannelCountCalculate($program_configs) {
		$channels = 0;
		for ($i = 0; $i < $program_configs['num_front_channel_elements']; $i++) {
			$channels++;
			if ($program_configs['front_element_is_cpe'][$i]) {
				// each front element is channel pair (CPE = Channel Pair Element)
				$channels++;
			}
		}
		for ($i = 0; $i < $program_configs['num_side_channel_elements']; $i++) {
			$channels++;
			if ($program_configs['side_element_is_cpe'][$i]) {
				// each side element is channel pair (CPE = Channel Pair Element)
				$channels++;
			}
		}
		for ($i = 0; $i < $program_configs['num_back_channel_elements']; $i++) {
			$channels++;
			if ($program_configs['back_element_is_cpe'][$i]) {
				// each back element is channel pair (CPE = Channel Pair Element)
				$channels++;
			}
		}
		for ($i = 0; $i < $program_configs['num_lfe_channel_elements']; $i++) {
			$channels++;
		}
		return $channels;
	}

}
