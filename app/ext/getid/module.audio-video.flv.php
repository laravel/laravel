<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
//                                                             //
//  FLV module by Seth Kaufman <sethØwhirl-i-gig*com>          //
//                                                             //
//  * version 0.1 (26 June 2005)                               //
//                                                             //
//                                                             //
//  * version 0.1.1 (15 July 2005)                             //
//  minor modifications by James Heinrich <info@getid3.org>    //
//                                                             //
//  * version 0.2 (22 February 2006)                           //
//  Support for On2 VP6 codec and meta information             //
//    by Steve Webster <steve.websterØfeaturecreep*com>        //
//                                                             //
//  * version 0.3 (15 June 2006)                               //
//  Modified to not read entire file into memory               //
//    by James Heinrich <info@getid3.org>                      //
//                                                             //
//  * version 0.4 (07 December 2007)                           //
//  Bugfixes for incorrectly parsed FLV dimensions             //
//    and incorrect parsing of onMetaTag                       //
//    by Evgeny Moysevich <moysevichØgmail*com>                //
//                                                             //
//  * version 0.5 (21 May 2009)                                //
//  Fixed parsing of audio tags and added additional codec     //
//    details. The duration is now read from onMetaTag (if     //
//    exists), rather than parsing whole file                  //
//    by Nigel Barnes <ngbarnesØhotmail*com>                   //
//                                                             //
//  * version 0.6 (24 May 2009)                                //
//  Better parsing of files with h264 video                    //
//    by Evgeny Moysevich <moysevichØgmail*com>                //
//                                                             //
//  * version 0.6.1 (30 May 2011)                              //
//    prevent infinite loops in expGolombUe()                  //
//                                                             //
//  * version 0.7.0 (16 Jul 2013)                              //
//  handle GETID3_FLV_VIDEO_VP6FLV_ALPHA                       //
//  improved AVCSequenceParameterSetReader::readData()         //
//    by Xander Schouwerwou <schouwerwouØgmail*com>            //
//                                                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// module.audio-video.flv.php                                  //
// module for analyzing Shockwave Flash Video files            //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////

define('GETID3_FLV_TAG_AUDIO',          8);
define('GETID3_FLV_TAG_VIDEO',          9);
define('GETID3_FLV_TAG_META',          18);

define('GETID3_FLV_VIDEO_H263',         2);
define('GETID3_FLV_VIDEO_SCREEN',       3);
define('GETID3_FLV_VIDEO_VP6FLV',       4);
define('GETID3_FLV_VIDEO_VP6FLV_ALPHA', 5);
define('GETID3_FLV_VIDEO_SCREENV2',     6);
define('GETID3_FLV_VIDEO_H264',         7);

define('H264_AVC_SEQUENCE_HEADER',          0);
define('H264_PROFILE_BASELINE',            66);
define('H264_PROFILE_MAIN',                77);
define('H264_PROFILE_EXTENDED',            88);
define('H264_PROFILE_HIGH',               100);
define('H264_PROFILE_HIGH10',             110);
define('H264_PROFILE_HIGH422',            122);
define('H264_PROFILE_HIGH444',            144);
define('H264_PROFILE_HIGH444_PREDICTIVE', 244);

class getid3_flv extends getid3_handler {

	const magic = 'FLV';

