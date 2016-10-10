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
// module.audio-video.ts.php                                   //
// module for analyzing MPEG Transport Stream (.ts) files      //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_ts extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);
		$TSheader = $this->fread(19);
		$magic = "\x47";
		if (substr($TSheader, 0, 1) != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at '.$info['avdataoffset'].', found '.getid3_lib::PrintHexBytes(substr($TSheader, 0, 1)).' instead.';
			return false;
		}
		$info['fileformat'] = 'ts';

		// http://en.wikipedia.org/wiki/.ts

		$offset = 0;
		$info['ts']['packet']['sync'] = getid3_lib::BigEndian2Int(substr($TSheader, $offset, 1)); $offset += 1;
		$pid_flags_raw                = getid3_lib::BigEndian2Int(substr($TSheader, $offset, 2)); $offset += 2;
		$SAC_raw                      = getid3_lib::BigEndian2Int(substr($TSheader, $offset, 1)); $offset += 1;
		$info['ts']['packet']['flags']['transport_error_indicator']    =      (bool) ($pid_flags_raw & 0x8000);      // Set by demodulator if can't correct errors in the stream, to tell the demultiplexer that the packet has an uncorrectable error
		$info['ts']['packet']['flags']['payload_unit_start_indicator'] =      (bool) ($pid_flags_raw & 0x4000);      // 1 means start of PES data or PSI otherwise zero only.
		$info['ts']['packet']['flags']['transport_high_priority']      =      (bool) ($pid_flags_raw & 0x2000);      // 1 means higher priority than other packets with the same PID.
		$info['ts']['packet']['packet_id']                             =             ($pid_flags_raw & 0x1FFF) >> 0;

		$info['ts']['packet']['raw']['scrambling_control']             =                   ($SAC_raw &   0xC0) >> 6;
		$info['ts']['packet']['flags']['adaption_field_exists']        =      (bool)       ($SAC_raw &   0x20);
		$info['ts']['packet']['flags']['payload_exists']               =      (bool)       ($SAC_raw &   0x10);
		$info['ts']['packet']['continuity_counter']                    =                   ($SAC_raw &   0x0F) >> 0; // Incremented only when a payload is present
		$info['ts']['packet']['scrambling_control']                    = $this->TSscramblingControlLookup($info['ts']['packet']['raw']['scrambling_control']);

		if ($info['ts']['packet']['flags']['adaption_field_exists']) {
			$AdaptionField_raw        = getid3_lib::BigEndian2Int(substr($TSheader, $offset, 2)); $offset += 2;
			$info['ts']['packet']['adaption']['field_length']           =        ($AdaptionField_raw & 0xFF00) >> 8;  // Number of bytes in the adaptation field immediately following this byte
			$info['ts']['packet']['adaption']['flags']['discontinuity'] = (bool) ($AdaptionField_raw & 0x0080);       // Set to 1 if current TS packet is in a discontinuity state with respect to either the continuity counter or the program clock reference
			$info['ts']['packet']['adaption']['flags']['random_access'] = (bool) ($AdaptionField_raw & 0x0040);       // Set to 1 if the PES packet in this TS packet starts a video/audio sequence
			$info['ts']['packet']['adaption']['flags']['high_priority'] = (bool) ($AdaptionField_raw & 0x0020);       // 1 = higher priority
			$info['ts']['packet']['adaption']['flags']['pcr']           = (bool) ($AdaptionField_raw & 0x0010);       // 1 means adaptation field does contain a PCR field
			$info['ts']['packet']['adaption']['flags']['opcr']          = (bool) ($AdaptionField_raw & 0x0008);       // 1 means adaptation field does contain an OPCR field
			$info['ts']['packet']['adaption']['flags']['splice_point']  = (bool) ($AdaptionField_raw & 0x0004);       // 1 means presence of splice countdown field in adaptation field
			$info['ts']['packet']['adaption']['flags']['private_data']  = (bool) ($AdaptionField_raw & 0x0002);       // 1 means presence of private data bytes in adaptation field
			$info['ts']['packet']['adaption']['flags']['extension']     = (bool) ($AdaptionField_raw & 0x0001);       // 1 means presence of adaptation field extension
			if ($info['ts']['packet']['adaption']['flags']['pcr']) {
				$info['ts']['packet']['adaption']['raw']['pcr'] = getid3_lib::BigEndian2Int(substr($TSheader, $offset, 6)); $offset += 6;
			}
			if ($info['ts']['packet']['adaption']['flags']['opcr']) {
				$info['ts']['packet']['adaption']['raw']['opcr'] = getid3_lib::BigEndian2Int(substr($TSheader, $offset, 6)); $offset += 6;
			}
		}

$info['error'][] = 'MPEG Transport Stream (.ts) parsing not enabled in this version of getID3() ['.$this->getid3->version().']';
return false;

	}


	public function TSscramblingControlLookup($raw) {
		$TSscramblingControlLookup = array(0x00=>'not scrambled', 0x01=>'reserved', 0x02=>'scrambled, even key', 0x03=>'scrambled, odd key');
		return (isset($TSscramblingControlLookup[$raw]) ? $TSscramblingControlLookup[$raw] : 'invalid');
	}
}
