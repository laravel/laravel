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
// module.graphic.gif.php                                      //
// module for analyzing GIF Image files                        //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_gif extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat']                  = 'gif';
		$info['video']['dataformat']         = 'gif';
		$info['video']['lossless']           = true;
		$info['video']['pixel_aspect_ratio'] = (float) 1;

		$this->fseek($info['avdataoffset']);
		$GIFheader = $this->fread(13);
		$offset = 0;

		$info['gif']['header']['raw']['identifier']            =                              substr($GIFheader, $offset, 3);
		$offset += 3;

		$magic = 'GIF';
		if ($info['gif']['header']['raw']['identifier'] != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($info['gif']['header']['raw']['identifier']).'"';
			unset($info['fileformat']);
			unset($info['gif']);
			return false;
		}

		$info['gif']['header']['raw']['version']               =                              substr($GIFheader, $offset, 3);
		$offset += 3;
		$info['gif']['header']['raw']['width']                 = getid3_lib::LittleEndian2Int(substr($GIFheader, $offset, 2));
		$offset += 2;
		$info['gif']['header']['raw']['height']                = getid3_lib::LittleEndian2Int(substr($GIFheader, $offset, 2));
		$offset += 2;
		$info['gif']['header']['raw']['flags']                 = getid3_lib::LittleEndian2Int(substr($GIFheader, $offset, 1));
		$offset += 1;
		$info['gif']['header']['raw']['bg_color_index']        = getid3_lib::LittleEndian2Int(substr($GIFheader, $offset, 1));
		$offset += 1;
		$info['gif']['header']['raw']['aspect_ratio']          = getid3_lib::LittleEndian2Int(substr($GIFheader, $offset, 1));
		$offset += 1;

		$info['video']['resolution_x']                         = $info['gif']['header']['raw']['width'];
		$info['video']['resolution_y']                         = $info['gif']['header']['raw']['height'];
		$info['gif']['version']                                = $info['gif']['header']['raw']['version'];
		$info['gif']['header']['flags']['global_color_table']  = (bool) ($info['gif']['header']['raw']['flags'] & 0x80);
		if ($info['gif']['header']['raw']['flags'] & 0x80) {
			// Number of bits per primary color available to the original image, minus 1
			$info['gif']['header']['bits_per_pixel']  = 3 * ((($info['gif']['header']['raw']['flags'] & 0x70) >> 4) + 1);
		} else {
			$info['gif']['header']['bits_per_pixel']  = 0;
		}
		$info['gif']['header']['flags']['global_color_sorted'] = (bool) ($info['gif']['header']['raw']['flags'] & 0x40);
		if ($info['gif']['header']['flags']['global_color_table']) {
			// the number of bytes contained in the Global Color Table. To determine that
			// actual size of the color table, raise 2 to [the value of the field + 1]
			$info['gif']['header']['global_color_size'] = pow(2, ($info['gif']['header']['raw']['flags'] & 0x07) + 1);
			$info['video']['bits_per_sample']           = ($info['gif']['header']['raw']['flags'] & 0x07) + 1;
		} else {
			$info['gif']['header']['global_color_size'] = 0;
		}
		if ($info['gif']['header']['raw']['aspect_ratio'] != 0) {
			// Aspect Ratio = (Pixel Aspect Ratio + 15) / 64
			$info['gif']['header']['aspect_ratio'] = ($info['gif']['header']['raw']['aspect_ratio'] + 15) / 64;
		}

