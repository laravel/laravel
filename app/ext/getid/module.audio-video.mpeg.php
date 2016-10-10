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
// module.audio-video.mpeg.php                                 //
// module for analyzing MPEG files                             //
// dependencies: module.audio.mp3.php                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.audio.mp3.php', __FILE__, true);

class getid3_mpeg extends getid3_handler {

	const START_CODE_BASE       = "\x00\x00\x01";
	const VIDEO_PICTURE_START   = "\x00\x00\x01\x00";
	const VIDEO_USER_DATA_START = "\x00\x00\x01\xB2";
	const VIDEO_SEQUENCE_HEADER = "\x00\x00\x01\xB3";
	const VIDEO_SEQUENCE_ERROR  = "\x00\x00\x01\xB4";
	const VIDEO_EXTENSION_START = "\x00\x00\x01\xB5";
	const VIDEO_SEQUENCE_END    = "\x00\x00\x01\xB7";
	const VIDEO_GROUP_START     = "\x00\x00\x01\xB8";
	const AUDIO_START           = "\x00\x00\x01\xC0";


	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'mpeg';
		$this->fseek($info['avdataoffset']);

		$MPEGstreamData = $this->fread($this->getid3->option_fread_buffer_size);
		$MPEGstreamBaseOffset = 0; // how far are we from the beginning of the file data ($info['avdataoffset'])
		$MPEGstreamDataOffset = 0; // how far are we from the beginning of the buffer data (~32kB)

		$StartCodeValue     = false;
		$prevStartCodeValue = false;

		$GOPcounter = -1;
		$FramesByGOP = array();
		$ParsedAVchannels = array();

		do {
//echo $MPEGstreamDataOffset.' vs '.(strlen($MPEGstreamData) - 1024).'<Br>';
			if ($MPEGstreamDataOffset > (strlen($MPEGstreamData) - 16384)) {
				// buffer running low, get more data
//echo 'reading more data<br>';
				$MPEGstreamData .= $this->fread($this->getid3->option_fread_buffer_size);
				if (strlen($MPEGstreamData) > $this->getid3->option_fread_buffer_size) {
					$MPEGstreamData = substr($MPEGstreamData, $MPEGstreamDataOffset);
					$MPEGstreamBaseOffset += $MPEGstreamDataOffset;
					$MPEGstreamDataOffset  = 0;
				}
			}
			if (($StartCodeOffset = strpos($MPEGstreamData, self::START_CODE_BASE, $MPEGstreamDataOffset)) === false) {
//echo 'no more start codes found.<br>';
				break;
			} else {
				$MPEGstreamDataOffset = $StartCodeOffset;
				$prevStartCodeValue = $StartCodeValue;
				$StartCodeValue = ord(substr($MPEGstreamData, $StartCodeOffset + 3, 1));
//echo 'Found "'.strtoupper(dechex($StartCodeValue)).'" at offset '.($MPEGstreamBaseOffset + $StartCodeOffset).' ($MPEGstreamDataOffset = '.$MPEGstreamDataOffset.')<br>';
			}
			$MPEGstreamDataOffset += 4;
			switch ($StartCodeValue) {

				case 0x00: // picture_start_code
					if (!empty($info['mpeg']['video']['bitrate_mode']) && ($info['mpeg']['video']['bitrate_mode'] == 'vbr')) {
						$bitstream = getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 4, 4));
						$bitstreamoffset = 0;

						$PictureHeader = array();

						$PictureHeader['temporal_reference']  = self::readBitsFromStream($bitstream, $bitstreamoffset, 10); // 10-bit unsigned integer associated with each input picture. It is incremented by one, modulo 1024, for each input frame. When a frame is coded as two fields the temporal reference in the picture header of both fields is the same. Following a group start header the temporal reference of the earliest picture (in display order) shall be reset to zero.
						$PictureHeader['picture_coding_type'] = self::readBitsFromStream($bitstream, $bitstreamoffset,  3); //  3 bits for picture_coding_type
						$PictureHeader['vbv_delay']           = self::readBitsFromStream($bitstream, $bitstreamoffset, 16); // 16 bits for vbv_delay
						//... etc

						$FramesByGOP[$GOPcounter][] = $PictureHeader;
					}
					break;

				case 0xB3: // sequence_header_code
					/*
					Note: purposely doing the less-pretty (and probably a bit slower) method of using string of bits rather than bitwise operations.
					      Mostly because PHP 32-bit doesn't handle unsigned integers well for bitwise operation.
					      Also the MPEG stream is designed as a bitstream and often doesn't align nicely with byte boundaries.
					*/
					$info['video']['codec'] = 'MPEG-1'; // will be updated if extension_start_code found