	public $max_frames = 100000; // break out of the loop if too many frames have been scanned; only scan this many if meta frame does not contain useful duration

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);

		$FLVdataLength = $info['avdataend'] - $info['avdataoffset'];
		$FLVheader = $this->fread(5);

		$info['fileformat'] = 'flv';
		$info['flv']['header']['signature'] =                           substr($FLVheader, 0, 3);
		$info['flv']['header']['version']   = getid3_lib::BigEndian2Int(substr($FLVheader, 3, 1));
		$TypeFlags                          = getid3_lib::BigEndian2Int(substr($FLVheader, 4, 1));

		if ($info['flv']['header']['signature'] != self::magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes(self::magic).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($info['flv']['header']['signature']).'"';
			unset($info['flv'], $info['fileformat']);
			return false;
		}

		$info['flv']['header']['hasAudio'] = (bool) ($TypeFlags & 0x04);
		$info['flv']['header']['hasVideo'] = (bool) ($TypeFlags & 0x01);

		$FrameSizeDataLength = getid3_lib::BigEndian2Int($this->fread(4));
		$FLVheaderFrameLength = 9;
		if ($FrameSizeDataLength > $FLVheaderFrameLength) {
			$this->fseek($FrameSizeDataLength - $FLVheaderFrameLength, SEEK_CUR);
		}
		$Duration = 0;
		$found_video = false;
		$found_audio = false;
		$found_meta  = false;
		$found_valid_meta_playtime = false;
		$tagParseCount = 0;
		$info['flv']['framecount'] = array('total'=>0, 'audio'=>0, 'video'=>0);
		$flv_framecount = &$info['flv']['framecount'];
		while ((($this->ftell() + 16) < $info['avdataend']) && (($tagParseCount++ <= $this->max_frames) || !$found_valid_meta_playtime))  {
			$ThisTagHeader = $this->fread(16);

			$PreviousTagLength = getid3_lib::BigEndian2Int(substr($ThisTagHeader,  0, 4));
			$TagType           = getid3_lib::BigEndian2Int(substr($ThisTagHeader,  4, 1));
			$DataLength        = getid3_lib::BigEndian2Int(substr($ThisTagHeader,  5, 3));
			$Timestamp         = getid3_lib::BigEndian2Int(substr($ThisTagHeader,  8, 3));
			$LastHeaderByte    = getid3_lib::BigEndian2Int(substr($ThisTagHeader, 15, 1));
			$NextOffset = $this->ftell() - 1 + $DataLength;
			if ($Timestamp > $Duration) {
				$Duration = $Timestamp;
			}

			$flv_framecount['total']++;
			switch ($TagType) {
				case GETID3_FLV_TAG_AUDIO:
					$flv_framecount['audio']++;
					if (!$found_audio) {
						$found_audio = true;
						$info['flv']['audio']['audioFormat']     = ($LastHeaderByte >> 4) & 0x0F;
						$info['flv']['audio']['audioRate']       = ($LastHeaderByte >> 2) & 0x03;
						$info['flv']['audio']['audioSampleSize'] = ($LastHeaderByte >> 1) & 0x01;
						$info['flv']['audio']['audioType']       =  $LastHeaderByte       & 0x01;
					}
					break;

				case GETID3_FLV_TAG_VIDEO:
					$flv_framecount['video']++;
					if (!$found_video) {
						$found_video = true;
						$info['flv']['video']['videoCodec'] = $LastHeaderByte & 0x07;

						$FLVvideoHeader = $this->fread(11);

						if ($info['flv']['video']['videoCodec'] == GETID3_FLV_VIDEO_H264) {
							// this code block contributed by: moysevichØgmail*com

							$AVCPacketType = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 0, 1));
							if ($AVCPacketType == H264_AVC_SEQUENCE_HEADER) {
								//	read AVCDecoderConfigurationRecord
								$configurationVersion       = getid3_lib::BigEndian2Int(substr($FLVvideoHeader,  4, 1));
								$AVCProfileIndication       = getid3_lib::BigEndian2Int(substr($FLVvideoHeader,  5, 1));
								$profile_compatibility      = getid3_lib::BigEndian2Int(substr($FLVvideoHeader,  6, 1));
								$lengthSizeMinusOne         = getid3_lib::BigEndian2Int(substr($FLVvideoHeader,  7, 1));
								$numOfSequenceParameterSets = getid3_lib::BigEndian2Int(substr($FLVvideoHeader,  8, 1));

								if (($numOfSequenceParameterSets & 0x1F) != 0) {
									//	there is at least one SequenceParameterSet
									//	read size of the first SequenceParameterSet
									//$spsSize = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 9, 2));
									$spsSize = getid3_lib::LittleEndian2Int(substr($FLVvideoHeader, 9, 2));
									//	read the first SequenceParameterSet
									$sps = $this->fread($spsSize);
									if (strlen($sps) == $spsSize) {	//	make sure that whole SequenceParameterSet was red
										$spsReader = new AVCSequenceParameterSetReader($sps);
										$spsReader->readData();
										$info['video']['resolution_x'] = $spsReader->getWidth();
										$info['video']['resolution_y'] = $spsReader->getHeight();
									}
								}
							}
							// end: moysevichØgmail*com

						} elseif ($info['flv']['video']['videoCodec'] == GETID3_FLV_VIDEO_H263) {

							$PictureSizeType = (getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 3, 2))) >> 7;
							$PictureSizeType = $PictureSizeType & 0x0007;
							$info['flv']['header']['videoSizeType'] = $PictureSizeType;
							switch ($PictureSizeType) {
								case 0:
									//$PictureSizeEnc = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 5, 2));
									//$PictureSizeEnc <<= 1;
									//$info['video']['resolution_x'] = ($PictureSizeEnc & 0xFF00) >> 8;
									//$PictureSizeEnc = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 6, 2));
									//$PictureSizeEnc <<= 1;
									//$info['video']['resolution_y'] = ($PictureSizeEnc & 0xFF00) >> 8;

									$PictureSizeEnc['x'] = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 4, 2)) >> 7;
									$PictureSizeEnc['y'] = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 5, 2)) >> 7;
									$info['video']['resolution_x'] = $PictureSizeEnc['x'] & 0xFF;
									$info['video']['resolution_y'] = $PictureSizeEnc['y'] & 0xFF;
									break;

								case 1:
									$PictureSizeEnc['x'] = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 4, 3)) >> 7;
									$PictureSizeEnc['y'] = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 6, 3)) >> 7;
									$info['video']['resolution_x'] = $PictureSizeEnc['x'] & 0xFFFF;
									$info['video']['resolution_y'] = $PictureSizeEnc['y'] & 0xFFFF;
									break;

								case 2:
									$info['video']['resolution_x'] = 352;
									$info['video']['resolution_y'] = 288;
									break;

								case 3:
									$info['video']['resolution_x'] = 176;
									$info['video']['resolution_y'] = 144;
									break;

								case 4:
									$info['video']['resolution_x'] = 128;
									$info['video']['resolution_y'] = 96;
									break;

								case 5:
									$info['video']['resolution_x'] = 320;
									$info['video']['resolution_y'] = 240;
									break;

								case 6:
									$info['video']['resolution_x'] = 160;
									$info['video']['resolution_y'] = 120;
									break;

								default:
									$info['video']['resolution_x'] = 0;
									$info['video']['resolution_y'] = 0;
									break;

							}

						} elseif ($info['flv']['video']['videoCodec'] ==  GETID3_FLV_VIDEO_VP6FLV_ALPHA) {

							/* contributed by schouwerwouØgmail*com */
							if (!isset($info['video']['resolution_x'])) { // only when meta data isn't set
								$PictureSizeEnc['x'] = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 6, 2));
								$PictureSizeEnc['y'] = getid3_lib::BigEndian2Int(substr($FLVvideoHeader, 7, 2));
								$info['video']['resolution_x'] = ($PictureSizeEnc['x'] & 0xFF) << 3;
								$info['video']['resolution_y'] = ($PictureSizeEnc['y'] & 0xFF) << 3;
							}
							/* end schouwerwouØgmail*com */

						}
						if (!empty($info['video']['resolution_x']) && !empty($info['video']['resolution_y'])) {
							$info['video']['pixel_aspect_ratio'] = $info['video']['resolution_x'] / $info['video']['resolution_y'];
						}
					}
					break;

				// Meta tag
				case GETID3_FLV_TAG_META:
					if (!$found_meta) {
						$found_meta = true;
						$this->fseek(-1, SEEK_CUR);
						$datachunk = $this->fread($DataLength);
						$AMFstream = new AMFStream($datachunk);
						$reader = new AMFReader($AMFstream);
						$eventName = $reader->readData();
						$info['flv']['meta'][$eventName] = $reader->readData();
						unset($reader);

						$copykeys = array('framerate'=>'frame_rate', 'width'=>'resolution_x', 'height'=>'resolution_y', 'audiodatarate'=>'bitrate', 'videodatarate'=>'bitrate');
						foreach ($copykeys as $sourcekey => $destkey) {
							if (isset($info['flv']['meta']['onMetaData'][$sourcekey])) {
								switch ($sourcekey) {
									case 'width':
									case 'height':
										$info['video'][$destkey] = intval(round($info['flv']['meta']['onMetaData'][$sourcekey]));
										break;
									case 'audiodatarate':
										$info['audio'][$destkey] = getid3_lib::CastAsInt(round($info['flv']['meta']['onMetaData'][$sourcekey] * 1000));
										break;
									case 'videodatarate':
									case 'frame_rate':
									default:
										$info['video'][$destkey] = $info['flv']['meta']['onMetaData'][$sourcekey];
										break;
								}
							}
						}
						if (!empty($info['flv']['meta']['onMetaData']['duration'])) {
							$found_valid_meta_playtime = true;
						}
					}
					break;

				default:
					// noop
					break;
			}
			$this->fseek($NextOffset);
		}

		$info['playtime_seconds'] = $Duration / 1000;
		if ($info['playtime_seconds'] > 0) {
			$info['bitrate'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];
		}

		if ($info['flv']['header']['hasAudio']) {
			$info['audio']['codec']           =   self::audioFormatLookup($info['flv']['audio']['audioFormat']);
			$info['audio']['sample_rate']     =     self::audioRateLookup($info['flv']['audio']['audioRate']);
			$info['audio']['bits_per_sample'] = self::audioBitDepthLookup($info['flv']['audio']['audioSampleSize']);

			$info['audio']['channels']   =  $info['flv']['audio']['audioType'] + 1; // 0=mono,1=stereo
			$info['audio']['lossless']   = ($info['flv']['audio']['audioFormat'] ? false : true); // 0=uncompressed
			$info['audio']['dataformat'] = 'flv';
		}
		if (!empty($info['flv']['header']['hasVideo'])) {
			$info['video']['codec']      = self::videoCodecLookup($info['flv']['video']['videoCodec']);
			$info['video']['dataformat'] = 'flv';
			$info['video']['lossless']   = false;
		}

		// Set information from meta
		if (!empty($info['flv']['meta']['onMetaData']['duration'])) {
			$info['playtime_seconds'] = $info['flv']['meta']['onMetaData']['duration'];
			$info['bitrate'] = (($info['avdataend'] - $info['avdataoffset']) * 8) / $info['playtime_seconds'];
		}
		if (isset($info['flv']['meta']['onMetaData']['audiocodecid'])) {
			$info['audio']['codec'] = self::audioFormatLookup($info['flv']['meta']['onMetaData']['audiocodecid']);
		}
		if (isset($info['flv']['meta']['onMetaData']['videocodecid'])) {
			$info['video']['codec'] = self::videoCodecLookup($info['flv']['meta']['onMetaData']['videocodecid']);
		}
		return true;
	}


	public static function audioFormatLookup($id) {
		static $lookup = array(
			0  => 'Linear PCM, platform endian',
			1  => 'ADPCM',
			2  => 'mp3',
			3  => 'Linear PCM, little endian',
			4  => 'Nellymoser 16kHz mono',
			5  => 'Nellymoser 8kHz mono',
			6  => 'Nellymoser',
			7  => 'G.711A-law logarithmic PCM',
			8  => 'G.711 mu-law logarithmic PCM',
			9  => 'reserved',
			10 => 'AAC',
			11 => 'Speex',
			12 => false, // unknown?
			13 => false, // unknown?
			14 => 'mp3 8kHz',
			15 => 'Device-specific sound',
		);
		return (isset($lookup[$id]) ? $lookup[$id] : false);
	}

	public static function audioRateLookup($id) {
		static $lookup = array(
			0 =>  5500,
			1 => 11025,
			2 => 22050,
			3 => 44100,
		);
		return (isset($lookup[$id]) ? $lookup[$id] : false);
	}

	public static function audioBitDepthLookup($id) {
		static $lookup = array(
			0 =>  8,
			1 => 16,
		);
		return (isset($lookup[$id]) ? $lookup[$id] : false);
	}

	public static function videoCodecLookup($id) {
		static $lookup = array(
			GETID3_FLV_VIDEO_H263         => 'Sorenson H.263',
			GETID3_FLV_VIDEO_SCREEN       => 'Screen video',
			GETID3_FLV_VIDEO_VP6FLV       => 'On2 VP6',
			GETID3_FLV_VIDEO_VP6FLV_ALPHA => 'On2 VP6 with alpha channel',
			GETID3_FLV_VIDEO_SCREENV2     => 'Screen video v2',
			GETID3_FLV_VIDEO_H264         => 'Sorenson H.264',
		);
		return (isset($lookup[$id]) ? $lookup[$id] : false);
	}
}

