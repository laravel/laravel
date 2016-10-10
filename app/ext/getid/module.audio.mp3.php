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
// module.audio.mp3.php                                        //
// module for analyzing MP3 files                              //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


// number of frames to scan to determine if MPEG-audio sequence is valid
// Lower this number to 5-20 for faster scanning
// Increase this number to 50+ for most accurate detection of valid VBR/CBR
// mpeg-audio streams
define('GETID3_MP3_VALID_CHECK_FRAMES', 35);


class getid3_mp3 extends getid3_handler
{

	public $allow_bruteforce = false; // forces getID3() to scan the file byte-by-byte and log all the valid audio frame headers - extremely slow, unrecommended, but may provide data from otherwise-unusuable files

	public function Analyze() {
		$info = &$this->getid3->info;

		$initialOffset = $info['avdataoffset'];

		if (!$this->getOnlyMPEGaudioInfo($info['avdataoffset'])) {
			if ($this->allow_bruteforce) {
				$info['error'][] = 'Rescanning file in BruteForce mode';
				$this->getOnlyMPEGaudioInfoBruteForce($this->getid3->fp, $info);
			}
		}


		if (isset($info['mpeg']['audio']['bitrate_mode'])) {
			$info['audio']['bitrate_mode'] = strtolower($info['mpeg']['audio']['bitrate_mode']);
		}

		if (((isset($info['id3v2']['headerlength']) && ($info['avdataoffset'] > $info['id3v2']['headerlength'])) || (!isset($info['id3v2']) && ($info['avdataoffset'] > 0) && ($info['avdataoffset'] != $initialOffset)))) {

			$synchoffsetwarning = 'Unknown data before synch ';
			if (isset($info['id3v2']['headerlength'])) {
				$synchoffsetwarning .= '(ID3v2 header ends at '.$info['id3v2']['headerlength'].', then '.($info['avdataoffset'] - $info['id3v2']['headerlength']).' bytes garbage, ';
			} elseif ($initialOffset > 0) {
				$synchoffsetwarning .= '(should be at '.$initialOffset.', ';
			} else {
				$synchoffsetwarning .= '(should be at beginning of file, ';
			}
			$synchoffsetwarning .= 'synch detected at '.$info['avdataoffset'].')';
			if (isset($info['audio']['bitrate_mode']) && ($info['audio']['bitrate_mode'] == 'cbr')) {

				if (!empty($info['id3v2']['headerlength']) && (($info['avdataoffset'] - $info['id3v2']['headerlength']) == $info['mpeg']['audio']['framelength'])) {

					$synchoffsetwarning .= '. This is a known problem with some versions of LAME (3.90-3.92) DLL in CBR mode.';
					$info['audio']['codec'] = 'LAME';
					$CurrentDataLAMEversionString = 'LAME3.';

				} elseif (empty($info['id3v2']['headerlength']) && ($info['avdataoffset'] == $info['mpeg']['audio']['framelength'])) {

					$synchoffsetwarning .= '. This is a known problem with some versions of LAME (3.90 - 3.92) DLL in CBR mode.';
					$info['audio']['codec'] = 'LAME';
					$CurrentDataLAMEversionString = 'LAME3.';

				}

			}
			$info['warning'][] = $synchoffsetwarning;

		}

		if (isset($info['mpeg']['audio']['LAME'])) {
			$info['audio']['codec'] = 'LAME';
			if (!empty($info['mpeg']['audio']['LAME']['long_version'])) {
				$info['audio']['encoder'] = rtrim($info['mpeg']['audio']['LAME']['long_version'], "\x00");
			} elseif (!empty($info['mpeg']['audio']['LAME']['short_version'])) {
				$info['audio']['encoder'] = rtrim($info['mpeg']['audio']['LAME']['short_version'], "\x00");
			}
		}

		$CurrentDataLAMEversionString = (!empty($CurrentDataLAMEversionString) ? $CurrentDataLAMEversionString : (isset($info['audio']['encoder']) ? $info['audio']['encoder'] : ''));
		if (!empty($CurrentDataLAMEversionString) && (substr($CurrentDataLAMEversionString, 0, 6) == 'LAME3.') && !preg_match('[0-9\)]', substr($CurrentDataLAMEversionString, -1))) {
			// a version number of LAME that does not end with a number like "LAME3.92"
			// or with a closing parenthesis like "LAME3.88 (alpha)"
			// or a version of LAME with the LAMEtag-not-filled-in-DLL-mode bug (3.90-3.92)

			// not sure what the actual last frame length will be, but will be less than or equal to 1441
			$PossiblyLongerLAMEversion_FrameLength = 1441;

			// Not sure what version of LAME this is - look in padding of last frame for longer version string
			$PossibleLAMEversionStringOffset = $info['avdataend'] - $PossiblyLongerLAMEversion_FrameLength;
			$this->fseek($PossibleLAMEversionStringOffset);
			$PossiblyLongerLAMEversion_Data = $this->fread($PossiblyLongerLAMEversion_FrameLength);
			switch (substr($CurrentDataLAMEversionString, -1)) {
				case 'a':
				case 'b':
					// "LAME3.94a" will have a longer version string of "LAME3.94 (alpha)" for example
					// need to trim off "a" to match longer string
					$CurrentDataLAMEversionString = substr($CurrentDataLAMEversionString, 0, -1);
					break;
			}
			if (($PossiblyLongerLAMEversion_String = strstr($PossiblyLongerLAMEversion_Data, $CurrentDataLAMEversionString)) !== false) {
				if (substr($PossiblyLongerLAMEversion_String, 0, strlen($CurrentDataLAMEversionString)) == $CurrentDataLAMEversionString) {
					$PossiblyLongerLAMEversion_NewString = substr($PossiblyLongerLAMEversion_String, 0, strspn($PossiblyLongerLAMEversion_String, 'LAME0123456789., (abcdefghijklmnopqrstuvwxyzJFSOND)')); //"LAME3.90.3"  "LAME3.87 (beta 1, Sep 27 2000)" "LAME3.88 (beta)"
					if (empty($info['audio']['encoder']) || (strlen($PossiblyLongerLAMEversion_NewString) > strlen($info['audio']['encoder']))) {
						$info['audio']['encoder'] = $PossiblyLongerLAMEversion_NewString;
					}
				}
			}
		}
		if (!empty($info['audio']['encoder'])) {
			$info['audio']['encoder'] = rtrim($info['audio']['encoder'], "\x00 ");
		}

		switch (isset($info['mpeg']['audio']['layer']) ? $info['mpeg']['audio']['layer'] : '') {
			case 1:
			case 2:
				$info['audio']['dataformat'] = 'mp'.$info['mpeg']['audio']['layer'];
				break;
		}
		if (isset($info['fileformat']) && ($info['fileformat'] == 'mp3')) {
			switch ($info['audio']['dataformat']) {
				case 'mp1':
				case 'mp2':
				case 'mp3':
					$info['fileformat'] = $info['audio']['dataformat'];
					break;

				default:
					$info['warning'][] = 'Expecting [audio][dataformat] to be mp1/mp2/mp3 when fileformat == mp3, [audio][dataformat] actually "'.$info['audio']['dataformat'].'"';
					break;
			}
		}

		if (empty($info['fileformat'])) {
			unset($info['fileformat']);
			unset($info['audio']['bitrate_mode']);
			unset($info['avdataoffset']);
			unset($info['avdataend']);
			return false;
		}

		$info['mime_type']         = 'audio/mpeg';
		$info['audio']['lossless'] = false;

		// Calculate playtime
		if (!isset($info['playtime_seconds']) && isset($info['audio']['bitrate']) && ($info['audio']['bitrate'] > 0)) {
			$info['playtime_seconds'] = ($info['avdataend'] - $info['avdataoffset']) * 8 / $info['audio']['bitrate'];
		}

		$info['audio']['encoder_options'] = $this->GuessEncoderOptions();

		return true;
	}