					$bitstream = getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 4, 8));
					$bitstreamoffset = 0;

					$info['mpeg']['video']['raw']['horizontal_size_value']       = self::readBitsFromStream($bitstream, $bitstreamoffset, 12); // 12 bits for horizontal frame size. Note: horizontal_size_extension, if present, will add 2 most-significant bits to this value
					$info['mpeg']['video']['raw']['vertical_size_value']         = self::readBitsFromStream($bitstream, $bitstreamoffset, 12); // 12 bits for vertical frame size.   Note: vertical_size_extension,   if present, will add 2 most-significant bits to this value
					$info['mpeg']['video']['raw']['aspect_ratio_information']    = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); //  4 bits for aspect_ratio_information
					$info['mpeg']['video']['raw']['frame_rate_code']             = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); //  4 bits for Frame Rate id code
					$info['mpeg']['video']['raw']['bitrate']                     = self::readBitsFromStream($bitstream, $bitstreamoffset, 18); // 18 bits for bit_rate_value (18 set bits = VBR, otherwise bitrate = this value * 400)
					$marker_bit                                                  = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // The term "marker_bit" indicates a one bit field in which the value zero is forbidden. These marker bits are introduced at several points in the syntax to avoid start code emulation.
					$info['mpeg']['video']['raw']['vbv_buffer_size']             = self::readBitsFromStream($bitstream, $bitstreamoffset, 10); // 10 bits vbv_buffer_size_value
					$info['mpeg']['video']['raw']['constrained_param_flag']      = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: constrained_param_flag
					$info['mpeg']['video']['raw']['load_intra_quantiser_matrix'] = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: load_intra_quantiser_matrix

					if ($info['mpeg']['video']['raw']['load_intra_quantiser_matrix']) {
						$bitstream .= getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 12, 64));
						for ($i = 0; $i < 64; $i++) {
							$info['mpeg']['video']['raw']['intra_quantiser_matrix'][$i] = self::readBitsFromStream($bitstream, $bitstreamoffset,  8);
						}
					}
					$info['mpeg']['video']['raw']['load_non_intra_quantiser_matrix'] = self::readBitsFromStream($bitstream, $bitstreamoffset,  1);

					if ($info['mpeg']['video']['raw']['load_non_intra_quantiser_matrix']) {
						$bitstream .= getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 12 + ($info['mpeg']['video']['raw']['load_intra_quantiser_matrix'] ? 64 : 0), 64));
						for ($i = 0; $i < 64; $i++) {
							$info['mpeg']['video']['raw']['non_intra_quantiser_matrix'][$i] = self::readBitsFromStream($bitstream, $bitstreamoffset,  8);
						}
					}

					$info['mpeg']['video']['pixel_aspect_ratio']      =     self::videoAspectRatioLookup($info['mpeg']['video']['raw']['aspect_ratio_information']);
					$info['mpeg']['video']['pixel_aspect_ratio_text'] = self::videoAspectRatioTextLookup($info['mpeg']['video']['raw']['aspect_ratio_information']);
					$info['mpeg']['video']['frame_rate']              =       self::videoFramerateLookup($info['mpeg']['video']['raw']['frame_rate_code']);
					if ($info['mpeg']['video']['raw']['bitrate'] == 0x3FFFF) { // 18 set bits = VBR
						//$this->warning('This version of getID3() ['.$this->getid3->version().'] cannot determine average bitrate of VBR MPEG video files');
						$info['mpeg']['video']['bitrate_mode'] = 'vbr';
					} else {
						$info['mpeg']['video']['bitrate']      = $info['mpeg']['video']['raw']['bitrate'] * 400;
						$info['mpeg']['video']['bitrate_mode'] = 'cbr';
						$info['video']['bitrate']              = $info['mpeg']['video']['bitrate'];
					}
					$info['video']['resolution_x']       = $info['mpeg']['video']['raw']['horizontal_size_value'];
					$info['video']['resolution_y']       = $info['mpeg']['video']['raw']['vertical_size_value'];
					$info['video']['frame_rate']         = $info['mpeg']['video']['frame_rate'];
					$info['video']['bitrate_mode']       = $info['mpeg']['video']['bitrate_mode'];
					$info['video']['pixel_aspect_ratio'] = $info['mpeg']['video']['pixel_aspect_ratio'];
					$info['video']['lossless']           = false;
					$info['video']['bits_per_sample']    = 24;
					break;

				case 0xB5: // extension_start_code
					$info['video']['codec'] = 'MPEG-2';

					$bitstream = getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 4, 8)); // 48 bits for Sequence Extension ID; 61 bits for Sequence Display Extension ID; 59 bits for Sequence Scalable Extension ID
					$bitstreamoffset = 0;

					$info['mpeg']['video']['raw']['extension_start_code_identifier'] = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); //  4 bits for extension_start_code_identifier