class AMFStream {
	public $bytes;
	public $pos;

	public function __construct(&$bytes) {
		$this->bytes =& $bytes;
		$this->pos = 0;
	}

	public function readByte() {
		return getid3_lib::BigEndian2Int(substr($this->bytes, $this->pos++, 1));
	}

	public function readInt() {
		return ($this->readByte() << 8) + $this->readByte();
	}

	public function readLong() {
		return ($this->readByte() << 24) + ($this->readByte() << 16) + ($this->readByte() << 8) + $this->readByte();
	}

	public function readDouble() {
		return getid3_lib::BigEndian2Float($this->read(8));
	}

	public function readUTF() {
		$length = $this->readInt();
		return $this->read($length);
	}

	public function readLongUTF() {
		$length = $this->readLong();
		return $this->read($length);
	}

	public function read($length) {
		$val = substr($this->bytes, $this->pos, $length);
		$this->pos += $length;
		return $val;
	}

	public function peekByte() {
		$pos = $this->pos;
		$val = $this->readByte();
		$this->pos = $pos;
		return $val;
	}

	public function peekInt() {
		$pos = $this->pos;
		$val = $this->readInt();
		$this->pos = $pos;
		return $val;
	}

	public function peekLong() {
		$pos = $this->pos;
		$val = $this->readLong();
		$this->pos = $pos;
		return $val;
	}

