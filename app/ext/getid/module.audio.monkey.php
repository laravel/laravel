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
// module.audio.monkey.php                                     //
// module for analyzing Monkey's Audio files                   //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_monkey extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		// based loosely on code from TMonkey by Jurgen Faul <jfaulØgmx*de>
		// http://jfaul.de/atl  or  http://j-faul.virtualave.net/atl/atl.html

		$info['fileformat']            = 'mac';
		$info['audio']['dataformat']   = 'mac';
		$info['audio']['bitrate_mode'] = 'vbr';
		$info['audio']['lossless']     = true;

		$info['monkeys_audio']['raw'] = array();
		$thisfile_monkeysaudio                = &$info['monkeys_audio'];
		$thisfile_monkeysaudio_raw            = &$thisfile_monkeysaudio['raw'];

		$this->fseek($info['avdataoffset']);
		$MACheaderData = $this->fread(74);

		$thisfile_monkeysaudio_raw['magic'] = substr($MACheaderData, 0, 4);
		$magic = 'MAC ';
		if ($thisfile_monkeysaudio_raw['magic'] != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($thisfile_monkeysaudio_raw['magic']).'"';
			unset($info['fileformat']);
			return false;
		}
		$thisfile_monkeysaudio_raw['nVersion']             = getid3_lib::LittleEndian2Int(substr($MACheaderData, 4, 2)); // appears to be uint32 in 3.98+

		if ($thisfile_monkeysaudio_raw['nVersion'] < 3980) {
			$thisfile_monkeysaudio_raw['nCompressionLevel']    = getid3_lib::LittleEndian2Int(substr($MACheaderData, 6, 2));
			$thisfile_monkeysaudio_raw['nFormatFlags']         = getid3_lib::LittleEndian2Int(substr($MACheaderData, 8, 2));
			$thisfile_monkeysaudio_raw['nChannels']            = getid3_lib::LittleEndian2Int(substr($MACheaderData, 10, 2));
			$thisfile_monkeysaudio_raw['nSampleRate']          = getid3_lib::LittleEndian2Int(substr($MACheaderData, 12, 4));
			$thisfile_monkeysaudio_raw['nHeaderDataBytes']     = getid3_lib::LittleEndian2Int(substr($MACheaderData, 16, 4));
			$thisfile_monkeysaudio_raw['nWAVTerminatingBytes'] = getid3_lib::LittleEndian2Int(substr($MACheaderData, 20, 4));
			$thisfile_monkeysaudio_raw['nTotalFrames']         = getid3_lib::LittleEndian2Int(substr($MACheaderData, 24, 4));
			$thisfile_monkeysaudio_raw['nFinalFrameSamples']   = getid3_lib::LittleEndian2Int(substr($MACheaderData, 28, 4));
			$thisfile_monkeysaudio_raw['nPeakLevel']           = getid3_lib::LittleEndian2Int(substr($MACheaderData, 32, 4));
			$thisfile_monkeysaudio_raw['nSeekElements']        = getid3_lib::LittleEndian2Int(substr($MACheaderData, 38, 2));
			$offset = 8;
		} else {
			$offset = 8;
			// APE_DESCRIPTOR
			$thisfile_monkeysaudio_raw['nDescriptorBytes']       = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset,  4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nHeaderBytes']           = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset,  4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nSeekTableBytes']        = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset,  4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nHeaderDataBytes']       = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset,  4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nAPEFrameDataBytes']     = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset,  4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nAPEFrameDataBytesHigh'] = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset,  4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nTerminatingDataBytes']  = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset,  4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['cFileMD5']               =                              substr($MACheaderData, $offset, 16);
			$offset += 16;

			// APE_HEADER
			$thisfile_monkeysaudio_raw['nCompressionLevel']    = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 2));
			$offset += 2;
			$thisfile_monkeysaudio_raw['nFormatFlags']         = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 2));
			$offset += 2;
			$thisfile_monkeysaudio_raw['nBlocksPerFrame']      = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nFinalFrameBlocks']    = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nTotalFrames']         = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 4));
			$offset += 4;
			$thisfile_monkeysaudio_raw['nBitsPerSample']       = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 2));
			$offset += 2;
			$thisfile_monkeysaudio_raw['nChannels']            = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 2));
			$offset += 2;
			$thisfile_monkeysaudio_raw['nSampleRate']          = getid3_lib::LittleEndian2Int(substr($MACheaderData, $offset, 4));
			$offset += 4;
		}

		$thisfile_monkeysaudio['flags']['8-bit']         = (bool) ($thisfile_monkeysaudio_raw['nFormatFlags'] & 0x0001);
		$thisfile_monkeysaudio['flags']['crc-32']        = (bool) ($thisfile_monkeysaudio_raw['nFormatFlags'] & 0x0002);
		$thisfile_monkeysaudio['flags']['peak_level']    = (bool) ($thisfile_monkeysaudio_raw['nFormatFlags'] & 0x0004);
		$thisfile_monkeysaudio['flags']['24-bit']        = (bool) ($thisfile_monkeysaudio_raw['nFormatFlags'] & 0x0008);
		$thisfile_monkeysaudio['flags']['seek_elements'] = (bool) ($thisfile_monkeysaudio_raw['nFormatFlags'] & 0x0010);
		$thisfile_monkeysaudio['flags']['no_wav_header'] = (bool) ($thisfile_monkeysaudio_raw['nFormatFlags'] & 0x0020);
		$thisfile_monkeysaudio['version']                = $thisfile_monkeysaudio_raw['nVersion'] / 1000;
		$thisfile_monkeysaudio['compression']            = $this->MonkeyCompressionLevelNameLookup($thisfile_monkeysaudio_raw['nCompressionLevel']);
		if ($thisfile_monkeysaudio_raw['nVersion'] < 3980) {
			$thisfile_monkeysaudio['samples_per_frame']      = $this->MonkeySamplesPerFrame($thisfile_monkeysaudio_raw['nVersion'], $thisfile_monkeysaudio_raw['nCompressionLevel']);
		}
		$thisfile_monkeysaudio['bits_per_sample']        = ($thisfile_monkeysaudio['flags']['24-bit'] ? 24 : ($thisfile_monkeysaudio['flags']['8-bit'] ? 8 : 16));
		$thisfile_monkeysaudio['channels']               = $thisfile_monkeysaudio_raw['nChannels'];
		$info['audio']['channels']               = $thisfile_monkeysaudio['channels'];
		$thisfile_monkeysaudio['sample_rate']            = $thisfile_monkeysaudio_raw['nSampleRate'];
		if ($thisfile_monkeysaudio['sample_rate'] == 0) {
			$info['error'][] = 'Corrupt MAC file: frequency == zero';
			return false;
		}
		$info['audio']['sample_rate']            = $thisfile_monkeysaudio['sample_rate'];
		if ($thisfile_monkeysaudio['flags']['peak_level']) {
			$thisfile_monkeysaudio['peak_level']         = $thisfile_monkeysaudio_raw['nPeakLevel'];
			$thisfile_monkeysaudio['peak_ratio']         = $thisfile_monkeysaudio['peak_level'] / pow(2, $thisfile_monkeysaudio['bits_per_sample'] - 1);
		}
		if ($thisfile_monkeysaudio_raw['nVersion'] >= 3980) {
			$thisfile_monkeysaudio['samples']            = (($thisfile_monkeysaudio_raw['nTotalFrames'] - 1) * $thisfile_monkeysaudio_raw['nBlocksPerFrame']) + $thisfile_monkeysaudio_raw['nFinalFrameBlocks'];
		} else {
			$thisfile_monkeysaudio['samples']            = (($thisfile_monkeysaudio_raw['nTotalFrames'] - 1) * $thisfile_monkeysaudio['samples_per_frame']) + $thisfile_monkeysaudio_raw['nFinalFrameSamples'];
		}
		$thisfile_monkeysaudio['playtime']               = $thisfile_monkeysaudio['samples'] / $thisfile_monkeysaudio['sample_rate'];
		if ($thisfile_monkeysaudio['playtime'] == 0) {
			$info['error'][] = 'Corrupt MAC file: playtime == zero';
			return false;
		}
		$info['playtime_seconds']                = $thisfile_monkeysaudio['playtime'];
		$thisfile_monkeysaudio['compressed_size']        = $info['avdataend'] - $info['avdataoffset'];
		$thisfile_monkeysaudio['uncompressed_size']      = $thisfile_monkeysaudio['samples'] * $thisfile_monkeysaudio['channels'] * ($thisfile_monkeysaudio['bits_per_sample'] / 8);
		if ($thisfile_monkeysaudio['uncompressed_size'] == 0) {
			$info['error'][] = 'Corrupt MAC file: uncompressed_size == zero';
			return false;
		}
		$thisfile_monkeysaudio['compression_ratio']      = $thisfile_monkeysaudio['compressed_size'] / ($thisfile_monkeysaudio['uncompressed_size'] + $thisfile_monkeysaudio_raw['nHeaderDataBytes']);
		$thisfile_monkeysaudio['bitrate']                = (($thisfile_monkeysaudio['samples'] * $thisfile_monkeysaudio['channels'] * $thisfile_monkeysaudio['bits_per_sample']) / $thisfile_monkeysaudio['playtime']) * $thisfile_monkeysaudio['compression_ratio'];
		$info['audio']['bitrate']                = $thisfile_monkeysaudio['bitrate'];

		// add size of MAC header to avdataoffset
		if ($thisfile_monkeysaudio_raw['nVersion'] >= 3980) {
			$info['avdataoffset'] += $thisfile_monkeysaudio_raw['nDescriptorBytes'];
			$info['avdataoffset'] += $thisfile_monkeysaudio_raw['nHeaderBytes'];
			$info['avdataoffset'] += $thisfile_monkeysaudio_raw['nSeekTableBytes'];
			$info['avdataoffset'] += $thisfile_monkeysaudio_raw['nHeaderDataBytes'];

			$info['avdataend'] -= $thisfile_monkeysaudio_raw['nTerminatingDataBytes'];
		} else {
			$info['avdataoffset'] += $offset;
		}

		if ($thisfile_monkeysaudio_raw['nVersion'] >= 3980) {
			if ($thisfile_monkeysaudio_raw['cFileMD5'] === str_repeat("\x00", 16)) {
				//$info['warning'][] = 'cFileMD5 is null';
			} else {
				$info['md5_data_source'] = '';
				$md5 = $thisfile_monkeysaudio_raw['cFileMD5'];
				for ($i = 0; $i < strlen($md5); $i++) {
					$info['md5_data_source'] .= str_pad(dechex(ord($md5{$i})), 2, '00', STR_PAD_LEFT);
				}
				if (!preg_match('/^[0-9a-f]{32}$/', $info['md5_data_source'])) {
					unset($info['md5_data_source']);
				}
			}
		}



		$info['audio']['bits_per_sample'] = $thisfile_monkeysaudio['bits_per_sample'];
		$info['audio']['encoder']         = 'MAC v'.number_format($thisfile_monkeysaudio['version'], 2);
		$info['audio']['encoder_options'] = ucfirst($thisfile_monkeysaudio['compression']).' compression';

		return true;
	}

	public function MonkeyCompressionLevelNameLookup($compressionlevel) {
		static $MonkeyCompressionLevelNameLookup = array(
			0     => 'unknown',
			1000  => 'fast',
			2000  => 'normal',
			3000  => 'high',
			4000  => 'extra-high',
			5000  => 'insane'
		);
		return (isset($MonkeyCompressionLevelNameLookup[$compressionlevel]) ? $MonkeyCompressionLevelNameLookup[$compressionlevel] : 'invalid');
	}

	public function MonkeySamplesPerFrame($versionid, $compressionlevel) {
		if ($versionid >= 3950) {
			return 73728 * 4;
		} elseif ($versionid >= 3900) {
			return 73728;
		} elseif (($versionid >= 3800) && ($compressionlevel == 4000)) {
			return 73728;
		} else {
			return 9216;
		}
	}

}