//echo $info['mpeg']['video']['raw']['extension_start_code_identifier'].'<br>';
					switch ($info['mpeg']['video']['raw']['extension_start_code_identifier']) {
						case  1: // 0001 Sequence Extension ID
							$info['mpeg']['video']['raw']['profile_and_level_indication']    = self::readBitsFromStream($bitstream, $bitstreamoffset,  8); //  8 bits for profile_and_level_indication
							$info['mpeg']['video']['raw']['progressive_sequence']            = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: progressive_sequence
							$info['mpeg']['video']['raw']['chroma_format']                   = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); //  2 bits for chroma_format
							$info['mpeg']['video']['raw']['horizontal_size_extension']       = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); //  2 bits for horizontal_size_extension
							$info['mpeg']['video']['raw']['vertical_size_extension']         = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); //  2 bits for vertical_size_extension
							$info['mpeg']['video']['raw']['bit_rate_extension']              = self::readBitsFromStream($bitstream, $bitstreamoffset, 12); // 12 bits for bit_rate_extension
							$marker_bit                                                      = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // The term "marker_bit" indicates a one bit field in which the value zero is forbidden. These marker bits are introduced at several points in the syntax to avoid start code emulation.
							$info['mpeg']['video']['raw']['vbv_buffer_size_extension']       = self::readBitsFromStream($bitstream, $bitstreamoffset,  8); //  8 bits for vbv_buffer_size_extension
							$info['mpeg']['video']['raw']['low_delay']                       = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: low_delay
							$info['mpeg']['video']['raw']['frame_rate_extension_n']          = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); //  2 bits for frame_rate_extension_n
							$info['mpeg']['video']['raw']['frame_rate_extension_d']          = self::readBitsFromStream($bitstream, $bitstreamoffset,  5); //  5 bits for frame_rate_extension_d

							$info['video']['resolution_x']          = ($info['mpeg']['video']['raw']['horizontal_size_extension'] << 12) | $info['mpeg']['video']['raw']['horizontal_size_value'];
							$info['video']['resolution_y']          = ($info['mpeg']['video']['raw']['vertical_size_extension']   << 12) | $info['mpeg']['video']['raw']['vertical_size_value'];
							$info['video']['interlaced']            = !$info['mpeg']['video']['raw']['progressive_sequence'];
							$info['mpeg']['video']['interlaced']    = !$info['mpeg']['video']['raw']['progressive_sequence'];
							$info['mpeg']['video']['chroma_format'] = self::chromaFormatTextLookup($info['mpeg']['video']['raw']['chroma_format']);
							break;

						case  2: // 0010 Sequence Display Extension ID
							$info['mpeg']['video']['raw']['video_format']                    = self::readBitsFromStream($bitstream, $bitstreamoffset,  3); //  3 bits for video_format
							$info['mpeg']['video']['raw']['colour_description']              = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: colour_description
							if ($info['mpeg']['video']['raw']['colour_description']) {
								$info['mpeg']['video']['raw']['colour_primaries']            = self::readBitsFromStream($bitstream, $bitstreamoffset,  8); //  8 bits for colour_primaries
								$info['mpeg']['video']['raw']['transfer_characteristics']    = self::readBitsFromStream($bitstream, $bitstreamoffset,  8); //  8 bits for transfer_characteristics
								$info['mpeg']['video']['raw']['matrix_coefficients']         = self::readBitsFromStream($bitstream, $bitstreamoffset,  8); //  8 bits for matrix_coefficients
							}
							$info['mpeg']['video']['raw']['display_horizontal_size']         = self::readBitsFromStream($bitstream, $bitstreamoffset, 14); // 14 bits for display_horizontal_size
							$marker_bit                                                      = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // The term "marker_bit" indicates a one bit field in which the value zero is forbidden. These marker bits are introduced at several points in the syntax to avoid start code emulation.
							$info['mpeg']['video']['raw']['display_vertical_size']           = self::readBitsFromStream($bitstream, $bitstreamoffset, 14); // 14 bits for display_vertical_size

							$info['mpeg']['video']['video_format'] = self::videoFormatTextLookup($info['mpeg']['video']['raw']['video_format']);
							break;

						case  3: // 0011 Quant Matrix Extension ID
							break;

						case  5: // 0101 Sequence Scalable Extension ID
							$info['mpeg']['video']['raw']['scalable_mode']                              = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); //  2 bits for scalable_mode
							$info['mpeg']['video']['raw']['layer_id']                                   = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); //  4 bits for layer_id
							if ($info['mpeg']['video']['raw']['scalable_mode'] == 1) { // "spatial scalability"
								$info['mpeg']['video']['raw']['lower_layer_prediction_horizontal_size'] = self::readBitsFromStream($bitstream, $bitstreamoffset, 14); // 14 bits for lower_layer_prediction_horizontal_size
								$marker_bit                                                             = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // The term "marker_bit" indicates a one bit field in which the value zero is forbidden. These marker bits are introduced at several points in the syntax to avoid start code emulation.
								$info['mpeg']['video']['raw']['lower_layer_prediction_vertical_size']   = self::readBitsFromStream($bitstream, $bitstreamoffset, 14); // 14 bits for lower_layer_prediction_vertical_size
								$info['mpeg']['video']['raw']['horizontal_subsampling_factor_m']        = self::readBitsFromStream($bitstream, $bitstreamoffset,  5); //  5 bits for horizontal_subsampling_factor_m
								$info['mpeg']['video']['raw']['horizontal_subsampling_factor_n']        = self::readBitsFromStream($bitstream, $bitstreamoffset,  5); //  5 bits for horizontal_subsampling_factor_n
								$info['mpeg']['video']['raw']['vertical_subsampling_factor_m']          = self::readBitsFromStream($bitstream, $bitstreamoffset,  5); //  5 bits for vertical_subsampling_factor_m
								$info['mpeg']['video']['raw']['vertical_subsampling_factor_n']          = self::readBitsFromStream($bitstream, $bitstreamoffset,  5); //  5 bits for vertical_subsampling_factor_n
							} elseif ($info['mpeg']['video']['raw']['scalable_mode'] == 3) { // "temporal scalability"
								$info['mpeg']['video']['raw']['picture_mux_enable']                     = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: picture_mux_enable
								if ($info['mpeg']['video']['raw']['picture_mux_enable']) {
									$info['mpeg']['video']['raw']['mux_to_progressive_sequence']        = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: mux_to_progressive_sequence
								}
								$info['mpeg']['video']['raw']['picture_mux_order']                      = self::readBitsFromStream($bitstream, $bitstreamoffset,  3); //  3 bits for picture_mux_order
								$info['mpeg']['video']['raw']['picture_mux_factor']                     = self::readBitsFromStream($bitstream, $bitstreamoffset,  3); //  3 bits for picture_mux_factor
							}

							$info['mpeg']['video']['scalable_mode'] = self::scalableModeTextLookup($info['mpeg']['video']['raw']['scalable_mode']);
							break;

						case  7: // 0111 Picture Display Extension ID
							break;

						case  8: // 1000 Picture Coding Extension ID
							$info['mpeg']['video']['raw']['f_code_00']                       = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); // 4 bits for f_code[0][0] (forward horizontal)
							$info['mpeg']['video']['raw']['f_code_01']                       = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); // 4 bits for f_code[0][1] (forward vertical)
							$info['mpeg']['video']['raw']['f_code_10']                       = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); // 4 bits for f_code[1][0] (backward horizontal)
							$info['mpeg']['video']['raw']['f_code_11']                       = self::readBitsFromStream($bitstream, $bitstreamoffset,  4); // 4 bits for f_code[1][1] (backward vertical)
							$info['mpeg']['video']['raw']['intra_dc_precision']              = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); // 2 bits for intra_dc_precision
							$info['mpeg']['video']['raw']['picture_structure']               = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); // 2 bits for picture_structure
							$info['mpeg']['video']['raw']['top_field_first']                 = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: top_field_first
							$info['mpeg']['video']['raw']['frame_pred_frame_dct']            = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: frame_pred_frame_dct
							$info['mpeg']['video']['raw']['concealment_motion_vectors']      = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: concealment_motion_vectors
							$info['mpeg']['video']['raw']['q_scale_type']                    = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: q_scale_type
							$info['mpeg']['video']['raw']['intra_vlc_format']                = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: intra_vlc_format
							$info['mpeg']['video']['raw']['alternate_scan']                  = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: alternate_scan
							$info['mpeg']['video']['raw']['repeat_first_field']              = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: repeat_first_field
							$info['mpeg']['video']['raw']['chroma_420_type']                 = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: chroma_420_type
							$info['mpeg']['video']['raw']['progressive_frame']               = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: progressive_frame
							$info['mpeg']['video']['raw']['composite_display_flag']          = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: composite_display_flag
							if ($info['mpeg']['video']['raw']['composite_display_flag']) {
								$info['mpeg']['video']['raw']['v_axis']                      = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: v_axis
								$info['mpeg']['video']['raw']['field_sequence']              = self::readBitsFromStream($bitstream, $bitstreamoffset,  3); // 3 bits for field_sequence
								$info['mpeg']['video']['raw']['sub_carrier']                 = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // 1 bit flag: sub_carrier
								$info['mpeg']['video']['raw']['burst_amplitude']             = self::readBitsFromStream($bitstream, $bitstreamoffset,  7); // 7 bits for burst_amplitude
								$info['mpeg']['video']['raw']['sub_carrier_phase']           = self::readBitsFromStream($bitstream, $bitstreamoffset,  8); // 8 bits for sub_carrier_phase
							}

							$info['mpeg']['video']['intra_dc_precision_bits'] = $info['mpeg']['video']['raw']['intra_dc_precision'] + 8;
							$info['mpeg']['video']['picture_structure'] = self::pictureStructureTextLookup($info['mpeg']['video']['raw']['picture_structure']);
							break;

						case  9: // 1001 Picture Spatial Scalable Extension ID
							break;
						case 10: // 1010 Picture Temporal Scalable Extension ID
							break;

						default:
							$this->warning('Unexpected $info[mpeg][video][raw][extension_start_code_identifier] value of '.$info['mpeg']['video']['raw']['extension_start_code_identifier']);
							break;
					}
					break;


				case 0xB8: // group_of_pictures_header
					$GOPcounter++;
					if ($info['mpeg']['video']['bitrate_mode'] == 'vbr') {
						$bitstream = getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 4, 4)); // 27 bits needed for group_of_pictures_header
						$bitstreamoffset = 0;

						$GOPheader = array();

						$GOPheader['byte_offset'] = $MPEGstreamBaseOffset + $StartCodeOffset;
						$GOPheader['drop_frame_flag']    = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: drop_frame_flag
						$GOPheader['time_code_hours']    = self::readBitsFromStream($bitstream, $bitstreamoffset,  5); //  5 bits for time_code_hours
						$GOPheader['time_code_minutes']  = self::readBitsFromStream($bitstream, $bitstreamoffset,  6); //  6 bits for time_code_minutes
						$marker_bit                      = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); // The term "marker_bit" indicates a one bit field in which the value zero is forbidden. These marker bits are introduced at several points in the syntax to avoid start code emulation.
						$GOPheader['time_code_seconds']  = self::readBitsFromStream($bitstream, $bitstreamoffset,  6); //  6 bits for time_code_seconds
						$GOPheader['time_code_pictures'] = self::readBitsFromStream($bitstream, $bitstreamoffset,  6); //  6 bits for time_code_pictures
						$GOPheader['closed_gop']         = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: closed_gop
						$GOPheader['broken_link']        = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: broken_link

						$time_code_separator = ($GOPheader['drop_frame_flag'] ? ';' : ':'); // While non-drop time code is displayed with colons separating the digit pairs—"HH:MM:SS:FF"—drop frame is usually represented with a semi-colon (;) or period (.) as the divider between all the digit pairs—"HH;MM;SS;FF", "HH.MM.SS.FF"
						$GOPheader['time_code'] = sprintf('%02d'.$time_code_separator.'%02d'.$time_code_separator.'%02d'.$time_code_separator.'%02d', $GOPheader['time_code_hours'], $GOPheader['time_code_minutes'], $GOPheader['time_code_seconds'], $GOPheader['time_code_pictures']);

						$info['mpeg']['group_of_pictures'][] = $GOPheader;
					}
					break;

				case 0xC0: // audio stream
				case 0xC1: // audio stream
				case 0xC2: // audio stream
				case 0xC3: // audio stream
				case 0xC4: // audio stream
				case 0xC5: // audio stream
				case 0xC6: // audio stream
				case 0xC7: // audio stream
				case 0xC8: // audio stream
				case 0xC9: // audio stream
				case 0xCA: // audio stream
				case 0xCB: // audio stream
				case 0xCC: // audio stream
				case 0xCD: // audio stream
				case 0xCE: // audio stream
				case 0xCF: // audio stream
				case 0xD0: // audio stream
				case 0xD1: // audio stream
				case 0xD2: // audio stream
				case 0xD3: // audio stream
				case 0xD4: // audio stream
				case 0xD5: // audio stream
				case 0xD6: // audio stream
				case 0xD7: // audio stream
				case 0xD8: // audio stream
				case 0xD9: // audio stream
				case 0xDA: // audio stream
				case 0xDB: // audio stream
				case 0xDC: // audio stream
				case 0xDD: // audio stream
				case 0xDE: // audio stream
				case 0xDF: // audio stream
				//case 0xE0: // video stream
				//case 0xE1: // video stream
				//case 0xE2: // video stream
				//case 0xE3: // video stream
				//case 0xE4: // video stream
				//case 0xE5: // video stream
				//case 0xE6: // video stream
				//case 0xE7: // video stream
				//case 0xE8: // video stream
				//case 0xE9: // video stream
				//case 0xEA: // video stream
				//case 0xEB: // video stream
				//case 0xEC: // video stream
				//case 0xED: // video stream
				//case 0xEE: // video stream
				//case 0xEF: // video stream
					if (isset($ParsedAVchannels[$StartCodeValue])) {
						break;
					}
					$ParsedAVchannels[$StartCodeValue] = $StartCodeValue;
					// http://en.wikipedia.org/wiki/Packetized_elementary_stream
					// http://dvd.sourceforge.net/dvdinfo/pes-hdr.html
