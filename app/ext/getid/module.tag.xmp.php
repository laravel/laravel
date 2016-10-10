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
// module.tag.xmp.php                                          //
// module for analyzing XMP metadata (e.g. in JPEG files)      //
// dependencies: NONE                                          //
//                                                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// Module originally written [2009-Mar-26] by                  //
//      Nigel Barnes <ngbarnesØhotmail*com>                    //
// Bundled into getID3 with permission                         //
//   called by getID3 in module.graphic.jpg.php                //
//                                                            ///
/////////////////////////////////////////////////////////////////

/**************************************************************************************************
 * SWISScenter Source                                                              Nigel Barnes
 *
 * 	Provides functions for reading information from the 'APP1' Extensible Metadata
 *	Platform (XMP) segment of JPEG format files.
 *	This XMP segment is XML based and contains the Resource Description Framework (RDF)
 *	data, which itself can contain the Dublin Core Metadata Initiative (DCMI) information.
 *
 * 	This code uses segments from the JPEG Metadata Toolkit project by Evan Hunter.
 *************************************************************************************************/
class Image_XMP
{
	/**
	* @var string
	* The name of the image file that contains the XMP fields to extract and modify.
	* @see Image_XMP()
	*/
	public $_sFilename = null;

	/**
	* @var array
	* The XMP fields that were extracted from the image or updated by this class.
	* @see getAllTags()
	*/
	public $_aXMP = array();

	/**
	* @var boolean
	* True if an APP1 segment was found to contain XMP metadata.
	* @see isValid()
	*/
	public $_bXMPParse = false;

	/**
	* Returns the status of XMP parsing during instantiation
	*
	* You'll normally want to call this method before trying to get XMP fields.
	*
	* @return boolean
	* Returns true if an APP1 segment was found to contain XMP metadata.
	*/
	public function isValid()
	{
		return $this->_bXMPParse;
	}

	/**
	* Get a copy of all XMP tags extracted from the image
	*
	* @return array - An array of XMP fields as it extracted by the XMPparse() function
	*/
	public function getAllTags()
	{
		return $this->_aXMP;
	}

	/**
	* Reads all the JPEG header segments from an JPEG image file into an array
	*
	* @param string $filename - the filename of the JPEG file to read
	* @return array $headerdata - Array of JPEG header segments
	* @return boolean FALSE - if headers could not be read
	*/
	public function _get_jpeg_header_data($filename)
	{
		// prevent refresh from aborting file operations and hosing file
		ignore_user_abort(true);

		// Attempt to open the jpeg file - the at symbol supresses the error message about
		// not being able to open files. The file_exists would have been used, but it
		// does not work with files fetched over http or ftp.
		if (is_readable($filename) && is_file($filename) && ($filehnd = fopen($filename, 'rb'))) {
			// great
		} else {
			return false;
		}

		// Read the first two characters
		$data = fread($filehnd, 2);

		// Check that the first two characters are 0xFF 0xD8  (SOI - Start of image)
		if ($data != "\xFF\xD8")
		{
			// No SOI (FF D8) at start of file - This probably isn't a JPEG file - close file and return;
			echo '<p>This probably is not a JPEG file</p>'."\n";
			fclose($filehnd);
			return false;
		}

		// Read the third character
		$data = fread($filehnd, 2);

		// Check that the third character is 0xFF (Start of first segment header)
		if ($data{0} != "\xFF")
		{
			// NO FF found - close file and return - JPEG is probably corrupted
			fclose($filehnd);
			return false;
		}

		// Flag that we havent yet hit the compressed image data
		$hit_compressed_image_data = false;

		// Cycle through the file until, one of: 1) an EOI (End of image) marker is hit,
		//                                       2) we have hit the compressed image data (no more headers are allowed after data)
		//                                       3) or end of file is hit

		while (($data{1} != "\xD9") && (!$hit_compressed_image_data) && (!feof($filehnd)))
		{
			// Found a segment to look at.
			// Check that the segment marker is not a Restart marker - restart markers don't have size or data after them
			if ((ord($data{1}) < 0xD0) || (ord($data{1}) > 0xD7))
			{
				// Segment isn't a Restart marker
				// Read the next two bytes (size)
				$sizestr = fread($filehnd, 2);

				// convert the size bytes to an integer
				$decodedsize = unpack('nsize', $sizestr);

				// Save the start position of the data
				$segdatastart = ftell($filehnd);

				// Read the segment data with length indicated by the previously read size
				$segdata = fread($filehnd, $decodedsize['size'] - 2);

				// Store the segment information in the output array
				$headerdata[] = array(
					'SegType'      => ord($data{1}),
					'SegName'      => $GLOBALS['JPEG_Segment_Names'][ord($data{1})],
					'SegDataStart' => $segdatastart,
					'SegData'      => $segdata,
				);
			}

			// If this is a SOS (Start Of Scan) segment, then there is no more header data - the compressed image data follows
			if ($data{1} == "\xDA")
			{
				// Flag that we have hit the compressed image data - exit loop as no more headers available.
				$hit_compressed_image_data = true;
			}
			else
			{
				// Not an SOS - Read the next two bytes - should be the segment marker for the next segment
				$data = fread($filehnd, 2);

				// Check that the first byte of the two is 0xFF as it should be for a marker
				if ($data{0} != "\xFF")
				{
					// NO FF found - close file and return - JPEG is probably corrupted
					fclose($filehnd);
					return false;
				}
			}
		}

		// Close File
		fclose($filehnd);
		// Alow the user to abort from now on
		ignore_user_abort(false);

		// Return the header data retrieved
		return $headerdata;
	}


