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
// module.audio.aa.php                                         //
// module for analyzing Audible Audiobook files                //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_amr extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);
		$AMRheader = $this->fread(6);

		$magic = '#!AMR'."\x0A";
		if (substr($AMRheader, 0, 6) != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes(substr($AMRheader, 0, 6)).'"';
			return false;
		}

		// shortcut
		$info['amr'] = array();
		$thisfile_amr = &$info['amr'];

		$info['fileformat']               = 'amr';
		$info['audio']['dataformat']      = 'amr';
		$info['audio']['bitrate_mode']    = 'vbr';   // within a small predefined range: 4.75kbps to 12.2kbps
		$info['audio']['bits_per_sample'] =    13;   // http://en.wikipedia.org/wiki/Adaptive_Multi-Rate_audio_codec: "Sampling frequency 8 kHz/13-bit (160 samples for 20 ms frames), filtered to 200–3400 Hz"
		$info['audio']['sample_rate']     =  8000;   // http://en.wikipedia.org/wiki/Adaptive_Multi-Rate_audio_codec: "Sampling frequency 8 kHz/13-bit (160 samples for 20 ms frames), filtered to 200–3400 Hz"
		$info['audio']['channels']        =     1;
		$thisfile_amr['frame_mode_count'] = array(0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0);

		$buffer = '';
		do {
			if ((strlen($buffer) < $this->getid3->fread_buffer_size()) && !feof($this->getid3->fp)) {
				$buffer .= $this->fread($this->getid3->fread_buffer_size() * 2);
			}
			$AMR_frame_header = ord(substr($buffer, 0, 1));
			$codec_mode_request = ($AMR_frame_header & 0x78) >> 3; // The 2nd bit through 5th bit (counting the most significant bit as the first bit) comprise the CMR (Codec Mode Request), values 0-7 being valid for AMR. The top bit of the CMR can actually be ignored, though it is used when AMR forms RTP payloads. The lower 3-bits of the header are reserved and are not used. Viewing the header from most significant bit to least significant bit, the encoding is XCCCCXXX, where Xs are reserved (typically 0) and the Cs are the CMR.
			if ($codec_mode_request > 7) {
				$info['error'][] = '';
				break;
			}
			$thisfile_amr['frame_mode_count'][$codec_mode_request]++;
			$buffer = substr($buffer, $this->amr_mode_bytes_per_frame($codec_mode_request));
		} while (strlen($buffer) > 0);

		$info['playtime_seconds'] = array_sum($thisfile_amr['frame_mode_count']) * 0.020; // each frame contain 160 samples and is 20 milliseconds long
		$info['audio']['bitrate'] = (8 * ($info['avdataend'] - $info['avdataoffset'])) / $info['playtime_seconds']; // bitrate could be calculated from average bitrate by distributation of frame types. That would give effective audio bitrate, this gives overall file bitrate which will be a little bit higher since every frame will waste 8 bits for header, plus a few bits for octet padding
		$info['bitrate'] = $info['audio']['bitrate'];

		return true;
	}


	public function amr_mode_bitrate($key) {
		static $amr_mode_bitrate = array(
			0 =>  4750,
			1 =>  5150,
			2 =>  5900,
			3 =>  6700,
			4 =>  7400,
			5 =>  7950,
			6 => 10200,
			7 => 12200,
		);
		return (isset($amr_mode_bitrate[$key]) ? $amr_mode_bitrate[$key] : false);
	}

	public function amr_mode_bytes_per_frame($key) {
		static $amr_mode_bitrate = array(
			0 =>  13, // 1-byte frame header +  95 bits [padded to: 12 bytes] audio data
			1 =>  14, // 1-byte frame header + 103 bits [padded to: 13 bytes] audio data
			2 =>  16, // 1-byte frame header + 118 bits [padded to: 15 bytes] audio data
			3 =>  18, // 1-byte frame header + 134 bits [padded to: 17 bytes] audio data
			4 =>  20, // 1-byte frame header + 148 bits [padded to: 19 bytes] audio data
			5 =>  21, // 1-byte frame header + 159 bits [padded to: 20 bytes] audio data
			6 =>  27, // 1-byte frame header + 204 bits [padded to: 26 bytes] audio data
			7 =>  32, // 1-byte frame header + 244 bits [padded to: 31 bytes] audio data
		);
		return (isset($amr_mode_bitrate[$key]) ? $amr_mode_bitrate[$key] : false);
	}


}
