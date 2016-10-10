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
// module.audio-video.asf.php                                  //
// module for analyzing ASF, WMA and WMV files                 //
// dependencies: module.audio-video.riff.php                   //
//                                                            ///
/////////////////////////////////////////////////////////////////

getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.audio-video.riff.php', __FILE__, true);

class getid3_asf extends getid3_handler {

	public function __construct(getID3 $getid3) {
		parent::__construct($getid3);  // extends getid3_handler::__construct()

		// initialize all GUID constants
		$GUIDarray = $this->KnownGUIDs();
		foreach ($GUIDarray as $GUIDname => $hexstringvalue) {
			if (!defined($GUIDname)) {
				define($GUIDname, $this->GUIDtoBytestring($hexstringvalue));
			}
		}
	}

	public function Analyze() {
		$info = &$this->getid3->info;

		// Shortcuts
		$thisfile_audio = &$info['audio'];
		$thisfile_video = &$info['video'];
		$info['asf']  = array();
		$thisfile_asf = &$info['asf'];
		$thisfile_asf['comments'] = array();
		$thisfile_asf_comments    = &$thisfile_asf['comments'];
		$thisfile_asf['header_object'] = array();
		$thisfile_asf_headerobject     = &$thisfile_asf['header_object'];


		// ASF structure:
		// * Header Object [required]
		//   * File Properties Object [required]   (global file attributes)
		//   * Stream Properties Object [required] (defines media stream & characteristics)
		//   * Header Extension Object [required]  (additional functionality)
		//   * Content Description Object          (bibliographic information)
		//   * Script Command Object               (commands for during playback)
		//   * Marker Object                       (named jumped points within the file)
		// * Data Object [required]
		//   * Data Packets
		// * Index Object

		// Header Object: (mandatory, one only)
		// Field Name                   Field Type   Size (bits)
		// Object ID                    GUID         128             // GUID for header object - GETID3_ASF_Header_Object
		// Object Size                  QWORD        64              // size of header object, including 30 bytes of Header Object header
		// Number of Header Objects     DWORD        32              // number of objects in header object
		// Reserved1                    BYTE         8               // hardcoded: 0x01
		// Reserved2                    BYTE         8               // hardcoded: 0x02

		$info['fileformat'] = 'asf';

		$this->fseek($info['avdataoffset']);
		$HeaderObjectData = $this->fread(30);

		$thisfile_asf_headerobject['objectid']      = substr($HeaderObjectData, 0, 16);
		$thisfile_asf_headerobject['objectid_guid'] = $this->BytestringToGUID($thisfile_asf_headerobject['objectid']);
		if ($thisfile_asf_headerobject['objectid'] != GETID3_ASF_Header_Object) {
			unset($info['fileformat'], $info['asf']);
			return $this->error('ASF header GUID {'.$this->BytestringToGUID($thisfile_asf_headerobject['objectid']).'} does not match expected "GETID3_ASF_Header_Object" GUID {'.$this->BytestringToGUID(GETID3_ASF_Header_Object).'}');
		}
		$thisfile_asf_headerobject['objectsize']    = getid3_lib::LittleEndian2Int(substr($HeaderObjectData, 16, 8));
		$thisfile_asf_headerobject['headerobjects'] = getid3_lib::LittleEndian2Int(substr($HeaderObjectData, 24, 4));
		$thisfile_asf_headerobject['reserved1']     = getid3_lib::LittleEndian2Int(substr($HeaderObjectData, 28, 1));
		$thisfile_asf_headerobject['reserved2']     = getid3_lib::LittleEndian2Int(substr($HeaderObjectData, 29, 1));

		$NextObjectOffset = $this->ftell();
		$ASFHeaderData = $this->fread($thisfile_asf_headerobject['objectsize'] - 30);
		$offset = 0;

		for ($HeaderObjectsCounter = 0; $HeaderObjectsCounter < $thisfile_asf_headerobject['headerobjects']; $HeaderObjectsCounter++) {
			$NextObjectGUID = substr($ASFHeaderData, $offset, 16);
			$offset += 16;
			$NextObjectGUIDtext = $this->BytestringToGUID($NextObjectGUID);
			$NextObjectSize = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
			$offset += 8;
			switch ($NextObjectGUID) {

				case GETID3_ASF_File_Properties_Object:
					// File Properties Object: (mandatory, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for file properties object - GETID3_ASF_File_Properties_Object
					// Object Size                  QWORD        64              // size of file properties object, including 104 bytes of File Properties Object header
					// File ID                      GUID         128             // unique ID - identical to File ID in Data Object
					// File Size                    QWORD        64              // entire file in bytes. Invalid if Broadcast Flag == 1
					// Creation Date                QWORD        64              // date & time of file creation. Maybe invalid if Broadcast Flag == 1
					// Data Packets Count           QWORD        64              // number of data packets in Data Object. Invalid if Broadcast Flag == 1
					// Play Duration                QWORD        64              // playtime, in 100-nanosecond units. Invalid if Broadcast Flag == 1
					// Send Duration                QWORD        64              // time needed to send file, in 100-nanosecond units. Players can ignore this value. Invalid if Broadcast Flag == 1
					// Preroll                      QWORD        64              // time to buffer data before starting to play file, in 1-millisecond units. If <> 0, PlayDuration and PresentationTime have been offset by this amount
					// Flags                        DWORD        32              //
					// * Broadcast Flag             bits         1  (0x01)       // file is currently being written, some header values are invalid
					// * Seekable Flag              bits         1  (0x02)       // is file seekable
					// * Reserved                   bits         30 (0xFFFFFFFC) // reserved - set to zero
					// Minimum Data Packet Size     DWORD        32              // in bytes. should be same as Maximum Data Packet Size. Invalid if Broadcast Flag == 1
					// Maximum Data Packet Size     DWORD        32              // in bytes. should be same as Minimum Data Packet Size. Invalid if Broadcast Flag == 1
					// Maximum Bitrate              DWORD        32              // maximum instantaneous bitrate in bits per second for entire file, including all data streams and ASF overhead

					// shortcut
					$thisfile_asf['file_properties_object'] = array();
					$thisfile_asf_filepropertiesobject      = &$thisfile_asf['file_properties_object'];

					$thisfile_asf_filepropertiesobject['offset']             = $NextObjectOffset + $offset;
					$thisfile_asf_filepropertiesobject['objectid']           = $NextObjectGUID;
					$thisfile_asf_filepropertiesobject['objectid_guid']      = $NextObjectGUIDtext;
					$thisfile_asf_filepropertiesobject['objectsize']         = $NextObjectSize;
					$thisfile_asf_filepropertiesobject['fileid']             = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$thisfile_asf_filepropertiesobject['fileid_guid']        = $this->BytestringToGUID($thisfile_asf_filepropertiesobject['fileid']);
					$thisfile_asf_filepropertiesobject['filesize']           = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$thisfile_asf_filepropertiesobject['creation_date']      = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$thisfile_asf_filepropertiesobject['creation_date_unix'] = $this->FILETIMEtoUNIXtime($thisfile_asf_filepropertiesobject['creation_date']);
					$offset += 8;
					$thisfile_asf_filepropertiesobject['data_packets']       = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$thisfile_asf_filepropertiesobject['play_duration']      = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$thisfile_asf_filepropertiesobject['send_duration']      = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$thisfile_asf_filepropertiesobject['preroll']            = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$thisfile_asf_filepropertiesobject['flags_raw']          = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$thisfile_asf_filepropertiesobject['flags']['broadcast'] = (bool) ($thisfile_asf_filepropertiesobject['flags_raw'] & 0x0001);
					$thisfile_asf_filepropertiesobject['flags']['seekable']  = (bool) ($thisfile_asf_filepropertiesobject['flags_raw'] & 0x0002);

					$thisfile_asf_filepropertiesobject['min_packet_size']    = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$thisfile_asf_filepropertiesobject['max_packet_size']    = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$thisfile_asf_filepropertiesobject['max_bitrate']        = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;

					if ($thisfile_asf_filepropertiesobject['flags']['broadcast']) {

						// broadcast flag is set, some values invalid
						unset($thisfile_asf_filepropertiesobject['filesize']);
						unset($thisfile_asf_filepropertiesobject['data_packets']);
						unset($thisfile_asf_filepropertiesobject['play_duration']);
						unset($thisfile_asf_filepropertiesobject['send_duration']);
						unset($thisfile_asf_filepropertiesobject['min_packet_size']);
						unset($thisfile_asf_filepropertiesobject['max_packet_size']);

					} else {

						// broadcast flag NOT set, perform calculations
						$info['playtime_seconds'] = ($thisfile_asf_filepropertiesobject['play_duration'] / 10000000) - ($thisfile_asf_filepropertiesobject['preroll'] / 1000);

						//$info['bitrate'] = $thisfile_asf_filepropertiesobject['max_bitrate'];
						$info['bitrate'] = ((isset($thisfile_asf_filepropertiesobject['filesize']) ? $thisfile_asf_filepropertiesobject['filesize'] : $info['filesize']) * 8) / $info['playtime_seconds'];
					}
					break;

				case GETID3_ASF_Stream_Properties_Object:
					// Stream Properties Object: (mandatory, one per media stream)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for stream properties object - GETID3_ASF_Stream_Properties_Object
					// Object Size                  QWORD        64              // size of stream properties object, including 78 bytes of Stream Properties Object header
					// Stream Type                  GUID         128             // GETID3_ASF_Audio_Media, GETID3_ASF_Video_Media or GETID3_ASF_Command_Media
					// Error Correction Type        GUID         128             // GETID3_ASF_Audio_Spread for audio-only streams, GETID3_ASF_No_Error_Correction for other stream types
					// Time Offset                  QWORD        64              // 100-nanosecond units. typically zero. added to all timestamps of samples in the stream
					// Type-Specific Data Length    DWORD        32              // number of bytes for Type-Specific Data field
					// Error Correction Data Length DWORD        32              // number of bytes for Error Correction Data field
					// Flags                        WORD         16              //
					// * Stream Number              bits         7 (0x007F)      // number of this stream.  1 <= valid <= 127
					// * Reserved                   bits         8 (0x7F80)      // reserved - set to zero
					// * Encrypted Content Flag     bits         1 (0x8000)      // stream contents encrypted if set
					// Reserved                     DWORD        32              // reserved - set to zero
					// Type-Specific Data           BYTESTREAM   variable        // type-specific format data, depending on value of Stream Type
					// Error Correction Data        BYTESTREAM   variable        // error-correction-specific format data, depending on value of Error Correct Type

					// There is one GETID3_ASF_Stream_Properties_Object for each stream (audio, video) but the
					// stream number isn't known until halfway through decoding the structure, hence it
					// it is decoded to a temporary variable and then stuck in the appropriate index later

					$StreamPropertiesObjectData['offset']             = $NextObjectOffset + $offset;
					$StreamPropertiesObjectData['objectid']           = $NextObjectGUID;
					$StreamPropertiesObjectData['objectid_guid']      = $NextObjectGUIDtext;
					$StreamPropertiesObjectData['objectsize']         = $NextObjectSize;
					$StreamPropertiesObjectData['stream_type']        = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$StreamPropertiesObjectData['stream_type_guid']   = $this->BytestringToGUID($StreamPropertiesObjectData['stream_type']);
					$StreamPropertiesObjectData['error_correct_type'] = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$StreamPropertiesObjectData['error_correct_guid'] = $this->BytestringToGUID($StreamPropertiesObjectData['error_correct_type']);
					$StreamPropertiesObjectData['time_offset']        = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
					$offset += 8;
					$StreamPropertiesObjectData['type_data_length']   = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$StreamPropertiesObjectData['error_data_length']  = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$StreamPropertiesObjectData['flags_raw']          = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$StreamPropertiesObjectStreamNumber               = $StreamPropertiesObjectData['flags_raw'] & 0x007F;
					$StreamPropertiesObjectData['flags']['encrypted'] = (bool) ($StreamPropertiesObjectData['flags_raw'] & 0x8000);

					$offset += 4; // reserved - DWORD
					$StreamPropertiesObjectData['type_specific_data'] = substr($ASFHeaderData, $offset, $StreamPropertiesObjectData['type_data_length']);
					$offset += $StreamPropertiesObjectData['type_data_length'];
					$StreamPropertiesObjectData['error_correct_data'] = substr($ASFHeaderData, $offset, $StreamPropertiesObjectData['error_data_length']);
					$offset += $StreamPropertiesObjectData['error_data_length'];

					switch ($StreamPropertiesObjectData['stream_type']) {

						case GETID3_ASF_Audio_Media:
							$thisfile_audio['dataformat']   = (!empty($thisfile_audio['dataformat'])   ? $thisfile_audio['dataformat']   : 'asf');
							$thisfile_audio['bitrate_mode'] = (!empty($thisfile_audio['bitrate_mode']) ? $thisfile_audio['bitrate_mode'] : 'cbr');

							$audiodata = getid3_riff::parseWAVEFORMATex(substr($StreamPropertiesObjectData['type_specific_data'], 0, 16));
							unset($audiodata['raw']);
							$thisfile_audio = getid3_lib::array_merge_noclobber($audiodata, $thisfile_audio);
							break;

						case GETID3_ASF_Video_Media:
							$thisfile_video['dataformat']   = (!empty($thisfile_video['dataformat'])   ? $thisfile_video['dataformat']   : 'asf');
							$thisfile_video['bitrate_mode'] = (!empty($thisfile_video['bitrate_mode']) ? $thisfile_video['bitrate_mode'] : 'cbr');
							break;

						case GETID3_ASF_Command_Media:
						default:
							// do nothing
							break;

					}

					$thisfile_asf['stream_properties_object'][$StreamPropertiesObjectStreamNumber] = $StreamPropertiesObjectData;
					unset($StreamPropertiesObjectData); // clear for next stream, if any
					break;

				case GETID3_ASF_Header_Extension_Object:
					// Header Extension Object: (mandatory, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Header Extension object - GETID3_ASF_Header_Extension_Object
					// Object Size                  QWORD        64              // size of Header Extension object, including 46 bytes of Header Extension Object header
					// Reserved Field 1             GUID         128             // hardcoded: GETID3_ASF_Reserved_1
					// Reserved Field 2             WORD         16              // hardcoded: 0x00000006
					// Header Extension Data Size   DWORD        32              // in bytes. valid: 0, or > 24. equals object size minus 46
					// Header Extension Data        BYTESTREAM   variable        // array of zero or more extended header objects

					// shortcut
					$thisfile_asf['header_extension_object'] = array();
					$thisfile_asf_headerextensionobject      = &$thisfile_asf['header_extension_object'];

					$thisfile_asf_headerextensionobject['offset']              = $NextObjectOffset + $offset;
					$thisfile_asf_headerextensionobject['objectid']            = $NextObjectGUID;
					$thisfile_asf_headerextensionobject['objectid_guid']       = $NextObjectGUIDtext;
					$thisfile_asf_headerextensionobject['objectsize']          = $NextObjectSize;
					$thisfile_asf_headerextensionobject['reserved_1']          = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$thisfile_asf_headerextensionobject['reserved_1_guid']     = $this->BytestringToGUID($thisfile_asf_headerextensionobject['reserved_1']);
					if ($thisfile_asf_headerextensionobject['reserved_1'] != GETID3_ASF_Reserved_1) {
						$info['warning'][] = 'header_extension_object.reserved_1 GUID ('.$this->BytestringToGUID($thisfile_asf_headerextensionobject['reserved_1']).') does not match expected "GETID3_ASF_Reserved_1" GUID ('.$this->BytestringToGUID(GETID3_ASF_Reserved_1).')';
						//return false;
						break;
					}
					$thisfile_asf_headerextensionobject['reserved_2']          = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					if ($thisfile_asf_headerextensionobject['reserved_2'] != 6) {
						$info['warning'][] = 'header_extension_object.reserved_2 ('.getid3_lib::PrintHexBytes($thisfile_asf_headerextensionobject['reserved_2']).') does not match expected value of "6"';
						//return false;
						break;
					}
					$thisfile_asf_headerextensionobject['extension_data_size'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$thisfile_asf_headerextensionobject['extension_data']      =                              substr($ASFHeaderData, $offset, $thisfile_asf_headerextensionobject['extension_data_size']);
					$unhandled_sections = 0;
					$thisfile_asf_headerextensionobject['extension_data_parsed'] = $this->HeaderExtensionObjectDataParse($thisfile_asf_headerextensionobject['extension_data'], $unhandled_sections);
					if ($unhandled_sections === 0) {
						unset($thisfile_asf_headerextensionobject['extension_data']);
					}
					$offset += $thisfile_asf_headerextensionobject['extension_data_size'];
					break;

				case GETID3_ASF_Codec_List_Object:
					// Codec List Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Codec List object - GETID3_ASF_Codec_List_Object
					// Object Size                  QWORD        64              // size of Codec List object, including 44 bytes of Codec List Object header
					// Reserved                     GUID         128             // hardcoded: 86D15241-311D-11D0-A3A4-00A0C90348F6
					// Codec Entries Count          DWORD        32              // number of entries in Codec Entries array
					// Codec Entries                array of:    variable        //
					// * Type                       WORD         16              // 0x0001 = Video Codec, 0x0002 = Audio Codec, 0xFFFF = Unknown Codec
					// * Codec Name Length          WORD         16              // number of Unicode characters stored in the Codec Name field
					// * Codec Name                 WCHAR        variable        // array of Unicode characters - name of codec used to create the content
					// * Codec Description Length   WORD         16              // number of Unicode characters stored in the Codec Description field
					// * Codec Description          WCHAR        variable        // array of Unicode characters - description of format used to create the content
					// * Codec Information Length   WORD         16              // number of Unicode characters stored in the Codec Information field
					// * Codec Information          BYTESTREAM   variable        // opaque array of information bytes about the codec used to create the content

					// shortcut
					$thisfile_asf['codec_list_object'] = array();
					$thisfile_asf_codeclistobject      = &$thisfile_asf['codec_list_object'];

					$thisfile_asf_codeclistobject['offset']                    = $NextObjectOffset + $offset;
					$thisfile_asf_codeclistobject['objectid']                  = $NextObjectGUID;
					$thisfile_asf_codeclistobject['objectid_guid']             = $NextObjectGUIDtext;
					$thisfile_asf_codeclistobject['objectsize']                = $NextObjectSize;
					$thisfile_asf_codeclistobject['reserved']                  = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$thisfile_asf_codeclistobject['reserved_guid']             = $this->BytestringToGUID($thisfile_asf_codeclistobject['reserved']);
					if ($thisfile_asf_codeclistobject['reserved'] != $this->GUIDtoBytestring('86D15241-311D-11D0-A3A4-00A0C90348F6')) {
						$info['warning'][] = 'codec_list_object.reserved GUID {'.$this->BytestringToGUID($thisfile_asf_codeclistobject['reserved']).'} does not match expected "GETID3_ASF_Reserved_1" GUID {86D15241-311D-11D0-A3A4-00A0C90348F6}';
						//return false;
						break;
					}
					$thisfile_asf_codeclistobject['codec_entries_count'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					for ($CodecEntryCounter = 0; $CodecEntryCounter < $thisfile_asf_codeclistobject['codec_entries_count']; $CodecEntryCounter++) {
						// shortcut
						$thisfile_asf_codeclistobject['codec_entries'][$CodecEntryCounter] = array();
						$thisfile_asf_codeclistobject_codecentries_current = &$thisfile_asf_codeclistobject['codec_entries'][$CodecEntryCounter];

						$thisfile_asf_codeclistobject_codecentries_current['type_raw'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$thisfile_asf_codeclistobject_codecentries_current['type'] = self::codecListObjectTypeLookup($thisfile_asf_codeclistobject_codecentries_current['type_raw']);

						$CodecNameLength = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
						$offset += 2;
						$thisfile_asf_codeclistobject_codecentries_current['name'] = substr($ASFHeaderData, $offset, $CodecNameLength);
						$offset += $CodecNameLength;

						$CodecDescriptionLength = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
						$offset += 2;
						$thisfile_asf_codeclistobject_codecentries_current['description'] = substr($ASFHeaderData, $offset, $CodecDescriptionLength);
						$offset += $CodecDescriptionLength;

						$CodecInformationLength = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$thisfile_asf_codeclistobject_codecentries_current['information'] = substr($ASFHeaderData, $offset, $CodecInformationLength);
						$offset += $CodecInformationLength;

						if ($thisfile_asf_codeclistobject_codecentries_current['type_raw'] == 2) { // audio codec

							if (strpos($thisfile_asf_codeclistobject_codecentries_current['description'], ',') === false) {
								$info['warning'][] = '[asf][codec_list_object][codec_entries]['.$CodecEntryCounter.'][description] expected to contain comma-seperated list of parameters: "'.$thisfile_asf_codeclistobject_codecentries_current['description'].'"';
							} else {

								list($AudioCodecBitrate, $AudioCodecFrequency, $AudioCodecChannels) = explode(',', $this->TrimConvert($thisfile_asf_codeclistobject_codecentries_current['description']));
								$thisfile_audio['codec'] = $this->TrimConvert($thisfile_asf_codeclistobject_codecentries_current['name']);

								if (!isset($thisfile_audio['bitrate']) && strstr($AudioCodecBitrate, 'kbps')) {
									$thisfile_audio['bitrate'] = (int) (trim(str_replace('kbps', '', $AudioCodecBitrate)) * 1000);
								}
								//if (!isset($thisfile_video['bitrate']) && isset($thisfile_audio['bitrate']) && isset($thisfile_asf['file_properties_object']['max_bitrate']) && ($thisfile_asf_codeclistobject['codec_entries_count'] > 1)) {
								if (empty($thisfile_video['bitrate']) && !empty($thisfile_audio['bitrate']) && !empty($info['bitrate'])) {
									//$thisfile_video['bitrate'] = $thisfile_asf['file_properties_object']['max_bitrate'] - $thisfile_audio['bitrate'];
									$thisfile_video['bitrate'] = $info['bitrate'] - $thisfile_audio['bitrate'];
								}

								$AudioCodecFrequency = (int) trim(str_replace('kHz', '', $AudioCodecFrequency));
								switch ($AudioCodecFrequency) {
									case 8:
									case 8000:
										$thisfile_audio['sample_rate'] = 8000;
										break;

									case 11:
									case 11025:
										$thisfile_audio['sample_rate'] = 11025;
										break;

									case 12:
									case 12000:
										$thisfile_audio['sample_rate'] = 12000;
										break;

									case 16:
									case 16000:
										$thisfile_audio['sample_rate'] = 16000;
										break;

									case 22:
									case 22050:
										$thisfile_audio['sample_rate'] = 22050;
										break;

									case 24:
									case 24000:
										$thisfile_audio['sample_rate'] = 24000;
										break;

									case 32:
									case 32000:
										$thisfile_audio['sample_rate'] = 32000;
										break;

									case 44:
									case 441000:
										$thisfile_audio['sample_rate'] = 44100;
										break;

									case 48:
									case 48000:
										$thisfile_audio['sample_rate'] = 48000;
										break;

									default:
										$info['warning'][] = 'unknown frequency: "'.$AudioCodecFrequency.'" ('.$this->TrimConvert($thisfile_asf_codeclistobject_codecentries_current['description']).')';
										break;
								}

								if (!isset($thisfile_audio['channels'])) {
									if (strstr($AudioCodecChannels, 'stereo')) {
										$thisfile_audio['channels'] = 2;
									} elseif (strstr($AudioCodecChannels, 'mono')) {
										$thisfile_audio['channels'] = 1;
									}
								}

							}
						}
					}
					break;

				case GETID3_ASF_Script_Command_Object:
					// Script Command Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Script Command object - GETID3_ASF_Script_Command_Object
					// Object Size                  QWORD        64              // size of Script Command object, including 44 bytes of Script Command Object header
					// Reserved                     GUID         128             // hardcoded: 4B1ACBE3-100B-11D0-A39B-00A0C90348F6
					// Commands Count               WORD         16              // number of Commands structures in the Script Commands Objects
					// Command Types Count          WORD         16              // number of Command Types structures in the Script Commands Objects
					// Command Types                array of:    variable        //
					// * Command Type Name Length   WORD         16              // number of Unicode characters for Command Type Name
					// * Command Type Name          WCHAR        variable        // array of Unicode characters - name of a type of command
					// Commands                     array of:    variable        //
					// * Presentation Time          DWORD        32              // presentation time of that command, in milliseconds
					// * Type Index                 WORD         16              // type of this command, as a zero-based index into the array of Command Types of this object
					// * Command Name Length        WORD         16              // number of Unicode characters for Command Name
					// * Command Name               WCHAR        variable        // array of Unicode characters - name of this command

					// shortcut
					$thisfile_asf['script_command_object'] = array();
					$thisfile_asf_scriptcommandobject      = &$thisfile_asf['script_command_object'];

					$thisfile_asf_scriptcommandobject['offset']               = $NextObjectOffset + $offset;
					$thisfile_asf_scriptcommandobject['objectid']             = $NextObjectGUID;
					$thisfile_asf_scriptcommandobject['objectid_guid']        = $NextObjectGUIDtext;
					$thisfile_asf_scriptcommandobject['objectsize']           = $NextObjectSize;
					$thisfile_asf_scriptcommandobject['reserved']             = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$thisfile_asf_scriptcommandobject['reserved_guid']        = $this->BytestringToGUID($thisfile_asf_scriptcommandobject['reserved']);
					if ($thisfile_asf_scriptcommandobject['reserved'] != $this->GUIDtoBytestring('4B1ACBE3-100B-11D0-A39B-00A0C90348F6')) {
						$info['warning'][] = 'script_command_object.reserved GUID {'.$this->BytestringToGUID($thisfile_asf_scriptcommandobject['reserved']).'} does not match expected "GETID3_ASF_Reserved_1" GUID {4B1ACBE3-100B-11D0-A39B-00A0C90348F6}';
						//return false;
						break;
					}
					$thisfile_asf_scriptcommandobject['commands_count']       = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$thisfile_asf_scriptcommandobject['command_types_count']  = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					for ($CommandTypesCounter = 0; $CommandTypesCounter < $thisfile_asf_scriptcommandobject['command_types_count']; $CommandTypesCounter++) {
						$CommandTypeNameLength = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
						$offset += 2;
						$thisfile_asf_scriptcommandobject['command_types'][$CommandTypesCounter]['name'] = substr($ASFHeaderData, $offset, $CommandTypeNameLength);
						$offset += $CommandTypeNameLength;
					}
					for ($CommandsCounter = 0; $CommandsCounter < $thisfile_asf_scriptcommandobject['commands_count']; $CommandsCounter++) {
						$thisfile_asf_scriptcommandobject['commands'][$CommandsCounter]['presentation_time']  = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
						$offset += 4;
						$thisfile_asf_scriptcommandobject['commands'][$CommandsCounter]['type_index']         = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;

						$CommandTypeNameLength = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2)) * 2; // 2 bytes per character
						$offset += 2;
						$thisfile_asf_scriptcommandobject['commands'][$CommandsCounter]['name'] = substr($ASFHeaderData, $offset, $CommandTypeNameLength);
						$offset += $CommandTypeNameLength;
					}
					break;

				case GETID3_ASF_Marker_Object:
					// Marker Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Marker object - GETID3_ASF_Marker_Object
					// Object Size                  QWORD        64              // size of Marker object, including 48 bytes of Marker Object header
					// Reserved                     GUID         128             // hardcoded: 4CFEDB20-75F6-11CF-9C0F-00A0C90349CB
					// Markers Count                DWORD        32              // number of Marker structures in Marker Object
					// Reserved                     WORD         16              // hardcoded: 0x0000
					// Name Length                  WORD         16              // number of bytes in the Name field
					// Name                         WCHAR        variable        // name of the Marker Object
					// Markers                      array of:    variable        //
					// * Offset                     QWORD        64              // byte offset into Data Object
					// * Presentation Time          QWORD        64              // in 100-nanosecond units
					// * Entry Length               WORD         16              // length in bytes of (Send Time + Flags + Marker Description Length + Marker Description + Padding)
					// * Send Time                  DWORD        32              // in milliseconds
					// * Flags                      DWORD        32              // hardcoded: 0x00000000
					// * Marker Description Length  DWORD        32              // number of bytes in Marker Description field
					// * Marker Description         WCHAR        variable        // array of Unicode characters - description of marker entry
					// * Padding                    BYTESTREAM   variable        // optional padding bytes

					// shortcut
					$thisfile_asf['marker_object'] = array();
					$thisfile_asf_markerobject     = &$thisfile_asf['marker_object'];

					$thisfile_asf_markerobject['offset']               = $NextObjectOffset + $offset;
					$thisfile_asf_markerobject['objectid']             = $NextObjectGUID;
					$thisfile_asf_markerobject['objectid_guid']        = $NextObjectGUIDtext;
					$thisfile_asf_markerobject['objectsize']           = $NextObjectSize;
					$thisfile_asf_markerobject['reserved']             = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$thisfile_asf_markerobject['reserved_guid']        = $this->BytestringToGUID($thisfile_asf_markerobject['reserved']);
					if ($thisfile_asf_markerobject['reserved'] != $this->GUIDtoBytestring('4CFEDB20-75F6-11CF-9C0F-00A0C90349CB')) {
						$info['warning'][] = 'marker_object.reserved GUID {'.$this->BytestringToGUID($thisfile_asf_markerobject['reserved_1']).'} does not match expected "GETID3_ASF_Reserved_1" GUID {4CFEDB20-75F6-11CF-9C0F-00A0C90349CB}';
						break;
					}
					$thisfile_asf_markerobject['markers_count'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					$thisfile_asf_markerobject['reserved_2'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					if ($thisfile_asf_markerobject['reserved_2'] != 0) {
						$info['warning'][] = 'marker_object.reserved_2 ('.getid3_lib::PrintHexBytes($thisfile_asf_markerobject['reserved_2']).') does not match expected value of "0"';
						break;
					}
					$thisfile_asf_markerobject['name_length'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$thisfile_asf_markerobject['name'] = substr($ASFHeaderData, $offset, $thisfile_asf_markerobject['name_length']);
					$offset += $thisfile_asf_markerobject['name_length'];
					for ($MarkersCounter = 0; $MarkersCounter < $thisfile_asf_markerobject['markers_count']; $MarkersCounter++) {
						$thisfile_asf_markerobject['markers'][$MarkersCounter]['offset']  = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
						$offset += 8;
						$thisfile_asf_markerobject['markers'][$MarkersCounter]['presentation_time']         = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 8));
						$offset += 8;
						$thisfile_asf_markerobject['markers'][$MarkersCounter]['entry_length']              = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$thisfile_asf_markerobject['markers'][$MarkersCounter]['send_time']                 = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
						$offset += 4;
						$thisfile_asf_markerobject['markers'][$MarkersCounter]['flags']                     = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
						$offset += 4;
						$thisfile_asf_markerobject['markers'][$MarkersCounter]['marker_description_length'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
						$offset += 4;
						$thisfile_asf_markerobject['markers'][$MarkersCounter]['marker_description']        = substr($ASFHeaderData, $offset, $thisfile_asf_markerobject['markers'][$MarkersCounter]['marker_description_length']);
						$offset += $thisfile_asf_markerobject['markers'][$MarkersCounter]['marker_description_length'];
						$PaddingLength = $thisfile_asf_markerobject['markers'][$MarkersCounter]['entry_length'] - 4 -  4 - 4 - $thisfile_asf_markerobject['markers'][$MarkersCounter]['marker_description_length'];
						if ($PaddingLength > 0) {
							$thisfile_asf_markerobject['markers'][$MarkersCounter]['padding']               = substr($ASFHeaderData, $offset, $PaddingLength);
							$offset += $PaddingLength;
						}
					}
					break;

				case GETID3_ASF_Bitrate_Mutual_Exclusion_Object:
					// Bitrate Mutual Exclusion Object: (optional)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Bitrate Mutual Exclusion object - GETID3_ASF_Bitrate_Mutual_Exclusion_Object
					// Object Size                  QWORD        64              // size of Bitrate Mutual Exclusion object, including 42 bytes of Bitrate Mutual Exclusion Object header
					// Exlusion Type                GUID         128             // nature of mutual exclusion relationship. one of: (GETID3_ASF_Mutex_Bitrate, GETID3_ASF_Mutex_Unknown)
					// Stream Numbers Count         WORD         16              // number of video streams
					// Stream Numbers               WORD         variable        // array of mutually exclusive video stream numbers. 1 <= valid <= 127

					// shortcut
					$thisfile_asf['bitrate_mutual_exclusion_object'] = array();
					$thisfile_asf_bitratemutualexclusionobject       = &$thisfile_asf['bitrate_mutual_exclusion_object'];

					$thisfile_asf_bitratemutualexclusionobject['offset']               = $NextObjectOffset + $offset;
					$thisfile_asf_bitratemutualexclusionobject['objectid']             = $NextObjectGUID;
					$thisfile_asf_bitratemutualexclusionobject['objectid_guid']        = $NextObjectGUIDtext;
					$thisfile_asf_bitratemutualexclusionobject['objectsize']           = $NextObjectSize;
					$thisfile_asf_bitratemutualexclusionobject['reserved']             = substr($ASFHeaderData, $offset, 16);
					$thisfile_asf_bitratemutualexclusionobject['reserved_guid']        = $this->BytestringToGUID($thisfile_asf_bitratemutualexclusionobject['reserved']);
					$offset += 16;
					if (($thisfile_asf_bitratemutualexclusionobject['reserved'] != GETID3_ASF_Mutex_Bitrate) && ($thisfile_asf_bitratemutualexclusionobject['reserved'] != GETID3_ASF_Mutex_Unknown)) {
						$info['warning'][] = 'bitrate_mutual_exclusion_object.reserved GUID {'.$this->BytestringToGUID($thisfile_asf_bitratemutualexclusionobject['reserved']).'} does not match expected "GETID3_ASF_Mutex_Bitrate" GUID {'.$this->BytestringToGUID(GETID3_ASF_Mutex_Bitrate).'} or  "GETID3_ASF_Mutex_Unknown" GUID {'.$this->BytestringToGUID(GETID3_ASF_Mutex_Unknown).'}';
						//return false;
						break;
					}
					$thisfile_asf_bitratemutualexclusionobject['stream_numbers_count'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					for ($StreamNumberCounter = 0; $StreamNumberCounter < $thisfile_asf_bitratemutualexclusionobject['stream_numbers_count']; $StreamNumberCounter++) {
						$thisfile_asf_bitratemutualexclusionobject['stream_numbers'][$StreamNumberCounter] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
					}
					break;

				case GETID3_ASF_Error_Correction_Object:
					// Error Correction Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Error Correction object - GETID3_ASF_Error_Correction_Object
					// Object Size                  QWORD        64              // size of Error Correction object, including 44 bytes of Error Correction Object header
					// Error Correction Type        GUID         128             // type of error correction. one of: (GETID3_ASF_No_Error_Correction, GETID3_ASF_Audio_Spread)
					// Error Correction Data Length DWORD        32              // number of bytes in Error Correction Data field
					// Error Correction Data        BYTESTREAM   variable        // structure depends on value of Error Correction Type field

					// shortcut
					$thisfile_asf['error_correction_object'] = array();
					$thisfile_asf_errorcorrectionobject      = &$thisfile_asf['error_correction_object'];

					$thisfile_asf_errorcorrectionobject['offset']                = $NextObjectOffset + $offset;
					$thisfile_asf_errorcorrectionobject['objectid']              = $NextObjectGUID;
					$thisfile_asf_errorcorrectionobject['objectid_guid']         = $NextObjectGUIDtext;
					$thisfile_asf_errorcorrectionobject['objectsize']            = $NextObjectSize;
					$thisfile_asf_errorcorrectionobject['error_correction_type'] = substr($ASFHeaderData, $offset, 16);
					$offset += 16;
					$thisfile_asf_errorcorrectionobject['error_correction_guid'] = $this->BytestringToGUID($thisfile_asf_errorcorrectionobject['error_correction_type']);
					$thisfile_asf_errorcorrectionobject['error_correction_data_length'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
					$offset += 4;
					switch ($thisfile_asf_errorcorrectionobject['error_correction_type']) {
						case GETID3_ASF_No_Error_Correction:
							// should be no data, but just in case there is, skip to the end of the field
							$offset += $thisfile_asf_errorcorrectionobject['error_correction_data_length'];
							break;

						case GETID3_ASF_Audio_Spread:
							// Field Name                   Field Type   Size (bits)
							// Span                         BYTE         8               // number of packets over which audio will be spread.
							// Virtual Packet Length        WORD         16              // size of largest audio payload found in audio stream
							// Virtual Chunk Length         WORD         16              // size of largest audio payload found in audio stream
							// Silence Data Length          WORD         16              // number of bytes in Silence Data field
							// Silence Data                 BYTESTREAM   variable        // hardcoded: 0x00 * (Silence Data Length) bytes

							$thisfile_asf_errorcorrectionobject['span']                  = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 1));
							$offset += 1;
							$thisfile_asf_errorcorrectionobject['virtual_packet_length'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
							$offset += 2;
							$thisfile_asf_errorcorrectionobject['virtual_chunk_length']  = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
							$offset += 2;
							$thisfile_asf_errorcorrectionobject['silence_data_length']   = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
							$offset += 2;
							$thisfile_asf_errorcorrectionobject['silence_data']          = substr($ASFHeaderData, $offset, $thisfile_asf_errorcorrectionobject['silence_data_length']);
							$offset += $thisfile_asf_errorcorrectionobject['silence_data_length'];
							break;

						default:
							$info['warning'][] = 'error_correction_object.error_correction_type GUID {'.$this->BytestringToGUID($thisfile_asf_errorcorrectionobject['reserved']).'} does not match expected "GETID3_ASF_No_Error_Correction" GUID {'.$this->BytestringToGUID(GETID3_ASF_No_Error_Correction).'} or  "GETID3_ASF_Audio_Spread" GUID {'.$this->BytestringToGUID(GETID3_ASF_Audio_Spread).'}';
							//return false;
							break;
					}

					break;

				case GETID3_ASF_Content_Description_Object:
					// Content Description Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Content Description object - GETID3_ASF_Content_Description_Object
					// Object Size                  QWORD        64              // size of Content Description object, including 34 bytes of Content Description Object header
					// Title Length                 WORD         16              // number of bytes in Title field
					// Author Length                WORD         16              // number of bytes in Author field
					// Copyright Length             WORD         16              // number of bytes in Copyright field
					// Description Length           WORD         16              // number of bytes in Description field
					// Rating Length                WORD         16              // number of bytes in Rating field
					// Title                        WCHAR        16              // array of Unicode characters - Title
					// Author                       WCHAR        16              // array of Unicode characters - Author
					// Copyright                    WCHAR        16              // array of Unicode characters - Copyright
					// Description                  WCHAR        16              // array of Unicode characters - Description
					// Rating                       WCHAR        16              // array of Unicode characters - Rating

					// shortcut
					$thisfile_asf['content_description_object'] = array();
					$thisfile_asf_contentdescriptionobject      = &$thisfile_asf['content_description_object'];

					$thisfile_asf_contentdescriptionobject['offset']                = $NextObjectOffset + $offset;
					$thisfile_asf_contentdescriptionobject['objectid']              = $NextObjectGUID;
					$thisfile_asf_contentdescriptionobject['objectid_guid']         = $NextObjectGUIDtext;
					$thisfile_asf_contentdescriptionobject['objectsize']            = $NextObjectSize;
					$thisfile_asf_contentdescriptionobject['title_length']          = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$thisfile_asf_contentdescriptionobject['author_length']         = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$thisfile_asf_contentdescriptionobject['copyright_length']      = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$thisfile_asf_contentdescriptionobject['description_length']    = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$thisfile_asf_contentdescriptionobject['rating_length']         = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					$thisfile_asf_contentdescriptionobject['title']                 = substr($ASFHeaderData, $offset, $thisfile_asf_contentdescriptionobject['title_length']);
					$offset += $thisfile_asf_contentdescriptionobject['title_length'];
					$thisfile_asf_contentdescriptionobject['author']                = substr($ASFHeaderData, $offset, $thisfile_asf_contentdescriptionobject['author_length']);
					$offset += $thisfile_asf_contentdescriptionobject['author_length'];
					$thisfile_asf_contentdescriptionobject['copyright']             = substr($ASFHeaderData, $offset, $thisfile_asf_contentdescriptionobject['copyright_length']);
					$offset += $thisfile_asf_contentdescriptionobject['copyright_length'];
					$thisfile_asf_contentdescriptionobject['description']           = substr($ASFHeaderData, $offset, $thisfile_asf_contentdescriptionobject['description_length']);
					$offset += $thisfile_asf_contentdescriptionobject['description_length'];
					$thisfile_asf_contentdescriptionobject['rating']                = substr($ASFHeaderData, $offset, $thisfile_asf_contentdescriptionobject['rating_length']);
					$offset += $thisfile_asf_contentdescriptionobject['rating_length'];

					$ASFcommentKeysToCopy = array('title'=>'title', 'author'=>'artist', 'copyright'=>'copyright', 'description'=>'comment', 'rating'=>'rating');
					foreach ($ASFcommentKeysToCopy as $keytocopyfrom => $keytocopyto) {
						if (!empty($thisfile_asf_contentdescriptionobject[$keytocopyfrom])) {
							$thisfile_asf_comments[$keytocopyto][] = $this->TrimTerm($thisfile_asf_contentdescriptionobject[$keytocopyfrom]);
						}
					}
					break;

				case GETID3_ASF_Extended_Content_Description_Object:
					// Extended Content Description Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Extended Content Description object - GETID3_ASF_Extended_Content_Description_Object
					// Object Size                  QWORD        64              // size of ExtendedContent Description object, including 26 bytes of Extended Content Description Object header
					// Content Descriptors Count    WORD         16              // number of entries in Content Descriptors list
					// Content Descriptors          array of:    variable        //
					// * Descriptor Name Length     WORD         16              // size in bytes of Descriptor Name field
					// * Descriptor Name            WCHAR        variable        // array of Unicode characters - Descriptor Name
					// * Descriptor Value Data Type WORD         16              // Lookup array:
																					// 0x0000 = Unicode String (variable length)
																					// 0x0001 = BYTE array     (variable length)
																					// 0x0002 = BOOL           (DWORD, 32 bits)
																					// 0x0003 = DWORD          (DWORD, 32 bits)
																					// 0x0004 = QWORD          (QWORD, 64 bits)
																					// 0x0005 = WORD           (WORD,  16 bits)
					// * Descriptor Value Length    WORD         16              // number of bytes stored in Descriptor Value field
					// * Descriptor Value           variable     variable        // value for Content Descriptor

					// shortcut
					$thisfile_asf['extended_content_description_object'] = array();
					$thisfile_asf_extendedcontentdescriptionobject       = &$thisfile_asf['extended_content_description_object'];

					$thisfile_asf_extendedcontentdescriptionobject['offset']                    = $NextObjectOffset + $offset;
					$thisfile_asf_extendedcontentdescriptionobject['objectid']                  = $NextObjectGUID;
					$thisfile_asf_extendedcontentdescriptionobject['objectid_guid']             = $NextObjectGUIDtext;
					$thisfile_asf_extendedcontentdescriptionobject['objectsize']                = $NextObjectSize;
					$thisfile_asf_extendedcontentdescriptionobject['content_descriptors_count'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					for ($ExtendedContentDescriptorsCounter = 0; $ExtendedContentDescriptorsCounter < $thisfile_asf_extendedcontentdescriptionobject['content_descriptors_count']; $ExtendedContentDescriptorsCounter++) {
						// shortcut
						$thisfile_asf_extendedcontentdescriptionobject['content_descriptors'][$ExtendedContentDescriptorsCounter] = array();
						$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current                 = &$thisfile_asf_extendedcontentdescriptionobject['content_descriptors'][$ExtendedContentDescriptorsCounter];

						$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['base_offset']  = $offset + 30;
						$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['name_length']  = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['name']         = substr($ASFHeaderData, $offset, $thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['name_length']);
						$offset += $thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['name_length'];
						$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_type']   = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_length'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']        = substr($ASFHeaderData, $offset, $thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_length']);
						$offset += $thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_length'];
						switch ($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_type']) {
							case 0x0000: // Unicode string
								break;

							case 0x0001: // BYTE array
								// do nothing
								break;

							case 0x0002: // BOOL
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'] = (bool) getid3_lib::LittleEndian2Int($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']);
								break;

							case 0x0003: // DWORD
							case 0x0004: // QWORD
							case 0x0005: // WORD
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'] = getid3_lib::LittleEndian2Int($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']);
								break;

							default:
								$info['warning'][] = 'extended_content_description.content_descriptors.'.$ExtendedContentDescriptorsCounter.'.value_type is invalid ('.$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_type'].')';
								//return false;
								break;
						}
						switch ($this->TrimConvert(strtolower($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['name']))) {

							case 'wm/albumartist':
							case 'artist':
								// Note: not 'artist', that comes from 'author' tag
								$thisfile_asf_comments['albumartist'] = array($this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								break;

							case 'wm/albumtitle':
							case 'album':
								$thisfile_asf_comments['album']  = array($this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								break;

							case 'wm/genre':
							case 'genre':
								$thisfile_asf_comments['genre'] = array($this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								break;

							case 'wm/partofset':
								$thisfile_asf_comments['partofset'] = array($this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								break;

							case 'wm/tracknumber':
							case 'tracknumber':
								// be careful casting to int: casting unicode strings to int gives unexpected results (stops parsing at first non-numeric character)
								$thisfile_asf_comments['track'] = array($this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								foreach ($thisfile_asf_comments['track'] as $key => $value) {
									if (preg_match('/^[0-9\x00]+$/', $value)) {
										$thisfile_asf_comments['track'][$key] = intval(str_replace("\x00", '', $value));
									}
								}
								break;

							case 'wm/track':
								if (empty($thisfile_asf_comments['track'])) {
									$thisfile_asf_comments['track'] = array(1 + $this->TrimConvert($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								}
								break;

							case 'wm/year':
							case 'year':
							case 'date':
								$thisfile_asf_comments['year'] = array( $this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								break;

							case 'wm/lyrics':
							case 'lyrics':
								$thisfile_asf_comments['lyrics'] = array($this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
								break;

							case 'isvbr':
								if ($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']) {
									$thisfile_audio['bitrate_mode'] = 'vbr';
									$thisfile_video['bitrate_mode'] = 'vbr';
								}
								break;

							case 'id3':
								$this->getid3->include_module('tag.id3v2');

								$getid3_id3v2 = new getid3_id3v2($this->getid3);
								$getid3_id3v2->AnalyzeString($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']);
								unset($getid3_id3v2);

								if ($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_length'] > 1024) {
									$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'] = '<value too large to display>';
								}
								break;

							case 'wm/encodingtime':
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['encoding_time_unix'] = $this->FILETIMEtoUNIXtime($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']);
								$thisfile_asf_comments['encoding_time_unix'] = array($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['encoding_time_unix']);
								break;

							case 'wm/picture':
								$WMpicture = $this->ASF_WMpicture($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']);
								foreach ($WMpicture as $key => $value) {
									$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current[$key] = $value;
								}
								unset($WMpicture);
/*
								$wm_picture_offset = 0;
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_type_id'] = getid3_lib::LittleEndian2Int(substr($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'], $wm_picture_offset, 1));
								$wm_picture_offset += 1;
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_type']    = self::WMpictureTypeLookup($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_type_id']);
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_size']    = getid3_lib::LittleEndian2Int(substr($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'], $wm_picture_offset, 4));
								$wm_picture_offset += 4;

								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_mime'] = '';
								do {
									$next_byte_pair = substr($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'], $wm_picture_offset, 2);
									$wm_picture_offset += 2;
									$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_mime'] .= $next_byte_pair;
								} while ($next_byte_pair !== "\x00\x00");

								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_description'] = '';
								do {
									$next_byte_pair = substr($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'], $wm_picture_offset, 2);
									$wm_picture_offset += 2;
									$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_description'] .= $next_byte_pair;
								} while ($next_byte_pair !== "\x00\x00");

								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['dataoffset'] = $wm_picture_offset;
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['data'] = substr($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value'], $wm_picture_offset);
								unset($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']);

								$imageinfo = array();
								$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_mime'] = '';
								$imagechunkcheck = getid3_lib::GetDataImageSize($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['data'], $imageinfo);
								unset($imageinfo);
								if (!empty($imagechunkcheck)) {
									$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_mime'] = image_type_to_mime_type($imagechunkcheck[2]);
								}
								if (!isset($thisfile_asf_comments['picture'])) {
									$thisfile_asf_comments['picture'] = array();
								}
								$thisfile_asf_comments['picture'][] = array('data'=>$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['data'], 'image_mime'=>$thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['image_mime']);
*/
								break;

							default:
								switch ($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value_type']) {
									case 0: // Unicode string
										if (substr($this->TrimConvert($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['name']), 0, 3) == 'WM/') {
											$thisfile_asf_comments[str_replace('wm/', '', strtolower($this->TrimConvert($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['name'])))] = array($this->TrimTerm($thisfile_asf_extendedcontentdescriptionobject_contentdescriptor_current['value']));
										}
										break;

									case 1:
										break;
								}
								break;
						}

					}
					break;

				case GETID3_ASF_Stream_Bitrate_Properties_Object:
					// Stream Bitrate Properties Object: (optional, one only)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Stream Bitrate Properties object - GETID3_ASF_Stream_Bitrate_Properties_Object
					// Object Size                  QWORD        64              // size of Extended Content Description object, including 26 bytes of Stream Bitrate Properties Object header
					// Bitrate Records Count        WORD         16              // number of records in Bitrate Records
					// Bitrate Records              array of:    variable        //
					// * Flags                      WORD         16              //
					// * * Stream Number            bits         7  (0x007F)     // number of this stream
					// * * Reserved                 bits         9  (0xFF80)     // hardcoded: 0
					// * Average Bitrate            DWORD        32              // in bits per second

					// shortcut
					$thisfile_asf['stream_bitrate_properties_object'] = array();
					$thisfile_asf_streambitratepropertiesobject       = &$thisfile_asf['stream_bitrate_properties_object'];

					$thisfile_asf_streambitratepropertiesobject['offset']                    = $NextObjectOffset + $offset;
					$thisfile_asf_streambitratepropertiesobject['objectid']                  = $NextObjectGUID;
					$thisfile_asf_streambitratepropertiesobject['objectid_guid']             = $NextObjectGUIDtext;
					$thisfile_asf_streambitratepropertiesobject['objectsize']                = $NextObjectSize;
					$thisfile_asf_streambitratepropertiesobject['bitrate_records_count']     = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
					$offset += 2;
					for ($BitrateRecordsCounter = 0; $BitrateRecordsCounter < $thisfile_asf_streambitratepropertiesobject['bitrate_records_count']; $BitrateRecordsCounter++) {
						$thisfile_asf_streambitratepropertiesobject['bitrate_records'][$BitrateRecordsCounter]['flags_raw'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 2));
						$offset += 2;
						$thisfile_asf_streambitratepropertiesobject['bitrate_records'][$BitrateRecordsCounter]['flags']['stream_number'] = $thisfile_asf_streambitratepropertiesobject['bitrate_records'][$BitrateRecordsCounter]['flags_raw'] & 0x007F;
						$thisfile_asf_streambitratepropertiesobject['bitrate_records'][$BitrateRecordsCounter]['bitrate'] = getid3_lib::LittleEndian2Int(substr($ASFHeaderData, $offset, 4));
						$offset += 4;
					}
					break;

				case GETID3_ASF_Padding_Object:
					// Padding Object: (optional)
					// Field Name                   Field Type   Size (bits)
					// Object ID                    GUID         128             // GUID for Padding object - GETID3_ASF_Padding_Object
					// Object Size                  QWORD        64              // size of Padding object, including 24 bytes of ASF Padding Object header
					// Padding Data                 BYTESTREAM   variable        // ignore

					// shortcut
					$thisfile_asf['padding_object'] = array();
					$thisfile_asf_paddingobject     = &$thisfile_asf['padding_object'];

					$thisfile_asf_paddingobject['offset']                    = $NextObjectOffset + $offset;
					$thisfile_asf_paddingobject['objectid']                  = $NextObjectGUID;
					$thisfile_asf_paddingobject['objectid_guid']             = $NextObjectGUIDtext;
					$thisfile_asf_paddingobject['objectsize']                = $NextObjectSize;
					$thisfile_asf_paddingobject['padding_length']            = $thisfile_asf_paddingobject['objectsize'] - 16 - 8;
					$thisfile_asf_paddingobject['padding']                   = substr($ASFHeaderData, $offset, $thisfile_asf_paddingobject['padding_length']);
					$offset += ($NextObjectSize - 16 - 8);
					break;

				case GETID3_ASF_Extended_Content_Encryption_Object:
				case GETID3_ASF_Content_Encryption_Object:
					// WMA DRM - just ignore
					$offset += ($NextObjectSize - 16 - 8);
					break;

				default:
					// Implementations shall ignore any standard or non-standard object that they do not know how to handle.
					if ($this->GUIDname($NextObjectGUIDtext)) {
						$info['warning'][] = 'unhandled GUID "'.$this->GUIDname($NextObjectGUIDtext).'" {'.$NextObjectGUIDtext.'} in ASF header at offset '.($offset - 16 - 8);
					} else {
						$info['warning'][] = 'unknown GUID {'.$NextObjectGUIDtext.'} in ASF header at offset '.($offset - 16 - 8);
					}
					$offset += ($NextObjectSize - 16 - 8);
					break;
			}
		}
		if (isset($thisfile_asf_streambitrateproperties['bitrate_records_count'])) {
			$ASFbitrateAudio = 0;
			$ASFbitrateVideo = 0;
			for ($BitrateRecordsCounter = 0; $BitrateRecordsCounter < $thisfile_asf_streambitrateproperties['bitrate_records_count']; $BitrateRecordsCounter++) {
				if (isset($thisfile_asf_codeclistobject['codec_entries'][$BitrateRecordsCounter])) {
					switch ($thisfile_asf_codeclistobject['codec_entries'][$BitrateRecordsCounter]['type_raw']) {
						case 1:
							$ASFbitrateVideo += $thisfile_asf_streambitrateproperties['bitrate_records'][$BitrateRecordsCounter]['bitrate'];
							break;

						case 2:
							$ASFbitrateAudio += $thisfile_asf_streambitrateproperties['bitrate_records'][$BitrateRecordsCounter]['bitrate'];
							break;

						default:
							// do nothing
							break;
					}
				}
			}
			if ($ASFbitrateAudio > 0) {
				$thisfile_audio['bitrate'] = $ASFbitrateAudio;
			}
			if ($ASFbitrateVideo > 0) {
				$thisfile_video['bitrate'] = $ASFbitrateVideo;
			}
		}
		if (isset($thisfile_asf['stream_properties_object']) && is_array($thisfile_asf['stream_properties_object'])) {

			$thisfile_audio['bitrate'] = 0;
			$thisfile_video['bitrate'] = 0;

			foreach ($thisfile_asf['stream_properties_object'] as $streamnumber => $streamdata) {

				switch ($streamdata['stream_type']) {
					case GETID3_ASF_Audio_Media:
						// Field Name                   Field Type   Size (bits)
						// Codec ID / Format Tag        WORD         16              // unique ID of audio codec - defined as wFormatTag field of WAVEFORMATEX structure
						// Number of Channels           WORD         16              // number of channels of audio - defined as nChannels field of WAVEFORMATEX structure
						// Samples Per Second           DWORD        32              // in Hertz - defined as nSamplesPerSec field of WAVEFORMATEX structure
						// Average number of Bytes/sec  DWORD        32              // bytes/sec of audio stream  - defined as nAvgBytesPerSec field of WAVEFORMATEX structure
						// Block Alignment              WORD         16              // block size in bytes of audio codec - defined as nBlockAlign field of WAVEFORMATEX structure
						// Bits per sample              WORD         16              // bits per sample of mono data. set to zero for variable bitrate codecs. defined as wBitsPerSample field of WAVEFORMATEX structure
						// Codec Specific Data Size     WORD         16              // size in bytes of Codec Specific Data buffer - defined as cbSize field of WAVEFORMATEX structure
						// Codec Specific Data          BYTESTREAM   variable        // array of codec-specific data bytes

						// shortcut
						$thisfile_asf['audio_media'][$streamnumber] = array();
						$thisfile_asf_audiomedia_currentstream      = &$thisfile_asf['audio_media'][$streamnumber];

						$audiomediaoffset = 0;

						$thisfile_asf_audiomedia_currentstream = getid3_riff::parseWAVEFORMATex(substr($streamdata['type_specific_data'], $audiomediaoffset, 16));
						$audiomediaoffset += 16;

						$thisfile_audio['lossless'] = false;
						switch ($thisfile_asf_audiomedia_currentstream['raw']['wFormatTag']) {
							case 0x0001: // PCM
							case 0x0163: // WMA9 Lossless
								$thisfile_audio['lossless'] = true;
								break;
						}

						if (!empty($thisfile_asf['stream_bitrate_properties_object']['bitrate_records'])) {
							foreach ($thisfile_asf['stream_bitrate_properties_object']['bitrate_records'] as $dummy => $dataarray) {
								if (isset($dataarray['flags']['stream_number']) && ($dataarray['flags']['stream_number'] == $streamnumber)) {
									$thisfile_asf_audiomedia_currentstream['bitrate'] = $dataarray['bitrate'];
									$thisfile_audio['bitrate'] += $dataarray['bitrate'];
									break;
								}
							}
						} else {
							if (!empty($thisfile_asf_audiomedia_currentstream['bytes_sec'])) {
								$thisfile_audio['bitrate'] += $thisfile_asf_audiomedia_currentstream['bytes_sec'] * 8;
							} elseif (!empty($thisfile_asf_audiomedia_currentstream['bitrate'])) {
								$thisfile_audio['bitrate'] += $thisfile_asf_audiomedia_currentstream['bitrate'];
							}
						}
						$thisfile_audio['streams'][$streamnumber]                = $thisfile_asf_audiomedia_currentstream;
						$thisfile_audio['streams'][$streamnumber]['wformattag']  = $thisfile_asf_audiomedia_currentstream['raw']['wFormatTag'];
						$thisfile_audio['streams'][$streamnumber]['lossless']    = $thisfile_audio['lossless'];
						$thisfile_audio['streams'][$streamnumber]['bitrate']     = $thisfile_audio['bitrate'];
						$thisfile_audio['streams'][$streamnumber]['dataformat']  = 'wma';
						unset($thisfile_audio['streams'][$streamnumber]['raw']);

						$thisfile_asf_audiomedia_currentstream['codec_data_size'] = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $audiomediaoffset, 2));
						$audiomediaoffset += 2;
						$thisfile_asf_audiomedia_currentstream['codec_data']      = substr($streamdata['type_specific_data'], $audiomediaoffset, $thisfile_asf_audiomedia_currentstream['codec_data_size']);
						$audiomediaoffset += $thisfile_asf_audiomedia_currentstream['codec_data_size'];

						break;

					case GETID3_ASF_Video_Media:
						// Field Name                   Field Type   Size (bits)
						// Encoded Image Width          DWORD        32              // width of image in pixels
						// Encoded Image Height         DWORD        32              // height of image in pixels
						// Reserved Flags               BYTE         8               // hardcoded: 0x02
						// Format Data Size             WORD         16              // size of Format Data field in bytes
						// Format Data                  array of:    variable        //
						// * Format Data Size           DWORD        32              // number of bytes in Format Data field, in bytes - defined as biSize field of BITMAPINFOHEADER structure
						// * Image Width                LONG         32              // width of encoded image in pixels - defined as biWidth field of BITMAPINFOHEADER structure
						// * Image Height               LONG         32              // height of encoded image in pixels - defined as biHeight field of BITMAPINFOHEADER structure
						// * Reserved                   WORD         16              // hardcoded: 0x0001 - defined as biPlanes field of BITMAPINFOHEADER structure
						// * Bits Per Pixel Count       WORD         16              // bits per pixel - defined as biBitCount field of BITMAPINFOHEADER structure
						// * Compression ID             FOURCC       32              // fourcc of video codec - defined as biCompression field of BITMAPINFOHEADER structure
						// * Image Size                 DWORD        32              // image size in bytes - defined as biSizeImage field of BITMAPINFOHEADER structure
						// * Horizontal Pixels / Meter  DWORD        32              // horizontal resolution of target device in pixels per meter - defined as biXPelsPerMeter field of BITMAPINFOHEADER structure
						// * Vertical Pixels / Meter    DWORD        32              // vertical resolution of target device in pixels per meter - defined as biYPelsPerMeter field of BITMAPINFOHEADER structure
						// * Colors Used Count          DWORD        32              // number of color indexes in the color table that are actually used - defined as biClrUsed field of BITMAPINFOHEADER structure
						// * Important Colors Count     DWORD        32              // number of color index required for displaying bitmap. if zero, all colors are required. defined as biClrImportant field of BITMAPINFOHEADER structure
						// * Codec Specific Data        BYTESTREAM   variable        // array of codec-specific data bytes

						// shortcut
						$thisfile_asf['video_media'][$streamnumber] = array();
						$thisfile_asf_videomedia_currentstream      = &$thisfile_asf['video_media'][$streamnumber];

						$videomediaoffset = 0;
						$thisfile_asf_videomedia_currentstream['image_width']                     = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['image_height']                    = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['flags']                           = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 1));
						$videomediaoffset += 1;
						$thisfile_asf_videomedia_currentstream['format_data_size']                = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 2));
						$videomediaoffset += 2;
						$thisfile_asf_videomedia_currentstream['format_data']['format_data_size'] = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['image_width']      = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['image_height']     = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['reserved']         = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 2));
						$videomediaoffset += 2;
						$thisfile_asf_videomedia_currentstream['format_data']['bits_per_pixel']   = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 2));
						$videomediaoffset += 2;
						$thisfile_asf_videomedia_currentstream['format_data']['codec_fourcc']     = substr($streamdata['type_specific_data'], $videomediaoffset, 4);
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['image_size']       = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['horizontal_pels']  = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['vertical_pels']    = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['colors_used']      = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['colors_important'] = getid3_lib::LittleEndian2Int(substr($streamdata['type_specific_data'], $videomediaoffset, 4));
						$videomediaoffset += 4;
						$thisfile_asf_videomedia_currentstream['format_data']['codec_data']       = substr($streamdata['type_specific_data'], $videomediaoffset);

						if (!empty($thisfile_asf['stream_bitrate_properties_object']['bitrate_records'])) {
							foreach ($thisfile_asf['stream_bitrate_properties_object']['bitrate_records'] as $dummy => $dataarray) {
								if (isset($dataarray['flags']['stream_number']) && ($dataarray['flags']['stream_number'] == $streamnumber)) {
									$thisfile_asf_videomedia_currentstream['bitrate'] = $dataarray['bitrate'];
									$thisfile_video['streams'][$streamnumber]['bitrate'] = $dataarray['bitrate'];
									$thisfile_video['bitrate'] += $dataarray['bitrate'];
									break;
								}
							}
						}

						$thisfile_asf_videomedia_currentstream['format_data']['codec'] = getid3_riff::fourccLookup($thisfile_asf_videomedia_currentstream['format_data']['codec_fourcc']);

						$thisfile_video['streams'][$streamnumber]['fourcc']          = $thisfile_asf_videomedia_currentstream['format_data']['codec_fourcc'];
						$thisfile_video['streams'][$streamnumber]['codec']           = $thisfile_asf_videomedia_currentstream['format_data']['codec'];
						$thisfile_video['streams'][$streamnumber]['resolution_x']    = $thisfile_asf_videomedia_currentstream['image_width'];
						$thisfile_video['streams'][$streamnumber]['resolution_y']    = $thisfile_asf_videomedia_currentstream['image_height'];
						$thisfile_video['streams'][$streamnumber]['bits_per_sample'] = $thisfile_asf_videomedia_currentstream['format_data']['bits_per_pixel'];
						break;

					default:
						break;
				}
			}
		}