	public function peekDouble() {
		$pos = $this->pos;
		$val = $this->readDouble();
		$this->pos = $pos;
		return $val;
	}

	public function peekUTF() {
		$pos = $this->pos;
		$val = $this->readUTF();
		$this->pos = $pos;
		return $val;
	}

	public function peekLongUTF() {
		$pos = $this->pos;
		$val = $this->readLongUTF();
		$this->pos = $pos;
		return $val;
	}
}

class AMFReader {
	public $stream;

	public function __construct(&$stream) {
		$this->stream =& $stream;
	}

	public function readData() {
		$value = null;

		$type = $this->stream->readByte();
		switch ($type) {

			// Double
			case 0:
				$value = $this->readDouble();
			break;

			// Boolean
			case 1:
				$value = $this->readBoolean();
				break;

			// String
			case 2:
				$value = $this->readString();
				break;

			// Object
			case 3:
				$value = $this->readObject();
				break;

			// null
			case 6:
				return null;
				break;

			// Mixed array
			case 8:
				$value = $this->readMixedArray();
				break;

			// Array
			case 10:
				$value = $this->readArray();
				break;

			// Date
			case 11:
				$value = $this->readDate();
				break;

			// Long string
			case 13:
				$value = $this->readLongString();
				break;

			// XML (handled as string)
			case 15:
				$value = $this->readXML();
				break;

			// Typed object (handled as object)
			case 16:
				$value = $this->readTypedObject();
				break;

			// Long string
			default:
				$value = '(unknown or unsupported data type)';
			break;
		}

		return $value;
	}