	/**
	* Retrieves XMP information from an APP1 JPEG segment and returns the raw XML text as a string.
	*
	* @param string $filename - the filename of the JPEG file to read
	* @return string $xmp_data - the string of raw XML text
	* @return boolean FALSE - if an APP 1 XMP segment could not be found, or if an error occured
	*/
	public function _get_XMP_text($filename)
	{
		//Get JPEG header data
		$jpeg_header_data = $this->_get_jpeg_header_data($filename);

		//Cycle through the header segments
		for ($i = 0; $i < count($jpeg_header_data); $i++)
		{
			// If we find an APP1 header,
			if (strcmp($jpeg_header_data[$i]['SegName'], 'APP1') == 0)
			{
				// And if it has the Adobe XMP/RDF label (http://ns.adobe.com/xap/1.0/\x00) ,
				if (strncmp($jpeg_header_data[$i]['SegData'], 'http://ns.adobe.com/xap/1.0/'."\x00", 29) == 0)
				{
					// Found a XMP/RDF block
					// Return the XMP text
					$xmp_data = substr($jpeg_header_data[$i]['SegData'], 29);

					return trim($xmp_data); // trim() should not be neccesary, but some files found in the wild with null-terminated block (known samples from Apple Aperture) causes problems elsewhere (see http://www.getid3.org/phpBB3/viewtopic.php?f=4&t=1153)
				}
			}
		}
		return false;
	}

	/**
	* Parses a string containing XMP data (XML), and returns an array
	* which contains all the XMP (XML) information.
	*
	* @param string $xml_text - a string containing the XMP data (XML) to be parsed
	* @return array $xmp_array - an array containing all xmp details retrieved.
	* @return boolean FALSE - couldn't parse the XMP data
	*/
	public function read_XMP_array_from_text($xmltext)
	{
		// Check if there actually is any text to parse
		if (trim($xmltext) == '')
		{
			return false;
		}

		// Create an instance of a xml parser to parse the XML text
		$xml_parser = xml_parser_create('UTF-8');

		// Change: Fixed problem that caused the whitespace (especially newlines) to be destroyed when converting xml text to an xml array, as of revision 1.10

		// We would like to remove unneccessary white space, but this will also
		// remove things like newlines (&#xA;) in the XML values, so white space
		// will have to be removed later
		if (xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 0) == false)
		{
			// Error setting case folding - destroy the parser and return
			xml_parser_free($xml_parser);
			return false;
		}