		while ($this->ftell() < $info['avdataend']) {
			$NextObjectDataHeader = $this->fread(24);
			$offset = 0;
			$NextObjectGUID = substr($NextObjectDataHeader, 0, 16);
			$offset += 16;
			$NextObjectGUIDtext = $this->BytestringToGUID($NextObjectGUID);
			$NextObjectSize = getid3_lib::LittleEndian2Int(substr($NextObjectDataHeader, $offset, 8));
			$offset += 8;

			switch ($NextObjectGUID) {
				case GETID3_ASF_Data_Object:
					// Data Object: (mandatory, one only)
					// Field Name                       Field Type   Size (bits)
					// Object ID                        GUID         128             // GUID for Data object - GETID3_ASF_Data_Object
					// Object Size                      QWORD        64              // size of Data object, including 50 bytes of Data Object header. may be 0 if FilePropertiesObject.BroadcastFlag == 1
					// File ID                          GUID         128             // unique identifier. identical to File ID field in Header Object
					// Total Data Packets               QWORD        64              // number of Data Packet entries in Data Object. invalid if FilePropertiesObject.BroadcastFlag == 1
					// Reserved                         WORD         16              // hardcoded: 0x0101

					// shortcut
					$thisfile_asf['data_object'] = array();
					$thisfile_asf_dataobject     = &$thisfile_asf['data_object'];

					$DataObjectData = $NextObjectDataHeader.$this->fread(50 - 24);
					$offset = 24;

					$thisfile_asf_dataobject['objectid']           = $NextObjectGUID;
					$thisfile_asf_dataobject['objectid_guid']      = $NextObjectGUIDtext;
					$thisfile_asf_dataobject['objectsize']         = $NextObjectSize;

					$thisfile_asf_dataobject['fileid']             = substr($DataObjectData, $offset, 16);
					$offset += 16;
					$thisfile_asf_dataobject['fileid_guid']        = $this->BytestringToGUID($thisfile_asf_dataobject['fileid']);
					$thisfile_asf_dataobject['total_data_packets'] = getid3_lib::LittleEndian2Int(substr($DataObjectData, $offset, 8));
					$offset += 8;
					$thisfile_asf_dataobject['reserved']           = getid3_lib::LittleEndian2Int(substr($DataObjectData, $offset, 2));
					$offset += 2;
					if ($thisfile_asf_dataobject['reserved'] != 0x0101) {
						$info['warning'][] = 'data_object.reserved ('.getid3_lib::PrintHexBytes($thisfile_asf_dataobject['reserved']).') does not match expected value of "0x0101"';
						//return false;
						break;
					}

					// Data Packets                     array of:    variable        //
					// * Error Correction Flags         BYTE         8               //
					// * * Error Correction Data Length bits         4               // if Error Correction Length Type == 00, size of Error Correction Data in bytes, else hardcoded: 0000
					// * * Opaque Data Present          bits         1               //
					// * * Error Correction Length Type bits         2               // number of bits for size of the error correction data. hardcoded: 00
					// * * Error Correction Present     bits         1               // If set, use Opaque Data Packet structure, else use Payload structure
					// * Error Correction Data

					$info['avdataoffset'] = $this->ftell();
					$this->fseek(($thisfile_asf_dataobject['objectsize'] - 50), SEEK_CUR); // skip actual audio/video data
					$info['avdataend'] = $this->ftell();
					break;

				case GETID3_ASF_Simple_Index_Object:
					// Simple Index Object: (optional, recommended, one per video stream)
					// Field Name                       Field Type   Size (bits)
					// Object ID                        GUID         128             // GUID for Simple Index object - GETID3_ASF_Data_Object
					// Object Size                      QWORD        64              // size of Simple Index object, including 56 bytes of Simple Index Object header
					// File ID                          GUID         128             // unique identifier. may be zero or identical to File ID field in Data Object and Header Object
					// Index Entry Time Interval        QWORD        64              // interval between index entries in 100-nanosecond units
					// Maximum Packet Count             DWORD        32              // maximum packet count for all index entries
					// Index Entries Count              DWORD        32              // number of Index Entries structures
					// Index Entries                    array of:    variable        //
					// * Packet Number                  DWORD        32              // number of the Data Packet associated with this index entry
					// * Packet Count                   WORD         16              // number of Data Packets to sent at this index entry

					// shortcut
					$thisfile_asf['simple_index_object'] = array();
					$thisfile_asf_simpleindexobject      = &$thisfile_asf['simple_index_object'];

					$SimpleIndexObjectData = $NextObjectDataHeader.$this->fread(56 - 24);
					$offset = 24;

					$thisfile_asf_simpleindexobject['objectid']                  = $NextObjectGUID;
					$thisfile_asf_simpleindexobject['objectid_guid']             = $NextObjectGUIDtext;
					$thisfile_asf_simpleindexobject['objectsize']                = $NextObjectSize;

					$thisfile_asf_simpleindexobject['fileid']                    =                  substr($SimpleIndexObjectData, $offset, 16);
					$offset += 16;
					$thisfile_asf_simpleindexobject['fileid_guid']               = $this->BytestringToGUID($thisfile_asf_simpleindexobject['fileid']);
					$thisfile_asf_simpleindexobject['index_entry_time_interval'] = getid3_lib::LittleEndian2Int(substr($SimpleIndexObjectData, $offset, 8));
					$offset += 8;
					$thisfile_asf_simpleindexobject['maximum_packet_count']      = getid3_lib::LittleEndian2Int(substr($SimpleIndexObjectData, $offset, 4));
					$offset += 4;
					$thisfile_asf_simpleindexobject['index_entries_count']       = getid3_lib::LittleEndian2Int(substr($SimpleIndexObjectData, $offset, 4));
					$offset += 4;

					$IndexEntriesData = $SimpleIndexObjectData.$this->fread(6 * $thisfile_asf_simpleindexobject['index_entries_count']);
					for ($IndexEntriesCounter = 0; $IndexEntriesCounter < $thisfile_asf_simpleindexobject['index_entries_count']; $IndexEntriesCounter++) {
						$thisfile_asf_simpleindexobject['index_entries'][$IndexEntriesCounter]['packet_number'] = getid3_lib::LittleEndian2Int(substr($IndexEntriesData, $offset, 4));
						$offset += 4;
						$thisfile_asf_simpleindexobject['index_entries'][$IndexEntriesCounter]['packet_count']  = getid3_lib::LittleEndian2Int(substr($IndexEntriesData, $offset, 4));
						$offset += 2;
					}

					break;

				case GETID3_ASF_Index_Object:
					// 6.2 ASF top-level Index Object (optional but recommended when appropriate, 0 or 1)
					// Field Name                       Field Type   Size (bits)
					// Object ID                        GUID         128             // GUID for the Index Object - GETID3_ASF_Index_Object
					// Object Size                      QWORD        64              // Specifies the size, in bytes, of the Index Object, including at least 34 bytes of Index Object header
					// Index Entry Time Interval        DWORD        32              // Specifies the time interval between each index entry in ms.
					// Index Specifiers Count           WORD         16              // Specifies the number of Index Specifiers structures in this Index Object.
					// Index Blocks Count               DWORD        32              // Specifies the number of Index Blocks structures in this Index Object.

					// Index Entry Time Interval        DWORD        32              // Specifies the time interval between index entries in milliseconds.  This value cannot be 0.
					// Index Specifiers Count           WORD         16              // Specifies the number of entries in the Index Specifiers list.  Valid values are 1 and greater.
					// Index Specifiers                 array of:    varies          //
					// * Stream Number                  WORD         16              // Specifies the stream number that the Index Specifiers refer to. Valid values are between 1 and 127.
					// * Index Type                     WORD         16              // Specifies Index Type values as follows:
																					//   1 = Nearest Past Data Packet - indexes point to the data packet whose presentation time is closest to the index entry time.
																					//   2 = Nearest Past Media Object - indexes point to the closest data packet containing an entire object or first fragment of an object.
																					//   3 = Nearest Past Cleanpoint. - indexes point to the closest data packet containing an entire object (or first fragment of an object) that has the Cleanpoint Flag set.
																					//   Nearest Past Cleanpoint is the most common type of index.
					// Index Entry Count                DWORD        32              // Specifies the number of Index Entries in the block.
					// * Block Positions                QWORD        varies          // Specifies a list of byte offsets of the beginnings of the blocks relative to the beginning of the first Data Packet (i.e., the beginning of the Data Object + 50 bytes). The number of entries in this list is specified by the value of the Index Specifiers Count field. The order of those byte offsets is tied to the order in which Index Specifiers are listed.
					// * Index Entries                  array of:    varies          //
					// * * Offsets                      DWORD        varies          // An offset value of 0xffffffff indicates an invalid offset value

					// shortcut
					$thisfile_asf['asf_index_object'] = array();
					$thisfile_asf_asfindexobject      = &$thisfile_asf['asf_index_object'];

					$ASFIndexObjectData = $NextObjectDataHeader.$this->fread(34 - 24);
					$offset = 24;

					$thisfile_asf_asfindexobject['objectid']                  = $NextObjectGUID;
					$thisfile_asf_asfindexobject['objectid_guid']             = $NextObjectGUIDtext;
					$thisfile_asf_asfindexobject['objectsize']                = $NextObjectSize;

					$thisfile_asf_asfindexobject['entry_time_interval']       = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
					$offset += 4;
					$thisfile_asf_asfindexobject['index_specifiers_count']    = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 2));
					$offset += 2;
					$thisfile_asf_asfindexobject['index_blocks_count']        = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
					$offset += 4;