	public function readDouble() {
		return $this->stream->readDouble();
	}

	public function readBoolean() {
		return $this->stream->readByte() == 1;
	}

	public function readString() {
		return $this->stream->readUTF();
	}

	public function readObject() {
		// Get highest numerical index - ignored
//		$highestIndex = $this->stream->readLong();

		$data = array();

		while ($key = $this->stream->readUTF()) {
			$data[$key] = $this->readData();
		}
		// Mixed array record ends with empty string (0x00 0x00) and 0x09
		if (($key == '') && ($this->stream->peekByte() == 0x09)) {
			// Consume byte
			$this->stream->readByte();
		}
		return $data;
	}

	public function readMixedArray() {
		// Get highest numerical index - ignored
		$highestIndex = $this->stream->readLong();

		$data = array();

		while ($key = $this->stream->readUTF()) {
			if (is_numeric($key)) {
				$key = (float) $key;
			}
			$data[$key] = $this->readData();
		}
		// Mixed array record ends with empty string (0x00 0x00) and 0x09
		if (($key == '') && ($this->stream->peekByte() == 0x09)) {
			// Consume byte
			$this->stream->readByte();
		}

		return $data;
	}

	public function readArray() {
		$length = $this->stream->readLong();
		$data = array();

		for ($i = 0; $i < $length; $i++) {
			$data[] = $this->readData();
		}
		return $data;
	}

	public function readDate() {
		$timestamp = $this->stream->readDouble();
		$timezone = $this->stream->readInt();
		return $timestamp;
	}

	public function readLongString() {
		return $this->stream->readLongUTF();
	}

	public function readXML() {
		return $this->stream->readLongUTF();
	}

	public function readTypedObject() {
		$className = $this->stream->readUTF();
		return $this->readObject();
	}
}

class AVCSequenceParameterSetReader {
	public $sps;
	public $start = 0;
	public $currentBytes = 0;
	public $currentBits = 0;
	public $width;
	public $height;

	public function __construct($sps) {
		$this->sps = $sps;
	}