//		if ($info['gif']['header']['flags']['global_color_table']) {
//			$GIFcolorTable = $this->fread(3 * $info['gif']['header']['global_color_size']);
//			$offset = 0;
//			for ($i = 0; $i < $info['gif']['header']['global_color_size']; $i++) {
//				$red   = getid3_lib::LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
//				$green = getid3_lib::LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
//				$blue  = getid3_lib::LittleEndian2Int(substr($GIFcolorTable, $offset++, 1));
//				$info['gif']['global_color_table'][$i] = (($red << 16) | ($green << 8) | ($blue));
//			}
//		}
//
//		// Image Descriptor
//		while (!feof($this->getid3->fp)) {
//			$NextBlockTest = $this->fread(1);
//			switch ($NextBlockTest) {
//
//				case ',': // ',' - Image separator character
//
//					$ImageDescriptorData = $NextBlockTest.$this->fread(9);
//					$ImageDescriptor = array();
//					$ImageDescriptor['image_left']   = getid3_lib::LittleEndian2Int(substr($ImageDescriptorData, 1, 2));
//					$ImageDescriptor['image_top']    = getid3_lib::LittleEndian2Int(substr($ImageDescriptorData, 3, 2));
//					$ImageDescriptor['image_width']  = getid3_lib::LittleEndian2Int(substr($ImageDescriptorData, 5, 2));
//					$ImageDescriptor['image_height'] = getid3_lib::LittleEndian2Int(substr($ImageDescriptorData, 7, 2));
//					$ImageDescriptor['flags_raw']    = getid3_lib::LittleEndian2Int(substr($ImageDescriptorData, 9, 1));
//					$ImageDescriptor['flags']['use_local_color_map'] = (bool) ($ImageDescriptor['flags_raw'] & 0x80);
//					$ImageDescriptor['flags']['image_interlaced']    = (bool) ($ImageDescriptor['flags_raw'] & 0x40);
//					$info['gif']['image_descriptor'][] = $ImageDescriptor;
//
//					if ($ImageDescriptor['flags']['use_local_color_map']) {
//
//						$info['warning'][] = 'This version of getID3() cannot parse local color maps for GIFs';
//						return true;
//
//					}
//echo 'Start of raster data: '.$this->ftell().'<BR>';
//					$RasterData = array();
//					$RasterData['code_size']        = getid3_lib::LittleEndian2Int($this->fread(1));
//					$RasterData['block_byte_count'] = getid3_lib::LittleEndian2Int($this->fread(1));
//					$info['gif']['raster_data'][count($info['gif']['image_descriptor']) - 1] = $RasterData;
//
//					$CurrentCodeSize = $RasterData['code_size'] + 1;
//					for ($i = 0; $i < pow(2, $RasterData['code_size']); $i++) {
//						$DefaultDataLookupTable[$i] = chr($i);
//					}
//					$DefaultDataLookupTable[pow(2, $RasterData['code_size']) + 0] = ''; // Clear Code
//					$DefaultDataLookupTable[pow(2, $RasterData['code_size']) + 1] = ''; // End Of Image Code
//
//
//					$NextValue = $this->GetLSBits($CurrentCodeSize);
//					echo 'Clear Code: '.$NextValue.'<BR>';
//
//					$NextValue = $this->GetLSBits($CurrentCodeSize);
//					echo 'First Color: '.$NextValue.'<BR>';
//
//					$Prefix = $NextValue;
//$i = 0;
//					while ($i++ < 20) {
//						$NextValue = $this->GetLSBits($CurrentCodeSize);
//						echo $NextValue.'<BR>';
//					}
//return true;
//					break;
//
//				case '!':
//					// GIF Extension Block
//					$ExtensionBlockData = $NextBlockTest.$this->fread(2);
//					$ExtensionBlock = array();
//					$ExtensionBlock['function_code']  = getid3_lib::LittleEndian2Int(substr($ExtensionBlockData, 1, 1));
//					$ExtensionBlock['byte_length']    = getid3_lib::LittleEndian2Int(substr($ExtensionBlockData, 2, 1));
//					$ExtensionBlock['data']           = $this->fread($ExtensionBlock['byte_length']);
//					$info['gif']['extension_blocks'][] = $ExtensionBlock;
//					break;
//
//				case ';':
//					$info['gif']['terminator_offset'] = $this->ftell() - 1;
//					// GIF Terminator
//					break;
//
//				default:
//					break;
//
//
//			}
//		}

		return true;
	}


	public function GetLSBits($bits) {
		static $bitbuffer = '';
		while (strlen($bitbuffer) < $bits) {
			$bitbuffer = str_pad(decbin(ord($this->fread(1))), 8, '0', STR_PAD_LEFT).$bitbuffer;
		}
		$value = bindec(substr($bitbuffer, 0 - $bits));
		$bitbuffer = substr($bitbuffer, 0, 0 - $bits);

		return $value;
	}

}