/*
					$PackedElementaryStream = array();
					if ($StartCodeValue >= 0xE0) {
						$PackedElementaryStream['stream_type'] = 'video';
						$PackedElementaryStream['stream_id']   = $StartCodeValue - 0xE0;
					} else {
						$PackedElementaryStream['stream_type'] = 'audio';
						$PackedElementaryStream['stream_id']   = $StartCodeValue - 0xC0;
					}
					$PackedElementaryStream['packet_length'] = getid3_lib::BigEndian2Int(substr($MPEGstreamData, $StartCodeOffset + 4, 2));

					$bitstream = getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 6, 3)); // more may be needed below
					$bitstreamoffset = 0;

					$PackedElementaryStream['marker_bits']               = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); //  2 bits for marker_bits -- should be "10" = 2
echo 'marker_bits = '.$PackedElementaryStream['marker_bits'].'<br>';
					$PackedElementaryStream['scrambling_control']        = self::readBitsFromStream($bitstream, $bitstreamoffset,  2); //  2 bits for scrambling_control -- 00 implies not scrambled
					$PackedElementaryStream['priority']                  = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: priority
					$PackedElementaryStream['data_alignment_indicator']  = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: data_alignment_indicator -- 1 indicates that the PES packet header is immediately followed by the video start code or audio syncword
					$PackedElementaryStream['copyright']                 = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: copyright -- 1 implies copyrighted
					$PackedElementaryStream['original_or_copy']          = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: original_or_copy -- 1 implies original
					$PackedElementaryStream['pts_flag']                  = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: pts_flag -- Presentation Time Stamp
					$PackedElementaryStream['dts_flag']                  = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: dts_flag -- Decode Time Stamp
					$PackedElementaryStream['escr_flag']                 = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: escr_flag -- Elementary Stream Clock Reference
					$PackedElementaryStream['es_rate_flag']              = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: es_rate_flag -- Elementary Stream [data] Rate
					$PackedElementaryStream['dsm_trick_mode_flag']       = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: dsm_trick_mode_flag -- DSM trick mode - not used by DVD
					$PackedElementaryStream['additional_copy_info_flag'] = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: additional_copy_info_flag
					$PackedElementaryStream['crc_flag']                  = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: crc_flag
					$PackedElementaryStream['extension_flag']            = self::readBitsFromStream($bitstream, $bitstreamoffset,  1); //  1 bit flag: extension_flag
					$PackedElementaryStream['pes_remain_header_length']  = self::readBitsFromStream($bitstream, $bitstreamoffset,  8); //  1 bit flag: priority

					$additional_header_bytes = 0;
					$additional_header_bytes += ($PackedElementaryStream['pts_flag']                  ? 5 : 0);
					$additional_header_bytes += ($PackedElementaryStream['dts_flag']                  ? 5 : 0);
					$additional_header_bytes += ($PackedElementaryStream['escr_flag']                 ? 6 : 0);
					$additional_header_bytes += ($PackedElementaryStream['es_rate_flag']              ? 3 : 0);
					$additional_header_bytes += ($PackedElementaryStream['additional_copy_info_flag'] ? 1 : 0);
					$additional_header_bytes += ($PackedElementaryStream['crc_flag']                  ? 2 : 0);
					$additional_header_bytes += ($PackedElementaryStream['extension_flag']            ? 1 : 0);
$PackedElementaryStream['additional_header_bytes'] = $additional_header_bytes;
					$bitstream .= getid3_lib::BigEndian2Bin(substr($MPEGstreamData, $StartCodeOffset + 9, $additional_header_bytes));

					$info['mpeg']['packed_elementary_streams'][$PackedElementaryStream['stream_type']][$PackedElementaryStream['stream_id']][] = $PackedElementaryStream;
*/
					$getid3_temp = new getID3();
					$getid3_temp->openfile($this->getid3->filename);
					$getid3_temp->info = $info;
					$getid3_mp3 = new getid3_mp3($getid3_temp);
					for ($i = 0; $i <= 7; $i++) {
						// some files have the MPEG-audio header 8 bytes after the end of the $00 $00 $01 $C0 signature, some have it up to 13 bytes (or more?) after
						// I have no idea why or what the difference is, so this is a stupid hack.
						// If anybody has any better idea of what's going on, please let me know - info@getid3.org
						$getid3_temp->info = $info; // only overwrite real data if valid header found
//echo 'audio at? '.($MPEGstreamBaseOffset + $StartCodeOffset + 4 + 8 + $i).'<br>';
						if ($getid3_mp3->decodeMPEGaudioHeader($MPEGstreamBaseOffset + $StartCodeOffset + 4 + 8 + $i, $getid3_temp->info, false)) {
//echo 'yes!<br>';
							$info = $getid3_temp->info;
							$info['audio']['bitrate_mode'] = 'cbr';
							$info['audio']['lossless']     = false;
							break;
						}
					}
					unset($getid3_temp, $getid3_mp3);
					break;

				case 0xBC: // Program Stream Map
				case 0xBD: // Private stream 1 (non MPEG audio, subpictures)
				case 0xBE: // Padding stream
				case 0xBF: // Private stream 2 (navigation data)
				case 0xF0: // ECM stream
				case 0xF1: // EMM stream
				case 0xF2: // DSM-CC stream
				case 0xF3: // ISO/IEC_13522_stream
				case 0xF4: // ITU-I Rec. H.222.1 type A
				case 0xF5: // ITU-I Rec. H.222.1 type B
				case 0xF6: // ITU-I Rec. H.222.1 type C
				case 0xF7: // ITU-I Rec. H.222.1 type D
				case 0xF8: // ITU-I Rec. H.222.1 type E
				case 0xF9: // ancilliary stream
				case 0xFA: // ISO/IEC 14496-1 SL-packtized stream
				case 0xFB: // ISO/IEC 14496-1 FlexMux stream
				case 0xFC: // metadata stream
				case 0xFD: // extended stream ID
				case 0xFE: // reserved data stream
				case 0xFF: // program stream directory
					// ignore
					break;

				default:
					// ignore
					break;
			}
		} while (true);