		// to use XML code correctly we have to turn case folding
		// (uppercasing) off. XML is case sensitive and upper
		// casing is in reality XML standards violation
		if (xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0) == false)
		{
			// Error setting case folding - destroy the parser and return
			xml_parser_free($xml_parser);
			return false;
		}

		// Parse the XML text into a array structure
		if (xml_parse_into_struct($xml_parser, $xmltext, $values, $tags) == 0)
		{
			// Error Parsing XML - destroy the parser and return
			xml_parser_free($xml_parser);
			return false;
		}

		// Destroy the xml parser
		xml_parser_free($xml_parser);

		// Clear the output array
		$xmp_array = array();

		// The XMP data has now been parsed into an array ...

		// Cycle through each of the array elements
		$current_property = ''; // current property being processed
		$container_index = -1; // -1 = no container open, otherwise index of container content
		foreach ($values as $xml_elem)
		{
			// Syntax and Class names
			switch ($xml_elem['tag'])
			{
				case 'x:xmpmeta':
					// only defined attribute is x:xmptk written by Adobe XMP Toolkit; value is the version of the toolkit
					break;

				case 'rdf:RDF':
					// required element immediately within x:xmpmeta; no data here
					break;

				case 'rdf:Description':
					switch ($xml_elem['type'])
					{
						case 'open':
						case 'complete':
							if (array_key_exists('attributes', $xml_elem))
							{
								// rdf:Description may contain wanted attributes
								foreach (array_keys($xml_elem['attributes']) as $key)
								{
									// Check whether we want this details from this attribute
//									if (in_array($key, $GLOBALS['XMP_tag_captions']))
									if (true)
									{
										// Attribute wanted
										$xmp_array[$key] = $xml_elem['attributes'][$key];
									}
								}
							}
						case 'cdata':
						case 'close':
							break;
					}

				case 'rdf:ID':
				case 'rdf:nodeID':
					// Attributes are ignored
					break;

				case 'rdf:li':
					// Property member
					if ($xml_elem['type'] == 'complete')
					{
						if (array_key_exists('attributes', $xml_elem))
						{
							// If Lang Alt (language alternatives) then ensure we take the default language
							if (isset($xml_elem['attributes']['xml:lang']) && ($xml_elem['attributes']['xml:lang'] != 'x-default'))
							{
								break;
							}
						}
						if ($current_property != '')
						{
							$xmp_array[$current_property][$container_index] = (isset($xml_elem['value']) ? $xml_elem['value'] : '');
							$container_index += 1;
						}
					//else unidentified attribute!!
					}
					break;

				case 'rdf:Seq':
				case 'rdf:Bag':
				case 'rdf:Alt':
					// Container found
					switch ($xml_elem['type'])
					{
						case 'open':
 							$container_index = 0;
 							break;
						case 'close':
							$container_index = -1;
							break;
						case 'cdata':
							break;
					}
					break;

				default:
					// Check whether we want the details from this attribute
//					if (in_array($xml_elem['tag'], $GLOBALS['XMP_tag_captions']))
					if (true)
					{
						switch ($xml_elem['type'])
						{
							case 'open':
								// open current element
								$current_property = $xml_elem['tag'];
								break;

							case 'close':
								// close current element
								$current_property = '';
								break;

							case 'complete':
								// store attribute value
								$xmp_array[$xml_elem['tag']] = (isset($xml_elem['attributes']) ? $xml_elem['attributes'] : (isset($xml_elem['value']) ? $xml_elem['value'] : ''));
								break;

							case 'cdata':
								// ignore
								break;
						}
					}
					break;
			}

		}
		return $xmp_array;
	}


	/**
	* Constructor
	*
	* @param string - Name of the image file to access and extract XMP information from.
	*/
	public function Image_XMP($sFilename)
	{
		$this->_sFilename = $sFilename;

		if (is_file($this->_sFilename))
		{
			// Get XMP data
			$xmp_data = $this->_get_XMP_text($sFilename);
			if ($xmp_data)
			{
				$this->_aXMP = $this->read_XMP_array_from_text($xmp_data);
				$this->_bXMPParse = true;
			}
		}
	}

}