					$ASFIndexObjectData .= $this->fread(4 * $thisfile_asf_asfindexobject['index_specifiers_count']);
					for ($IndexSpecifiersCounter = 0; $IndexSpecifiersCounter < $thisfile_asf_asfindexobject['index_specifiers_count']; $IndexSpecifiersCounter++) {
						$IndexSpecifierStreamNumber = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 2));
						$offset += 2;
						$thisfile_asf_asfindexobject['index_specifiers'][$IndexSpecifiersCounter]['stream_number']   = $IndexSpecifierStreamNumber;
						$thisfile_asf_asfindexobject['index_specifiers'][$IndexSpecifiersCounter]['index_type']      = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 2));
						$offset += 2;
						$thisfile_asf_asfindexobject['index_specifiers'][$IndexSpecifiersCounter]['index_type_text'] = $this->ASFIndexObjectIndexTypeLookup($thisfile_asf_asfindexobject['index_specifiers'][$IndexSpecifiersCounter]['index_type']);
					}

					$ASFIndexObjectData .= $this->fread(4);
					$thisfile_asf_asfindexobject['index_entry_count'] = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
					$offset += 4;

					$ASFIndexObjectData .= $this->fread(8 * $thisfile_asf_asfindexobject['index_specifiers_count']);
					for ($IndexSpecifiersCounter = 0; $IndexSpecifiersCounter < $thisfile_asf_asfindexobject['index_specifiers_count']; $IndexSpecifiersCounter++) {
						$thisfile_asf_asfindexobject['block_positions'][$IndexSpecifiersCounter] = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 8));
						$offset += 8;
					}

					$ASFIndexObjectData .= $this->fread(4 * $thisfile_asf_asfindexobject['index_specifiers_count'] * $thisfile_asf_asfindexobject['index_entry_count']);
					for ($IndexEntryCounter = 0; $IndexEntryCounter < $thisfile_asf_asfindexobject['index_entry_count']; $IndexEntryCounter++) {
						for ($IndexSpecifiersCounter = 0; $IndexSpecifiersCounter < $thisfile_asf_asfindexobject['index_specifiers_count']; $IndexSpecifiersCounter++) {
							$thisfile_asf_asfindexobject['offsets'][$IndexSpecifiersCounter][$IndexEntryCounter] = getid3_lib::LittleEndian2Int(substr($ASFIndexObjectData, $offset, 4));
							$offset += 4;
						}
					}
					break;


				default:
					// Implementations shall ignore any standard or non-standard object that they do not know how to handle.
					if ($this->GUIDname($NextObjectGUIDtext)) {
						$info['warning'][] = 'unhandled GUID "'.$this->GUIDname($NextObjectGUIDtext).'" {'.$NextObjectGUIDtext.'} in ASF body at offset '.($offset - 16 - 8);
					} else {
						$info['warning'][] = 'unknown GUID {'.$NextObjectGUIDtext.'} in ASF body at offset '.($this->ftell() - 16 - 8);
					}
					$this->fseek(($NextObjectSize - 16 - 8), SEEK_CUR);
					break;
			}
		}

		if (isset($thisfile_asf_codeclistobject['codec_entries']) && is_array($thisfile_asf_codeclistobject['codec_entries'])) {
			foreach ($thisfile_asf_codeclistobject['codec_entries'] as $streamnumber => $streamdata) {
				switch ($streamdata['information']) {
					case 'WMV1':
					case 'WMV2':
					case 'WMV3':
					case 'MSS1':
					case 'MSS2':
					case 'WMVA':
					case 'WVC1':
					case 'WMVP':
					case 'WVP2':
						$thisfile_video['dataformat'] = 'wmv';
						$info['mime_type'] = 'video/x-ms-wmv';
						break;

					case 'MP42':
					case 'MP43':
					case 'MP4S':
					case 'mp4s':
						$thisfile_video['dataformat'] = 'asf';
						$info['mime_type'] = 'video/x-ms-asf';
						break;

					default:
						switch ($streamdata['type_raw']) {
							case 1:
								if (strstr($this->TrimConvert($streamdata['name']), 'Windows Media')) {
									$thisfile_video['dataformat'] = 'wmv';
									if ($info['mime_type'] == 'video/x-ms-asf') {
										$info['mime_type'] = 'video/x-ms-wmv';
									}
								}
								break;

							case 2:
								if (strstr($this->TrimConvert($streamdata['name']), 'Windows Media')) {
									$thisfile_audio['dataformat'] = 'wma';
									if ($info['mime_type'] == 'video/x-ms-asf') {
										$info['mime_type'] = 'audio/x-ms-wma';
									}
								}
								break;

						}
						break;
				}
			}
		}

		switch (isset($thisfile_audio['codec']) ? $thisfile_audio['codec'] : '') {
			case 'MPEG Layer-3':
				$thisfile_audio['dataformat'] = 'mp3';
				break;

			default:
				break;
		}

		if (isset($thisfile_asf_codeclistobject['codec_entries'])) {
			foreach ($thisfile_asf_codeclistobject['codec_entries'] as $streamnumber => $streamdata) {
				switch ($streamdata['type_raw']) {

					case 1: // video
						$thisfile_video['encoder'] = $this->TrimConvert($thisfile_asf_codeclistobject['codec_entries'][$streamnumber]['name']);
						break;

					case 2: // audio
						$thisfile_audio['encoder'] = $this->TrimConvert($thisfile_asf_codeclistobject['codec_entries'][$streamnumber]['name']);

						// AH 2003-10-01
						$thisfile_audio['encoder_options'] = $this->TrimConvert($thisfile_asf_codeclistobject['codec_entries'][0]['description']);

						$thisfile_audio['codec']   = $thisfile_audio['encoder'];
						break;

					default:
						$info['warning'][] = 'Unknown streamtype: [codec_list_object][codec_entries]['.$streamnumber.'][type_raw] == '.$streamdata['type_raw'];
						break;

				}
			}
		}

		if (isset($info['audio'])) {
			$thisfile_audio['lossless']           = (isset($thisfile_audio['lossless'])           ? $thisfile_audio['lossless']           : false);
			$thisfile_audio['dataformat']         = (!empty($thisfile_audio['dataformat'])        ? $thisfile_audio['dataformat']         : 'asf');
		}
		if (!empty($thisfile_video['dataformat'])) {
			$thisfile_video['lossless']           = (isset($thisfile_audio['lossless'])           ? $thisfile_audio['lossless']           : false);
			$thisfile_video['pixel_aspect_ratio'] = (isset($thisfile_audio['pixel_aspect_ratio']) ? $thisfile_audio['pixel_aspect_ratio'] : (float) 1);
			$thisfile_video['dataformat']         = (!empty($thisfile_video['dataformat'])        ? $thisfile_video['dataformat']         : 'asf');
		}
		if (!empty($thisfile_video['streams'])) {
			$thisfile_video['resolution_x'] = 0;
			$thisfile_video['resolution_y'] = 0;
			foreach ($thisfile_video['streams'] as $key => $valuearray) {
				if (($valuearray['resolution_x'] > $thisfile_video['resolution_x']) || ($valuearray['resolution_y'] > $thisfile_video['resolution_y'])) {
					$thisfile_video['resolution_x'] = $valuearray['resolution_x'];
					$thisfile_video['resolution_y'] = $valuearray['resolution_y'];
				}
			}
		}
		$info['bitrate'] = (isset($thisfile_audio['bitrate']) ? $thisfile_audio['bitrate'] : 0) + (isset($thisfile_video['bitrate']) ? $thisfile_video['bitrate'] : 0);

		if ((!isset($info['playtime_seconds']) || ($info['playtime_seconds'] <= 0)) && ($info['bitrate'] > 0)) {
			$info['playtime_seconds'] = ($info['filesize'] - $info['avdataoffset']) / ($info['bitrate'] / 8);
		}

		return true;
	}

	public static function codecListObjectTypeLookup($CodecListType) {
		static $lookup = array(
			0x0001 => 'Video Codec',
			0x0002 => 'Audio Codec',
			0xFFFF => 'Unknown Codec'
		);

		return (isset($lookup[$CodecListType]) ? $lookup[$CodecListType] : 'Invalid Codec Type');
	}

	public static function KnownGUIDs() {
		static $GUIDarray = array(
			'GETID3_ASF_Extended_Stream_Properties_Object'   => '14E6A5CB-C672-4332-8399-A96952065B5A',
			'GETID3_ASF_Padding_Object'                      => '1806D474-CADF-4509-A4BA-9AABCB96AAE8',
			'GETID3_ASF_Payload_Ext_Syst_Pixel_Aspect_Ratio' => '1B1EE554-F9EA-4BC8-821A-376B74E4C4B8',
			'GETID3_ASF_Script_Command_Object'               => '1EFB1A30-0B62-11D0-A39B-00A0C90348F6',
			'GETID3_ASF_No_Error_Correction'                 => '20FB5700-5B55-11CF-A8FD-00805F5C442B',
			'GETID3_ASF_Content_Branding_Object'             => '2211B3FA-BD23-11D2-B4B7-00A0C955FC6E',
			'GETID3_ASF_Content_Encryption_Object'           => '2211B3FB-BD23-11D2-B4B7-00A0C955FC6E',
			'GETID3_ASF_Digital_Signature_Object'            => '2211B3FC-BD23-11D2-B4B7-00A0C955FC6E',
			'GETID3_ASF_Extended_Content_Encryption_Object'  => '298AE614-2622-4C17-B935-DAE07EE9289C',
			'GETID3_ASF_Simple_Index_Object'                 => '33000890-E5B1-11CF-89F4-00A0C90349CB',
			'GETID3_ASF_Degradable_JPEG_Media'               => '35907DE0-E415-11CF-A917-00805F5C442B',
			'GETID3_ASF_Payload_Extension_System_Timecode'   => '399595EC-8667-4E2D-8FDB-98814CE76C1E',
			'GETID3_ASF_Binary_Media'                        => '3AFB65E2-47EF-40F2-AC2C-70A90D71D343',
			'GETID3_ASF_Timecode_Index_Object'               => '3CB73FD0-0C4A-4803-953D-EDF7B6228F0C',
			'GETID3_ASF_Metadata_Library_Object'             => '44231C94-9498-49D1-A141-1D134E457054',
			'GETID3_ASF_Reserved_3'                          => '4B1ACBE3-100B-11D0-A39B-00A0C90348F6',
			'GETID3_ASF_Reserved_4'                          => '4CFEDB20-75F6-11CF-9C0F-00A0C90349CB',
			'GETID3_ASF_Command_Media'                       => '59DACFC0-59E6-11D0-A3AC-00A0C90348F6',
			'GETID3_ASF_Header_Extension_Object'             => '5FBF03B5-A92E-11CF-8EE3-00C00C205365',
			'GETID3_ASF_Media_Object_Index_Parameters_Obj'   => '6B203BAD-3F11-4E84-ACA8-D7613DE2CFA7',
			'GETID3_ASF_Header_Object'                       => '75B22630-668E-11CF-A6D9-00AA0062CE6C',
			'GETID3_ASF_Content_Description_Object'          => '75B22633-668E-11CF-A6D9-00AA0062CE6C',
			'GETID3_ASF_Error_Correction_Object'             => '75B22635-668E-11CF-A6D9-00AA0062CE6C',
			'GETID3_ASF_Data_Object'                         => '75B22636-668E-11CF-A6D9-00AA0062CE6C',
			'GETID3_ASF_Web_Stream_Media_Subtype'            => '776257D4-C627-41CB-8F81-7AC7FF1C40CC',
			'GETID3_ASF_Stream_Bitrate_Properties_Object'    => '7BF875CE-468D-11D1-8D82-006097C9A2B2',
			'GETID3_ASF_Language_List_Object'                => '7C4346A9-EFE0-4BFC-B229-393EDE415C85',
			'GETID3_ASF_Codec_List_Object'                   => '86D15240-311D-11D0-A3A4-00A0C90348F6',
			'GETID3_ASF_Reserved_2'                          => '86D15241-311D-11D0-A3A4-00A0C90348F6',
			'GETID3_ASF_File_Properties_Object'              => '8CABDCA1-A947-11CF-8EE4-00C00C205365',
			'GETID3_ASF_File_Transfer_Media'                 => '91BD222C-F21C-497A-8B6D-5AA86BFC0185',
			'GETID3_ASF_Old_RTP_Extension_Data'              => '96800C63-4C94-11D1-837B-0080C7A37F95',
			'GETID3_ASF_Advanced_Mutual_Exclusion_Object'    => 'A08649CF-4775-4670-8A16-6E35357566CD',
			'GETID3_ASF_Bandwidth_Sharing_Object'            => 'A69609E6-517B-11D2-B6AF-00C04FD908E9',
			'GETID3_ASF_Reserved_1'                          => 'ABD3D211-A9BA-11cf-8EE6-00C00C205365',
			'GETID3_ASF_Bandwidth_Sharing_Exclusive'         => 'AF6060AA-5197-11D2-B6AF-00C04FD908E9',
			'GETID3_ASF_Bandwidth_Sharing_Partial'           => 'AF6060AB-5197-11D2-B6AF-00C04FD908E9',
			'GETID3_ASF_JFIF_Media'                          => 'B61BE100-5B4E-11CF-A8FD-00805F5C442B',
			'GETID3_ASF_Stream_Properties_Object'            => 'B7DC0791-A9B7-11CF-8EE6-00C00C205365',
			'GETID3_ASF_Video_Media'                         => 'BC19EFC0-5B4D-11CF-A8FD-00805F5C442B',
			'GETID3_ASF_Audio_Spread'                        => 'BFC3CD50-618F-11CF-8BB2-00AA00B4E220',
			'GETID3_ASF_Metadata_Object'                     => 'C5F8CBEA-5BAF-4877-8467-AA8C44FA4CCA',
			'GETID3_ASF_Payload_Ext_Syst_Sample_Duration'    => 'C6BD9450-867F-4907-83A3-C77921B733AD',
			'GETID3_ASF_Group_Mutual_Exclusion_Object'       => 'D1465A40-5A79-4338-B71B-E36B8FD6C249',
			'GETID3_ASF_Extended_Content_Description_Object' => 'D2D0A440-E307-11D2-97F0-00A0C95EA850',
			'GETID3_ASF_Stream_Prioritization_Object'        => 'D4FED15B-88D3-454F-81F0-ED5C45999E24',
			'GETID3_ASF_Payload_Ext_System_Content_Type'     => 'D590DC20-07BC-436C-9CF7-F3BBFBF1A4DC',
			'GETID3_ASF_Old_File_Properties_Object'          => 'D6E229D0-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_ASF_Header_Object'               => 'D6E229D1-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_ASF_Data_Object'                 => 'D6E229D2-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Index_Object'                        => 'D6E229D3-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Stream_Properties_Object'        => 'D6E229D4-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Content_Description_Object'      => 'D6E229D5-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Script_Command_Object'           => 'D6E229D6-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Marker_Object'                   => 'D6E229D7-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Component_Download_Object'       => 'D6E229D8-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Stream_Group_Object'             => 'D6E229D9-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Scalable_Object'                 => 'D6E229DA-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Prioritization_Object'           => 'D6E229DB-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Bitrate_Mutual_Exclusion_Object'     => 'D6E229DC-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Inter_Media_Dependency_Object'   => 'D6E229DD-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Rating_Object'                   => 'D6E229DE-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Index_Parameters_Object'             => 'D6E229DF-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Color_Table_Object'              => 'D6E229E0-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Language_List_Object'            => 'D6E229E1-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Audio_Media'                     => 'D6E229E2-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Video_Media'                     => 'D6E229E3-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Image_Media'                     => 'D6E229E4-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Timecode_Media'                  => 'D6E229E5-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Text_Media'                      => 'D6E229E6-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_MIDI_Media'                      => 'D6E229E7-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Command_Media'                   => 'D6E229E8-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_No_Error_Concealment'            => 'D6E229EA-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Scrambled_Audio'                 => 'D6E229EB-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_No_Color_Table'                  => 'D6E229EC-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_SMPTE_Time'                      => 'D6E229ED-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_ASCII_Text'                      => 'D6E229EE-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Unicode_Text'                    => 'D6E229EF-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_HTML_Text'                       => 'D6E229F0-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_URL_Command'                     => 'D6E229F1-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Filename_Command'                => 'D6E229F2-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_ACM_Codec'                       => 'D6E229F3-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_VCM_Codec'                       => 'D6E229F4-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_QuickTime_Codec'                 => 'D6E229F5-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_DirectShow_Transform_Filter'     => 'D6E229F6-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_DirectShow_Rendering_Filter'     => 'D6E229F7-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_No_Enhancement'                  => 'D6E229F8-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Unknown_Enhancement_Type'        => 'D6E229F9-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Temporal_Enhancement'            => 'D6E229FA-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Spatial_Enhancement'             => 'D6E229FB-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Quality_Enhancement'             => 'D6E229FC-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Number_of_Channels_Enhancement'  => 'D6E229FD-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Frequency_Response_Enhancement'  => 'D6E229FE-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Media_Object'                    => 'D6E229FF-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Mutex_Language'                      => 'D6E22A00-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Mutex_Bitrate'                       => 'D6E22A01-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Mutex_Unknown'                       => 'D6E22A02-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_ASF_Placeholder_Object'          => 'D6E22A0E-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Old_Data_Unit_Extension_Object'      => 'D6E22A0F-35DA-11D1-9034-00A0C90349BE',
			'GETID3_ASF_Web_Stream_Format'                   => 'DA1E6B13-8359-4050-B398-388E965BF00C',
			'GETID3_ASF_Payload_Ext_System_File_Name'        => 'E165EC0E-19ED-45D7-B4A7-25CBD1E28E9B',
			'GETID3_ASF_Marker_Object'                       => 'F487CD01-A951-11CF-8EE6-00C00C205365',
			'GETID3_ASF_Timecode_Index_Parameters_Object'    => 'F55E496D-9797-4B5D-8C8B-604DFE9BFB24',
			'GETID3_ASF_Audio_Media'                         => 'F8699E40-5B4D-11CF-A8FD-00805F5C442B',
			'GETID3_ASF_Media_Object_Index_Object'           => 'FEB103F8-12AD-4C64-840F-2A1D2F7AD48C',
			'GETID3_ASF_Alt_Extended_Content_Encryption_Obj' => 'FF889EF1-ADEE-40DA-9E71-98704BB928CE',
			'GETID3_ASF_Index_Placeholder_Object'            => 'D9AADE20-7C17-4F9C-BC28-8555DD98E2A2', // http://cpan.uwinnipeg.ca/htdocs/Audio-WMA/Audio/WMA.pm.html
			'GETID3_ASF_Compatibility_Object'                => '26F18B5D-4584-47EC-9F5F-0E651F0452C9', // http://cpan.uwinnipeg.ca/htdocs/Audio-WMA/Audio/WMA.pm.html
		);
		return $GUIDarray;
	}

	public static function GUIDname($GUIDstring) {
		static $GUIDarray = array();
		if (empty($GUIDarray)) {
			$GUIDarray = self::KnownGUIDs();
		}
		return array_search($GUIDstring, $GUIDarray);
	}

	public static function ASFIndexObjectIndexTypeLookup($id) {
		static $ASFIndexObjectIndexTypeLookup = array();
		if (empty($ASFIndexObjectIndexTypeLookup)) {
			$ASFIndexObjectIndexTypeLookup[1] = 'Nearest Past Data Packet';
			$ASFIndexObjectIndexTypeLookup[2] = 'Nearest Past Media Object';
			$ASFIndexObjectIndexTypeLookup[3] = 'Nearest Past Cleanpoint';
		}
		return (isset($ASFIndexObjectIndexTypeLookup[$id]) ? $ASFIndexObjectIndexTypeLookup[$id] : 'invalid');
	}

	public static function GUIDtoBytestring($GUIDstring) {
		// Microsoft defines these 16-byte (128-bit) GUIDs in the strangest way:
		// first 4 bytes are in little-endian order
		// next 2 bytes are appended in little-endian order
		// next 2 bytes are appended in little-endian order
		// next 2 bytes are appended in big-endian order
		// next 6 bytes are appended in big-endian order

		// AaBbCcDd-EeFf-GgHh-IiJj-KkLlMmNnOoPp is stored as this 16-byte string:
		// $Dd $Cc $Bb $Aa $Ff $Ee $Hh $Gg $Ii $Jj $Kk $Ll $Mm $Nn $Oo $Pp

		$hexbytecharstring  = chr(hexdec(substr($GUIDstring,  6, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring,  4, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring,  2, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring,  0, 2)));

		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 11, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring,  9, 2)));

		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 16, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 14, 2)));

		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 19, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 21, 2)));

		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 24, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 26, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 28, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 30, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 32, 2)));
		$hexbytecharstring .= chr(hexdec(substr($GUIDstring, 34, 2)));

		return $hexbytecharstring;
	}

	public static function BytestringToGUID($Bytestring) {
		$GUIDstring  = str_pad(dechex(ord($Bytestring{3})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{2})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{1})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{0})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= '-';
		$GUIDstring .= str_pad(dechex(ord($Bytestring{5})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{4})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= '-';
		$GUIDstring .= str_pad(dechex(ord($Bytestring{7})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{6})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= '-';
		$GUIDstring .= str_pad(dechex(ord($Bytestring{8})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{9})),  2, '0', STR_PAD_LEFT);
		$GUIDstring .= '-';
		$GUIDstring .= str_pad(dechex(ord($Bytestring{10})), 2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{11})), 2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{12})), 2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{13})), 2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{14})), 2, '0', STR_PAD_LEFT);
		$GUIDstring .= str_pad(dechex(ord($Bytestring{15})), 2, '0', STR_PAD_LEFT);

		return strtoupper($GUIDstring);
	}

	public static function FILETIMEtoUNIXtime($FILETIME, $round=true) {
		// FILETIME is a 64-bit unsigned integer representing
		// the number of 100-nanosecond intervals since January 1, 1601
		// UNIX timestamp is number of seconds since January 1, 1970
		// 116444736000000000 = 10000000 * 60 * 60 * 24 * 365 * 369 + 89 leap days
		if ($round) {
			return intval(round(($FILETIME - 116444736000000000) / 10000000));
		}
		return ($FILETIME - 116444736000000000) / 10000000;
	}

	public static function WMpictureTypeLookup($WMpictureType) {
		static $lookup = null;
		if ($lookup === null) {
			$lookup = array(
				0x03 => 'Front Cover',
				0x04 => 'Back Cover',
				0x00 => 'User Defined',
				0x05 => 'Leaflet Page',
				0x06 => 'Media Label',
				0x07 => 'Lead Artist',
				0x08 => 'Artist',
				0x09 => 'Conductor',
				0x0A => 'Band',
				0x0B => 'Composer',
				0x0C => 'Lyricist',
				0x0D => 'Recording Location',
				0x0E => 'During Recording',
				0x0F => 'During Performance',
				0x10 => 'Video Screen Capture',
				0x12 => 'Illustration',
				0x13 => 'Band Logotype',
				0x14 => 'Publisher Logotype'
			);
			$lookup = array_map(function($str) {
				return getid3_lib::iconv_fallback('UTF-8', 'UTF-16LE', $str);
			}, $lookup);
		}

		return (isset($lookup[$WMpictureType]) ? $lookup[$WMpictureType] : '');
	}

	public function HeaderExtensionObjectDataParse(&$asf_header_extension_object_data, &$unhandled_sections) {
		// http://msdn.microsoft.com/en-us/library/bb643323.aspx

		$offset = 0;
		$objectOffset = 0;
		$HeaderExtensionObjectParsed = array();
		while ($objectOffset < strlen($asf_header_extension_object_data)) {
			$offset = $objectOffset;
			$thisObject = array();

			$thisObject['guid']                              =                              substr($asf_header_extension_object_data, $offset, 16);
			$offset += 16;
			$thisObject['guid_text'] = $this->BytestringToGUID($thisObject['guid']);
			$thisObject['guid_name'] = $this->GUIDname($thisObject['guid_text']);

			$thisObject['size']                              = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  8));
			$offset += 8;
			if ($thisObject['size'] <= 0) {
				break;
			}

			switch ($thisObject['guid']) {
				case GETID3_ASF_Extended_Stream_Properties_Object:
					$thisObject['start_time']                        = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  8));
					$offset += 8;
					$thisObject['start_time_unix']                   = $this->FILETIMEtoUNIXtime($thisObject['start_time']);

					$thisObject['end_time']                          = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  8));
					$offset += 8;
					$thisObject['end_time_unix']                     = $this->FILETIMEtoUNIXtime($thisObject['end_time']);

					$thisObject['data_bitrate']                      = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['buffer_size']                       = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['initial_buffer_fullness']           = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['alternate_data_bitrate']            = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['alternate_buffer_size']             = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['alternate_initial_buffer_fullness'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['maximum_object_size']               = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['flags_raw']                         = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;
					$thisObject['flags']['reliable']                = (bool) $thisObject['flags_raw'] & 0x00000001;
					$thisObject['flags']['seekable']                = (bool) $thisObject['flags_raw'] & 0x00000002;
					$thisObject['flags']['no_cleanpoints']          = (bool) $thisObject['flags_raw'] & 0x00000004;
					$thisObject['flags']['resend_live_cleanpoints'] = (bool) $thisObject['flags_raw'] & 0x00000008;

					$thisObject['stream_number']                     = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
					$offset += 2;

					$thisObject['stream_language_id_index']          = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
					$offset += 2;

					$thisObject['average_time_per_frame']            = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
					$offset += 4;

					$thisObject['stream_name_count']                 = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
					$offset += 2;

					$thisObject['payload_extension_system_count']    = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
					$offset += 2;

					for ($i = 0; $i < $thisObject['stream_name_count']; $i++) {
						$streamName = array();

						$streamName['language_id_index']             = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;

						$streamName['stream_name_length']            = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;

						$streamName['stream_name']                   = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  $streamName['stream_name_length']));
						$offset += $streamName['stream_name_length'];

						$thisObject['stream_names'][$i] = $streamName;
					}

					for ($i = 0; $i < $thisObject['payload_extension_system_count']; $i++) {
						$payloadExtensionSystem = array();

						$payloadExtensionSystem['extension_system_id']   =                              substr($asf_header_extension_object_data, $offset, 16);
						$offset += 16;
						$payloadExtensionSystem['extension_system_id_text'] = $this->BytestringToGUID($payloadExtensionSystem['extension_system_id']);

						$payloadExtensionSystem['extension_system_size'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;
						if ($payloadExtensionSystem['extension_system_size'] <= 0) {
							break 2;
						}

						$payloadExtensionSystem['extension_system_info_length'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
						$offset += 4;

						$payloadExtensionSystem['extension_system_info_length'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  $payloadExtensionSystem['extension_system_info_length']));
						$offset += $payloadExtensionSystem['extension_system_info_length'];

						$thisObject['payload_extension_systems'][$i] = $payloadExtensionSystem;
					}

					break;

				case GETID3_ASF_Padding_Object:
					// padding, skip it
					break;

				case GETID3_ASF_Metadata_Object:
					$thisObject['description_record_counts'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
					$offset += 2;

					for ($i = 0; $i < $thisObject['description_record_counts']; $i++) {
						$descriptionRecord = array();

						$descriptionRecord['reserved_1']         = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2)); // must be zero
						$offset += 2;

						$descriptionRecord['stream_number']      = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;

						$descriptionRecord['name_length']        = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;

						$descriptionRecord['data_type']          = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;
						$descriptionRecord['data_type_text'] = self::metadataLibraryObjectDataTypeLookup($descriptionRecord['data_type']);

						$descriptionRecord['data_length']        = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
						$offset += 4;

						$descriptionRecord['name']               =                              substr($asf_header_extension_object_data, $offset,  $descriptionRecord['name_length']);
						$offset += $descriptionRecord['name_length'];

						$descriptionRecord['data']               =                              substr($asf_header_extension_object_data, $offset,  $descriptionRecord['data_length']);
						$offset += $descriptionRecord['data_length'];
						switch ($descriptionRecord['data_type']) {
							case 0x0000: // Unicode string
								break;

							case 0x0001: // BYTE array
								// do nothing
								break;

							case 0x0002: // BOOL
								$descriptionRecord['data'] = (bool) getid3_lib::LittleEndian2Int($descriptionRecord['data']);
								break;

							case 0x0003: // DWORD
							case 0x0004: // QWORD
							case 0x0005: // WORD
								$descriptionRecord['data'] = getid3_lib::LittleEndian2Int($descriptionRecord['data']);
								break;

							case 0x0006: // GUID
								$descriptionRecord['data_text'] = $this->BytestringToGUID($descriptionRecord['data']);
								break;
						}

						$thisObject['description_record'][$i] = $descriptionRecord;
					}
					break;

				case GETID3_ASF_Language_List_Object:
					$thisObject['language_id_record_counts'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
					$offset += 2;

					for ($i = 0; $i < $thisObject['language_id_record_counts']; $i++) {
						$languageIDrecord = array();

						$languageIDrecord['language_id_length']         = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  1));
						$offset += 1;

						$languageIDrecord['language_id']                =                              substr($asf_header_extension_object_data, $offset,  $languageIDrecord['language_id_length']);
						$offset += $languageIDrecord['language_id_length'];

						$thisObject['language_id_record'][$i] = $languageIDrecord;
					}
					break;

				case GETID3_ASF_Metadata_Library_Object:
					$thisObject['description_records_count'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
					$offset += 2;

					for ($i = 0; $i < $thisObject['description_records_count']; $i++) {
						$descriptionRecord = array();

						$descriptionRecord['language_list_index'] = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;

						$descriptionRecord['stream_number']       = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;

						$descriptionRecord['name_length']         = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;

						$descriptionRecord['data_type']           = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  2));
						$offset += 2;
						$descriptionRecord['data_type_text'] = self::metadataLibraryObjectDataTypeLookup($descriptionRecord['data_type']);

						$descriptionRecord['data_length']         = getid3_lib::LittleEndian2Int(substr($asf_header_extension_object_data, $offset,  4));
						$offset += 4;

						$descriptionRecord['name']                =                              substr($asf_header_extension_object_data, $offset,  $descriptionRecord['name_length']);
						$offset += $descriptionRecord['name_length'];

						$descriptionRecord['data']                =                              substr($asf_header_extension_object_data, $offset,  $descriptionRecord['data_length']);
						$offset += $descriptionRecord['data_length'];

						if (preg_match('#^WM/Picture$#', str_replace("\x00", '', trim($descriptionRecord['name'])))) {
							$WMpicture = $this->ASF_WMpicture($descriptionRecord['data']);
							foreach ($WMpicture as $key => $value) {
								$descriptionRecord['data'] = $WMpicture;
							}
							unset($WMpicture);
						}

						$thisObject['description_record'][$i] = $descriptionRecord;
					}
					break;

				default:
					$unhandled_sections++;
					if ($this->GUIDname($thisObject['guid_text'])) {
						$this->getid3->info['warning'][] = 'unhandled Header Extension Object GUID "'.$this->GUIDname($thisObject['guid_text']).'" {'.$thisObject['guid_text'].'} at offset '.($offset - 16 - 8);
					} else {
						$this->getid3->info['warning'][] = 'unknown Header Extension Object GUID {'.$thisObject['guid_text'].'} in at offset '.($offset - 16 - 8);
					}
					break;
			}
			$HeaderExtensionObjectParsed[] = $thisObject;

			$objectOffset += $thisObject['size'];
		}
		return $HeaderExtensionObjectParsed;
	}


	public static function metadataLibraryObjectDataTypeLookup($id) {
		static $lookup = array(
			0x0000 => 'Unicode string', // The data consists of a sequence of Unicode characters
			0x0001 => 'BYTE array',     // The type of the data is implementation-specific
			0x0002 => 'BOOL',           // The data is 2 bytes long and should be interpreted as a 16-bit unsigned integer. Only 0x0000 or 0x0001 are permitted values
			0x0003 => 'DWORD',          // The data is 4 bytes long and should be interpreted as a 32-bit unsigned integer
			0x0004 => 'QWORD',          // The data is 8 bytes long and should be interpreted as a 64-bit unsigned integer
			0x0005 => 'WORD',           // The data is 2 bytes long and should be interpreted as a 16-bit unsigned integer
			0x0006 => 'GUID',           // The data is 16 bytes long and should be interpreted as a 128-bit GUID
		);
		return (isset($lookup[$id]) ? $lookup[$id] : 'invalid');
	}

	public function ASF_WMpicture(&$data) {
		//typedef struct _WMPicture{
		//  LPWSTR  pwszMIMEType;
		//  BYTE  bPictureType;
		//  LPWSTR  pwszDescription;
		//  DWORD  dwDataLen;
		//  BYTE*  pbData;
		//} WM_PICTURE;

		$WMpicture = array();

		$offset = 0;
		$WMpicture['image_type_id'] = getid3_lib::LittleEndian2Int(substr($data, $offset, 1));
		$offset += 1;
		$WMpicture['image_type']    = self::WMpictureTypeLookup($WMpicture['image_type_id']);
		$WMpicture['image_size']    = getid3_lib::LittleEndian2Int(substr($data, $offset, 4));
		$offset += 4;

		$WMpicture['image_mime'] = '';
		do {
			$next_byte_pair = substr($data, $offset, 2);
			$offset += 2;
			$WMpicture['image_mime'] .= $next_byte_pair;
		} while ($next_byte_pair !== "\x00\x00");

		$WMpicture['image_description'] = '';
		do {
			$next_byte_pair = substr($data, $offset, 2);
			$offset += 2;
			$WMpicture['image_description'] .= $next_byte_pair;
		} while ($next_byte_pair !== "\x00\x00");

		$WMpicture['dataoffset'] = $offset;
		$WMpicture['data'] = substr($data, $offset);

		$imageinfo = array();
		$WMpicture['image_mime'] = '';
		$imagechunkcheck = getid3_lib::GetDataImageSize($WMpicture['data'], $imageinfo);
		unset($imageinfo);
		if (!empty($imagechunkcheck)) {
			$WMpicture['image_mime'] = image_type_to_mime_type($imagechunkcheck[2]);
		}
		if (!isset($this->getid3->info['asf']['comments']['picture'])) {
			$this->getid3->info['asf']['comments']['picture'] = array();
		}
		$this->getid3->info['asf']['comments']['picture'][] = array('data'=>$WMpicture['data'], 'image_mime'=>$WMpicture['image_mime']);

		return $WMpicture;
	}


	// Remove terminator 00 00 and convert UTF-16LE to Latin-1
	public static function TrimConvert($string) {
		return trim(getid3_lib::iconv_fallback('UTF-16LE', 'ISO-8859-1', self::TrimTerm($string)), ' ');
	}


	// Remove terminator 00 00
	public static function TrimTerm($string) {
		// remove terminator, only if present (it should be, but...)
		if (substr($string, -2) === "\x00\x00") {
			$string = substr($string, 0, -2);
		}
		return $string;
	}

}