	public function GuessEncoderOptions() {
		// shortcuts
		$info = &$this->getid3->info;
		if (!empty($info['mpeg']['audio'])) {
			$thisfile_mpeg_audio = &$info['mpeg']['audio'];
			if (!empty($thisfile_mpeg_audio['LAME'])) {
				$thisfile_mpeg_audio_lame = &$thisfile_mpeg_audio['LAME'];
			}
		}

		$encoder_options = '';
		static $NamedPresetBitrates = array(16, 24, 40, 56, 112, 128, 160, 192, 256);

		if (isset($thisfile_mpeg_audio['VBR_method']) && ($thisfile_mpeg_audio['VBR_method'] == 'Fraunhofer') && !empty($thisfile_mpeg_audio['VBR_quality'])) {

			$encoder_options = 'VBR q'.$thisfile_mpeg_audio['VBR_quality'];

		} elseif (!empty($thisfile_mpeg_audio_lame['preset_used']) && (!in_array($thisfile_mpeg_audio_lame['preset_used_id'], $NamedPresetBitrates))) {

			$encoder_options = $thisfile_mpeg_audio_lame['preset_used'];

		} elseif (!empty($thisfile_mpeg_audio_lame['vbr_quality'])) {

			static $KnownEncoderValues = array();
			if (empty($KnownEncoderValues)) {

				//$KnownEncoderValues[abrbitrate_minbitrate][vbr_quality][raw_vbr_method][raw_noise_shaping][raw_stereo_mode][ath_type][lowpass_frequency] = 'preset name';
				$KnownEncoderValues[0xFF][58][1][1][3][2][20500] = '--alt-preset insane';        // 3.90,   3.90.1, 3.92
				$KnownEncoderValues[0xFF][58][1][1][3][2][20600] = '--alt-preset insane';        // 3.90.2, 3.90.3, 3.91
				$KnownEncoderValues[0xFF][57][1][1][3][4][20500] = '--alt-preset insane';        // 3.94,   3.95
				$KnownEncoderValues['**'][78][3][2][3][2][19500] = '--alt-preset extreme';       // 3.90,   3.90.1, 3.92
				$KnownEncoderValues['**'][78][3][2][3][2][19600] = '--alt-preset extreme';       // 3.90.2, 3.91
				$KnownEncoderValues['**'][78][3][1][3][2][19600] = '--alt-preset extreme';       // 3.90.3
				$KnownEncoderValues['**'][78][4][2][3][2][19500] = '--alt-preset fast extreme';  // 3.90,   3.90.1, 3.92
				$KnownEncoderValues['**'][78][4][2][3][2][19600] = '--alt-preset fast extreme';  // 3.90.2, 3.90.3, 3.91
				$KnownEncoderValues['**'][78][3][2][3][4][19000] = '--alt-preset standard';      // 3.90,   3.90.1, 3.90.2, 3.91, 3.92
				$KnownEncoderValues['**'][78][3][1][3][4][19000] = '--alt-preset standard';      // 3.90.3
				$KnownEncoderValues['**'][78][4][2][3][4][19000] = '--alt-preset fast standard'; // 3.90,   3.90.1, 3.90.2, 3.91, 3.92
				$KnownEncoderValues['**'][78][4][1][3][4][19000] = '--alt-preset fast standard'; // 3.90.3
				$KnownEncoderValues['**'][88][4][1][3][3][19500] = '--r3mix';                    // 3.90,   3.90.1, 3.92
				$KnownEncoderValues['**'][88][4][1][3][3][19600] = '--r3mix';                    // 3.90.2, 3.90.3, 3.91
				$KnownEncoderValues['**'][67][4][1][3][4][18000] = '--r3mix';                    // 3.94,   3.95
				$KnownEncoderValues['**'][68][3][2][3][4][18000] = '--alt-preset medium';        // 3.90.3
				$KnownEncoderValues['**'][68][4][2][3][4][18000] = '--alt-preset fast medium';   // 3.90.3

				$KnownEncoderValues[0xFF][99][1][1][1][2][0]     = '--preset studio';            // 3.90,   3.90.1, 3.90.2, 3.91, 3.92
				$KnownEncoderValues[0xFF][58][2][1][3][2][20600] = '--preset studio';            // 3.90.3, 3.93.1
				$KnownEncoderValues[0xFF][58][2][1][3][2][20500] = '--preset studio';            // 3.93
				$KnownEncoderValues[0xFF][57][2][1][3][4][20500] = '--preset studio';            // 3.94,   3.95
				$KnownEncoderValues[0xC0][88][1][1][1][2][0]     = '--preset cd';                // 3.90,   3.90.1, 3.90.2,   3.91, 3.92
				$KnownEncoderValues[0xC0][58][2][2][3][2][19600] = '--preset cd';                // 3.90.3, 3.93.1
				$KnownEncoderValues[0xC0][58][2][2][3][2][19500] = '--preset cd';                // 3.93
				$KnownEncoderValues[0xC0][57][2][1][3][4][19500] = '--preset cd';                // 3.94,   3.95
				$KnownEncoderValues[0xA0][78][1][1][3][2][18000] = '--preset hifi';              // 3.90,   3.90.1, 3.90.2,   3.91, 3.92
				$KnownEncoderValues[0xA0][58][2][2][3][2][18000] = '--preset hifi';              // 3.90.3, 3.93,   3.93.1
				$KnownEncoderValues[0xA0][57][2][1][3][4][18000] = '--preset hifi';              // 3.94,   3.95
				$KnownEncoderValues[0x80][67][1][1][3][2][18000] = '--preset tape';              // 3.90,   3.90.1, 3.90.2,   3.91, 3.92
				$KnownEncoderValues[0x80][67][1][1][3][2][15000] = '--preset radio';             // 3.90,   3.90.1, 3.90.2,   3.91, 3.92
				$KnownEncoderValues[0x70][67][1][1][3][2][15000] = '--preset fm';                // 3.90,   3.90.1, 3.90.2,   3.91, 3.92
				$KnownEncoderValues[0x70][58][2][2][3][2][16000] = '--preset tape/radio/fm';     // 3.90.3, 3.93,   3.93.1
				$KnownEncoderValues[0x70][57][2][1][3][4][16000] = '--preset tape/radio/fm';     // 3.94,   3.95
				$KnownEncoderValues[0x38][58][2][2][0][2][10000] = '--preset voice';             // 3.90.3, 3.93,   3.93.1
				$KnownEncoderValues[0x38][57][2][1][0][4][15000] = '--preset voice';             // 3.94,   3.95
				$KnownEncoderValues[0x38][57][2][1][0][4][16000] = '--preset voice';             // 3.94a14
				$KnownEncoderValues[0x28][65][1][1][0][2][7500]  = '--preset mw-us';             // 3.90,   3.90.1, 3.92
				$KnownEncoderValues[0x28][65][1][1][0][2][7600]  = '--preset mw-us';             // 3.90.2, 3.91
				$KnownEncoderValues[0x28][58][2][2][0][2][7000]  = '--preset mw-us';             // 3.90.3, 3.93,   3.93.1
				$KnownEncoderValues[0x28][57][2][1][0][4][10500] = '--preset mw-us';             // 3.94,   3.95
				$KnownEncoderValues[0x28][57][2][1][0][4][11200] = '--preset mw-us';             // 3.94a14
				$KnownEncoderValues[0x28][57][2][1][0][4][8800]  = '--preset mw-us';             // 3.94a15
				$KnownEncoderValues[0x18][58][2][2][0][2][4000]  = '--preset phon+/lw/mw-eu/sw'; // 3.90.3, 3.93.1
				$KnownEncoderValues[0x18][58][2][2][0][2][3900]  = '--preset phon+/lw/mw-eu/sw'; // 3.93
				$KnownEncoderValues[0x18][57][2][1][0][4][5900]  = '--preset phon+/lw/mw-eu/sw'; // 3.94,   3.95
				$KnownEncoderValues[0x18][57][2][1][0][4][6200]  = '--preset phon+/lw/mw-eu/sw'; // 3.94a14
				$KnownEncoderValues[0x18][57][2][1][0][4][3200]  = '--preset phon+/lw/mw-eu/sw'; // 3.94a15
				$KnownEncoderValues[0x10][58][2][2][0][2][3800]  = '--preset phone';             // 3.90.3, 3.93.1
				$KnownEncoderValues[0x10][58][2][2][0][2][3700]  = '--preset phone';             // 3.93
				$KnownEncoderValues[0x10][57][2][1][0][4][5600]  = '--preset phone';             // 3.94,   3.95
			}

			if (isset($KnownEncoderValues[$thisfile_mpeg_audio_lame['raw']['abrbitrate_minbitrate']][$thisfile_mpeg_audio_lame['vbr_quality']][$thisfile_mpeg_audio_lame['raw']['vbr_method']][$thisfile_mpeg_audio_lame['raw']['noise_shaping']][$thisfile_mpeg_audio_lame['raw']['stereo_mode']][$thisfile_mpeg_audio_lame['ath_type']][$thisfile_mpeg_audio_lame['lowpass_frequency']])) {

				$encoder_options = $KnownEncoderValues[$thisfile_mpeg_audio_lame['raw']['abrbitrate_minbitrate']][$thisfile_mpeg_audio_lame['vbr_quality']][$thisfile_mpeg_audio_lame['raw']['vbr_method']][$thisfile_mpeg_audio_lame['raw']['noise_shaping']][$thisfile_mpeg_audio_lame['raw']['stereo_mode']][$thisfile_mpeg_audio_lame['ath_type']][$thisfile_mpeg_audio_lame['lowpass_frequency']];

			} elseif (isset($KnownEncoderValues['**'][$thisfile_mpeg_audio_lame['vbr_quality']][$thisfile_mpeg_audio_lame['raw']['vbr_method']][$thisfile_mpeg_audio_lame['raw']['noise_shaping']][$thisfile_mpeg_audio_lame['raw']['stereo_mode']][$thisfile_mpeg_audio_lame['ath_type']][$thisfile_mpeg_audio_lame['lowpass_frequency']])) {

				$encoder_options = $KnownEncoderValues['**'][$thisfile_mpeg_audio_lame['vbr_quality']][$thisfile_mpeg_audio_lame['raw']['vbr_method']][$thisfile_mpeg_audio_lame['raw']['noise_shaping']][$thisfile_mpeg_audio_lame['raw']['stereo_mode']][$thisfile_mpeg_audio_lame['ath_type']][$thisfile_mpeg_audio_lame['lowpass_frequency']];

			} elseif ($info['audio']['bitrate_mode'] == 'vbr') {

				// http://gabriel.mp3-tech.org/mp3infotag.html
				// int    Quality = (100 - 10 * gfp->VBR_q - gfp->quality)h


				$LAME_V_value = 10 - ceil($thisfile_mpeg_audio_lame['vbr_quality'] / 10);
				$LAME_q_value = 100 - $thisfile_mpeg_audio_lame['vbr_quality'] - ($LAME_V_value * 10);
				$encoder_options = '-V'.$LAME_V_value.' -q'.$LAME_q_value;

			} elseif ($info['audio']['bitrate_mode'] == 'cbr') {

				$encoder_options = strtoupper($info['audio']['bitrate_mode']).ceil($info['audio']['bitrate'] / 1000);

			} else {

				$encoder_options = strtoupper($info['audio']['bitrate_mode']);

			}

		} elseif (!empty($thisfile_mpeg_audio_lame['bitrate_abr'])) {

			$encoder_options = 'ABR'.$thisfile_mpeg_audio_lame['bitrate_abr'];

		} elseif (!empty($info['audio']['bitrate'])) {

			if ($info['audio']['bitrate_mode'] == 'cbr') {
				$encoder_options = strtoupper($info['audio']['bitrate_mode']).ceil($info['audio']['bitrate'] / 1000);
			} else {
				$encoder_options = strtoupper($info['audio']['bitrate_mode']);
			}

		}
		if (!empty($thisfile_mpeg_audio_lame['bitrate_min'])) {
			$encoder_options .= ' -b'.$thisfile_mpeg_audio_lame['bitrate_min'];
		}

		if (!empty($thisfile_mpeg_audio_lame['encoding_flags']['nogap_prev']) || !empty($thisfile_mpeg_audio_lame['encoding_flags']['nogap_next'])) {
			$encoder_options .= ' --nogap';
		}

		if (!empty($thisfile_mpeg_audio_lame['lowpass_frequency'])) {
			$ExplodedOptions = explode(' ', $encoder_options, 4);
			if ($ExplodedOptions[0] == '--r3mix') {
				$ExplodedOptions[1] = 'r3mix';
			}
			switch ($ExplodedOptions[0]) {
				case '--preset':
				case '--alt-preset':
				case '--r3mix':
					if ($ExplodedOptions[1] == 'fast') {
						$ExplodedOptions[1] .= ' '.$ExplodedOptions[2];
					}
					switch ($ExplodedOptions[1]) {
						case 'portable':
						case 'medium':
						case 'standard':
						case 'extreme':
						case 'insane':
						case 'fast portable':
						case 'fast medium':
						case 'fast standard':
						case 'fast extreme':
						case 'fast insane':
						case 'r3mix':
							static $ExpectedLowpass = array(
									'insane|20500'        => 20500,
									'insane|20600'        => 20600,  // 3.90.2, 3.90.3, 3.91
									'medium|18000'        => 18000,
									'fast medium|18000'   => 18000,
									'extreme|19500'       => 19500,  // 3.90,   3.90.1, 3.92, 3.95
									'extreme|19600'       => 19600,  // 3.90.2, 3.90.3, 3.91, 3.93.1
									'fast extreme|19500'  => 19500,  // 3.90,   3.90.1, 3.92, 3.95
									'fast extreme|19600'  => 19600,  // 3.90.2, 3.90.3, 3.91, 3.93.1
									'standard|19000'      => 19000,
									'fast standard|19000' => 19000,
									'r3mix|19500'         => 19500,  // 3.90,   3.90.1, 3.92
									'r3mix|19600'         => 19600,  // 3.90.2, 3.90.3, 3.91
									'r3mix|18000'         => 18000,  // 3.94,   3.95
								);
							if (!isset($ExpectedLowpass[$ExplodedOptions[1].'|'.$thisfile_mpeg_audio_lame['lowpass_frequency']]) && ($thisfile_mpeg_audio_lame['lowpass_frequency'] < 22050) && (round($thisfile_mpeg_audio_lame['lowpass_frequency'] / 1000) < round($thisfile_mpeg_audio['sample_rate'] / 2000))) {
								$encoder_options .= ' --lowpass '.$thisfile_mpeg_audio_lame['lowpass_frequency'];
							}
							break;

						default:
							break;
					}
					break;
			}
		}

		if (isset($thisfile_mpeg_audio_lame['raw']['source_sample_freq'])) {
			if (($thisfile_mpeg_audio['sample_rate'] == 44100) && ($thisfile_mpeg_audio_lame['raw']['source_sample_freq'] != 1)) {
				$encoder_options .= ' --resample 44100';
			} elseif (($thisfile_mpeg_audio['sample_rate'] == 48000) && ($thisfile_mpeg_audio_lame['raw']['source_sample_freq'] != 2)) {
				$encoder_options .= ' --resample 48000';
			} elseif ($thisfile_mpeg_audio['sample_rate'] < 44100) {
				switch ($thisfile_mpeg_audio_lame['raw']['source_sample_freq']) {
					case 0: // <= 32000
						// may or may not be same as source frequency - ignore
						break;
					case 1: // 44100
					case 2: // 48000
					case 3: // 48000+
						$ExplodedOptions = explode(' ', $encoder_options, 4);
						switch ($ExplodedOptions[0]) {
							case '--preset':
							case '--alt-preset':
								switch ($ExplodedOptions[1]) {
									case 'fast':
									case 'portable':
									case 'medium':
									case 'standard':
									case 'extreme':
									case 'insane':
										$encoder_options .= ' --resample '.$thisfile_mpeg_audio['sample_rate'];
										break;

									default:
										static $ExpectedResampledRate = array(
												'phon+/lw/mw-eu/sw|16000' => 16000,
												'mw-us|24000'             => 24000, // 3.95
												'mw-us|32000'             => 32000, // 3.93
												'mw-us|16000'             => 16000, // 3.92
												'phone|16000'             => 16000,
												'phone|11025'             => 11025, // 3.94a15
												'radio|32000'             => 32000, // 3.94a15
												'fm/radio|32000'          => 32000, // 3.92
												'fm|32000'                => 32000, // 3.90
												'voice|32000'             => 32000);
										if (!isset($ExpectedResampledRate[$ExplodedOptions[1].'|'.$thisfile_mpeg_audio['sample_rate']])) {
											$encoder_options .= ' --resample '.$thisfile_mpeg_audio['sample_rate'];
										}
										break;
								}
								break;

							case '--r3mix':
							default:
								$encoder_options .= ' --resample '.$thisfile_mpeg_audio['sample_rate'];
								break;
						}
						break;
				}
			}
		}
		if (empty($encoder_options) && !empty($info['audio']['bitrate']) && !empty($info['audio']['bitrate_mode'])) {
			//$encoder_options = strtoupper($info['audio']['bitrate_mode']).ceil($info['audio']['bitrate'] / 1000);
			$encoder_options = strtoupper($info['audio']['bitrate_mode']);
		}

		return $encoder_options;
	}


