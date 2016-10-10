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
// module.archive.zip.php                                      //
// module for analyzing pkZip files                            //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_zip extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat']      = 'zip';
		$info['zip']['encoding'] = 'ISO-8859-1';
		$info['zip']['files']    = array();

		$info['zip']['compressed_size']   = 0;
		$info['zip']['uncompressed_size'] = 0;
		$info['zip']['entries_count']     = 0;

		if (!getid3_lib::intValueSupported($info['filesize'])) {
			$info['error'][] = 'File is larger than '.round(PHP_INT_MAX / 1073741824).'GB, not supported by PHP';
			return false;
		} else {
			$EOCDsearchData    = '';
			$EOCDsearchCounter = 0;
			while ($EOCDsearchCounter++ < 512) {

				$this->fseek(-128 * $EOCDsearchCounter, SEEK_END);
				$EOCDsearchData = $this->fread(128).$EOCDsearchData;

				if (strstr($EOCDsearchData, 'PK'."\x05\x06")) {

					$EOCDposition = strpos($EOCDsearchData, 'PK'."\x05\x06");
					$this->fseek((-128 * $EOCDsearchCounter) + $EOCDposition, SEEK_END);
					$info['zip']['end_central_directory'] = $this->ZIPparseEndOfCentralDirectory();

					$this->fseek($info['zip']['end_central_directory']['directory_offset']);
					$info['zip']['entries_count'] = 0;
					while ($centraldirectoryentry = $this->ZIPparseCentralDirectory($this->getid3->fp)) {
						$info['zip']['central_directory'][] = $centraldirectoryentry;
						$info['zip']['entries_count']++;
						$info['zip']['compressed_size']   += $centraldirectoryentry['compressed_size'];
						$info['zip']['uncompressed_size'] += $centraldirectoryentry['uncompressed_size'];

						//if ($centraldirectoryentry['uncompressed_size'] > 0) { zero-byte files are valid
						if (!empty($centraldirectoryentry['filename'])) {
							$info['zip']['files'] = getid3_lib::array_merge_clobber($info['zip']['files'], getid3_lib::CreateDeepArray($centraldirectoryentry['filename'], '/', $centraldirectoryentry['uncompressed_size']));
						}
					}

					if ($info['zip']['entries_count'] == 0) {
						$info['error'][] = 'No Central Directory entries found (truncated file?)';
						return false;
					}

					if (!empty($info['zip']['end_central_directory']['comment'])) {
						$info['zip']['comments']['comment'][] = $info['zip']['end_central_directory']['comment'];
					}

					if (isset($info['zip']['central_directory'][0]['compression_method'])) {
						$info['zip']['compression_method'] = $info['zip']['central_directory'][0]['compression_method'];
					}
					if (isset($info['zip']['central_directory'][0]['flags']['compression_speed'])) {
						$info['zip']['compression_speed']  = $info['zip']['central_directory'][0]['flags']['compression_speed'];
					}
					if (isset($info['zip']['compression_method']) && ($info['zip']['compression_method'] == 'store') && !isset($info['zip']['compression_speed'])) {
						$info['zip']['compression_speed']  = 'store';
					}

					// secondary check - we (should) already have all the info we NEED from the Central Directory above, but scanning each
					// Local File Header entry will
					foreach ($info['zip']['central_directory'] as $central_directory_entry) {
						$this->fseek($central_directory_entry['entry_offset']);
						if ($fileentry = $this->ZIPparseLocalFileHeader()) {
							$info['zip']['entries'][] = $fileentry;
						} else {
							$info['warning'][] = 'Error parsing Local File Header at offset '.$central_directory_entry['entry_offset'];
						}
					}

					if (!empty($info['zip']['files']['[Content_Types].xml']) &&
					    !empty($info['zip']['files']['_rels']['.rels'])      &&
					    !empty($info['zip']['files']['docProps']['app.xml']) &&
					    !empty($info['zip']['files']['docProps']['core.xml'])) {
						   // http://technet.microsoft.com/en-us/library/cc179224.aspx
						   $info['fileformat'] = 'zip.msoffice';
						   if (!empty($ThisFileInfo['zip']['files']['ppt'])) {
						      $info['mime_type'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
						   } elseif (!empty($ThisFileInfo['zip']['files']['xl'])) {
						      $info['mime_type'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
						   } elseif (!empty($ThisFileInfo['zip']['files']['word'])) {
						      $info['mime_type'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
						   }
					}

					return true;
				}
			}
		}

		if (!$this->getZIPentriesFilepointer()) {
			unset($info['zip']);
			$info['fileformat'] = '';
			$info['error'][] = 'Cannot find End Of Central Directory (truncated file?)';
			return false;
		}

		// central directory couldn't be found and/or parsed
		// scan through actual file data entries, recover as much as possible from probable trucated file
		if ($info['zip']['compressed_size'] > ($info['filesize'] - 46 - 22)) {
			$info['error'][] = 'Warning: Truncated file! - Total compressed file sizes ('.$info['zip']['compressed_size'].' bytes) is greater than filesize minus Central Directory and End Of Central Directory structures ('.($info['filesize'] - 46 - 22).' bytes)';
		}
		$info['error'][] = 'Cannot find End Of Central Directory - returned list of files in [zip][entries] array may not be complete';
		foreach ($info['zip']['entries'] as $key => $valuearray) {
			$info['zip']['files'][$valuearray['filename']] = $valuearray['uncompressed_size'];
		}
		return true;
	}


	public function getZIPHeaderFilepointerTopDown() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'zip';

		$info['zip']['compressed_size']   = 0;
		$info['zip']['uncompressed_size'] = 0;
		$info['zip']['entries_count']     = 0;

		rewind($this->getid3->fp);
		while ($fileentry = $this->ZIPparseLocalFileHeader()) {
			$info['zip']['entries'][] = $fileentry;
			$info['zip']['entries_count']++;
		}
		if ($info['zip']['entries_count'] == 0) {
			$info['error'][] = 'No Local File Header entries found';
			return false;
		}

		$info['zip']['entries_count']     = 0;
		while ($centraldirectoryentry = $this->ZIPparseCentralDirectory($this->getid3->fp)) {
			$info['zip']['central_directory'][] = $centraldirectoryentry;
			$info['zip']['entries_count']++;
			$info['zip']['compressed_size']   += $centraldirectoryentry['compressed_size'];
			$info['zip']['uncompressed_size'] += $centraldirectoryentry['uncompressed_size'];
		}
		if ($info['zip']['entries_count'] == 0) {
			$info['error'][] = 'No Central Directory entries found (truncated file?)';
			return false;
		}

		if ($EOCD = $this->ZIPparseEndOfCentralDirectory()) {
			$info['zip']['end_central_directory'] = $EOCD;
		} else {
			$info['error'][] = 'No End Of Central Directory entry found (truncated file?)';
			return false;
		}

		if (!empty($info['zip']['end_central_directory']['comment'])) {
			$info['zip']['comments']['comment'][] = $info['zip']['end_central_directory']['comment'];
		}

		return true;
	}


	public function getZIPentriesFilepointer() {
		$info = &$this->getid3->info;

		$info['zip']['compressed_size']   = 0;
		$info['zip']['uncompressed_size'] = 0;
		$info['zip']['entries_count']     = 0;

		rewind($this->getid3->fp);
		while ($fileentry = $this->ZIPparseLocalFileHeader()) {
			$info['zip']['entries'][] = $fileentry;
			$info['zip']['entries_count']++;
			$info['zip']['compressed_size']   += $fileentry['compressed_size'];
			$info['zip']['uncompressed_size'] += $fileentry['uncompressed_size'];
		}
		if ($info['zip']['entries_count'] == 0) {
			$info['error'][] = 'No Local File Header entries found';
			return false;
		}

		return true;
	}


	public function ZIPparseLocalFileHeader() {
		$LocalFileHeader['offset'] = $this->ftell();

		$ZIPlocalFileHeader = $this->fread(30);

		$LocalFileHeader['raw']['signature']          = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader,  0, 4));
		if ($LocalFileHeader['raw']['signature'] != 0x04034B50) { // "PK\x03\x04"
			// invalid Local File Header Signature
			$this->fseek($LocalFileHeader['offset']); // seek back to where filepointer originally was so it can be handled properly
			return false;
		}
		$LocalFileHeader['raw']['extract_version']    = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader,  4, 2));
		$LocalFileHeader['raw']['general_flags']      = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader,  6, 2));
		$LocalFileHeader['raw']['compression_method'] = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader,  8, 2));
		$LocalFileHeader['raw']['last_mod_file_time'] = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader, 10, 2));
		$LocalFileHeader['raw']['last_mod_file_date'] = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader, 12, 2));
		$LocalFileHeader['raw']['crc_32']             = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader, 14, 4));
		$LocalFileHeader['raw']['compressed_size']    = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader, 18, 4));
		$LocalFileHeader['raw']['uncompressed_size']  = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader, 22, 4));
		$LocalFileHeader['raw']['filename_length']    = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader, 26, 2));
		$LocalFileHeader['raw']['extra_field_length'] = getid3_lib::LittleEndian2Int(substr($ZIPlocalFileHeader, 28, 2));

		$LocalFileHeader['extract_version']           = sprintf('%1.1f', $LocalFileHeader['raw']['extract_version'] / 10);
		$LocalFileHeader['host_os']                   = $this->ZIPversionOSLookup(($LocalFileHeader['raw']['extract_version'] & 0xFF00) >> 8);
		$LocalFileHeader['compression_method']        = $this->ZIPcompressionMethodLookup($LocalFileHeader['raw']['compression_method']);
		$LocalFileHeader['compressed_size']           = $LocalFileHeader['raw']['compressed_size'];
		$LocalFileHeader['uncompressed_size']         = $LocalFileHeader['raw']['uncompressed_size'];
		$LocalFileHeader['flags']                     = $this->ZIPparseGeneralPurposeFlags($LocalFileHeader['raw']['general_flags'], $LocalFileHeader['raw']['compression_method']);
		$LocalFileHeader['last_modified_timestamp']   = $this->DOStime2UNIXtime($LocalFileHeader['raw']['last_mod_file_date'], $LocalFileHeader['raw']['last_mod_file_time']);

		$FilenameExtrafieldLength = $LocalFileHeader['raw']['filename_length'] + $LocalFileHeader['raw']['extra_field_length'];
		if ($FilenameExtrafieldLength > 0) {
			$ZIPlocalFileHeader .= $this->fread($FilenameExtrafieldLength);

			if ($LocalFileHeader['raw']['filename_length'] > 0) {
				$LocalFileHeader['filename']                = substr($ZIPlocalFileHeader, 30, $LocalFileHeader['raw']['filename_length']);
			}
			if ($LocalFileHeader['raw']['extra_field_length'] > 0) {
				$LocalFileHeader['raw']['extra_field_data'] = substr($ZIPlocalFileHeader, 30 + $LocalFileHeader['raw']['filename_length'], $LocalFileHeader['raw']['extra_field_length']);
			}
		}

		if ($LocalFileHeader['compressed_size'] == 0) {
			// *Could* be a zero-byte file
			// But could also be a file written on the fly that didn't know compressed filesize beforehand.
			// Correct compressed filesize should be in the data_descriptor located after this file data, and also in Central Directory (at end of zip file)
			if (!empty($this->getid3->info['zip']['central_directory'])) {
				foreach ($this->getid3->info['zip']['central_directory'] as $central_directory_entry) {
					if ($central_directory_entry['entry_offset'] == $LocalFileHeader['offset']) {
						if ($central_directory_entry['compressed_size'] > 0) {
							// overwrite local zero value (but not ['raw']'compressed_size']) so that seeking for data_descriptor (and next file entry) works correctly
							$LocalFileHeader['compressed_size'] = $central_directory_entry['compressed_size'];
						}
						break;
					}
				}
			}

		}
		$LocalFileHeader['data_offset'] = $this->ftell();
		$this->fseek($LocalFileHeader['compressed_size'], SEEK_CUR); // this should (but may not) match value in $LocalFileHeader['raw']['compressed_size'] -- $LocalFileHeader['compressed_size'] could have been overwritten above with value from Central Directory

		if ($LocalFileHeader['flags']['data_descriptor_used']) {
			$DataDescriptor = $this->fread(16);
			$LocalFileHeader['data_descriptor']['signature']         = getid3_lib::LittleEndian2Int(substr($DataDescriptor,  0, 4));
			if ($LocalFileHeader['data_descriptor']['signature'] != 0x08074B50) { // "PK\x07\x08"
				$this->getid3->warning[] = 'invalid Local File Header Data Descriptor Signature at offset '.($this->ftell() - 16).' - expecting 08 07 4B 50, found '.getid3_lib::PrintHexBytes($LocalFileHeader['data_descriptor']['signature']);
				$this->fseek($LocalFileHeader['offset']); // seek back to where filepointer originally was so it can be handled properly
				return false;
			}
			$LocalFileHeader['data_descriptor']['crc_32']            = getid3_lib::LittleEndian2Int(substr($DataDescriptor,  4, 4));
			$LocalFileHeader['data_descriptor']['compressed_size']   = getid3_lib::LittleEndian2Int(substr($DataDescriptor,  8, 4));
			$LocalFileHeader['data_descriptor']['uncompressed_size'] = getid3_lib::LittleEndian2Int(substr($DataDescriptor, 12, 4));
			if (!$LocalFileHeader['raw']['compressed_size'] && $LocalFileHeader['data_descriptor']['compressed_size']) {
				foreach ($this->getid3->info['zip']['central_directory'] as $central_directory_entry) {
					if ($central_directory_entry['entry_offset'] == $LocalFileHeader['offset']) {
						if ($LocalFileHeader['data_descriptor']['compressed_size'] == $central_directory_entry['compressed_size']) {
							// $LocalFileHeader['compressed_size'] already set from Central Directory
						} else {
							$this->getid3->info['warning'][] = 'conflicting compressed_size from data_descriptor ('.$LocalFileHeader['data_descriptor']['compressed_size'].') vs Central Directory ('.$central_directory_entry['compressed_size'].') for file at offset '.$LocalFileHeader['offset'];
						}

						if ($LocalFileHeader['data_descriptor']['uncompressed_size'] == $central_directory_entry['uncompressed_size']) {
							$LocalFileHeader['uncompressed_size'] = $LocalFileHeader['data_descriptor']['uncompressed_size'];
						} else {
							$this->getid3->info['warning'][] = 'conflicting uncompressed_size from data_descriptor ('.$LocalFileHeader['data_descriptor']['uncompressed_size'].') vs Central Directory ('.$central_directory_entry['uncompressed_size'].') for file at offset '.$LocalFileHeader['offset'];
						}
						break;
					}
				}
			}
		}
		return $LocalFileHeader;
	}


	public function ZIPparseCentralDirectory() {
		$CentralDirectory['offset'] = $this->ftell();

		$ZIPcentralDirectory = $this->fread(46);

		$CentralDirectory['raw']['signature']            = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory,  0, 4));
		if ($CentralDirectory['raw']['signature'] != 0x02014B50) {
			// invalid Central Directory Signature
			$this->fseek($CentralDirectory['offset']); // seek back to where filepointer originally was so it can be handled properly
			return false;
		}
		$CentralDirectory['raw']['create_version']       = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory,  4, 2));
		$CentralDirectory['raw']['extract_version']      = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory,  6, 2));
		$CentralDirectory['raw']['general_flags']        = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory,  8, 2));
		$CentralDirectory['raw']['compression_method']   = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 10, 2));
		$CentralDirectory['raw']['last_mod_file_time']   = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 12, 2));
		$CentralDirectory['raw']['last_mod_file_date']   = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 14, 2));
		$CentralDirectory['raw']['crc_32']               = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 16, 4));
		$CentralDirectory['raw']['compressed_size']      = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 20, 4));
		$CentralDirectory['raw']['uncompressed_size']    = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 24, 4));
		$CentralDirectory['raw']['filename_length']      = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 28, 2));
		$CentralDirectory['raw']['extra_field_length']   = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 30, 2));
		$CentralDirectory['raw']['file_comment_length']  = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 32, 2));
		$CentralDirectory['raw']['disk_number_start']    = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 34, 2));
		$CentralDirectory['raw']['internal_file_attrib'] = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 36, 2));
		$CentralDirectory['raw']['external_file_attrib'] = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 38, 4));
		$CentralDirectory['raw']['local_header_offset']  = getid3_lib::LittleEndian2Int(substr($ZIPcentralDirectory, 42, 4));

		$CentralDirectory['entry_offset']              = $CentralDirectory['raw']['local_header_offset'];
		$CentralDirectory['create_version']            = sprintf('%1.1f', $CentralDirectory['raw']['create_version'] / 10);
		$CentralDirectory['extract_version']           = sprintf('%1.1f', $CentralDirectory['raw']['extract_version'] / 10);
		$CentralDirectory['host_os']                   = $this->ZIPversionOSLookup(($CentralDirectory['raw']['extract_version'] & 0xFF00) >> 8);
		$CentralDirectory['compression_method']        = $this->ZIPcompressionMethodLookup($CentralDirectory['raw']['compression_method']);
		$CentralDirectory['compressed_size']           = $CentralDirectory['raw']['compressed_size'];
		$CentralDirectory['uncompressed_size']         = $CentralDirectory['raw']['uncompressed_size'];
		$CentralDirectory['flags']                     = $this->ZIPparseGeneralPurposeFlags($CentralDirectory['raw']['general_flags'], $CentralDirectory['raw']['compression_method']);
		$CentralDirectory['last_modified_timestamp']   = $this->DOStime2UNIXtime($CentralDirectory['raw']['last_mod_file_date'], $CentralDirectory['raw']['last_mod_file_time']);

		$FilenameExtrafieldCommentLength = $CentralDirectory['raw']['filename_length'] + $CentralDirectory['raw']['extra_field_length'] + $CentralDirectory['raw']['file_comment_length'];
		if ($FilenameExtrafieldCommentLength > 0) {
			$FilenameExtrafieldComment = $this->fread($FilenameExtrafieldCommentLength);

			if ($CentralDirectory['raw']['filename_length'] > 0) {
				$CentralDirectory['filename']                  = substr($FilenameExtrafieldComment, 0, $CentralDirectory['raw']['filename_length']);
			}
			if ($CentralDirectory['raw']['extra_field_length'] > 0) {
				$CentralDirectory['raw']['extra_field_data']   = substr($FilenameExtrafieldComment, $CentralDirectory['raw']['filename_length'], $CentralDirectory['raw']['extra_field_length']);
			}
			if ($CentralDirectory['raw']['file_comment_length'] > 0) {
				$CentralDirectory['file_comment']              = substr($FilenameExtrafieldComment, $CentralDirectory['raw']['filename_length'] + $CentralDirectory['raw']['extra_field_length'], $CentralDirectory['raw']['file_comment_length']);
			}
		}

		return $CentralDirectory;
	}

	public function ZIPparseEndOfCentralDirectory() {
		$EndOfCentralDirectory['offset'] = $this->ftell();

		$ZIPendOfCentralDirectory = $this->fread(22);

		$EndOfCentralDirectory['signature']                   = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory,  0, 4));
		if ($EndOfCentralDirectory['signature'] != 0x06054B50) {
			// invalid End Of Central Directory Signature
			$this->fseek($EndOfCentralDirectory['offset']); // seek back to where filepointer originally was so it can be handled properly
			return false;
		}
		$EndOfCentralDirectory['disk_number_current']         = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory,  4, 2));
		$EndOfCentralDirectory['disk_number_start_directory'] = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory,  6, 2));
		$EndOfCentralDirectory['directory_entries_this_disk'] = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory,  8, 2));
		$EndOfCentralDirectory['directory_entries_total']     = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory, 10, 2));
		$EndOfCentralDirectory['directory_size']              = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory, 12, 4));
		$EndOfCentralDirectory['directory_offset']            = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory, 16, 4));
		$EndOfCentralDirectory['comment_length']              = getid3_lib::LittleEndian2Int(substr($ZIPendOfCentralDirectory, 20, 2));

		if ($EndOfCentralDirectory['comment_length'] > 0) {
			$EndOfCentralDirectory['comment']                 = $this->fread($EndOfCentralDirectory['comment_length']);
		}

		return $EndOfCentralDirectory;
	}


	public static function ZIPparseGeneralPurposeFlags($flagbytes, $compressionmethod) {
		// https://users.cs.jmu.edu/buchhofp/forensics/formats/pkzip-printable.html
		$ParsedFlags['encrypted']               = (bool) ($flagbytes & 0x0001);
		//                                                             0x0002 -- see below
		//                                                             0x0004 -- see below
		$ParsedFlags['data_descriptor_used']    = (bool) ($flagbytes & 0x0008);
		$ParsedFlags['enhanced_deflation']      = (bool) ($flagbytes & 0x0010);
		$ParsedFlags['compressed_patched_data'] = (bool) ($flagbytes & 0x0020);
		$ParsedFlags['strong_encryption']       = (bool) ($flagbytes & 0x0040);
		//                                                             0x0080 - unused
		//                                                             0x0100 - unused
		//                                                             0x0200 - unused
		//                                                             0x0400 - unused
		$ParsedFlags['language_encoding']       = (bool) ($flagbytes & 0x0800);
		//                                                             0x1000 - reserved
		$ParsedFlags['mask_header_values']      = (bool) ($flagbytes & 0x2000);
		//                                                             0x4000 - reserved
		//                                                             0x8000 - reserved

		switch ($compressionmethod) {
			case 6:
				$ParsedFlags['dictionary_size']    = (($flagbytes & 0x0002) ? 8192 : 4096);
				$ParsedFlags['shannon_fano_trees'] = (($flagbytes & 0x0004) ? 3    : 2);
				break;

			case 8:
			case 9:
				switch (($flagbytes & 0x0006) >> 1) {
					case 0:
						$ParsedFlags['compression_speed'] = 'normal';
						break;
					case 1:
						$ParsedFlags['compression_speed'] = 'maximum';
						break;
					case 2:
						$ParsedFlags['compression_speed'] = 'fast';
						break;
					case 3:
						$ParsedFlags['compression_speed'] = 'superfast';
						break;
				}
				break;
		}

		return $ParsedFlags;
	}


	public static function ZIPversionOSLookup($index) {
		static $ZIPversionOSLookup = array(
			0  => 'MS-DOS and OS/2 (FAT / VFAT / FAT32 file systems)',
			1  => 'Amiga',
			2  => 'OpenVMS',
			3  => 'Unix',
			4  => 'VM/CMS',
			5  => 'Atari ST',
			6  => 'OS/2 H.P.F.S.',
			7  => 'Macintosh',
			8  => 'Z-System',
			9  => 'CP/M',
			10 => 'Windows NTFS',
			11 => 'MVS',
			12 => 'VSE',
			13 => 'Acorn Risc',
			14 => 'VFAT',
			15 => 'Alternate MVS',
			16 => 'BeOS',
			17 => 'Tandem',
			18 => 'OS/400',
			19 => 'OS/X (Darwin)',
		);

		return (isset($ZIPversionOSLookup[$index]) ? $ZIPversionOSLookup[$index] : '[unknown]');
	}

	public static function ZIPcompressionMethodLookup($index) {
		// http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/ZIP.html
		static $ZIPcompressionMethodLookup = array(
			0  => 'store',
			1  => 'shrink',
			2  => 'reduce-1',
			3  => 'reduce-2',
			4  => 'reduce-3',
			5  => 'reduce-4',
			6  => 'implode',
			7  => 'tokenize',
			8  => 'deflate',
			9  => 'deflate64',
			10 => 'Imploded (old IBM TERSE)',
			11 => 'RESERVED[11]',
			12 => 'BZIP2',
			13 => 'RESERVED[13]',
			14 => 'LZMA (EFS)',
			15 => 'RESERVED[15]',
			16 => 'RESERVED[16]',
			17 => 'RESERVED[17]',
			18 => 'IBM TERSE (new)',
			19 => 'IBM LZ77 z Architecture (PFS)',
			96 => 'JPEG recompressed',
			97 => 'WavPack compressed',
			98 => 'PPMd version I, Rev 1',
		);

		return (isset($ZIPcompressionMethodLookup[$index]) ? $ZIPcompressionMethodLookup[$index] : '[unknown]');
	}

	public static function DOStime2UNIXtime($DOSdate, $DOStime) {
		// wFatDate
		// Specifies the MS-DOS date. The date is a packed 16-bit value with the following format:
		// Bits      Contents
		// 0-4    Day of the month (1-31)
		// 5-8    Month (1 = January, 2 = February, and so on)
		// 9-15   Year offset from 1980 (add 1980 to get actual year)

		$UNIXday    =  ($DOSdate & 0x001F);
		$UNIXmonth  = (($DOSdate & 0x01E0) >> 5);
		$UNIXyear   = (($DOSdate & 0xFE00) >> 9) + 1980;

		// wFatTime
		// Specifies the MS-DOS time. The time is a packed 16-bit value with the following format:
		// Bits   Contents
		// 0-4    Second divided by 2
		// 5-10   Minute (0-59)
		// 11-15  Hour (0-23 on a 24-hour clock)

		$UNIXsecond =  ($DOStime & 0x001F) * 2;
		$UNIXminute = (($DOStime & 0x07E0) >> 5);
		$UNIXhour   = (($DOStime & 0xF800) >> 11);

		return gmmktime($UNIXhour, $UNIXminute, $UNIXsecond, $UNIXmonth, $UNIXday, $UNIXyear);
	}

}
