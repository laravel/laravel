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
// module.misc.iso.php                                         //
// module for analyzing ISO files                              //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_iso extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'iso';

		for ($i = 16; $i <= 19; $i++) {
			$this->fseek(2048 * $i);
			$ISOheader = $this->fread(2048);
			if (substr($ISOheader, 1, 5) == 'CD001') {
				switch (ord($ISOheader{0})) {
					case 1:
						$info['iso']['primary_volume_descriptor']['offset'] = 2048 * $i;
						$this->ParsePrimaryVolumeDescriptor($ISOheader);
						break;

					case 2:
						$info['iso']['supplementary_volume_descriptor']['offset'] = 2048 * $i;
						$this->ParseSupplementaryVolumeDescriptor($ISOheader);
						break;

					default:
						// skip
						break;
				}
			}
		}

		$this->ParsePathTable();

		$info['iso']['files'] = array();
		foreach ($info['iso']['path_table']['directories'] as $directorynum => $directorydata) {
			$info['iso']['directories'][$directorynum] = $this->ParseDirectoryRecord($directorydata);
		}

		return true;
	}


	public function ParsePrimaryVolumeDescriptor(&$ISOheader) {
		// ISO integer values are stored *BOTH* Little-Endian AND Big-Endian format!!
		// ie 12345 == 0x3039  is stored as $39 $30 $30 $39 in a 4-byte field

		// shortcuts
		$info = &$this->getid3->info;
		$info['iso']['primary_volume_descriptor']['raw'] = array();
		$thisfile_iso_primaryVD     = &$info['iso']['primary_volume_descriptor'];
		$thisfile_iso_primaryVD_raw = &$thisfile_iso_primaryVD['raw'];

		$thisfile_iso_primaryVD_raw['volume_descriptor_type']         = getid3_lib::LittleEndian2Int(substr($ISOheader,    0, 1));
		$thisfile_iso_primaryVD_raw['standard_identifier']            =                  substr($ISOheader,    1, 5);
		if ($thisfile_iso_primaryVD_raw['standard_identifier'] != 'CD001') {
			$info['error'][] = 'Expected "CD001" at offset ('.($thisfile_iso_primaryVD['offset'] + 1).'), found "'.$thisfile_iso_primaryVD_raw['standard_identifier'].'" instead';
			unset($info['fileformat']);
			unset($info['iso']);
			return false;
		}


		$thisfile_iso_primaryVD_raw['volume_descriptor_version']     = getid3_lib::LittleEndian2Int(substr($ISOheader,    6, 1));
		//$thisfile_iso_primaryVD_raw['unused_1']                      =                              substr($ISOheader,    7, 1);
		$thisfile_iso_primaryVD_raw['system_identifier']             =                              substr($ISOheader,    8, 32);
		$thisfile_iso_primaryVD_raw['volume_identifier']             =                              substr($ISOheader,   40, 32);
		//$thisfile_iso_primaryVD_raw['unused_2']                      =                              substr($ISOheader,   72, 8);
		$thisfile_iso_primaryVD_raw['volume_space_size']             = getid3_lib::LittleEndian2Int(substr($ISOheader,   80, 4));
		//$thisfile_iso_primaryVD_raw['unused_3']                      =                              substr($ISOheader,   88, 32);
		$thisfile_iso_primaryVD_raw['volume_set_size']               = getid3_lib::LittleEndian2Int(substr($ISOheader,  120, 2));
		$thisfile_iso_primaryVD_raw['volume_sequence_number']        = getid3_lib::LittleEndian2Int(substr($ISOheader,  124, 2));
		$thisfile_iso_primaryVD_raw['logical_block_size']            = getid3_lib::LittleEndian2Int(substr($ISOheader,  128, 2));
		$thisfile_iso_primaryVD_raw['path_table_size']               = getid3_lib::LittleEndian2Int(substr($ISOheader,  132, 4));
		$thisfile_iso_primaryVD_raw['path_table_l_location']         = getid3_lib::LittleEndian2Int(substr($ISOheader,  140, 2));
		$thisfile_iso_primaryVD_raw['path_table_l_opt_location']     = getid3_lib::LittleEndian2Int(substr($ISOheader,  144, 2));
		$thisfile_iso_primaryVD_raw['path_table_m_location']         = getid3_lib::LittleEndian2Int(substr($ISOheader,  148, 2));
		$thisfile_iso_primaryVD_raw['path_table_m_opt_location']     = getid3_lib::LittleEndian2Int(substr($ISOheader,  152, 2));
		$thisfile_iso_primaryVD_raw['root_directory_record']         =                              substr($ISOheader,  156, 34);
		$thisfile_iso_primaryVD_raw['volume_set_identifier']         =                              substr($ISOheader,  190, 128);
		$thisfile_iso_primaryVD_raw['publisher_identifier']          =                              substr($ISOheader,  318, 128);
		$thisfile_iso_primaryVD_raw['data_preparer_identifier']      =                              substr($ISOheader,  446, 128);
		$thisfile_iso_primaryVD_raw['application_identifier']        =                              substr($ISOheader,  574, 128);
		$thisfile_iso_primaryVD_raw['copyright_file_identifier']     =                              substr($ISOheader,  702, 37);
		$thisfile_iso_primaryVD_raw['abstract_file_identifier']      =                              substr($ISOheader,  739, 37);
		$thisfile_iso_primaryVD_raw['bibliographic_file_identifier'] =                              substr($ISOheader,  776, 37);
		$thisfile_iso_primaryVD_raw['volume_creation_date_time']     =                              substr($ISOheader,  813, 17);
		$thisfile_iso_primaryVD_raw['volume_modification_date_time'] =                              substr($ISOheader,  830, 17);
		$thisfile_iso_primaryVD_raw['volume_expiration_date_time']   =                              substr($ISOheader,  847, 17);
		$thisfile_iso_primaryVD_raw['volume_effective_date_time']    =                              substr($ISOheader,  864, 17);
		$thisfile_iso_primaryVD_raw['file_structure_version']        = getid3_lib::LittleEndian2Int(substr($ISOheader,  881, 1));
		//$thisfile_iso_primaryVD_raw['unused_4']                      = getid3_lib::LittleEndian2Int(substr($ISOheader,  882, 1));
		$thisfile_iso_primaryVD_raw['application_data']              =                              substr($ISOheader,  883, 512);
		//$thisfile_iso_primaryVD_raw['unused_5']                      =                              substr($ISOheader, 1395, 653);

		$thisfile_iso_primaryVD['system_identifier']             = trim($thisfile_iso_primaryVD_raw['system_identifier']);
		$thisfile_iso_primaryVD['volume_identifier']             = trim($thisfile_iso_primaryVD_raw['volume_identifier']);
		$thisfile_iso_primaryVD['volume_set_identifier']         = trim($thisfile_iso_primaryVD_raw['volume_set_identifier']);
		$thisfile_iso_primaryVD['publisher_identifier']          = trim($thisfile_iso_primaryVD_raw['publisher_identifier']);
		$thisfile_iso_primaryVD['data_preparer_identifier']      = trim($thisfile_iso_primaryVD_raw['data_preparer_identifier']);
		$thisfile_iso_primaryVD['application_identifier']        = trim($thisfile_iso_primaryVD_raw['application_identifier']);
		$thisfile_iso_primaryVD['copyright_file_identifier']     = trim($thisfile_iso_primaryVD_raw['copyright_file_identifier']);
		$thisfile_iso_primaryVD['abstract_file_identifier']      = trim($thisfile_iso_primaryVD_raw['abstract_file_identifier']);
		$thisfile_iso_primaryVD['bibliographic_file_identifier'] = trim($thisfile_iso_primaryVD_raw['bibliographic_file_identifier']);
		$thisfile_iso_primaryVD['volume_creation_date_time']     = $this->ISOtimeText2UNIXtime($thisfile_iso_primaryVD_raw['volume_creation_date_time']);
		$thisfile_iso_primaryVD['volume_modification_date_time'] = $this->ISOtimeText2UNIXtime($thisfile_iso_primaryVD_raw['volume_modification_date_time']);
		$thisfile_iso_primaryVD['volume_expiration_date_time']   = $this->ISOtimeText2UNIXtime($thisfile_iso_primaryVD_raw['volume_expiration_date_time']);
		$thisfile_iso_primaryVD['volume_effective_date_time']    = $this->ISOtimeText2UNIXtime($thisfile_iso_primaryVD_raw['volume_effective_date_time']);

		if (($thisfile_iso_primaryVD_raw['volume_space_size'] * 2048) > $info['filesize']) {
			$info['error'][] = 'Volume Space Size ('.($thisfile_iso_primaryVD_raw['volume_space_size'] * 2048).' bytes) is larger than the file size ('.$info['filesize'].' bytes) (truncated file?)';
		}

		return true;
	}


	public function ParseSupplementaryVolumeDescriptor(&$ISOheader) {
		// ISO integer values are stored Both-Endian format!!
		// ie 12345 == 0x3039  is stored as $39 $30 $30 $39 in a 4-byte field

		// shortcuts
		$info = &$this->getid3->info;
		$info['iso']['supplementary_volume_descriptor']['raw'] = array();
		$thisfile_iso_supplementaryVD     = &$info['iso']['supplementary_volume_descriptor'];
		$thisfile_iso_supplementaryVD_raw = &$thisfile_iso_supplementaryVD['raw'];

		$thisfile_iso_supplementaryVD_raw['volume_descriptor_type'] = getid3_lib::LittleEndian2Int(substr($ISOheader,    0, 1));
		$thisfile_iso_supplementaryVD_raw['standard_identifier']    =                  substr($ISOheader,    1, 5);
		if ($thisfile_iso_supplementaryVD_raw['standard_identifier'] != 'CD001') {
			$info['error'][] = 'Expected "CD001" at offset ('.($thisfile_iso_supplementaryVD['offset'] + 1).'), found "'.$thisfile_iso_supplementaryVD_raw['standard_identifier'].'" instead';
			unset($info['fileformat']);
			unset($info['iso']);
			return false;
		}

		$thisfile_iso_supplementaryVD_raw['volume_descriptor_version'] = getid3_lib::LittleEndian2Int(substr($ISOheader,    6, 1));
		//$thisfile_iso_supplementaryVD_raw['unused_1']                  =                              substr($ISOheader,    7, 1);
		$thisfile_iso_supplementaryVD_raw['system_identifier']         =                              substr($ISOheader,    8, 32);
		$thisfile_iso_supplementaryVD_raw['volume_identifier']         =                              substr($ISOheader,   40, 32);
		//$thisfile_iso_supplementaryVD_raw['unused_2']                  =                              substr($ISOheader,   72, 8);
		$thisfile_iso_supplementaryVD_raw['volume_space_size']         = getid3_lib::LittleEndian2Int(substr($ISOheader,   80, 4));
		if ($thisfile_iso_supplementaryVD_raw['volume_space_size'] == 0) {
			// Supplementary Volume Descriptor not used
			//unset($thisfile_iso_supplementaryVD);
			//return false;
		}

		//$thisfile_iso_supplementaryVD_raw['unused_3']                       =                              substr($ISOheader,   88, 32);
		$thisfile_iso_supplementaryVD_raw['volume_set_size']                = getid3_lib::LittleEndian2Int(substr($ISOheader,  120, 2));
		$thisfile_iso_supplementaryVD_raw['volume_sequence_number']         = getid3_lib::LittleEndian2Int(substr($ISOheader,  124, 2));
		$thisfile_iso_supplementaryVD_raw['logical_block_size']             = getid3_lib::LittleEndian2Int(substr($ISOheader,  128, 2));
		$thisfile_iso_supplementaryVD_raw['path_table_size']                = getid3_lib::LittleEndian2Int(substr($ISOheader,  132, 4));
		$thisfile_iso_supplementaryVD_raw['path_table_l_location']          = getid3_lib::LittleEndian2Int(substr($ISOheader,  140, 2));
		$thisfile_iso_supplementaryVD_raw['path_table_l_opt_location']      = getid3_lib::LittleEndian2Int(substr($ISOheader,  144, 2));
		$thisfile_iso_supplementaryVD_raw['path_table_m_location']          = getid3_lib::LittleEndian2Int(substr($ISOheader,  148, 2));
		$thisfile_iso_supplementaryVD_raw['path_table_m_opt_location']      = getid3_lib::LittleEndian2Int(substr($ISOheader,  152, 2));
		$thisfile_iso_supplementaryVD_raw['root_directory_record']          =                              substr($ISOheader,  156, 34);
		$thisfile_iso_supplementaryVD_raw['volume_set_identifier']          =                              substr($ISOheader,  190, 128);
		$thisfile_iso_supplementaryVD_raw['publisher_identifier']           =                              substr($ISOheader,  318, 128);
		$thisfile_iso_supplementaryVD_raw['data_preparer_identifier']       =                              substr($ISOheader,  446, 128);
		$thisfile_iso_supplementaryVD_raw['application_identifier']         =                              substr($ISOheader,  574, 128);
		$thisfile_iso_supplementaryVD_raw['copyright_file_identifier']      =                              substr($ISOheader,  702, 37);
		$thisfile_iso_supplementaryVD_raw['abstract_file_identifier']       =                              substr($ISOheader,  739, 37);
		$thisfile_iso_supplementaryVD_raw['bibliographic_file_identifier']  =                              substr($ISOheader,  776, 37);
		$thisfile_iso_supplementaryVD_raw['volume_creation_date_time']      =                              substr($ISOheader,  813, 17);
		$thisfile_iso_supplementaryVD_raw['volume_modification_date_time']  =                              substr($ISOheader,  830, 17);
		$thisfile_iso_supplementaryVD_raw['volume_expiration_date_time']    =                              substr($ISOheader,  847, 17);
		$thisfile_iso_supplementaryVD_raw['volume_effective_date_time']     =                              substr($ISOheader,  864, 17);
		$thisfile_iso_supplementaryVD_raw['file_structure_version']         = getid3_lib::LittleEndian2Int(substr($ISOheader,  881, 1));
		//$thisfile_iso_supplementaryVD_raw['unused_4']                       = getid3_lib::LittleEndian2Int(substr($ISOheader,  882, 1));
		$thisfile_iso_supplementaryVD_raw['application_data']               =                              substr($ISOheader,  883, 512);
		//$thisfile_iso_supplementaryVD_raw['unused_5']                       =                              substr($ISOheader, 1395, 653);

		$thisfile_iso_supplementaryVD['system_identifier']              = trim($thisfile_iso_supplementaryVD_raw['system_identifier']);
		$thisfile_iso_supplementaryVD['volume_identifier']              = trim($thisfile_iso_supplementaryVD_raw['volume_identifier']);
		$thisfile_iso_supplementaryVD['volume_set_identifier']          = trim($thisfile_iso_supplementaryVD_raw['volume_set_identifier']);
		$thisfile_iso_supplementaryVD['publisher_identifier']           = trim($thisfile_iso_supplementaryVD_raw['publisher_identifier']);
		$thisfile_iso_supplementaryVD['data_preparer_identifier']       = trim($thisfile_iso_supplementaryVD_raw['data_preparer_identifier']);
		$thisfile_iso_supplementaryVD['application_identifier']         = trim($thisfile_iso_supplementaryVD_raw['application_identifier']);
		$thisfile_iso_supplementaryVD['copyright_file_identifier']      = trim($thisfile_iso_supplementaryVD_raw['copyright_file_identifier']);
		$thisfile_iso_supplementaryVD['abstract_file_identifier']       = trim($thisfile_iso_supplementaryVD_raw['abstract_file_identifier']);
		$thisfile_iso_supplementaryVD['bibliographic_file_identifier']  = trim($thisfile_iso_supplementaryVD_raw['bibliographic_file_identifier']);
		$thisfile_iso_supplementaryVD['volume_creation_date_time']      = $this->ISOtimeText2UNIXtime($thisfile_iso_supplementaryVD_raw['volume_creation_date_time']);
		$thisfile_iso_supplementaryVD['volume_modification_date_time']  = $this->ISOtimeText2UNIXtime($thisfile_iso_supplementaryVD_raw['volume_modification_date_time']);
		$thisfile_iso_supplementaryVD['volume_expiration_date_time']    = $this->ISOtimeText2UNIXtime($thisfile_iso_supplementaryVD_raw['volume_expiration_date_time']);
		$thisfile_iso_supplementaryVD['volume_effective_date_time']     = $this->ISOtimeText2UNIXtime($thisfile_iso_supplementaryVD_raw['volume_effective_date_time']);

		if (($thisfile_iso_supplementaryVD_raw['volume_space_size'] * $thisfile_iso_supplementaryVD_raw['logical_block_size']) > $info['filesize']) {
			$info['error'][] = 'Volume Space Size ('.($thisfile_iso_supplementaryVD_raw['volume_space_size'] * $thisfile_iso_supplementaryVD_raw['logical_block_size']).' bytes) is larger than the file size ('.$info['filesize'].' bytes) (truncated file?)';
		}

		return true;
	}


	public function ParsePathTable() {
		$info = &$this->getid3->info;
		if (!isset($info['iso']['supplementary_volume_descriptor']['raw']['path_table_l_location']) && !isset($info['iso']['primary_volume_descriptor']['raw']['path_table_l_location'])) {
			return false;
		}
		if (isset($info['iso']['supplementary_volume_descriptor']['raw']['path_table_l_location'])) {
			$PathTableLocation = $info['iso']['supplementary_volume_descriptor']['raw']['path_table_l_location'];
			$PathTableSize     = $info['iso']['supplementary_volume_descriptor']['raw']['path_table_size'];
			$TextEncoding      = 'UTF-16BE'; // Big-Endian Unicode
		} else {
			$PathTableLocation = $info['iso']['primary_volume_descriptor']['raw']['path_table_l_location'];
			$PathTableSize     = $info['iso']['primary_volume_descriptor']['raw']['path_table_size'];
			$TextEncoding      = 'ISO-8859-1'; // Latin-1
		}

		if (($PathTableLocation * 2048) > $info['filesize']) {
			$info['error'][] = 'Path Table Location specifies an offset ('.($PathTableLocation * 2048).') beyond the end-of-file ('.$info['filesize'].')';
			return false;
		}

		$info['iso']['path_table']['offset'] = $PathTableLocation * 2048;
		$this->fseek($info['iso']['path_table']['offset']);
		$info['iso']['path_table']['raw'] = $this->fread($PathTableSize);

		$offset = 0;
		$pathcounter = 1;
		while ($offset < $PathTableSize) {
			// shortcut
			$info['iso']['path_table']['directories'][$pathcounter] = array();
			$thisfile_iso_pathtable_directories_current = &$info['iso']['path_table']['directories'][$pathcounter];

			$thisfile_iso_pathtable_directories_current['length']           = getid3_lib::LittleEndian2Int(substr($info['iso']['path_table']['raw'], $offset, 1));
			$offset += 1;
			$thisfile_iso_pathtable_directories_current['extended_length']  = getid3_lib::LittleEndian2Int(substr($info['iso']['path_table']['raw'], $offset, 1));
			$offset += 1;
			$thisfile_iso_pathtable_directories_current['location_logical'] = getid3_lib::LittleEndian2Int(substr($info['iso']['path_table']['raw'], $offset, 4));
			$offset += 4;
			$thisfile_iso_pathtable_directories_current['parent_directory'] = getid3_lib::LittleEndian2Int(substr($info['iso']['path_table']['raw'], $offset, 2));
			$offset += 2;
			$thisfile_iso_pathtable_directories_current['name']             =                  substr($info['iso']['path_table']['raw'], $offset, $thisfile_iso_pathtable_directories_current['length']);
			$offset += $thisfile_iso_pathtable_directories_current['length'] + ($thisfile_iso_pathtable_directories_current['length'] % 2);

			$thisfile_iso_pathtable_directories_current['name_ascii']       = getid3_lib::iconv_fallback($TextEncoding, $info['encoding'], $thisfile_iso_pathtable_directories_current['name']);

			$thisfile_iso_pathtable_directories_current['location_bytes'] = $thisfile_iso_pathtable_directories_current['location_logical'] * 2048;
			if ($pathcounter == 1) {
				$thisfile_iso_pathtable_directories_current['full_path'] = '/';
			} else {
				$thisfile_iso_pathtable_directories_current['full_path'] = $info['iso']['path_table']['directories'][$thisfile_iso_pathtable_directories_current['parent_directory']]['full_path'].$thisfile_iso_pathtable_directories_current['name_ascii'].'/';
			}
			$FullPathArray[] = $thisfile_iso_pathtable_directories_current['full_path'];

			$pathcounter++;
		}

		return true;
	}


	public function ParseDirectoryRecord($directorydata) {
		$info = &$this->getid3->info;
		if (isset($info['iso']['supplementary_volume_descriptor'])) {
			$TextEncoding = 'UTF-16BE';   // Big-Endian Unicode
		} else {
			$TextEncoding = 'ISO-8859-1'; // Latin-1
		}

		$this->fseek($directorydata['location_bytes']);
		$DirectoryRecordData = $this->fread(1);

		while (ord($DirectoryRecordData{0}) > 33) {

			$DirectoryRecordData .= $this->fread(ord($DirectoryRecordData{0}) - 1);

			$ThisDirectoryRecord['raw']['length']                    = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData,  0, 1));
			$ThisDirectoryRecord['raw']['extended_attribute_length'] = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData,  1, 1));
			$ThisDirectoryRecord['raw']['offset_logical']            = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData,  2, 4));
			$ThisDirectoryRecord['raw']['filesize']                  = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData, 10, 4));
			$ThisDirectoryRecord['raw']['recording_date_time']       =                  substr($DirectoryRecordData, 18, 7);
			$ThisDirectoryRecord['raw']['file_flags']                = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData, 25, 1));
			$ThisDirectoryRecord['raw']['file_unit_size']            = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData, 26, 1));
			$ThisDirectoryRecord['raw']['interleave_gap_size']       = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData, 27, 1));
			$ThisDirectoryRecord['raw']['volume_sequence_number']    = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData, 28, 2));
			$ThisDirectoryRecord['raw']['file_identifier_length']    = getid3_lib::LittleEndian2Int(substr($DirectoryRecordData, 32, 1));
			$ThisDirectoryRecord['raw']['file_identifier']           =                  substr($DirectoryRecordData, 33, $ThisDirectoryRecord['raw']['file_identifier_length']);

			$ThisDirectoryRecord['file_identifier_ascii']            = getid3_lib::iconv_fallback($TextEncoding, $info['encoding'], $ThisDirectoryRecord['raw']['file_identifier']);

			$ThisDirectoryRecord['filesize']                  = $ThisDirectoryRecord['raw']['filesize'];
			$ThisDirectoryRecord['offset_bytes']              = $ThisDirectoryRecord['raw']['offset_logical'] * 2048;
			$ThisDirectoryRecord['file_flags']['hidden']      = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x01);
			$ThisDirectoryRecord['file_flags']['directory']   = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x02);
			$ThisDirectoryRecord['file_flags']['associated']  = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x04);
			$ThisDirectoryRecord['file_flags']['extended']    = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x08);
			$ThisDirectoryRecord['file_flags']['permissions'] = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x10);
			$ThisDirectoryRecord['file_flags']['multiple']    = (bool) ($ThisDirectoryRecord['raw']['file_flags'] & 0x80);
			$ThisDirectoryRecord['recording_timestamp']       = $this->ISOtime2UNIXtime($ThisDirectoryRecord['raw']['recording_date_time']);

			if ($ThisDirectoryRecord['file_flags']['directory']) {
				$ThisDirectoryRecord['filename'] = $directorydata['full_path'];
			} else {
				$ThisDirectoryRecord['filename'] = $directorydata['full_path'].$this->ISOstripFilenameVersion($ThisDirectoryRecord['file_identifier_ascii']);
				$info['iso']['files'] = getid3_lib::array_merge_clobber($info['iso']['files'], getid3_lib::CreateDeepArray($ThisDirectoryRecord['filename'], '/', $ThisDirectoryRecord['filesize']));
			}

			$DirectoryRecord[] = $ThisDirectoryRecord;
			$DirectoryRecordData = $this->fread(1);
		}

		return $DirectoryRecord;
	}

	public function ISOstripFilenameVersion($ISOfilename) {
		// convert 'filename.ext;1' to 'filename.ext'
		if (!strstr($ISOfilename, ';')) {
			return $ISOfilename;
		} else {
			return substr($ISOfilename, 0, strpos($ISOfilename, ';'));
		}
	}

	public function ISOtimeText2UNIXtime($ISOtime) {

		$UNIXyear   = (int) substr($ISOtime,  0, 4);
		$UNIXmonth  = (int) substr($ISOtime,  4, 2);
		$UNIXday    = (int) substr($ISOtime,  6, 2);
		$UNIXhour   = (int) substr($ISOtime,  8, 2);
		$UNIXminute = (int) substr($ISOtime, 10, 2);
		$UNIXsecond = (int) substr($ISOtime, 12, 2);

		if (!$UNIXyear) {
			return false;
		}
		return gmmktime($UNIXhour, $UNIXminute, $UNIXsecond, $UNIXmonth, $UNIXday, $UNIXyear);
	}

	public function ISOtime2UNIXtime($ISOtime) {
		// Represented by seven bytes:
		// 1: Number of years since 1900
		// 2: Month of the year from 1 to 12
		// 3: Day of the Month from 1 to 31
		// 4: Hour of the day from 0 to 23
		// 5: Minute of the hour from 0 to 59
		// 6: second of the minute from 0 to 59
		// 7: Offset from Greenwich Mean Time in number of 15 minute intervals from -48 (West) to +52 (East)

		$UNIXyear   = ord($ISOtime{0}) + 1900;
		$UNIXmonth  = ord($ISOtime{1});
		$UNIXday    = ord($ISOtime{2});
		$UNIXhour   = ord($ISOtime{3});
		$UNIXminute = ord($ISOtime{4});
		$UNIXsecond = ord($ISOtime{5});
		$GMToffset  = $this->TwosCompliment2Decimal(ord($ISOtime{5}));

		return gmmktime($UNIXhour, $UNIXminute, $UNIXsecond, $UNIXmonth, $UNIXday, $UNIXyear);
	}

	public function TwosCompliment2Decimal($BinaryValue) {
		// http://sandbox.mc.edu/~bennet/cs110/tc/tctod.html
		// First check if the number is negative or positive by looking at the sign bit.
		// If it is positive, simply convert it to decimal.
		// If it is negative, make it positive by inverting the bits and adding one.
		// Then, convert the result to decimal.
		// The negative of this number is the value of the original binary.

		if ($BinaryValue & 0x80) {

			// negative number
			return (0 - ((~$BinaryValue & 0xFF) + 1));
		} else {
			// positive number
			return $BinaryValue;
		}
	}


}