/**
* Global Variable: XMP_tag_captions
*
* The Property names of all known XMP fields.
* Note: this is a full list with unrequired properties commented out.
*/
/*
$GLOBALS['XMP_tag_captions'] = array(
// IPTC Core
	'Iptc4xmpCore:CiAdrCity',
	'Iptc4xmpCore:CiAdrCtry',
	'Iptc4xmpCore:CiAdrExtadr',
	'Iptc4xmpCore:CiAdrPcode',
	'Iptc4xmpCore:CiAdrRegion',
	'Iptc4xmpCore:CiEmailWork',
	'Iptc4xmpCore:CiTelWork',
	'Iptc4xmpCore:CiUrlWork',
	'Iptc4xmpCore:CountryCode',
	'Iptc4xmpCore:CreatorContactInfo',
	'Iptc4xmpCore:IntellectualGenre',
	'Iptc4xmpCore:Location',
	'Iptc4xmpCore:Scene',
	'Iptc4xmpCore:SubjectCode',
// Dublin Core Schema
	'dc:contributor',
	'dc:coverage',
	'dc:creator',
	'dc:date',
	'dc:description',
	'dc:format',
	'dc:identifier',
	'dc:language',
	'dc:publisher',
	'dc:relation',
	'dc:rights',
	'dc:source',
	'dc:subject',
	'dc:title',
	'dc:type',
// XMP Basic Schema
	'xmp:Advisory',
	'xmp:BaseURL',
	'xmp:CreateDate',
	'xmp:CreatorTool',
	'xmp:Identifier',
	'xmp:Label',
	'xmp:MetadataDate',
	'xmp:ModifyDate',
	'xmp:Nickname',
	'xmp:Rating',
	'xmp:Thumbnails',
	'xmpidq:Scheme',
// XMP Rights Management Schema
	'xmpRights:Certificate',
	'xmpRights:Marked',
	'xmpRights:Owner',
	'xmpRights:UsageTerms',
	'xmpRights:WebStatement',
// These are not in spec but Photoshop CS seems to use them
	'xap:Advisory',
	'xap:BaseURL',
	'xap:CreateDate',
	'xap:CreatorTool',
	'xap:Identifier',
	'xap:MetadataDate',
	'xap:ModifyDate',
	'xap:Nickname',
	'xap:Rating',
	'xap:Thumbnails',
	'xapidq:Scheme',
	'xapRights:Certificate',
	'xapRights:Copyright',
	'xapRights:Marked',
	'xapRights:Owner',
	'xapRights:UsageTerms',
	'xapRights:WebStatement',
// XMP Media Management Schema
	'xapMM:DerivedFrom',
	'xapMM:DocumentID',
	'xapMM:History',
	'xapMM:InstanceID',
	'xapMM:ManagedFrom',
	'xapMM:Manager',
	'xapMM:ManageTo',
	'xapMM:ManageUI',
	'xapMM:ManagerVariant',
	'xapMM:RenditionClass',
	'xapMM:RenditionParams',
	'xapMM:VersionID',
	'xapMM:Versions',
	'xapMM:LastURL',
	'xapMM:RenditionOf',
	'xapMM:SaveID',
// XMP Basic Job Ticket Schema
	'xapBJ:JobRef',
// XMP Paged-Text Schema
	'xmpTPg:MaxPageSize',
	'xmpTPg:NPages',
	'xmpTPg:Fonts',
	'xmpTPg:Colorants',
	'xmpTPg:PlateNames',
// Adobe PDF Schema
	'pdf:Keywords',
	'pdf:PDFVersion',
	'pdf:Producer',
// Photoshop Schema
	'photoshop:AuthorsPosition',
	'photoshop:CaptionWriter',
	'photoshop:Category',
	'photoshop:City',
	'photoshop:Country',
	'photoshop:Credit',
	'photoshop:DateCreated',
	'photoshop:Headline',
	'photoshop:History',
// Not in XMP spec
	'photoshop:Instructions',
	'photoshop:Source',
	'photoshop:State',
	'photoshop:SupplementalCategories',
	'photoshop:TransmissionReference',
	'photoshop:Urgency',
// EXIF Schemas
	'tiff:ImageWidth',
	'tiff:ImageLength',
	'tiff:BitsPerSample',
	'tiff:Compression',
	'tiff:PhotometricInterpretation',
	'tiff:Orientation',
	'tiff:SamplesPerPixel',
	'tiff:PlanarConfiguration',
	'tiff:YCbCrSubSampling',
	'tiff:YCbCrPositioning',
	'tiff:XResolution',
	'tiff:YResolution',
	'tiff:ResolutionUnit',
	'tiff:TransferFunction',
	'tiff:WhitePoint',
	'tiff:PrimaryChromaticities',
	'tiff:YCbCrCoefficients',
	'tiff:ReferenceBlackWhite',
	'tiff:DateTime',
	'tiff:ImageDescription',
	'tiff:Make',
	'tiff:Model',
	'tiff:Software',
	'tiff:Artist',
	'tiff:Copyright',
	'exif:ExifVersion',
	'exif:FlashpixVersion',
	'exif:ColorSpace',
	'exif:ComponentsConfiguration',
	'exif:CompressedBitsPerPixel',
	'exif:PixelXDimension',
	'exif:PixelYDimension',
	'exif:MakerNote',
	'exif:UserComment',
	'exif:RelatedSoundFile',
	'exif:DateTimeOriginal',
	'exif:DateTimeDigitized',
	'exif:ExposureTime',
	'exif:FNumber',
	'exif:ExposureProgram',
	'exif:SpectralSensitivity',
	'exif:ISOSpeedRatings',
	'exif:OECF',
	'exif:ShutterSpeedValue',
	'exif:ApertureValue',
	'exif:BrightnessValue',
	'exif:ExposureBiasValue',
	'exif:MaxApertureValue',
	'exif:SubjectDistance',
	'exif:MeteringMode',
	'exif:LightSource',
	'exif:Flash',
	'exif:FocalLength',
	'exif:SubjectArea',
	'exif:FlashEnergy',
	'exif:SpatialFrequencyResponse',
	'exif:FocalPlaneXResolution',
	'exif:FocalPlaneYResolution',
	'exif:FocalPlaneResolutionUnit',
	'exif:SubjectLocation',
	'exif:SensingMethod',
	'exif:FileSource',
	'exif:SceneType',
	'exif:CFAPattern',
	'exif:CustomRendered',
	'exif:ExposureMode',
	'exif:WhiteBalance',
	'exif:DigitalZoomRatio',
	'exif:FocalLengthIn35mmFilm',
	'exif:SceneCaptureType',
	'exif:GainControl',
	'exif:Contrast',
	'exif:Saturation',
	'exif:Sharpness',
	'exif:DeviceSettingDescription',
	'exif:SubjectDistanceRange',
	'exif:ImageUniqueID',
	'exif:GPSVersionID',
	'exif:GPSLatitude',
	'exif:GPSLongitude',
	'exif:GPSAltitudeRef',
	'exif:GPSAltitude',
	'exif:GPSTimeStamp',
	'exif:GPSSatellites',
	'exif:GPSStatus',
	'exif:GPSMeasureMode',
	'exif:GPSDOP',
	'exif:GPSSpeedRef',
	'exif:GPSSpeed',
	'exif:GPSTrackRef',
	'exif:GPSTrack',
	'exif:GPSImgDirectionRef',
	'exif:GPSImgDirection',
	'exif:GPSMapDatum',
	'exif:GPSDestLatitude',
	'exif:GPSDestLongitude',
	'exif:GPSDestBearingRef',
	'exif:GPSDestBearing',
	'exif:GPSDestDistanceRef',
	'exif:GPSDestDistance',
	'exif:GPSProcessingMethod',
	'exif:GPSAreaInformation',
	'exif:GPSDifferential',
	'stDim:w',
	'stDim:h',
	'stDim:unit',
	'xapGImg:height',
	'xapGImg:width',
	'xapGImg:format',
	'xapGImg:image',
	'stEvt:action',
	'stEvt:instanceID',
	'stEvt:parameters',
	'stEvt:softwareAgent',
	'stEvt:when',
	'stRef:instanceID',
	'stRef:documentID',
	'stRef:versionID',
	'stRef:renditionClass',
	'stRef:renditionParams',
	'stRef:manager',
	'stRef:managerVariant',
	'stRef:manageTo',
	'stRef:manageUI',
	'stVer:comments',
	'stVer:event',
	'stVer:modifyDate',
	'stVer:modifier',
	'stVer:version',
	'stJob:name',
	'stJob:id',
	'stJob:url',
// Exif Flash
	'exif:Fired',
	'exif:Return',
	'exif:Mode',
	'exif:Function',
	'exif:RedEyeMode',
// Exif OECF/SFR
	'exif:Columns',
	'exif:Rows',
	'exif:Names',
	'exif:Values',
// Exif CFAPattern
	'exif:Columns',
	'exif:Rows',
	'exif:Values',
// Exif DeviceSettings
	'exif:Columns',
	'exif:Rows',
	'exif:Settings',
);
*/