	public function decodeMPEGaudioHeader($offset, &$info, $recursivesearch=true, $ScanAsCBR=false, $FastMPEGheaderScan=false) {
		static $MPEGaudioVersionLookup;
		static $MPEGaudioLayerLookup;
		static $MPEGaudioBitrateLookup;
		static $MPEGaudioFrequencyLookup;
		static $MPEGaudioChannelModeLookup;
		static $MPEGaudioModeExtensionLookup;
		static $MPEGaudioEmphasisLookup;
		if (empty($MPEGaudioVersionLookup)) {
			$MPEGaudioVersionLookup       = self::MPEGaudioVersionArray();
			$MPEGaudioLayerLookup         = self::MPEGaudioLayerArray();
			$MPEGaudioBitrateLookup       = self::MPEGaudioBitrateArray();
			$MPEGaudioFrequencyLookup     = self::MPEGaudioFrequencyArray();
			$MPEGaudioChannelModeLookup   = self::MPEGaudioChannelModeArray();
			$MPEGaudioModeExtensionLookup = self::MPEGaudioModeExtensionArray();
			$MPEGaudioEmphasisLookup      = self::MPEGaudioEmphasisArray();
		}

		if ($this->fseek($offset) != 0) {
			$info['error'][] = 'decodeMPEGaudioHeader() failed to seek to next offset at '.$offset;
			return false;
		}
		//$headerstring = $this->fread(1441); // worst-case max length = 32kHz @ 320kbps layer 3 = 1441 bytes/frame
		$headerstring = $this->fread(226); // LAME header at offset 36 + 190 bytes of Xing/LAME data

		// MP3 audio frame structure:
		// $aa $aa $aa $aa [$bb $bb] $cc...
		// where $aa..$aa is the four-byte mpeg-audio header (below)
		// $bb $bb is the optional 2-byte CRC
		// and $cc... is the audio data

		$head4 = substr($headerstring, 0, 4);

		static $MPEGaudioHeaderDecodeCache = array();
		if (isset($MPEGaudioHeaderDecodeCache[$head4])) {
			$MPEGheaderRawArray = $MPEGaudioHeaderDecodeCache[$head4];
		} else {
			$MPEGheaderRawArray = self::MPEGaudioHeaderDecode($head4);
			$MPEGaudioHeaderDecodeCache[$head4] = $MPEGheaderRawArray;
		}

		static $MPEGaudioHeaderValidCache = array();
		if (!isset($MPEGaudioHeaderValidCache[$head4])) { // Not in cache
			//$MPEGaudioHeaderValidCache[$head4] = self::MPEGaudioHeaderValid($MPEGheaderRawArray, false, true);  // allow badly-formatted freeformat (from LAME 3.90 - 3.93.1)
			$MPEGaudioHeaderValidCache[$head4] = self::MPEGaudioHeaderValid($MPEGheaderRawArray, false, false);
		}

		// shortcut
		if (!isset($info['mpeg']['audio'])) {
			$info['mpeg']['audio'] = array();
		}
		$thisfile_mpeg_audio = &$info['mpeg']['audio'];


		if ($MPEGaudioHeaderValidCache[$head4]) {
			$thisfile_mpeg_audio['raw'] = $MPEGheaderRawArray;
		} else {
			$info['error'][] = 'Invalid MPEG audio header ('.getid3_lib::PrintHexBytes($head4).') at offset '.$offset;
			return false;
		}

		if (!$FastMPEGheaderScan) {
			$thisfile_mpeg_audio['version']       = $MPEGaudioVersionLookup[$thisfile_mpeg_audio['raw']['version']];
			$thisfile_mpeg_audio['layer']         = $MPEGaudioLayerLookup[$thisfile_mpeg_audio['raw']['layer']];

			$thisfile_mpeg_audio['channelmode']   = $MPEGaudioChannelModeLookup[$thisfile_mpeg_audio['raw']['channelmode']];
			$thisfile_mpeg_audio['channels']      = (($thisfile_mpeg_audio['channelmode'] == 'mono') ? 1 : 2);
			$thisfile_mpeg_audio['sample_rate']   = $MPEGaudioFrequencyLookup[$thisfile_mpeg_audio['version']][$thisfile_mpeg_audio['raw']['sample_rate']];
			$thisfile_mpeg_audio['protection']    = !$thisfile_mpeg_audio['raw']['protection'];
			$thisfile_mpeg_audio['private']       = (bool) $thisfile_mpeg_audio['raw']['private'];
			$thisfile_mpeg_audio['modeextension'] = $MPEGaudioModeExtensionLookup[$thisfile_mpeg_audio['layer']][$thisfile_mpeg_audio['raw']['modeextension']];
			$thisfile_mpeg_audio['copyright']     = (bool) $thisfile_mpeg_audio['raw']['copyright'];
			$thisfile_mpeg_audio['original']      = (bool) $thisfile_mpeg_audio['raw']['original'];
			$thisfile_mpeg_audio['emphasis']      = $MPEGaudioEmphasisLookup[$thisfile_mpeg_audio['raw']['emphasis']];

			$info['audio']['channels']    = $thisfile_mpeg_audio['channels'];
			$info['audio']['sample_rate'] = $thisfile_mpeg_audio['sample_rate'];

			if ($thisfile_mpeg_audio['protection']) {
				$thisfile_mpeg_audio['crc'] = getid3_lib::BigEndian2Int(substr($headerstring, 4, 2));
			}
		}

		if ($thisfile_mpeg_audio['raw']['bitrate'] == 15) {
			// http://www.hydrogenaudio.org/?act=ST&f=16&t=9682&st=0
			$info['warning'][] = 'Invalid bitrate index (15), this is a known bug in free-format MP3s encoded by LAME v3.90 - 3.93.1';
			$thisfile_mpeg_audio['raw']['bitrate'] = 0;
		}
		$thisfile_mpeg_audio['padding'] = (bool) $thisfile_mpeg_audio['raw']['padding'];
		$thisfile_mpeg_audio['bitrate'] = $MPEGaudioBitrateLookup[$thisfile_mpeg_audio['version']][$thisfile_mpeg_audio['layer']][$thisfile_mpeg_audio['raw']['bitrate']];

		if (($thisfile_mpeg_audio['bitrate'] == 'free') && ($offset == $info['avdataoffset'])) {
			// only skip multiple frame check if free-format bitstream found at beginning of file
			// otherwise is quite possibly simply corrupted data
			$recursivesearch = false;
		}

		// For Layer 2 there are some combinations of bitrate and mode which are not allowed.
		if (!$FastMPEGheaderScan && ($thisfile_mpeg_audio['layer'] == '2')) {

			$info['audio']['dataformat'] = 'mp2';
			switch ($thisfile_mpeg_audio['channelmode']) {

				case 'mono':
					if (($thisfile_mpeg_audio['bitrate'] == 'free') || ($thisfile_mpeg_audio['bitrate'] <= 192000)) {
						// these are ok
					} else {
						$info['error'][] = $thisfile_mpeg_audio['bitrate'].'kbps not allowed in Layer 2, '.$thisfile_mpeg_audio['channelmode'].'.';
						return false;
					}
					break;

				case 'stereo':
				case 'joint stereo':
				case 'dual channel':
					if (($thisfile_mpeg_audio['bitrate'] == 'free') || ($thisfile_mpeg_audio['bitrate'] == 64000) || ($thisfile_mpeg_audio['bitrate'] >= 96000)) {
						// these are ok
					} else {
						$info['error'][] = intval(round($thisfile_mpeg_audio['bitrate'] / 1000)).'kbps not allowed in Layer 2, '.$thisfile_mpeg_audio['channelmode'].'.';
						return false;
					}
					break;

			}

		}


		if ($info['audio']['sample_rate'] > 0) {
			$thisfile_mpeg_audio['framelength'] = self::MPEGaudioFrameLength($thisfile_mpeg_audio['bitrate'], $thisfile_mpeg_audio['version'], $thisfile_mpeg_audio['layer'], (int) $thisfile_mpeg_audio['padding'], $info['audio']['sample_rate']);
		}

		$nextframetestoffset = $offset + 1;
		if ($thisfile_mpeg_audio['bitrate'] != 'free') {

			$info['audio']['bitrate'] = $thisfile_mpeg_audio['bitrate'];

			if (isset($thisfile_mpeg_audio['framelength'])) {
				$nextframetestoffset = $offset + $thisfile_mpeg_audio['framelength'];
			} else {
				$info['error'][] = 'Frame at offset('.$offset.') is has an invalid frame length.';
				return false;
			}

		}

		$ExpectedNumberOfAudioBytes = 0;

		////////////////////////////////////////////////////////////////////////////////////
		// Variable-bitrate headers

		if (substr($headerstring, 4 + 32, 4) == 'VBRI') {
			// Fraunhofer VBR header is hardcoded 'VBRI' at offset 0x24 (36)
			// specs taken from http://minnie.tuhs.org/pipermail/mp3encoder/2001-January/001800.html

			$thisfile_mpeg_audio['bitrate_mode'] = 'vbr';
			$thisfile_mpeg_audio['VBR_method']   = 'Fraunhofer';
			$info['audio']['codec']                = 'Fraunhofer';

			$SideInfoData = substr($headerstring, 4 + 2, 32);

			$FraunhoferVBROffset = 36;

			$thisfile_mpeg_audio['VBR_encoder_version']     = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset +  4, 2)); // VbriVersion
			$thisfile_mpeg_audio['VBR_encoder_delay']       = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset +  6, 2)); // VbriDelay
			$thisfile_mpeg_audio['VBR_quality']             = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset +  8, 2)); // VbriQuality
			$thisfile_mpeg_audio['VBR_bytes']               = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset + 10, 4)); // VbriStreamBytes
			$thisfile_mpeg_audio['VBR_frames']              = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset + 14, 4)); // VbriStreamFrames
			$thisfile_mpeg_audio['VBR_seek_offsets']        = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset + 18, 2)); // VbriTableSize
			$thisfile_mpeg_audio['VBR_seek_scale']          = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset + 20, 2)); // VbriTableScale
			$thisfile_mpeg_audio['VBR_entry_bytes']         = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset + 22, 2)); // VbriEntryBytes
			$thisfile_mpeg_audio['VBR_entry_frames']        = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset + 24, 2)); // VbriEntryFrames

			$ExpectedNumberOfAudioBytes = $thisfile_mpeg_audio['VBR_bytes'];

			$previousbyteoffset = $offset;
			for ($i = 0; $i < $thisfile_mpeg_audio['VBR_seek_offsets']; $i++) {
				$Fraunhofer_OffsetN = getid3_lib::BigEndian2Int(substr($headerstring, $FraunhoferVBROffset, $thisfile_mpeg_audio['VBR_entry_bytes']));
				$FraunhoferVBROffset += $thisfile_mpeg_audio['VBR_entry_bytes'];
				$thisfile_mpeg_audio['VBR_offsets_relative'][$i] = ($Fraunhofer_OffsetN * $thisfile_mpeg_audio['VBR_seek_scale']);
				$thisfile_mpeg_audio['VBR_offsets_absolute'][$i] = ($Fraunhofer_OffsetN * $thisfile_mpeg_audio['VBR_seek_scale']) + $previousbyteoffset;
				$previousbyteoffset += $Fraunhofer_OffsetN;
			}


		} else {

			// Xing VBR header is hardcoded 'Xing' at a offset 0x0D (13), 0x15 (21) or 0x24 (36)
			// depending on MPEG layer and number of channels

			$VBRidOffset = self::XingVBRidOffset($thisfile_mpeg_audio['version'], $thisfile_mpeg_audio['channelmode']);
			$SideInfoData = substr($headerstring, 4 + 2, $VBRidOffset - 4);

			if ((substr($headerstring, $VBRidOffset, strlen('Xing')) == 'Xing') || (substr($headerstring, $VBRidOffset, strlen('Info')) == 'Info')) {
				// 'Xing' is traditional Xing VBR frame
				// 'Info' is LAME-encoded CBR (This was done to avoid CBR files to be recognized as traditional Xing VBR files by some decoders.)
				// 'Info' *can* legally be used to specify a VBR file as well, however.

				// http://www.multiweb.cz/twoinches/MP3inside.htm
				//00..03 = "Xing" or "Info"
				//04..07 = Flags:
				//  0x01  Frames Flag     set if value for number of frames in file is stored
				//  0x02  Bytes Flag      set if value for filesize in bytes is stored
				//  0x04  TOC Flag        set if values for TOC are stored
				//  0x08  VBR Scale Flag  set if values for VBR scale is stored
				//08..11  Frames: Number of frames in file (including the first Xing/Info one)
				//12..15  Bytes:  File length in Bytes
				//16..115  TOC (Table of Contents):
				//  Contains of 100 indexes (one Byte length) for easier lookup in file. Approximately solves problem with moving inside file.
				//  Each Byte has a value according this formula:
				//  (TOC[i] / 256) * fileLenInBytes
				//  So if song lasts eg. 240 sec. and you want to jump to 60. sec. (and file is 5 000 000 Bytes length) you can use:
				//  TOC[(60/240)*100] = TOC[25]
				//  and corresponding Byte in file is then approximately at:
				//  (TOC[25]/256) * 5000000
				//116..119  VBR Scale


				// should be safe to leave this at 'vbr' and let it be overriden to 'cbr' if a CBR preset/mode is used by LAME
//				if (substr($headerstring, $VBRidOffset, strlen('Info')) == 'Xing') {
					$thisfile_mpeg_audio['bitrate_mode'] = 'vbr';
					$thisfile_mpeg_audio['VBR_method']   = 'Xing';
//				} else {
//					$ScanAsCBR = true;
//					$thisfile_mpeg_audio['bitrate_mode'] = 'cbr';
//				}

				$thisfile_mpeg_audio['xing_flags_raw'] = getid3_lib::BigEndian2Int(substr($headerstring, $VBRidOffset + 4, 4));

				$thisfile_mpeg_audio['xing_flags']['frames']    = (bool) ($thisfile_mpeg_audio['xing_flags_raw'] & 0x00000001);
				$thisfile_mpeg_audio['xing_flags']['bytes']     = (bool) ($thisfile_mpeg_audio['xing_flags_raw'] & 0x00000002);
				$thisfile_mpeg_audio['xing_flags']['toc']       = (bool) ($thisfile_mpeg_audio['xing_flags_raw'] & 0x00000004);
				$thisfile_mpeg_audio['xing_flags']['vbr_scale'] = (bool) ($thisfile_mpeg_audio['xing_flags_raw'] & 0x00000008);

				if ($thisfile_mpeg_audio['xing_flags']['frames']) {
					$thisfile_mpeg_audio['VBR_frames'] = getid3_lib::BigEndian2Int(substr($headerstring, $VBRidOffset +  8, 4));
					//$thisfile_mpeg_audio['VBR_frames']--; // don't count header Xing/Info frame
				}
				if ($thisfile_mpeg_audio['xing_flags']['bytes']) {
					$thisfile_mpeg_audio['VBR_bytes']  = getid3_lib::BigEndian2Int(substr($headerstring, $VBRidOffset + 12, 4));
				}

				//if (($thisfile_mpeg_audio['bitrate'] == 'free') && !empty($thisfile_mpeg_audio['VBR_frames']) && !empty($thisfile_mpeg_audio['VBR_bytes'])) {
				if (!empty($thisfile_mpeg_audio['VBR_frames']) && !empty($thisfile_mpeg_audio['VBR_bytes'])) {

					$framelengthfloat = $thisfile_mpeg_audio['VBR_bytes'] / $thisfile_mpeg_audio['VBR_frames'];

					if ($thisfile_mpeg_audio['layer'] == '1') {
						// BitRate = (((FrameLengthInBytes / 4) - Padding) * SampleRate) / 12
						//$info['audio']['bitrate'] = ((($framelengthfloat / 4) - intval($thisfile_mpeg_audio['padding'])) * $thisfile_mpeg_audio['sample_rate']) / 12;
						$info['audio']['bitrate'] = ($framelengthfloat / 4) * $thisfile_mpeg_audio['sample_rate'] * (2 / $info['audio']['channels']) / 12;
					} else {
						// Bitrate = ((FrameLengthInBytes - Padding) * SampleRate) / 144
						//$info['audio']['bitrate'] = (($framelengthfloat - intval($thisfile_mpeg_audio['padding'])) * $thisfile_mpeg_audio['sample_rate']) / 144;
						$info['audio']['bitrate'] = $framelengthfloat * $thisfile_mpeg_audio['sample_rate'] * (2 / $info['audio']['channels']) / 144;
					}
					$thisfile_mpeg_audio['framelength'] = floor($framelengthfloat);
				}

				if ($thisfile_mpeg_audio['xing_flags']['toc']) {
					$LAMEtocData = substr($headerstring, $VBRidOffset + 16, 100);
					for ($i = 0; $i < 100; $i++) {
						$thisfile_mpeg_audio['toc'][$i] = ord($LAMEtocData{$i});
					}
				}
				if ($thisfile_mpeg_audio['xing_flags']['vbr_scale']) {
					$thisfile_mpeg_audio['VBR_scale'] = getid3_lib::BigEndian2Int(substr($headerstring, $VBRidOffset + 116, 4));
				}


				// http://gabriel.mp3-tech.org/mp3infotag.html
				if (substr($headerstring, $VBRidOffset + 120, 4) == 'LAME') {

					// shortcut
					$thisfile_mpeg_audio['LAME'] = array();
					$thisfile_mpeg_audio_lame    = &$thisfile_mpeg_audio['LAME'];


					$thisfile_mpeg_audio_lame['long_version']  = substr($headerstring, $VBRidOffset + 120, 20);
					$thisfile_mpeg_audio_lame['short_version'] = substr($thisfile_mpeg_audio_lame['long_version'], 0, 9);

					if ($thisfile_mpeg_audio_lame['short_version'] >= 'LAME3.90') {

						// extra 11 chars are not part of version string when LAMEtag present
						unset($thisfile_mpeg_audio_lame['long_version']);

						// It the LAME tag was only introduced in LAME v3.90
						// http://www.hydrogenaudio.org/?act=ST&f=15&t=9933

						// Offsets of various bytes in http://gabriel.mp3-tech.org/mp3infotag.html
						// are assuming a 'Xing' identifier offset of 0x24, which is the case for
						// MPEG-1 non-mono, but not for other combinations
						$LAMEtagOffsetContant = $VBRidOffset - 0x24;

						// shortcuts
						$thisfile_mpeg_audio_lame['RGAD']    = array('track'=>array(), 'album'=>array());
						$thisfile_mpeg_audio_lame_RGAD       = &$thisfile_mpeg_audio_lame['RGAD'];
						$thisfile_mpeg_audio_lame_RGAD_track = &$thisfile_mpeg_audio_lame_RGAD['track'];
						$thisfile_mpeg_audio_lame_RGAD_album = &$thisfile_mpeg_audio_lame_RGAD['album'];
						$thisfile_mpeg_audio_lame['raw'] = array();
						$thisfile_mpeg_audio_lame_raw    = &$thisfile_mpeg_audio_lame['raw'];

						// byte $9B  VBR Quality
						// This field is there to indicate a quality level, although the scale was not precised in the original Xing specifications.
						// Actually overwrites original Xing bytes
						unset($thisfile_mpeg_audio['VBR_scale']);
						$thisfile_mpeg_audio_lame['vbr_quality'] = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0x9B, 1));

						// bytes $9C-$A4  Encoder short VersionString
						$thisfile_mpeg_audio_lame['short_version'] = substr($headerstring, $LAMEtagOffsetContant + 0x9C, 9);

						// byte $A5  Info Tag revision + VBR method
						$LAMEtagRevisionVBRmethod = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xA5, 1));

						$thisfile_mpeg_audio_lame['tag_revision']   = ($LAMEtagRevisionVBRmethod & 0xF0) >> 4;
						$thisfile_mpeg_audio_lame_raw['vbr_method'] =  $LAMEtagRevisionVBRmethod & 0x0F;
						$thisfile_mpeg_audio_lame['vbr_method']     = self::LAMEvbrMethodLookup($thisfile_mpeg_audio_lame_raw['vbr_method']);
						$thisfile_mpeg_audio['bitrate_mode']        = substr($thisfile_mpeg_audio_lame['vbr_method'], 0, 3); // usually either 'cbr' or 'vbr', but truncates 'vbr-old / vbr-rh' to 'vbr'

						// byte $A6  Lowpass filter value
						$thisfile_mpeg_audio_lame['lowpass_frequency'] = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xA6, 1)) * 100;

						// bytes $A7-$AE  Replay Gain
						// http://privatewww.essex.ac.uk/~djmrob/replaygain/rg_data_format.html
						// bytes $A7-$AA : 32 bit floating point "Peak signal amplitude"
						if ($thisfile_mpeg_audio_lame['short_version'] >= 'LAME3.94b') {
							// LAME 3.94a16 and later - 9.23 fixed point
							// ie 0x0059E2EE / (2^23) = 5890798 / 8388608 = 0.7022378444671630859375
							$thisfile_mpeg_audio_lame_RGAD['peak_amplitude'] = (float) ((getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xA7, 4))) / 8388608);
						} else {
							// LAME 3.94a15 and earlier - 32-bit floating point
							// Actually 3.94a16 will fall in here too and be WRONG, but is hard to detect 3.94a16 vs 3.94a15
							$thisfile_mpeg_audio_lame_RGAD['peak_amplitude'] = getid3_lib::LittleEndian2Float(substr($headerstring, $LAMEtagOffsetContant + 0xA7, 4));
						}
						if ($thisfile_mpeg_audio_lame_RGAD['peak_amplitude'] == 0) {
							unset($thisfile_mpeg_audio_lame_RGAD['peak_amplitude']);
						} else {
							$thisfile_mpeg_audio_lame_RGAD['peak_db'] = getid3_lib::RGADamplitude2dB($thisfile_mpeg_audio_lame_RGAD['peak_amplitude']);
						}

						$thisfile_mpeg_audio_lame_raw['RGAD_track']      =   getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xAB, 2));
						$thisfile_mpeg_audio_lame_raw['RGAD_album']      =   getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xAD, 2));


						if ($thisfile_mpeg_audio_lame_raw['RGAD_track'] != 0) {

							$thisfile_mpeg_audio_lame_RGAD_track['raw']['name']        = ($thisfile_mpeg_audio_lame_raw['RGAD_track'] & 0xE000) >> 13;
							$thisfile_mpeg_audio_lame_RGAD_track['raw']['originator']  = ($thisfile_mpeg_audio_lame_raw['RGAD_track'] & 0x1C00) >> 10;
							$thisfile_mpeg_audio_lame_RGAD_track['raw']['sign_bit']    = ($thisfile_mpeg_audio_lame_raw['RGAD_track'] & 0x0200) >> 9;
							$thisfile_mpeg_audio_lame_RGAD_track['raw']['gain_adjust'] =  $thisfile_mpeg_audio_lame_raw['RGAD_track'] & 0x01FF;
							$thisfile_mpeg_audio_lame_RGAD_track['name']       = getid3_lib::RGADnameLookup($thisfile_mpeg_audio_lame_RGAD_track['raw']['name']);
							$thisfile_mpeg_audio_lame_RGAD_track['originator'] = getid3_lib::RGADoriginatorLookup($thisfile_mpeg_audio_lame_RGAD_track['raw']['originator']);
							$thisfile_mpeg_audio_lame_RGAD_track['gain_db']    = getid3_lib::RGADadjustmentLookup($thisfile_mpeg_audio_lame_RGAD_track['raw']['gain_adjust'], $thisfile_mpeg_audio_lame_RGAD_track['raw']['sign_bit']);

							if (!empty($thisfile_mpeg_audio_lame_RGAD['peak_amplitude'])) {
								$info['replay_gain']['track']['peak']   = $thisfile_mpeg_audio_lame_RGAD['peak_amplitude'];
							}
							$info['replay_gain']['track']['originator'] = $thisfile_mpeg_audio_lame_RGAD_track['originator'];
							$info['replay_gain']['track']['adjustment'] = $thisfile_mpeg_audio_lame_RGAD_track['gain_db'];
						} else {
							unset($thisfile_mpeg_audio_lame_RGAD['track']);
						}
						if ($thisfile_mpeg_audio_lame_raw['RGAD_album'] != 0) {

							$thisfile_mpeg_audio_lame_RGAD_album['raw']['name']        = ($thisfile_mpeg_audio_lame_raw['RGAD_album'] & 0xE000) >> 13;
							$thisfile_mpeg_audio_lame_RGAD_album['raw']['originator']  = ($thisfile_mpeg_audio_lame_raw['RGAD_album'] & 0x1C00) >> 10;
							$thisfile_mpeg_audio_lame_RGAD_album['raw']['sign_bit']    = ($thisfile_mpeg_audio_lame_raw['RGAD_album'] & 0x0200) >> 9;
							$thisfile_mpeg_audio_lame_RGAD_album['raw']['gain_adjust'] = $thisfile_mpeg_audio_lame_raw['RGAD_album'] & 0x01FF;
							$thisfile_mpeg_audio_lame_RGAD_album['name']       = getid3_lib::RGADnameLookup($thisfile_mpeg_audio_lame_RGAD_album['raw']['name']);
							$thisfile_mpeg_audio_lame_RGAD_album['originator'] = getid3_lib::RGADoriginatorLookup($thisfile_mpeg_audio_lame_RGAD_album['raw']['originator']);
							$thisfile_mpeg_audio_lame_RGAD_album['gain_db']    = getid3_lib::RGADadjustmentLookup($thisfile_mpeg_audio_lame_RGAD_album['raw']['gain_adjust'], $thisfile_mpeg_audio_lame_RGAD_album['raw']['sign_bit']);

							if (!empty($thisfile_mpeg_audio_lame_RGAD['peak_amplitude'])) {
								$info['replay_gain']['album']['peak']   = $thisfile_mpeg_audio_lame_RGAD['peak_amplitude'];
							}
							$info['replay_gain']['album']['originator'] = $thisfile_mpeg_audio_lame_RGAD_album['originator'];
							$info['replay_gain']['album']['adjustment'] = $thisfile_mpeg_audio_lame_RGAD_album['gain_db'];
						} else {
							unset($thisfile_mpeg_audio_lame_RGAD['album']);
						}
						if (empty($thisfile_mpeg_audio_lame_RGAD)) {
							unset($thisfile_mpeg_audio_lame['RGAD']);
						}


						// byte $AF  Encoding flags + ATH Type
						$EncodingFlagsATHtype = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xAF, 1));
						$thisfile_mpeg_audio_lame['encoding_flags']['nspsytune']   = (bool) ($EncodingFlagsATHtype & 0x10);
						$thisfile_mpeg_audio_lame['encoding_flags']['nssafejoint'] = (bool) ($EncodingFlagsATHtype & 0x20);
						$thisfile_mpeg_audio_lame['encoding_flags']['nogap_next']  = (bool) ($EncodingFlagsATHtype & 0x40);
						$thisfile_mpeg_audio_lame['encoding_flags']['nogap_prev']  = (bool) ($EncodingFlagsATHtype & 0x80);
						$thisfile_mpeg_audio_lame['ath_type']                      =         $EncodingFlagsATHtype & 0x0F;

						// byte $B0  if ABR {specified bitrate} else {minimal bitrate}
						$thisfile_mpeg_audio_lame['raw']['abrbitrate_minbitrate'] = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xB0, 1));
						if ($thisfile_mpeg_audio_lame_raw['vbr_method'] == 2) { // Average BitRate (ABR)
							$thisfile_mpeg_audio_lame['bitrate_abr'] = $thisfile_mpeg_audio_lame['raw']['abrbitrate_minbitrate'];
						} elseif ($thisfile_mpeg_audio_lame_raw['vbr_method'] == 1) { // Constant BitRate (CBR)
							// ignore
						} elseif ($thisfile_mpeg_audio_lame['raw']['abrbitrate_minbitrate'] > 0) { // Variable BitRate (VBR) - minimum bitrate
							$thisfile_mpeg_audio_lame['bitrate_min'] = $thisfile_mpeg_audio_lame['raw']['abrbitrate_minbitrate'];
						}

						// bytes $B1-$B3  Encoder delays
						$EncoderDelays = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xB1, 3));
						$thisfile_mpeg_audio_lame['encoder_delay'] = ($EncoderDelays & 0xFFF000) >> 12;
						$thisfile_mpeg_audio_lame['end_padding']   =  $EncoderDelays & 0x000FFF;

						// byte $B4  Misc
						$MiscByte = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xB4, 1));
						$thisfile_mpeg_audio_lame_raw['noise_shaping']       = ($MiscByte & 0x03);
						$thisfile_mpeg_audio_lame_raw['stereo_mode']         = ($MiscByte & 0x1C) >> 2;
						$thisfile_mpeg_audio_lame_raw['not_optimal_quality'] = ($MiscByte & 0x20) >> 5;
						$thisfile_mpeg_audio_lame_raw['source_sample_freq']  = ($MiscByte & 0xC0) >> 6;
						$thisfile_mpeg_audio_lame['noise_shaping']       = $thisfile_mpeg_audio_lame_raw['noise_shaping'];
						$thisfile_mpeg_audio_lame['stereo_mode']         = self::LAMEmiscStereoModeLookup($thisfile_mpeg_audio_lame_raw['stereo_mode']);
						$thisfile_mpeg_audio_lame['not_optimal_quality'] = (bool) $thisfile_mpeg_audio_lame_raw['not_optimal_quality'];
						$thisfile_mpeg_audio_lame['source_sample_freq']  = self::LAMEmiscSourceSampleFrequencyLookup($thisfile_mpeg_audio_lame_raw['source_sample_freq']);

						// byte $B5  MP3 Gain
						$thisfile_mpeg_audio_lame_raw['mp3_gain'] = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xB5, 1), false, true);
						$thisfile_mpeg_audio_lame['mp3_gain_db']     = (getid3_lib::RGADamplitude2dB(2) / 4) * $thisfile_mpeg_audio_lame_raw['mp3_gain'];
						$thisfile_mpeg_audio_lame['mp3_gain_factor'] = pow(2, ($thisfile_mpeg_audio_lame['mp3_gain_db'] / 6));

						// bytes $B6-$B7  Preset and surround info
						$PresetSurroundBytes = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xB6, 2));
						// Reserved                                                    = ($PresetSurroundBytes & 0xC000);
						$thisfile_mpeg_audio_lame_raw['surround_info'] = ($PresetSurroundBytes & 0x3800);
						$thisfile_mpeg_audio_lame['surround_info']     = self::LAMEsurroundInfoLookup($thisfile_mpeg_audio_lame_raw['surround_info']);
						$thisfile_mpeg_audio_lame['preset_used_id']    = ($PresetSurroundBytes & 0x07FF);
						$thisfile_mpeg_audio_lame['preset_used']       = self::LAMEpresetUsedLookup($thisfile_mpeg_audio_lame);
						if (!empty($thisfile_mpeg_audio_lame['preset_used_id']) && empty($thisfile_mpeg_audio_lame['preset_used'])) {
							$info['warning'][] = 'Unknown LAME preset used ('.$thisfile_mpeg_audio_lame['preset_used_id'].') - please report to info@getid3.org';
						}
						if (($thisfile_mpeg_audio_lame['short_version'] == 'LAME3.90.') && !empty($thisfile_mpeg_audio_lame['preset_used_id'])) {
							// this may change if 3.90.4 ever comes out
							$thisfile_mpeg_audio_lame['short_version'] = 'LAME3.90.3';
						}

						// bytes $B8-$BB  MusicLength
						$thisfile_mpeg_audio_lame['audio_bytes'] = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xB8, 4));
						$ExpectedNumberOfAudioBytes = (($thisfile_mpeg_audio_lame['audio_bytes'] > 0) ? $thisfile_mpeg_audio_lame['audio_bytes'] : $thisfile_mpeg_audio['VBR_bytes']);

						// bytes $BC-$BD  MusicCRC
						$thisfile_mpeg_audio_lame['music_crc']    = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xBC, 2));

						// bytes $BE-$BF  CRC-16 of Info Tag
						$thisfile_mpeg_audio_lame['lame_tag_crc'] = getid3_lib::BigEndian2Int(substr($headerstring, $LAMEtagOffsetContant + 0xBE, 2));


						// LAME CBR
						if ($thisfile_mpeg_audio_lame_raw['vbr_method'] == 1) {

							$thisfile_mpeg_audio['bitrate_mode'] = 'cbr';
							$thisfile_mpeg_audio['bitrate'] = self::ClosestStandardMP3Bitrate($thisfile_mpeg_audio['bitrate']);
							$info['audio']['bitrate'] = $thisfile_mpeg_audio['bitrate'];
							//if (empty($thisfile_mpeg_audio['bitrate']) || (!empty($thisfile_mpeg_audio_lame['bitrate_min']) && ($thisfile_mpeg_audio_lame['bitrate_min'] != 255))) {
							//	$thisfile_mpeg_audio['bitrate'] = $thisfile_mpeg_audio_lame['bitrate_min'];
							//}

						}

					}
				}

			} else {

				// not Fraunhofer or Xing VBR methods, most likely CBR (but could be VBR with no header)
				$thisfile_mpeg_audio['bitrate_mode'] = 'cbr';
				if ($recursivesearch) {
					$thisfile_mpeg_audio['bitrate_mode'] = 'vbr';
					if ($this->RecursiveFrameScanning($offset, $nextframetestoffset, true)) {
						$recursivesearch = false;
						$thisfile_mpeg_audio['bitrate_mode'] = 'cbr';
					}
					if ($thisfile_mpeg_audio['bitrate_mode'] == 'vbr') {
						$info['warning'][] = 'VBR file with no VBR header. Bitrate values calculated from actual frame bitrates.';
					}
				}

			}

		}

		if (($ExpectedNumberOfAudioBytes > 0) && ($ExpectedNumberOfAudioBytes != ($info['avdataend'] - $info['avdataoffset']))) {
			if ($ExpectedNumberOfAudioBytes > ($info['avdataend'] - $info['avdataoffset'])) {
				if ($this->isDependencyFor('matroska') || $this->isDependencyFor('riff')) {
					// ignore, audio data is broken into chunks so will always be data "missing"
				}
				elseif (($ExpectedNumberOfAudioBytes - ($info['avdataend'] - $info['avdataoffset'])) == 1) {
					$this->warning('Last byte of data truncated (this is a known bug in Meracl ID3 Tag Writer before v1.3.5)');
				}
				else {
					$this->warning('Probable truncated file: expecting '.$ExpectedNumberOfAudioBytes.' bytes of audio data, only found '.($info['avdataend'] - $info['avdataoffset']).' (short by '.($ExpectedNumberOfAudioBytes - ($info['avdataend'] - $info['avdataoffset'])).' bytes)');
				}
			} else {
				if ((($info['avdataend'] - $info['avdataoffset']) - $ExpectedNumberOfAudioBytes) == 1) {
				//	$prenullbytefileoffset = $this->ftell();
				//	$this->fseek($info['avdataend']);
				//	$PossibleNullByte = $this->fread(1);
				//	$this->fseek($prenullbytefileoffset);
				//	if ($PossibleNullByte === "\x00") {
						$info['avdataend']--;
				//		$info['warning'][] = 'Extra null byte at end of MP3 data assumed to be RIFF padding and therefore ignored';
				//	} else {
				//		$info['warning'][] = 'Too much data in file: expecting '.$ExpectedNumberOfAudioBytes.' bytes of audio data, found '.($info['avdataend'] - $info['avdataoffset']).' ('.(($info['avdataend'] - $info['avdataoffset']) - $ExpectedNumberOfAudioBytes).' bytes too many)';
				//	}
				} else {
					$info['warning'][] = 'Too much data in file: expecting '.$ExpectedNumberOfAudioBytes.' bytes of audio data, found '.($info['avdataend'] - $info['avdataoffset']).' ('.(($info['avdataend'] - $info['avdataoffset']) - $ExpectedNumberOfAudioBytes).' bytes too many)';
				}
			}
		}

		if (($thisfile_mpeg_audio['bitrate'] == 'free') && empty($info['audio']['bitrate'])) {
			if (($offset == $info['avdataoffset']) && empty($thisfile_mpeg_audio['VBR_frames'])) {
				$framebytelength = $this->FreeFormatFrameLength($offset, true);
				if ($framebytelength > 0) {
					$thisfile_mpeg_audio['framelength'] = $framebytelength;
					if ($thisfile_mpeg_audio['layer'] == '1') {
						// BitRate = (((FrameLengthInBytes / 4) - Padding) * SampleRate) / 12
						$info['audio']['bitrate'] = ((($framebytelength / 4) - intval($thisfile_mpeg_audio['padding'])) * $thisfile_mpeg_audio['sample_rate']) / 12;
					} else {
						// Bitrate = ((FrameLengthInBytes - Padding) * SampleRate) / 144
						$info['audio']['bitrate'] = (($framebytelength - intval($thisfile_mpeg_audio['padding'])) * $thisfile_mpeg_audio['sample_rate']) / 144;
					}
				} else {
					$info['error'][] = 'Error calculating frame length of free-format MP3 without Xing/LAME header';
				}
			}
		}

		if (isset($thisfile_mpeg_audio['VBR_frames']) ? $thisfile_mpeg_audio['VBR_frames'] : '') {
			switch ($thisfile_mpeg_audio['bitrate_mode']) {
				case 'vbr':
				case 'abr':
					$bytes_per_frame = 1152;
					if (($thisfile_mpeg_audio['version'] == '1') && ($thisfile_mpeg_audio['layer'] == 1)) {
						$bytes_per_frame = 384;
					} elseif ((($thisfile_mpeg_audio['version'] == '2') || ($thisfile_mpeg_audio['version'] == '2.5')) && ($thisfile_mpeg_audio['layer'] == 3)) {
						$bytes_per_frame = 576;
					}
					$thisfile_mpeg_audio['VBR_bitrate'] = (isset($thisfile_mpeg_audio['VBR_bytes']) ? (($thisfile_mpeg_audio['VBR_bytes'] / $thisfile_mpeg_audio['VBR_frames']) * 8) * ($info['audio']['sample_rate'] / $bytes_per_frame) : 0);
					if ($thisfile_mpeg_audio['VBR_bitrate'] > 0) {
						$info['audio']['bitrate']         = $thisfile_mpeg_audio['VBR_bitrate'];
						$thisfile_mpeg_audio['bitrate'] = $thisfile_mpeg_audio['VBR_bitrate']; // to avoid confusion
					}
					break;
			}
		}

		// End variable-bitrate headers
		////////////////////////////////////////////////////////////////////////////////////

		if ($recursivesearch) {

			if (!$this->RecursiveFrameScanning($offset, $nextframetestoffset, $ScanAsCBR)) {
				return false;
			}

		}


		//if (false) {
		//    // experimental side info parsing section - not returning anything useful yet
		//
		//    $SideInfoBitstream = getid3_lib::BigEndian2Bin($SideInfoData);
		//    $SideInfoOffset = 0;
		//
		//    if ($thisfile_mpeg_audio['version'] == '1') {
		//        if ($thisfile_mpeg_audio['channelmode'] == 'mono') {
		//            // MPEG-1 (mono)
		//            $thisfile_mpeg_audio['side_info']['main_data_begin'] = substr($SideInfoBitstream, $SideInfoOffset, 9);
		//            $SideInfoOffset += 9;
		//            $SideInfoOffset += 5;
		//        } else {
		//            // MPEG-1 (stereo, joint-stereo, dual-channel)
		//            $thisfile_mpeg_audio['side_info']['main_data_begin'] = substr($SideInfoBitstream, $SideInfoOffset, 9);
		//            $SideInfoOffset += 9;
		//            $SideInfoOffset += 3;
		//        }
		//    } else { // 2 or 2.5
		//        if ($thisfile_mpeg_audio['channelmode'] == 'mono') {
		//            // MPEG-2, MPEG-2.5 (mono)
		//            $thisfile_mpeg_audio['side_info']['main_data_begin'] = substr($SideInfoBitstream, $SideInfoOffset, 8);
		//            $SideInfoOffset += 8;
		//            $SideInfoOffset += 1;
		//        } else {
		//            // MPEG-2, MPEG-2.5 (stereo, joint-stereo, dual-channel)
		//            $thisfile_mpeg_audio['side_info']['main_data_begin'] = substr($SideInfoBitstream, $SideInfoOffset, 8);
		//            $SideInfoOffset += 8;
		//            $SideInfoOffset += 2;
		//        }
		//    }
		//
		//    if ($thisfile_mpeg_audio['version'] == '1') {
		//        for ($channel = 0; $channel < $info['audio']['channels']; $channel++) {
		//            for ($scfsi_band = 0; $scfsi_band < 4; $scfsi_band++) {
		//                $thisfile_mpeg_audio['scfsi'][$channel][$scfsi_band] = substr($SideInfoBitstream, $SideInfoOffset, 1);
		//                $SideInfoOffset += 2;
		//            }
		//        }
		//    }
		//    for ($granule = 0; $granule < (($thisfile_mpeg_audio['version'] == '1') ? 2 : 1); $granule++) {
		//        for ($channel = 0; $channel < $info['audio']['channels']; $channel++) {
		//            $thisfile_mpeg_audio['part2_3_length'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 12);
		//            $SideInfoOffset += 12;
		//            $thisfile_mpeg_audio['big_values'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 9);
		//            $SideInfoOffset += 9;
		//            $thisfile_mpeg_audio['global_gain'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 8);
		//            $SideInfoOffset += 8;
		//            if ($thisfile_mpeg_audio['version'] == '1') {
		//                $thisfile_mpeg_audio['scalefac_compress'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 4);
		//                $SideInfoOffset += 4;
		//            } else {
		//                $thisfile_mpeg_audio['scalefac_compress'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 9);
		//                $SideInfoOffset += 9;
		//            }
		//            $thisfile_mpeg_audio['window_switching_flag'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 1);
		//            $SideInfoOffset += 1;
		//
		//            if ($thisfile_mpeg_audio['window_switching_flag'][$granule][$channel] == '1') {
		//
		//                $thisfile_mpeg_audio['block_type'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 2);
		//                $SideInfoOffset += 2;
		//                $thisfile_mpeg_audio['mixed_block_flag'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 1);
		//                $SideInfoOffset += 1;
		//
		//                for ($region = 0; $region < 2; $region++) {
		//                    $thisfile_mpeg_audio['table_select'][$granule][$channel][$region] = substr($SideInfoBitstream, $SideInfoOffset, 5);
		//                    $SideInfoOffset += 5;
		//                }
		//                $thisfile_mpeg_audio['table_select'][$granule][$channel][2] = 0;
		//
		//                for ($window = 0; $window < 3; $window++) {
		//                    $thisfile_mpeg_audio['subblock_gain'][$granule][$channel][$window] = substr($SideInfoBitstream, $SideInfoOffset, 3);
		//                    $SideInfoOffset += 3;
		//                }
		//
		//            } else {
		//
		//                for ($region = 0; $region < 3; $region++) {
		//                    $thisfile_mpeg_audio['table_select'][$granule][$channel][$region] = substr($SideInfoBitstream, $SideInfoOffset, 5);
		//                    $SideInfoOffset += 5;
		//                }
		//
		//                $thisfile_mpeg_audio['region0_count'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 4);
		//                $SideInfoOffset += 4;
		//                $thisfile_mpeg_audio['region1_count'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 3);
		//                $SideInfoOffset += 3;
		//                $thisfile_mpeg_audio['block_type'][$granule][$channel] = 0;
		//            }
		//
		//            if ($thisfile_mpeg_audio['version'] == '1') {
		//                $thisfile_mpeg_audio['preflag'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 1);
		//                $SideInfoOffset += 1;
		//            }
		//            $thisfile_mpeg_audio['scalefac_scale'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 1);
		//            $SideInfoOffset += 1;
		//            $thisfile_mpeg_audio['count1table_select'][$granule][$channel] = substr($SideInfoBitstream, $SideInfoOffset, 1);
		//            $SideInfoOffset += 1;
		//        }
		//    }
		//}

		return true;
	}

	public function RecursiveFrameScanning(&$offset, &$nextframetestoffset, $ScanAsCBR) {
		$info = &$this->getid3->info;
		$firstframetestarray = array('error'=>'', 'warning'=>'', 'avdataend'=>$info['avdataend'], 'avdataoffset'=>$info['avdataoffset']);
		$this->decodeMPEGaudioHeader($offset, $firstframetestarray, false);

		for ($i = 0; $i < GETID3_MP3_VALID_CHECK_FRAMES; $i++) {
			// check next GETID3_MP3_VALID_CHECK_FRAMES frames for validity, to make sure we haven't run across a false synch
			if (($nextframetestoffset + 4) >= $info['avdataend']) {
				// end of file
				return true;
			}

			$nextframetestarray = array('error'=>'', 'warning'=>'', 'avdataend'=>$info['avdataend'], 'avdataoffset'=>$info['avdataoffset']);
			if ($this->decodeMPEGaudioHeader($nextframetestoffset, $nextframetestarray, false)) {
				if ($ScanAsCBR) {
					// force CBR mode, used for trying to pick out invalid audio streams with valid(?) VBR headers, or VBR streams with no VBR header
					if (!isset($nextframetestarray['mpeg']['audio']['bitrate']) || !isset($firstframetestarray['mpeg']['audio']['bitrate']) || ($nextframetestarray['mpeg']['audio']['bitrate'] != $firstframetestarray['mpeg']['audio']['bitrate'])) {
						return false;
					}
				}


				// next frame is OK, get ready to check the one after that
				if (isset($nextframetestarray['mpeg']['audio']['framelength']) && ($nextframetestarray['mpeg']['audio']['framelength'] > 0)) {
					$nextframetestoffset += $nextframetestarray['mpeg']['audio']['framelength'];
				} else {
					$info['error'][] = 'Frame at offset ('.$offset.') is has an invalid frame length.';
					return false;
				}

			} elseif (!empty($firstframetestarray['mpeg']['audio']['framelength']) && (($nextframetestoffset + $firstframetestarray['mpeg']['audio']['framelength']) > $info['avdataend'])) {

				// it's not the end of the file, but there's not enough data left for another frame, so assume it's garbage/padding and return OK
				return true;

			} else {

				// next frame is not valid, note the error and fail, so scanning can contiue for a valid frame sequence
				$info['warning'][] = 'Frame at offset ('.$offset.') is valid, but the next one at ('.$nextframetestoffset.') is not.';

				return false;
			}
		}
		return true;
	}

	public function FreeFormatFrameLength($offset, $deepscan=false) {
		$info = &$this->getid3->info;

		$this->fseek($offset);
		$MPEGaudioData = $this->fread(32768);

		$SyncPattern1 = substr($MPEGaudioData, 0, 4);
		// may be different pattern due to padding
		$SyncPattern2 = $SyncPattern1{0}.$SyncPattern1{1}.chr(ord($SyncPattern1{2}) | 0x02).$SyncPattern1{3};
		if ($SyncPattern2 === $SyncPattern1) {
			$SyncPattern2 = $SyncPattern1{0}.$SyncPattern1{1}.chr(ord($SyncPattern1{2}) & 0xFD).$SyncPattern1{3};
		}

		$framelength = false;
		$framelength1 = strpos($MPEGaudioData, $SyncPattern1, 4);
		$framelength2 = strpos($MPEGaudioData, $SyncPattern2, 4);
		if ($framelength1 > 4) {
			$framelength = $framelength1;
		}
		if (($framelength2 > 4) && ($framelength2 < $framelength1)) {
			$framelength = $framelength2;
		}
		if (!$framelength) {

			// LAME 3.88 has a different value for modeextension on the first frame vs the rest
			$framelength1 = strpos($MPEGaudioData, substr($SyncPattern1, 0, 3), 4);
			$framelength2 = strpos($MPEGaudioData, substr($SyncPattern2, 0, 3), 4);

			if ($framelength1 > 4) {
				$framelength = $framelength1;
			}
			if (($framelength2 > 4) && ($framelength2 < $framelength1)) {
				$framelength = $framelength2;
			}
			if (!$framelength) {
				$info['error'][] = 'Cannot find next free-format synch pattern ('.getid3_lib::PrintHexBytes($SyncPattern1).' or '.getid3_lib::PrintHexBytes($SyncPattern2).') after offset '.$offset;
				return false;
			} else {
				$info['warning'][] = 'ModeExtension varies between first frame and other frames (known free-format issue in LAME 3.88)';
				$info['audio']['codec']   = 'LAME';
				$info['audio']['encoder'] = 'LAME3.88';
				$SyncPattern1 = substr($SyncPattern1, 0, 3);
				$SyncPattern2 = substr($SyncPattern2, 0, 3);
			}
		}

		if ($deepscan) {

			$ActualFrameLengthValues = array();
			$nextoffset = $offset + $framelength;
			while ($nextoffset < ($info['avdataend'] - 6)) {
				$this->fseek($nextoffset - 1);
				$NextSyncPattern = $this->fread(6);
				if ((substr($NextSyncPattern, 1, strlen($SyncPattern1)) == $SyncPattern1) || (substr($NextSyncPattern, 1, strlen($SyncPattern2)) == $SyncPattern2)) {
					// good - found where expected
					$ActualFrameLengthValues[] = $framelength;
				} elseif ((substr($NextSyncPattern, 0, strlen($SyncPattern1)) == $SyncPattern1) || (substr($NextSyncPattern, 0, strlen($SyncPattern2)) == $SyncPattern2)) {
					// ok - found one byte earlier than expected (last frame wasn't padded, first frame was)
					$ActualFrameLengthValues[] = ($framelength - 1);
					$nextoffset--;
				} elseif ((substr($NextSyncPattern, 2, strlen($SyncPattern1)) == $SyncPattern1) || (substr($NextSyncPattern, 2, strlen($SyncPattern2)) == $SyncPattern2)) {
					// ok - found one byte later than expected (last frame was padded, first frame wasn't)
					$ActualFrameLengthValues[] = ($framelength + 1);
					$nextoffset++;
				} else {
					$info['error'][] = 'Did not find expected free-format sync pattern at offset '.$nextoffset;
					return false;
				}
				$nextoffset += $framelength;
			}
			if (count($ActualFrameLengthValues) > 0) {
				$framelength = intval(round(array_sum($ActualFrameLengthValues) / count($ActualFrameLengthValues)));
			}
		}
		return $framelength;
	}

	public function getOnlyMPEGaudioInfoBruteForce() {
		$MPEGaudioHeaderDecodeCache   = array();
		$MPEGaudioHeaderValidCache    = array();
		$MPEGaudioHeaderLengthCache   = array();
		$MPEGaudioVersionLookup       = self::MPEGaudioVersionArray();
		$MPEGaudioLayerLookup         = self::MPEGaudioLayerArray();
		$MPEGaudioBitrateLookup       = self::MPEGaudioBitrateArray();
		$MPEGaudioFrequencyLookup     = self::MPEGaudioFrequencyArray();
		$MPEGaudioChannelModeLookup   = self::MPEGaudioChannelModeArray();
		$MPEGaudioModeExtensionLookup = self::MPEGaudioModeExtensionArray();
		$MPEGaudioEmphasisLookup      = self::MPEGaudioEmphasisArray();
		$LongMPEGversionLookup        = array();
		$LongMPEGlayerLookup          = array();
		$LongMPEGbitrateLookup        = array();
		$LongMPEGpaddingLookup        = array();
		$LongMPEGfrequencyLookup      = array();
		$Distribution['bitrate']      = array();
		$Distribution['frequency']    = array();
		$Distribution['layer']        = array();
		$Distribution['version']      = array();
		$Distribution['padding']      = array();

		$info = &$this->getid3->info;
		$this->fseek($info['avdataoffset']);

		$max_frames_scan = 5000;
		$frames_scanned  = 0;

		$previousvalidframe = $info['avdataoffset'];
		while ($this->ftell() < $info['avdataend']) {
			set_time_limit(30);
			$head4 = $this->fread(4);
			if (strlen($head4) < 4) {
				break;
			}
			if ($head4{0} != "\xFF") {
				for ($i = 1; $i < 4; $i++) {
					if ($head4{$i} == "\xFF") {
						$this->fseek($i - 4, SEEK_CUR);
						continue 2;
					}
				}
				continue;
			}
			if (!isset($MPEGaudioHeaderDecodeCache[$head4])) {
				$MPEGaudioHeaderDecodeCache[$head4] = self::MPEGaudioHeaderDecode($head4);
			}
			if (!isset($MPEGaudioHeaderValidCache[$head4])) {
				$MPEGaudioHeaderValidCache[$head4] = self::MPEGaudioHeaderValid($MPEGaudioHeaderDecodeCache[$head4], false, false);
			}
			if ($MPEGaudioHeaderValidCache[$head4]) {

				if (!isset($MPEGaudioHeaderLengthCache[$head4])) {
					$LongMPEGversionLookup[$head4]   = $MPEGaudioVersionLookup[$MPEGaudioHeaderDecodeCache[$head4]['version']];
					$LongMPEGlayerLookup[$head4]     = $MPEGaudioLayerLookup[$MPEGaudioHeaderDecodeCache[$head4]['layer']];
					$LongMPEGbitrateLookup[$head4]   = $MPEGaudioBitrateLookup[$LongMPEGversionLookup[$head4]][$LongMPEGlayerLookup[$head4]][$MPEGaudioHeaderDecodeCache[$head4]['bitrate']];
					$LongMPEGpaddingLookup[$head4]   = (bool) $MPEGaudioHeaderDecodeCache[$head4]['padding'];
					$LongMPEGfrequencyLookup[$head4] = $MPEGaudioFrequencyLookup[$LongMPEGversionLookup[$head4]][$MPEGaudioHeaderDecodeCache[$head4]['sample_rate']];
					$MPEGaudioHeaderLengthCache[$head4] = self::MPEGaudioFrameLength(
						$LongMPEGbitrateLookup[$head4],
						$LongMPEGversionLookup[$head4],
						$LongMPEGlayerLookup[$head4],
						$LongMPEGpaddingLookup[$head4],
						$LongMPEGfrequencyLookup[$head4]);
				}
				if ($MPEGaudioHeaderLengthCache[$head4] > 4) {
					$WhereWeWere = $this->ftell();
					$this->fseek($MPEGaudioHeaderLengthCache[$head4] - 4, SEEK_CUR);
					$next4 = $this->fread(4);
					if ($next4{0} == "\xFF") {
						if (!isset($MPEGaudioHeaderDecodeCache[$next4])) {
							$MPEGaudioHeaderDecodeCache[$next4] = self::MPEGaudioHeaderDecode($next4);
						}
						if (!isset($MPEGaudioHeaderValidCache[$next4])) {
							$MPEGaudioHeaderValidCache[$next4] = self::MPEGaudioHeaderValid($MPEGaudioHeaderDecodeCache[$next4], false, false);
						}
						if ($MPEGaudioHeaderValidCache[$next4]) {
							$this->fseek(-4, SEEK_CUR);

							getid3_lib::safe_inc($Distribution['bitrate'][$LongMPEGbitrateLookup[$head4]]);
							getid3_lib::safe_inc($Distribution['layer'][$LongMPEGlayerLookup[$head4]]);
							getid3_lib::safe_inc($Distribution['version'][$LongMPEGversionLookup[$head4]]);
							getid3_lib::safe_inc($Distribution['padding'][intval($LongMPEGpaddingLookup[$head4])]);
							getid3_lib::safe_inc($Distribution['frequency'][$LongMPEGfrequencyLookup[$head4]]);
							if ($max_frames_scan && (++$frames_scanned >= $max_frames_scan)) {
								$pct_data_scanned = ($this->ftell() - $info['avdataoffset']) / ($info['avdataend'] - $info['avdataoffset']);
								$info['warning'][] = 'too many MPEG audio frames to scan, only scanned first '.$max_frames_scan.' frames ('.number_format($pct_data_scanned * 100, 1).'% of file) and extrapolated distribution, playtime and bitrate may be incorrect.';
								foreach ($Distribution as $key1 => $value1) {
									foreach ($value1 as $key2 => $value2) {
										$Distribution[$key1][$key2] = round($value2 / $pct_data_scanned);
									}
								}
								break;
							}
							continue;
						}
					}
					unset($next4);
					$this->fseek($WhereWeWere - 3);
				}

			}
		}
		foreach ($Distribution as $key => $value) {
			ksort($Distribution[$key], SORT_NUMERIC);
		}
		ksort($Distribution['version'], SORT_STRING);
		$info['mpeg']['audio']['bitrate_distribution']   = $Distribution['bitrate'];
		$info['mpeg']['audio']['frequency_distribution'] = $Distribution['frequency'];
		$info['mpeg']['audio']['layer_distribution']     = $Distribution['layer'];
		$info['mpeg']['audio']['version_distribution']   = $Distribution['version'];
		$info['mpeg']['audio']['padding_distribution']   = $Distribution['padding'];
		if (count($Distribution['version']) > 1) {
			$info['error'][] = 'Corrupt file - more than one MPEG version detected';
		}
		if (count($Distribution['layer']) > 1) {
			$info['error'][] = 'Corrupt file - more than one MPEG layer detected';
		}
		if (count($Distribution['frequency']) > 1) {
			$info['error'][] = 'Corrupt file - more than one MPEG sample rate detected';
		}


		$bittotal = 0;
		foreach ($Distribution['bitrate'] as $bitratevalue => $bitratecount) {
			if ($bitratevalue != 'free') {
				$bittotal += ($bitratevalue * $bitratecount);
			}
		}
		$info['mpeg']['audio']['frame_count']  = array_sum($Distribution['bitrate']);
		if ($info['mpeg']['audio']['frame_count'] == 0) {
			$info['error'][] = 'no MPEG audio frames found';
			return false;
		}
		$info['mpeg']['audio']['bitrate']      = ($bittotal / $info['mpeg']['audio']['frame_count']);
		$info['mpeg']['audio']['bitrate_mode'] = ((count($Distribution['bitrate']) > 0) ? 'vbr' : 'cbr');
		$info['mpeg']['audio']['sample_rate']  = getid3_lib::array_max($Distribution['frequency'], true);

		$info['audio']['bitrate']      = $info['mpeg']['audio']['bitrate'];
		$info['audio']['bitrate_mode'] = $info['mpeg']['audio']['bitrate_mode'];
		$info['audio']['sample_rate']  = $info['mpeg']['audio']['sample_rate'];
		$info['audio']['dataformat']   = 'mp'.getid3_lib::array_max($Distribution['layer'], true);
		$info['fileformat']            = $info['audio']['dataformat'];

		return true;
	}


	public function getOnlyMPEGaudioInfo($avdataoffset, $BitrateHistogram=false) {
		// looks for synch, decodes MPEG audio header

		$info = &$this->getid3->info;

		static $MPEGaudioVersionLookup;
		static $MPEGaudioLayerLookup;
		static $MPEGaudioBitrateLookup;
		if (empty($MPEGaudioVersionLookup)) {
		   $MPEGaudioVersionLookup = self::MPEGaudioVersionArray();
		   $MPEGaudioLayerLookup   = self::MPEGaudioLayerArray();
		   $MPEGaudioBitrateLookup = self::MPEGaudioBitrateArray();

		}

		$this->fseek($avdataoffset);
		$sync_seek_buffer_size = min(128 * 1024, $info['avdataend'] - $avdataoffset);
		if ($sync_seek_buffer_size <= 0) {
			$info['error'][] = 'Invalid $sync_seek_buffer_size at offset '.$avdataoffset;
			return false;
		}
		$header = $this->fread($sync_seek_buffer_size);
		$sync_seek_buffer_size = strlen($header);
		$SynchSeekOffset = 0;
		while ($SynchSeekOffset < $sync_seek_buffer_size) {
			if ((($avdataoffset + $SynchSeekOffset)  < $info['avdataend']) && !feof($this->getid3->fp)) {

				if ($SynchSeekOffset > $sync_seek_buffer_size) {
					// if a synch's not found within the first 128k bytes, then give up
					$info['error'][] = 'Could not find valid MPEG audio synch within the first '.round($sync_seek_buffer_size / 1024).'kB';
					if (isset($info['audio']['bitrate'])) {
						unset($info['audio']['bitrate']);
					}
					if (isset($info['mpeg']['audio'])) {
						unset($info['mpeg']['audio']);
					}
					if (empty($info['mpeg'])) {
						unset($info['mpeg']);
					}
					return false;

				} elseif (feof($this->getid3->fp)) {

					$info['error'][] = 'Could not find valid MPEG audio synch before end of file';
					if (isset($info['audio']['bitrate'])) {
						unset($info['audio']['bitrate']);
					}
					if (isset($info['mpeg']['audio'])) {
						unset($info['mpeg']['audio']);
					}
					if (isset($info['mpeg']) && (!is_array($info['mpeg']) || (count($info['mpeg']) == 0))) {
						unset($info['mpeg']);
					}
					return false;
				}
			}

			if (($SynchSeekOffset + 1) >= strlen($header)) {
				$info['error'][] = 'Could not find valid MPEG synch before end of file';
				return false;
			}

			if (($header{$SynchSeekOffset} == "\xFF") && ($header{($SynchSeekOffset + 1)} > "\xE0")) { // synch detected
				if (!isset($FirstFrameThisfileInfo) && !isset($info['mpeg']['audio'])) {
					$FirstFrameThisfileInfo = $info;
					$FirstFrameAVDataOffset = $avdataoffset + $SynchSeekOffset;
					if (!$this->decodeMPEGaudioHeader($FirstFrameAVDataOffset, $FirstFrameThisfileInfo, false)) {
						// if this is the first valid MPEG-audio frame, save it in case it's a VBR header frame and there's
						// garbage between this frame and a valid sequence of MPEG-audio frames, to be restored below
						unset($FirstFrameThisfileInfo);
					}
				}

				$dummy = $info; // only overwrite real data if valid header found
				if ($this->decodeMPEGaudioHeader($avdataoffset + $SynchSeekOffset, $dummy, true)) {
					$info = $dummy;
					$info['avdataoffset'] = $avdataoffset + $SynchSeekOffset;
					switch (isset($info['fileformat']) ? $info['fileformat'] : '') {
						case '':
						case 'id3':
						case 'ape':
						case 'mp3':
							$info['fileformat']          = 'mp3';
							$info['audio']['dataformat'] = 'mp3';
							break;
					}
					if (isset($FirstFrameThisfileInfo['mpeg']['audio']['bitrate_mode']) && ($FirstFrameThisfileInfo['mpeg']['audio']['bitrate_mode'] == 'vbr')) {
						if (!(abs($info['audio']['bitrate'] - $FirstFrameThisfileInfo['audio']['bitrate']) <= 1)) {
							// If there is garbage data between a valid VBR header frame and a sequence
							// of valid MPEG-audio frames the VBR data is no longer discarded.
							$info = $FirstFrameThisfileInfo;
							$info['avdataoffset']        = $FirstFrameAVDataOffset;
							$info['fileformat']          = 'mp3';
							$info['audio']['dataformat'] = 'mp3';
							$dummy                       = $info;
							unset($dummy['mpeg']['audio']);
							$GarbageOffsetStart = $FirstFrameAVDataOffset + $FirstFrameThisfileInfo['mpeg']['audio']['framelength'];
							$GarbageOffsetEnd   = $avdataoffset + $SynchSeekOffset;
							if ($this->decodeMPEGaudioHeader($GarbageOffsetEnd, $dummy, true, true)) {
								$info = $dummy;
								$info['avdataoffset'] = $GarbageOffsetEnd;
								$info['warning'][] = 'apparently-valid VBR header not used because could not find '.GETID3_MP3_VALID_CHECK_FRAMES.' consecutive MPEG-audio frames immediately after VBR header (garbage data for '.($GarbageOffsetEnd - $GarbageOffsetStart).' bytes between '.$GarbageOffsetStart.' and '.$GarbageOffsetEnd.'), but did find valid CBR stream starting at '.$GarbageOffsetEnd;
							} else {
								$info['warning'][] = 'using data from VBR header even though could not find '.GETID3_MP3_VALID_CHECK_FRAMES.' consecutive MPEG-audio frames immediately after VBR header (garbage data for '.($GarbageOffsetEnd - $GarbageOffsetStart).' bytes between '.$GarbageOffsetStart.' and '.$GarbageOffsetEnd.')';
							}
						}
					}
					if (isset($info['mpeg']['audio']['bitrate_mode']) && ($info['mpeg']['audio']['bitrate_mode'] == 'vbr') && !isset($info['mpeg']['audio']['VBR_method'])) {
						// VBR file with no VBR header
						$BitrateHistogram = true;
					}

					if ($BitrateHistogram) {

						$info['mpeg']['audio']['stereo_distribution']  = array('stereo'=>0, 'joint stereo'=>0, 'dual channel'=>0, 'mono'=>0);
						$info['mpeg']['audio']['version_distribution'] = array('1'=>0, '2'=>0, '2.5'=>0);

						if ($info['mpeg']['audio']['version'] == '1') {
							if ($info['mpeg']['audio']['layer'] == 3) {
								$info['mpeg']['audio']['bitrate_distribution'] = array('free'=>0, 32000=>0, 40000=>0, 48000=>0, 56000=>0, 64000=>0, 80000=>0, 96000=>0, 112000=>0, 128000=>0, 160000=>0, 192000=>0, 224000=>0, 256000=>0, 320000=>0);
							} elseif ($info['mpeg']['audio']['layer'] == 2) {
								$info['mpeg']['audio']['bitrate_distribution'] = array('free'=>0, 32000=>0, 48000=>0, 56000=>0, 64000=>0, 80000=>0, 96000=>0, 112000=>0, 128000=>0, 160000=>0, 192000=>0, 224000=>0, 256000=>0, 320000=>0, 384000=>0);
							} elseif ($info['mpeg']['audio']['layer'] == 1) {
								$info['mpeg']['audio']['bitrate_distribution'] = array('free'=>0, 32000=>0, 64000=>0, 96000=>0, 128000=>0, 160000=>0, 192000=>0, 224000=>0, 256000=>0, 288000=>0, 320000=>0, 352000=>0, 384000=>0, 416000=>0, 448000=>0);
							}
						} elseif ($info['mpeg']['audio']['layer'] == 1) {
							$info['mpeg']['audio']['bitrate_distribution'] = array('free'=>0, 32000=>0, 48000=>0, 56000=>0, 64000=>0, 80000=>0, 96000=>0, 112000=>0, 128000=>0, 144000=>0, 160000=>0, 176000=>0, 192000=>0, 224000=>0, 256000=>0);
						} else {
							$info['mpeg']['audio']['bitrate_distribution'] = array('free'=>0, 8000=>0, 16000=>0, 24000=>0, 32000=>0, 40000=>0, 48000=>0, 56000=>0, 64000=>0, 80000=>0, 96000=>0, 112000=>0, 128000=>0, 144000=>0, 160000=>0);
						}

						$dummy = array('error'=>$info['error'], 'warning'=>$info['warning'], 'avdataend'=>$info['avdataend'], 'avdataoffset'=>$info['avdataoffset']);
						$synchstartoffset = $info['avdataoffset'];
						$this->fseek($info['avdataoffset']);

						// you can play with these numbers:
						$max_frames_scan  = 50000;
						$max_scan_segments = 10;

						// don't play with these numbers:
						$FastMode = false;
						$SynchErrorsFound = 0;
						$frames_scanned   = 0;
						$this_scan_segment = 0;
						$frames_scan_per_segment = ceil($max_frames_scan / $max_scan_segments);
						$pct_data_scanned = 0;
						for ($current_segment = 0; $current_segment < $max_scan_segments; $current_segment++) {
							$frames_scanned_this_segment = 0;
							if ($this->ftell() >= $info['avdataend']) {
								break;
							}
							$scan_start_offset[$current_segment] = max($this->ftell(), $info['avdataoffset'] + round($current_segment * (($info['avdataend'] - $info['avdataoffset']) / $max_scan_segments)));
							if ($current_segment > 0) {
								$this->fseek($scan_start_offset[$current_segment]);
								$buffer_4k = $this->fread(4096);
								for ($j = 0; $j < (strlen($buffer_4k) - 4); $j++) {
									if (($buffer_4k{$j} == "\xFF") && ($buffer_4k{($j + 1)} > "\xE0")) { // synch detected
										if ($this->decodeMPEGaudioHeader($scan_start_offset[$current_segment] + $j, $dummy, false, false, $FastMode)) {
											$calculated_next_offset = $scan_start_offset[$current_segment] + $j + $dummy['mpeg']['audio']['framelength'];
											if ($this->decodeMPEGaudioHeader($calculated_next_offset, $dummy, false, false, $FastMode)) {
												$scan_start_offset[$current_segment] += $j;
												break;
											}
										}
									}
								}
							}
							$synchstartoffset = $scan_start_offset[$current_segment];
							while ($this->decodeMPEGaudioHeader($synchstartoffset, $dummy, false, false, $FastMode)) {
								$FastMode = true;
								$thisframebitrate = $MPEGaudioBitrateLookup[$MPEGaudioVersionLookup[$dummy['mpeg']['audio']['raw']['version']]][$MPEGaudioLayerLookup[$dummy['mpeg']['audio']['raw']['layer']]][$dummy['mpeg']['audio']['raw']['bitrate']];

								if (empty($dummy['mpeg']['audio']['framelength'])) {
									$SynchErrorsFound++;
									$synchstartoffset++;
								} else {
									getid3_lib::safe_inc($info['mpeg']['audio']['bitrate_distribution'][$thisframebitrate]);
									getid3_lib::safe_inc($info['mpeg']['audio']['stereo_distribution'][$dummy['mpeg']['audio']['channelmode']]);
									getid3_lib::safe_inc($info['mpeg']['audio']['version_distribution'][$dummy['mpeg']['audio']['version']]);
									$synchstartoffset += $dummy['mpeg']['audio']['framelength'];
								}
								$frames_scanned++;
								if ($frames_scan_per_segment && (++$frames_scanned_this_segment >= $frames_scan_per_segment)) {
									$this_pct_scanned = ($this->ftell() - $scan_start_offset[$current_segment]) / ($info['avdataend'] - $info['avdataoffset']);
									if (($current_segment == 0) && (($this_pct_scanned * $max_scan_segments) >= 1)) {
										// file likely contains < $max_frames_scan, just scan as one segment
										$max_scan_segments = 1;
										$frames_scan_per_segment = $max_frames_scan;
									} else {
										$pct_data_scanned += $this_pct_scanned;
										break;
									}
								}
							}
						}
						if ($pct_data_scanned > 0) {
							$info['warning'][] = 'too many MPEG audio frames to scan, only scanned '.$frames_scanned.' frames in '.$max_scan_segments.' segments ('.number_format($pct_data_scanned * 100, 1).'% of file) and extrapolated distribution, playtime and bitrate may be incorrect.';
							foreach ($info['mpeg']['audio'] as $key1 => $value1) {
								if (!preg_match('#_distribution$#i', $key1)) {
									continue;
								}
								foreach ($value1 as $key2 => $value2) {
									$info['mpeg']['audio'][$key1][$key2] = round($value2 / $pct_data_scanned);
								}
							}
						}

						if ($SynchErrorsFound > 0) {
							$info['warning'][] = 'Found '.$SynchErrorsFound.' synch errors in histogram analysis';
							//return false;
						}

						$bittotal     = 0;
						$framecounter = 0;
						foreach ($info['mpeg']['audio']['bitrate_distribution'] as $bitratevalue => $bitratecount) {
							$framecounter += $bitratecount;
							if ($bitratevalue != 'free') {
								$bittotal += ($bitratevalue * $bitratecount);
							}
						}
						if ($framecounter == 0) {
							$info['error'][] = 'Corrupt MP3 file: framecounter == zero';
							return false;
						}
						$info['mpeg']['audio']['frame_count'] = getid3_lib::CastAsInt($framecounter);
						$info['mpeg']['audio']['bitrate']     = ($bittotal / $framecounter);

						$info['audio']['bitrate'] = $info['mpeg']['audio']['bitrate'];


						// Definitively set VBR vs CBR, even if the Xing/LAME/VBRI header says differently
						$distinct_bitrates = 0;
						foreach ($info['mpeg']['audio']['bitrate_distribution'] as $bitrate_value => $bitrate_count) {
							if ($bitrate_count > 0) {
								$distinct_bitrates++;
							}
						}
						if ($distinct_bitrates > 1) {
							$info['mpeg']['audio']['bitrate_mode'] = 'vbr';
						} else {
							$info['mpeg']['audio']['bitrate_mode'] = 'cbr';
						}
						$info['audio']['bitrate_mode'] = $info['mpeg']['audio']['bitrate_mode'];

					}

					break; // exit while()
				}
			}

			$SynchSeekOffset++;
			if (($avdataoffset + $SynchSeekOffset) >= $info['avdataend']) {
				// end of file/data

				if (empty($info['mpeg']['audio'])) {

					$info['error'][] = 'could not find valid MPEG synch before end of file';
					if (isset($info['audio']['bitrate'])) {
						unset($info['audio']['bitrate']);
					}
					if (isset($info['mpeg']['audio'])) {
						unset($info['mpeg']['audio']);
					}
					if (isset($info['mpeg']) && (!is_array($info['mpeg']) || empty($info['mpeg']))) {
						unset($info['mpeg']);
					}
					return false;

				}
				break;
			}

		}
		$info['audio']['channels']        = $info['mpeg']['audio']['channels'];
		$info['audio']['channelmode']     = $info['mpeg']['audio']['channelmode'];
		$info['audio']['sample_rate']     = $info['mpeg']['audio']['sample_rate'];
		return true;
	}


	public static function MPEGaudioVersionArray() {
		static $MPEGaudioVersion = array('2.5', false, '2', '1');
		return $MPEGaudioVersion;
	}

	public static function MPEGaudioLayerArray() {
		static $MPEGaudioLayer = array(false, 3, 2, 1);
		return $MPEGaudioLayer;
	}

	public static function MPEGaudioBitrateArray() {
		static $MPEGaudioBitrate;
		if (empty($MPEGaudioBitrate)) {
			$MPEGaudioBitrate = array (
				'1'  =>  array (1 => array('free', 32000, 64000, 96000, 128000, 160000, 192000, 224000, 256000, 288000, 320000, 352000, 384000, 416000, 448000),
								2 => array('free', 32000, 48000, 56000,  64000,  80000,  96000, 112000, 128000, 160000, 192000, 224000, 256000, 320000, 384000),
								3 => array('free', 32000, 40000, 48000,  56000,  64000,  80000,  96000, 112000, 128000, 160000, 192000, 224000, 256000, 320000)
							   ),

				'2'  =>  array (1 => array('free', 32000, 48000, 56000,  64000,  80000,  96000, 112000, 128000, 144000, 160000, 176000, 192000, 224000, 256000),
								2 => array('free',  8000, 16000, 24000,  32000,  40000,  48000,  56000,  64000,  80000,  96000, 112000, 128000, 144000, 160000),
							   )
			);
			$MPEGaudioBitrate['2'][3] = $MPEGaudioBitrate['2'][2];
			$MPEGaudioBitrate['2.5']  = $MPEGaudioBitrate['2'];
		}
		return $MPEGaudioBitrate;
	}

	public static function MPEGaudioFrequencyArray() {
		static $MPEGaudioFrequency;
		if (empty($MPEGaudioFrequency)) {
			$MPEGaudioFrequency = array (
				'1'   => array(44100, 48000, 32000),
				'2'   => array(22050, 24000, 16000),
				'2.5' => array(11025, 12000,  8000)
			);
		}
		return $MPEGaudioFrequency;
	}

	public static function MPEGaudioChannelModeArray() {
		static $MPEGaudioChannelMode = array('stereo', 'joint stereo', 'dual channel', 'mono');
		return $MPEGaudioChannelMode;
	}

	public static function MPEGaudioModeExtensionArray() {
		static $MPEGaudioModeExtension;
		if (empty($MPEGaudioModeExtension)) {
			$MPEGaudioModeExtension = array (
				1 => array('4-31', '8-31', '12-31', '16-31'),
				2 => array('4-31', '8-31', '12-31', '16-31'),
				3 => array('', 'IS', 'MS', 'IS+MS')
			);
		}
		return $MPEGaudioModeExtension;
	}

	public static function MPEGaudioEmphasisArray() {
		static $MPEGaudioEmphasis = array('none', '50/15ms', false, 'CCIT J.17');
		return $MPEGaudioEmphasis;
	}

	public static function MPEGaudioHeaderBytesValid($head4, $allowBitrate15=false) {
		return self::MPEGaudioHeaderValid(self::MPEGaudioHeaderDecode($head4), false, $allowBitrate15);
	}

	public static function MPEGaudioHeaderValid($rawarray, $echoerrors=false, $allowBitrate15=false) {
		if (($rawarray['synch'] & 0x0FFE) != 0x0FFE) {
			return false;
		}

		static $MPEGaudioVersionLookup;
		static $MPEGaudioLayerLookup;
		static $MPEGaudioBitrateLookup;
		static $MPEGaudioFrequencyLookup;
		static $MPEGaudioChannelModeLookup;
		static $MPEGaudioModeExtensionLookup;
		static $MPEGaudioEmphasisLookup;
		if (empty($MPEGaudioVersionLookup)) {
			$MPEGaudioVersionLookup       = self::MPEGaudioVersionArray();
			$MPEGaudioLayerLookup         = self::MPEGaudioLayerArray();
			$MPEGaudioBitrateLookup       = self::MPEGaudioBitrateArray();
			$MPEGaudioFrequencyLookup     = self::MPEGaudioFrequencyArray();
			$MPEGaudioChannelModeLookup   = self::MPEGaudioChannelModeArray();
			$MPEGaudioModeExtensionLookup = self::MPEGaudioModeExtensionArray();
			$MPEGaudioEmphasisLookup      = self::MPEGaudioEmphasisArray();
		}

		if (isset($MPEGaudioVersionLookup[$rawarray['version']])) {
			$decodedVersion = $MPEGaudioVersionLookup[$rawarray['version']];
		} else {
			echo ($echoerrors ? "\n".'invalid Version ('.$rawarray['version'].')' : '');
			return false;
		}
		if (isset($MPEGaudioLayerLookup[$rawarray['layer']])) {
			$decodedLayer = $MPEGaudioLayerLookup[$rawarray['layer']];
		} else {
			echo ($echoerrors ? "\n".'invalid Layer ('.$rawarray['layer'].')' : '');
			return false;
		}
		if (!isset($MPEGaudioBitrateLookup[$decodedVersion][$decodedLayer][$rawarray['bitrate']])) {
			echo ($echoerrors ? "\n".'invalid Bitrate ('.$rawarray['bitrate'].')' : '');
			if ($rawarray['bitrate'] == 15) {
				// known issue in LAME 3.90 - 3.93.1 where free-format has bitrate ID of 15 instead of 0
				// let it go through here otherwise file will not be identified
				if (!$allowBitrate15) {
					return false;
				}
			} else {
				return false;
			}
		}
		if (!isset($MPEGaudioFrequencyLookup[$decodedVersion][$rawarray['sample_rate']])) {
			echo ($echoerrors ? "\n".'invalid Frequency ('.$rawarray['sample_rate'].')' : '');
			return false;
		}
		if (!isset($MPEGaudioChannelModeLookup[$rawarray['channelmode']])) {
			echo ($echoerrors ? "\n".'invalid ChannelMode ('.$rawarray['channelmode'].')' : '');
			return false;
		}
		if (!isset($MPEGaudioModeExtensionLookup[$decodedLayer][$rawarray['modeextension']])) {
			echo ($echoerrors ? "\n".'invalid Mode Extension ('.$rawarray['modeextension'].')' : '');
			return false;
		}
		if (!isset($MPEGaudioEmphasisLookup[$rawarray['emphasis']])) {
			echo ($echoerrors ? "\n".'invalid Emphasis ('.$rawarray['emphasis'].')' : '');
			return false;
		}
		// These are just either set or not set, you can't mess that up :)
		// $rawarray['protection'];
		// $rawarray['padding'];
		// $rawarray['private'];
		// $rawarray['copyright'];
		// $rawarray['original'];

		return true;
	}

	public static function MPEGaudioHeaderDecode($Header4Bytes) {
		// AAAA AAAA  AAAB BCCD  EEEE FFGH  IIJJ KLMM
		// A - Frame sync (all bits set)
		// B - MPEG Audio version ID
		// C - Layer description
		// D - Protection bit
		// E - Bitrate index
		// F - Sampling rate frequency index
		// G - Padding bit
		// H - Private bit
		// I - Channel Mode
		// J - Mode extension (Only if Joint stereo)
		// K - Copyright
		// L - Original
		// M - Emphasis

		if (strlen($Header4Bytes) != 4) {
			return false;
		}

		$MPEGrawHeader['synch']         = (getid3_lib::BigEndian2Int(substr($Header4Bytes, 0, 2)) & 0xFFE0) >> 4;
		$MPEGrawHeader['version']       = (ord($Header4Bytes{1}) & 0x18) >> 3; //    BB
		$MPEGrawHeader['layer']         = (ord($Header4Bytes{1}) & 0x06) >> 1; //      CC
		$MPEGrawHeader['protection']    = (ord($Header4Bytes{1}) & 0x01);      //        D
		$MPEGrawHeader['bitrate']       = (ord($Header4Bytes{2}) & 0xF0) >> 4; // EEEE
		$MPEGrawHeader['sample_rate']   = (ord($Header4Bytes{2}) & 0x0C) >> 2; //     FF
		$MPEGrawHeader['padding']       = (ord($Header4Bytes{2}) & 0x02) >> 1; //       G
		$MPEGrawHeader['private']       = (ord($Header4Bytes{2}) & 0x01);      //        H
		$MPEGrawHeader['channelmode']   = (ord($Header4Bytes{3}) & 0xC0) >> 6; // II
		$MPEGrawHeader['modeextension'] = (ord($Header4Bytes{3}) & 0x30) >> 4; //   JJ
		$MPEGrawHeader['copyright']     = (ord($Header4Bytes{3}) & 0x08) >> 3; //     K
		$MPEGrawHeader['original']      = (ord($Header4Bytes{3}) & 0x04) >> 2; //      L
		$MPEGrawHeader['emphasis']      = (ord($Header4Bytes{3}) & 0x03);      //       MM

		return $MPEGrawHeader;
	}

	public static function MPEGaudioFrameLength(&$bitrate, &$version, &$layer, $padding, &$samplerate) {
		static $AudioFrameLengthCache = array();

		if (!isset($AudioFrameLengthCache[$bitrate][$version][$layer][$padding][$samplerate])) {
			$AudioFrameLengthCache[$bitrate][$version][$layer][$padding][$samplerate] = false;
			if ($bitrate != 'free') {

				if ($version == '1') {

					if ($layer == '1') {

						// For Layer I slot is 32 bits long
						$FrameLengthCoefficient = 48;
						$SlotLength = 4;

					} else { // Layer 2 / 3

						// for Layer 2 and Layer 3 slot is 8 bits long.
						$FrameLengthCoefficient = 144;
						$SlotLength = 1;

					}

				} else { // MPEG-2 / MPEG-2.5

					if ($layer == '1') {

						// For Layer I slot is 32 bits long
						$FrameLengthCoefficient = 24;
						$SlotLength = 4;

					} elseif ($layer == '2') {

						// for Layer 2 and Layer 3 slot is 8 bits long.
						$FrameLengthCoefficient = 144;
						$SlotLength = 1;

					} else { // layer 3

						// for Layer 2 and Layer 3 slot is 8 bits long.
						$FrameLengthCoefficient = 72;
						$SlotLength = 1;

					}

				}

				// FrameLengthInBytes = ((Coefficient * BitRate) / SampleRate) + Padding
				if ($samplerate > 0) {
					$NewFramelength  = ($FrameLengthCoefficient * $bitrate) / $samplerate;
					$NewFramelength  = floor($NewFramelength / $SlotLength) * $SlotLength; // round to next-lower multiple of SlotLength (1 byte for Layer 2/3, 4 bytes for Layer I)
					if ($padding) {
						$NewFramelength += $SlotLength;
					}
					$AudioFrameLengthCache[$bitrate][$version][$layer][$padding][$samplerate] = (int) $NewFramelength;
				}
			}
		}
		return $AudioFrameLengthCache[$bitrate][$version][$layer][$padding][$samplerate];
	}

	public static function ClosestStandardMP3Bitrate($bit_rate) {
		static $standard_bit_rates = array (320000, 256000, 224000, 192000, 160000, 128000, 112000, 96000, 80000, 64000, 56000, 48000, 40000, 32000, 24000, 16000, 8000);
		static $bit_rate_table = array (0=>'-');
		$round_bit_rate = intval(round($bit_rate, -3));
		if (!isset($bit_rate_table[$round_bit_rate])) {
			if ($round_bit_rate > max($standard_bit_rates)) {
				$bit_rate_table[$round_bit_rate] = round($bit_rate, 2 - strlen($bit_rate));
			} else {
				$bit_rate_table[$round_bit_rate] = max($standard_bit_rates);
				foreach ($standard_bit_rates as $standard_bit_rate) {
					if ($round_bit_rate >= $standard_bit_rate + (($bit_rate_table[$round_bit_rate] - $standard_bit_rate) / 2)) {
						break;
					}
					$bit_rate_table[$round_bit_rate] = $standard_bit_rate;
				}
			}
		}
		return $bit_rate_table[$round_bit_rate];
	}

	public static function XingVBRidOffset($version, $channelmode) {
		static $XingVBRidOffsetCache = array();
		if (empty($XingVBRidOffset)) {
			$XingVBRidOffset = array (
				'1'   => array ('mono'          => 0x15, // 4 + 17 = 21
								'stereo'        => 0x24, // 4 + 32 = 36
								'joint stereo'  => 0x24,
								'dual channel'  => 0x24
							   ),

				'2'   => array ('mono'          => 0x0D, // 4 +  9 = 13
								'stereo'        => 0x15, // 4 + 17 = 21
								'joint stereo'  => 0x15,
								'dual channel'  => 0x15
							   ),

				'2.5' => array ('mono'          => 0x15,
								'stereo'        => 0x15,
								'joint stereo'  => 0x15,
								'dual channel'  => 0x15
							   )
			);
		}
		return $XingVBRidOffset[$version][$channelmode];
	}

	public static function LAMEvbrMethodLookup($VBRmethodID) {
		static $LAMEvbrMethodLookup = array(
			0x00 => 'unknown',
			0x01 => 'cbr',
			0x02 => 'abr',
			0x03 => 'vbr-old / vbr-rh',
			0x04 => 'vbr-new / vbr-mtrh',
			0x05 => 'vbr-mt',
			0x06 => 'vbr (full vbr method 4)',
			0x08 => 'cbr (constant bitrate 2 pass)',
			0x09 => 'abr (2 pass)',
			0x0F => 'reserved'
		);
		return (isset($LAMEvbrMethodLookup[$VBRmethodID]) ? $LAMEvbrMethodLookup[$VBRmethodID] : '');
	}

	public static function LAMEmiscStereoModeLookup($StereoModeID) {
		static $LAMEmiscStereoModeLookup = array(
			0 => 'mono',
			1 => 'stereo',
			2 => 'dual mono',
			3 => 'joint stereo',
			4 => 'forced stereo',
			5 => 'auto',
			6 => 'intensity stereo',
			7 => 'other'
		);
		return (isset($LAMEmiscStereoModeLookup[$StereoModeID]) ? $LAMEmiscStereoModeLookup[$StereoModeID] : '');
	}

	public static function LAMEmiscSourceSampleFrequencyLookup($SourceSampleFrequencyID) {
		static $LAMEmiscSourceSampleFrequencyLookup = array(
			0 => '<= 32 kHz',
			1 => '44.1 kHz',
			2 => '48 kHz',
			3 => '> 48kHz'
		);
		return (isset($LAMEmiscSourceSampleFrequencyLookup[$SourceSampleFrequencyID]) ? $LAMEmiscSourceSampleFrequencyLookup[$SourceSampleFrequencyID] : '');
	}

	public static function LAMEsurroundInfoLookup($SurroundInfoID) {
		static $LAMEsurroundInfoLookup = array(
			0 => 'no surround info',
			1 => 'DPL encoding',
			2 => 'DPL2 encoding',
			3 => 'Ambisonic encoding'
		);
		return (isset($LAMEsurroundInfoLookup[$SurroundInfoID]) ? $LAMEsurroundInfoLookup[$SurroundInfoID] : 'reserved');
	}

	public static function LAMEpresetUsedLookup($LAMEtag) {

		if ($LAMEtag['preset_used_id'] == 0) {
			// no preset used (LAME >=3.93)
			// no preset recorded (LAME <3.93)
			return '';
		}
		$LAMEpresetUsedLookup = array();

		/////  THIS PART CANNOT BE STATIC .
		for ($i = 8; $i <= 320; $i++) {
			switch ($LAMEtag['vbr_method']) {
				case 'cbr':
					$LAMEpresetUsedLookup[$i] = '--alt-preset '.$LAMEtag['vbr_method'].' '.$i;
					break;
				case 'abr':
				default: // other VBR modes shouldn't be here(?)
					$LAMEpresetUsedLookup[$i] = '--alt-preset '.$i;
					break;
			}
		}

		// named old-style presets (studio, phone, voice, etc) are handled in GuessEncoderOptions()

		// named alt-presets
		$LAMEpresetUsedLookup[1000] = '--r3mix';
		$LAMEpresetUsedLookup[1001] = '--alt-preset standard';
		$LAMEpresetUsedLookup[1002] = '--alt-preset extreme';
		$LAMEpresetUsedLookup[1003] = '--alt-preset insane';
		$LAMEpresetUsedLookup[1004] = '--alt-preset fast standard';
		$LAMEpresetUsedLookup[1005] = '--alt-preset fast extreme';
		$LAMEpresetUsedLookup[1006] = '--alt-preset medium';
		$LAMEpresetUsedLookup[1007] = '--alt-preset fast medium';

		// LAME 3.94 additions/changes
		$LAMEpresetUsedLookup[1010] = '--preset portable';                                                           // 3.94a15 Oct 21 2003
		$LAMEpresetUsedLookup[1015] = '--preset radio';                                                              // 3.94a15 Oct 21 2003

		$LAMEpresetUsedLookup[320]  = '--preset insane';                                                             // 3.94a15 Nov 12 2003
		$LAMEpresetUsedLookup[410]  = '-V9';
		$LAMEpresetUsedLookup[420]  = '-V8';
		$LAMEpresetUsedLookup[440]  = '-V6';
		$LAMEpresetUsedLookup[430]  = '--preset radio';                                                              // 3.94a15 Nov 12 2003
		$LAMEpresetUsedLookup[450]  = '--preset '.(($LAMEtag['raw']['vbr_method'] == 4) ? 'fast ' : '').'portable';  // 3.94a15 Nov 12 2003
		$LAMEpresetUsedLookup[460]  = '--preset '.(($LAMEtag['raw']['vbr_method'] == 4) ? 'fast ' : '').'medium';    // 3.94a15 Nov 12 2003
		$LAMEpresetUsedLookup[470]  = '--r3mix';                                                                     // 3.94b1  Dec 18 2003
		$LAMEpresetUsedLookup[480]  = '--preset '.(($LAMEtag['raw']['vbr_method'] == 4) ? 'fast ' : '').'standard';  // 3.94a15 Nov 12 2003
		$LAMEpresetUsedLookup[490]  = '-V1';
		$LAMEpresetUsedLookup[500]  = '--preset '.(($LAMEtag['raw']['vbr_method'] == 4) ? 'fast ' : '').'extreme';   // 3.94a15 Nov 12 2003

		return (isset($LAMEpresetUsedLookup[$LAMEtag['preset_used_id']]) ? $LAMEpresetUsedLookup[$LAMEtag['preset_used_id']] : 'new/unknown preset: '.$LAMEtag['preset_used_id'].' - report to info@getid3.org');
	}

}