//		// Temporary hack to account for interleaving overhead:
//		if (!empty($info['video']['bitrate']) && !empty($info['audio']['bitrate'])) {
//			$info['playtime_seconds'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / ($info['video']['bitrate'] + $info['audio']['bitrate']);
//
//			// Interleaved MPEG audio/video files have a certain amount of overhead that varies
//			// by both video and audio bitrates, and not in any sensible, linear/logarithmic pattern
//			// Use interpolated lookup tables to approximately guess how much is overhead, because
//			// playtime is calculated as filesize / total-bitrate
//			$info['playtime_seconds'] *= self::systemNonOverheadPercentage($info['video']['bitrate'], $info['audio']['bitrate']);
//
//			//switch ($info['video']['bitrate']) {
//			//	case('5000000'):
//			//		$multiplier = 0.93292642112380355828048824319889;
//			//		break;
//			//	case('5500000'):
//			//		$multiplier = 0.93582895375200989965359777343219;
//			//		break;
//			//	case('6000000'):
//			//		$multiplier = 0.93796247714820932532911373859139;
//			//		break;
//			//	case('7000000'):
//			//		$multiplier = 0.9413264083635103463010117778776;
//			//		break;
//			//	default:
//			//		$multiplier = 1;
//			//		break;
//			//}
//			//$info['playtime_seconds'] *= $multiplier;
//			//$info['warning'][] = 'Interleaved MPEG audio/video playtime may be inaccurate. With current hack should be within a few seconds of accurate. Report to info@getid3.org if off by more than 10 seconds.';
//			if ($info['video']['bitrate'] < 50000) {
//				$this->warning('Interleaved MPEG audio/video playtime may be slightly inaccurate for video bitrates below 100kbps. Except in extreme low-bitrate situations, error should be less than 1%. Report to info@getid3.org if greater than this.');
//			}
//		}
//
/*
$time_prev = 0;
$byte_prev = 0;
$vbr_bitrates = array();
foreach ($info['mpeg']['group_of_pictures'] as $gopkey => $gopdata) {
	$time_this = ($gopdata['time_code_hours'] * 3600) + ($gopdata['time_code_minutes'] * 60) + $gopdata['time_code_seconds'] + ($gopdata['time_code_seconds'] / 30);
	$byte_this = $gopdata['byte_offset'];
	if ($gopkey > 0) {
		if ($time_this > $time_prev) {
			$bytedelta = $byte_this - $byte_prev;
			$timedelta = $time_this - $time_prev;
			$this_bitrate = ($bytedelta * 8) / $timedelta;
echo $gopkey.': ('.number_format($time_prev, 2).'-'.number_format($time_this, 2).') '.number_format($bytedelta).' bytes over '.number_format($timedelta, 3).' seconds = '.number_format($this_bitrate / 1000, 2).'kbps<br>';
			$time_prev = $time_this;
			$byte_prev = $byte_this;
			$vbr_bitrates[] = $this_bitrate;
		}
	}
}
echo 'average_File_bitrate = '.number_format(array_sum($vbr_bitrates) / count($vbr_bitrates), 1).'<br>';
*/
//echo '<pre>'.print_r($FramesByGOP, true).'</pre>';
		if (!empty($info['mpeg']['video']['bitrate_mode']) && ($info['mpeg']['video']['bitrate_mode'] == 'vbr')) {
			$last_GOP_id = max(array_keys($FramesByGOP));
			$frames_in_last_GOP = count($FramesByGOP[$last_GOP_id]);
			$gopdata = &$info['mpeg']['group_of_pictures'][$last_GOP_id];
			$info['playtime_seconds'] = ($gopdata['time_code_hours'] * 3600) + ($gopdata['time_code_minutes'] * 60) + $gopdata['time_code_seconds'] + (($gopdata['time_code_pictures'] + $frames_in_last_GOP + 1) / $info['mpeg']['video']['frame_rate']);
			if (!isset($info['video']['bitrate'])) {
				$overall_bitrate = ($info['avdataend'] - $info['avdataoffset']) * 8 / $info['playtime_seconds'];
				$info['video']['bitrate'] = $overall_bitrate - (isset($info['audio']['bitrate']) ? $info['audio']['bitrate'] : 0);
			}
			unset($info['mpeg']['group_of_pictures']);
		}

		return true;
	}

	private function readBitsFromStream(&$bitstream, &$bitstreamoffset, $bits_to_read, $return_singlebit_as_boolean=true) {
		$return = bindec(substr($bitstream, $bitstreamoffset, $bits_to_read));
		$bitstreamoffset += $bits_to_read;
		if (($bits_to_read == 1) && $return_singlebit_as_boolean) {
			$return = (bool) $return;
		}
		return $return;
	}


	public static function systemNonOverheadPercentage($VideoBitrate, $AudioBitrate) {
		$OverheadPercentage = 0;

		$AudioBitrate = max(min($AudioBitrate / 1000,   384), 32); // limit to range of 32kbps - 384kbps (should be only legal bitrates, but maybe VBR?)
		$VideoBitrate = max(min($VideoBitrate / 1000, 10000), 10); // limit to range of 10kbps -  10Mbps (beyond that curves flatten anyways, no big loss)


		//OMBB[audiobitrate]              = array(video-10kbps,       video-100kbps,      video-1000kbps,     video-10000kbps)
		$OverheadMultiplierByBitrate[32]  = array(0, 0.9676287944368530, 0.9802276264360310, 0.9844916183244460, 0.9852821845179940);
		$OverheadMultiplierByBitrate[48]  = array(0, 0.9779100089209830, 0.9787770035359320, 0.9846738664076130, 0.9852683013799960);
		$OverheadMultiplierByBitrate[56]  = array(0, 0.9731249855367600, 0.9776624308938040, 0.9832606361852130, 0.9843922606633340);
		$OverheadMultiplierByBitrate[64]  = array(0, 0.9755642683275760, 0.9795256705493390, 0.9836573009193170, 0.9851122539404470);
		$OverheadMultiplierByBitrate[96]  = array(0, 0.9788025247497290, 0.9798553314148700, 0.9822956869792560, 0.9834815119124690);
		$OverheadMultiplierByBitrate[128] = array(0, 0.9816940050925480, 0.9821675936072120, 0.9829756927470870, 0.9839763420152050);
		$OverheadMultiplierByBitrate[160] = array(0, 0.9825894094561180, 0.9820913399073960, 0.9823907143253970, 0.9832821783651570);
		$OverheadMultiplierByBitrate[192] = array(0, 0.9832038474336260, 0.9825731694317960, 0.9821028622712400, 0.9828262076447620);
		$OverheadMultiplierByBitrate[224] = array(0, 0.9836516298538770, 0.9824718601823890, 0.9818302180625380, 0.9823735101626480);
		$OverheadMultiplierByBitrate[256] = array(0, 0.9845863022094920, 0.9837229411967540, 0.9824521662210830, 0.9828645172100790);
		$OverheadMultiplierByBitrate[320] = array(0, 0.9849565280263180, 0.9837683142805110, 0.9822885275960400, 0.9824424382727190);
		$OverheadMultiplierByBitrate[384] = array(0, 0.9856094774357600, 0.9844573394432720, 0.9825970399837330, 0.9824673808303890);

		$BitrateToUseMin = 32;
		$BitrateToUseMax = 32;
		$previousBitrate = 32;
		foreach ($OverheadMultiplierByBitrate as $key => $value) {
			if ($AudioBitrate >= $previousBitrate) {
				$BitrateToUseMin = $previousBitrate;
			}
			if ($AudioBitrate < $key) {
				$BitrateToUseMax = $key;
				break;
			}
			$previousBitrate = $key;
		}
		$FactorA = ($BitrateToUseMax - $AudioBitrate) / ($BitrateToUseMax - $BitrateToUseMin);

		$VideoBitrateLog10 = log10($VideoBitrate);
		$VideoFactorMin1 = $OverheadMultiplierByBitrate[$BitrateToUseMin][floor($VideoBitrateLog10)];
		$VideoFactorMin2 = $OverheadMultiplierByBitrate[$BitrateToUseMax][floor($VideoBitrateLog10)];
		$VideoFactorMax1 = $OverheadMultiplierByBitrate[$BitrateToUseMin][ceil($VideoBitrateLog10)];
		$VideoFactorMax2 = $OverheadMultiplierByBitrate[$BitrateToUseMax][ceil($VideoBitrateLog10)];
		$FactorV = $VideoBitrateLog10 - floor($VideoBitrateLog10);

		$OverheadPercentage  = $VideoFactorMin1 *      $FactorA  *      $FactorV;
		$OverheadPercentage += $VideoFactorMin2 * (1 - $FactorA) *      $FactorV;
		$OverheadPercentage += $VideoFactorMax1 *      $FactorA  * (1 - $FactorV);
		$OverheadPercentage += $VideoFactorMax2 * (1 - $FactorA) * (1 - $FactorV);

		return $OverheadPercentage;
	}


	public static function videoFramerateLookup($rawframerate) {
		$lookup = array(0, 23.976, 24, 25, 29.97, 30, 50, 59.94, 60);
		return (isset($lookup[$rawframerate]) ? (float) $lookup[$rawframerate] : (float) 0);
	}

	public static function videoAspectRatioLookup($rawaspectratio) {
		$lookup = array(0, 1, 0.6735, 0.7031, 0.7615, 0.8055, 0.8437, 0.8935, 0.9157, 0.9815, 1.0255, 1.0695, 1.0950, 1.1575, 1.2015, 0);
		return (isset($lookup[$rawaspectratio]) ? (float) $lookup[$rawaspectratio] : (float) 0);
	}

	public static function videoAspectRatioTextLookup($rawaspectratio) {
		$lookup = array('forbidden', 'square pixels', '0.6735', '16:9, 625 line, PAL', '0.7615', '0.8055', '16:9, 525 line, NTSC', '0.8935', '4:3, 625 line, PAL, CCIR601', '0.9815', '1.0255', '1.0695', '4:3, 525 line, NTSC, CCIR601', '1.1575', '1.2015', 'reserved');
		return (isset($lookup[$rawaspectratio]) ? $lookup[$rawaspectratio] : '');
	}

	public static function videoFormatTextLookup($video_format) {
		// ISO/IEC 13818-2, section 6.3.6, Table 6-6. Meaning of video_format
		$lookup = array('component', 'PAL', 'NTSC', 'SECAM', 'MAC', 'Unspecified video format', 'reserved(6)', 'reserved(7)');
		return (isset($lookup[$video_format]) ? $lookup[$video_format] : '');
	}

	public static function scalableModeTextLookup($scalable_mode) {
		// ISO/IEC 13818-2, section 6.3.8, Table 6-10. Definition of scalable_mode
		$lookup = array('data partitioning', 'spatial scalability', 'SNR scalability', 'temporal scalability');
		return (isset($lookup[$scalable_mode]) ? $lookup[$scalable_mode] : '');
	}

	public static function pictureStructureTextLookup($picture_structure) {
		// ISO/IEC 13818-2, section 6.3.11, Table 6-14 Meaning of picture_structure
		$lookup = array('reserved', 'Top Field', 'Bottom Field', 'Frame picture');
		return (isset($lookup[$picture_structure]) ? $lookup[$picture_structure] : '');
	}

	public static function chromaFormatTextLookup($chroma_format) {
		// ISO/IEC 13818-2, section 6.3.11, Table 6-14 Meaning of picture_structure
		$lookup = array('reserved', '4:2:0', '4:2:2', '4:4:4');
		return (isset($lookup[$chroma_format]) ? $lookup[$chroma_format] : '');
	}

}