	public function readData() {
		$this->skipBits(8);
		$this->skipBits(8);
		$profile = $this->getBits(8);                               // read profile
		if ($profile > 0) {
			$this->skipBits(8);
			$level_idc = $this->getBits(8);                         // level_idc
			$this->expGolombUe();                                   // seq_parameter_set_id // sps
			$this->expGolombUe();                                   // log2_max_frame_num_minus4
			$picOrderType = $this->expGolombUe();                   // pic_order_cnt_type
			if ($picOrderType == 0) {
				$this->expGolombUe();                               // log2_max_pic_order_cnt_lsb_minus4
			} elseif ($picOrderType == 1) {
				$this->skipBits(1);                                 // delta_pic_order_always_zero_flag
				$this->expGolombSe();                               // offset_for_non_ref_pic
				$this->expGolombSe();                               // offset_for_top_to_bottom_field
				$num_ref_frames_in_pic_order_cnt_cycle = $this->expGolombUe(); // num_ref_frames_in_pic_order_cnt_cycle
				for ($i = 0; $i < $num_ref_frames_in_pic_order_cnt_cycle; $i++) {
					$this->expGolombSe();                           // offset_for_ref_frame[ i ]
				}
			}
			$this->expGolombUe();                                   // num_ref_frames
			$this->skipBits(1);                                     // gaps_in_frame_num_value_allowed_flag
			$pic_width_in_mbs_minus1 = $this->expGolombUe();        // pic_width_in_mbs_minus1
			$pic_height_in_map_units_minus1 = $this->expGolombUe(); // pic_height_in_map_units_minus1

			$frame_mbs_only_flag = $this->getBits(1);               // frame_mbs_only_flag
			if ($frame_mbs_only_flag == 0) {
				$this->skipBits(1);                                 // mb_adaptive_frame_field_flag
			}
			$this->skipBits(1);                                     // direct_8x8_inference_flag
			$frame_cropping_flag = $this->getBits(1);               // frame_cropping_flag

			$frame_crop_left_offset   = 0;
			$frame_crop_right_offset  = 0;
			$frame_crop_top_offset    = 0;
			$frame_crop_bottom_offset = 0;

			if ($frame_cropping_flag) {
				$frame_crop_left_offset   = $this->expGolombUe();   // frame_crop_left_offset
				$frame_crop_right_offset  = $this->expGolombUe();   // frame_crop_right_offset
				$frame_crop_top_offset    = $this->expGolombUe();   // frame_crop_top_offset
				$frame_crop_bottom_offset = $this->expGolombUe();   // frame_crop_bottom_offset
			}
			$this->skipBits(1);                                     // vui_parameters_present_flag
			// etc

			$this->width  = (($pic_width_in_mbs_minus1 + 1) * 16) - ($frame_crop_left_offset * 2) - ($frame_crop_right_offset * 2);
			$this->height = ((2 - $frame_mbs_only_flag) * ($pic_height_in_map_units_minus1 + 1) * 16) - ($frame_crop_top_offset * 2) - ($frame_crop_bottom_offset * 2);
		}
	}

	public function skipBits($bits) {
		$newBits = $this->currentBits + $bits;
		$this->currentBytes += (int)floor($newBits / 8);
		$this->currentBits = $newBits % 8;
	}

	public function getBit() {
		$result = (getid3_lib::BigEndian2Int(substr($this->sps, $this->currentBytes, 1)) >> (7 - $this->currentBits)) & 0x01;
		$this->skipBits(1);
		return $result;
	}

	public function getBits($bits) {
		$result = 0;
		for ($i = 0; $i < $bits; $i++) {
			$result = ($result << 1) + $this->getBit();
		}
		return $result;
	}

	public function expGolombUe() {
		$significantBits = 0;
		$bit = $this->getBit();
		while ($bit == 0) {
			$significantBits++;
			$bit = $this->getBit();

			if ($significantBits > 31) {
				// something is broken, this is an emergency escape to prevent infinite loops
				return 0;
			}
		}
		return (1 << $significantBits) + $this->getBits($significantBits) - 1;
	}

	public function expGolombSe() {
		$result = $this->expGolombUe();
		if (($result & 0x01) == 0) {
			return -($result >> 1);
		} else {
			return ($result + 1) >> 1;
		}
	}

	public function getWidth() {
		return $this->width;
	}

	public function getHeight() {
		return $this->height;
	}
}