/**
* Global Variable: JPEG_Segment_Names
*
* The names of the JPEG segment markers, indexed by their marker number
*/
$GLOBALS['JPEG_Segment_Names'] = array(
	0x01 => 'TEM',
	0x02 => 'RES',
	0xC0 => 'SOF0',
	0xC1 => 'SOF1',
	0xC2 => 'SOF2',
	0xC3 => 'SOF4',
	0xC4 => 'DHT',
	0xC5 => 'SOF5',
	0xC6 => 'SOF6',
	0xC7 => 'SOF7',
	0xC8 => 'JPG',
	0xC9 => 'SOF9',
	0xCA => 'SOF10',
	0xCB => 'SOF11',
	0xCC => 'DAC',
	0xCD => 'SOF13',
	0xCE => 'SOF14',
	0xCF => 'SOF15',
	0xD0 => 'RST0',
	0xD1 => 'RST1',
	0xD2 => 'RST2',
	0xD3 => 'RST3',
	0xD4 => 'RST4',
	0xD5 => 'RST5',
	0xD6 => 'RST6',
	0xD7 => 'RST7',
	0xD8 => 'SOI',
	0xD9 => 'EOI',
	0xDA => 'SOS',
	0xDB => 'DQT',
	0xDC => 'DNL',
	0xDD => 'DRI',
	0xDE => 'DHP',
	0xDF => 'EXP',
	0xE0 => 'APP0',
	0xE1 => 'APP1',
	0xE2 => 'APP2',
	0xE3 => 'APP3',
	0xE4 => 'APP4',
	0xE5 => 'APP5',
	0xE6 => 'APP6',
	0xE7 => 'APP7',
	0xE8 => 'APP8',
	0xE9 => 'APP9',
	0xEA => 'APP10',
	0xEB => 'APP11',
	0xEC => 'APP12',
	0xED => 'APP13',
	0xEE => 'APP14',
	0xEF => 'APP15',
	0xF0 => 'JPG0',
	0xF1 => 'JPG1',
	0xF2 => 'JPG2',
	0xF3 => 'JPG3',
	0xF4 => 'JPG4',
	0xF5 => 'JPG5',
	0xF6 => 'JPG6',
	0xF7 => 'JPG7',
	0xF8 => 'JPG8',
	0xF9 => 'JPG9',
	0xFA => 'JPG10',
	0xFB => 'JPG11',
	0xFC => 'JPG12',
	0xFD => 'JPG13',
	0xFE => 'COM',
);
