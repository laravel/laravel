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
// module.audio.lpac.php                                       //
// module for analyzing LPAC Audio files                       //
// dependencies: module.audio-video.riff.php                   //
//                                                            ///
/////////////////////////////////////////////////////////////////

getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.audio-video.riff.php', __FILE__, true);

class getid3_lpac extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);
		$LPACheader = $this->fread(14);
		if (substr($LPACheader, 0, 4) != 'LPAC') {
			$info['error'][] = 'Expected "LPAC" at offset '.$info['avdataoffset'].', found "'.$StreamMarker.'"';
			return false;
		}
		$info['avdataoffset'] += 14;

		$info['fileformat']            = 'lpac';
		$info['audio']['dataformat']   = 'lpac';
		$info['audio']['lossless']     = true;
		$info['audio']['bitrate_mode'] = 'vbr';

		$info['lpac']['file_version'] = getid3_lib::BigEndian2Int(substr($LPACheader,  4, 1));
		$flags['audio_type']                  = getid3_lib::BigEndian2Int(substr($LPACheader,  5, 1));
		$info['lpac']['total_samples']= getid3_lib::BigEndian2Int(substr($LPACheader,  6, 4));
		$flags['parameters']                  = getid3_lib::BigEndian2Int(substr($LPACheader, 10, 4));

		$info['lpac']['flags']['is_wave'] = (bool) ($flags['audio_type'] & 0x40);
		$info['lpac']['flags']['stereo']  = (bool) ($flags['audio_type'] & 0x04);
		$info['lpac']['flags']['24_bit']  = (bool) ($flags['audio_type'] & 0x02);
		$info['lpac']['flags']['16_bit']  = (bool) ($flags['audio_type'] & 0x01);

		if ($info['lpac']['flags']['24_bit'] && $info['lpac']['flags']['16_bit']) {
			$info['warning'][] = '24-bit and 16-bit flags cannot both be set';
		}

		$info['lpac']['flags']['fast_compress']             =  (bool) ($flags['parameters'] & 0x40000000);
		$info['lpac']['flags']['random_access']             =  (bool) ($flags['parameters'] & 0x08000000);
		$info['lpac']['block_length']                       = pow(2, (($flags['parameters'] & 0x07000000) >> 24)) * 256;
		$info['lpac']['flags']['adaptive_prediction_order'] =  (bool) ($flags['parameters'] & 0x00800000);
		$info['lpac']['flags']['adaptive_quantization']     =  (bool) ($flags['parameters'] & 0x00400000);
		$info['lpac']['flags']['joint_stereo']              =  (bool) ($flags['parameters'] & 0x00040000);
		$info['lpac']['quantization']                       =         ($flags['parameters'] & 0x00001F00) >> 8;
		$info['lpac']['max_prediction_order']               =         ($flags['parameters'] & 0x0000003F);

		if ($info['lpac']['flags']['fast_compress'] && ($info['lpac']['max_prediction_order'] != 3)) {
			$info['warning'][] = 'max_prediction_order expected to be "3" if fast_compress is true, actual value is "'.$info['lpac']['max_prediction_order'].'"';
		}
		switch ($info['lpac']['file_version']) {
			case 6:
				if ($info['lpac']['flags']['adaptive_quantization']) {
					$info['warning'][] = 'adaptive_quantization expected to be false in LPAC file stucture v6, actually true';
				}
				if ($info['lpac']['quantization'] != 20) {
					$info['warning'][] = 'Quantization expected to be 20 in LPAC file stucture v6, actually '.$info['lpac']['flags']['Q'];
				}
				break;

			default:
				//$info['warning'][] = 'This version of getID3() ['.$this->getid3->version().'] only supports LPAC file format version 6, this file is version '.$info['lpac']['file_version'].' - please report to info@getid3.org';
				break;
		}

		$getid3_temp = new getID3();
		$getid3_temp->openfile($this->getid3->filename);
		$getid3_temp->info = $info;
		$getid3_riff = new getid3_riff($getid3_temp);
		$getid3_riff->Analyze();
		$info['avdataoffset']                = $getid3_temp->info['avdataoffset'];
		$info['riff']                        = $getid3_temp->info['riff'];
		$info['error']                       = $getid3_temp->info['error'];
		$info['warning']                     = $getid3_temp->info['warning'];
		$info['lpac']['comments']['comment'] = $getid3_temp->info['comments'];
		$info['audio']['sample_rate']        = $getid3_temp->info['audio']['sample_rate'];
		unset($getid3_temp, $getid3_riff);

		$info['audio']['channels']    = ($info['lpac']['flags']['stereo'] ? 2 : 1);

		if ($info['lpac']['flags']['24_bit']) {
			$info['audio']['bits_per_sample'] = $info['riff']['audio'][0]['bits_per_sample'];
		} elseif ($info['lpac']['flags']['16_bit']) {
			$info['audio']['bits_per_sample'] = 16;
		} else {
			$info['audio']['bits_per_sample'] = 8;
		}

		if ($info['lpac']['flags']['fast_compress']) {
			 // fast
			$info['audio']['encoder_options'] = '-1';
		} else {
			switch ($info['lpac']['max_prediction_order']) {
				case 20: // simple
					$info['audio']['encoder_options'] = '-2';
					break;
				case 30: // medium
					$info['audio']['encoder_options'] = '-3';
					break;
				case 40: // high
					$info['audio']['encoder_options'] = '-4';
					break;
				case 60: // extrahigh
					$info['audio']['encoder_options'] = '-5';
					break;
			}
		}

		$info['playtime_seconds'] = $info['lpac']['total_samples'] / $info['audio']['sample_rate'];
		$info['audio']['bitrate'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];

		return true;
	}

}
