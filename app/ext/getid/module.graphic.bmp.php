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
// module.graphic.bmp.php                                      //
// module for analyzing BMP Image files                        //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_bmp extends getid3_handler
{
	public $ExtractPalette = false;
	public $ExtractData    = false;

	public function Analyze() {
		$info = &$this->getid3->info;

		// shortcuts
		$info['bmp']['header']['raw'] = array();
		$thisfile_bmp                         = &$info['bmp'];
		$thisfile_bmp_header                  = &$thisfile_bmp['header'];
		$thisfile_bmp_header_raw              = &$thisfile_bmp_header['raw'];

		// BITMAPFILEHEADER [14 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_62uq.asp
		// all versions
		// WORD    bfType;
		// DWORD   bfSize;
		// WORD    bfReserved1;
		// WORD    bfReserved2;
		// DWORD   bfOffBits;

		$this->fseek($info['avdataoffset']);
		$offset = 0;
		$BMPheader = $this->fread(14 + 40);

		$thisfile_bmp_header_raw['identifier']  = substr($BMPheader, $offset, 2);
		$offset += 2;

		$magic = 'BM';
		if ($thisfile_bmp_header_raw['identifier'] != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at offset '.$info['avdataoffset'].', found "'.getid3_lib::PrintHexBytes($thisfile_bmp_header_raw['identifier']).'"';
			unset($info['fileformat']);
			unset($info['bmp']);
			return false;
		}

		$thisfile_bmp_header_raw['filesize']    = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
		$offset += 4;
		$thisfile_bmp_header_raw['reserved1']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
		$offset += 2;
		$thisfile_bmp_header_raw['reserved2']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
		$offset += 2;
		$thisfile_bmp_header_raw['data_offset'] = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
		$offset += 4;
		$thisfile_bmp_header_raw['header_size'] = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
		$offset += 4;


		// check if the hardcoded-to-1 "planes" is at offset 22 or 26
		$planes22 = getid3_lib::LittleEndian2Int(substr($BMPheader, 22, 2));
		$planes26 = getid3_lib::LittleEndian2Int(substr($BMPheader, 26, 2));
		if (($planes22 == 1) && ($planes26 != 1)) {
			$thisfile_bmp['type_os']      = 'OS/2';
			$thisfile_bmp['type_version'] = 1;
		} elseif (($planes26 == 1) && ($planes22 != 1)) {
			$thisfile_bmp['type_os']      = 'Windows';
			$thisfile_bmp['type_version'] = 1;
		} elseif ($thisfile_bmp_header_raw['header_size'] == 12) {
			$thisfile_bmp['type_os']      = 'OS/2';
			$thisfile_bmp['type_version'] = 1;
		} elseif ($thisfile_bmp_header_raw['header_size'] == 40) {
			$thisfile_bmp['type_os']      = 'Windows';
			$thisfile_bmp['type_version'] = 1;
		} elseif ($thisfile_bmp_header_raw['header_size'] == 84) {
			$thisfile_bmp['type_os']      = 'Windows';
			$thisfile_bmp['type_version'] = 4;
		} elseif ($thisfile_bmp_header_raw['header_size'] == 100) {
			$thisfile_bmp['type_os']      = 'Windows';
			$thisfile_bmp['type_version'] = 5;
		} else {
			$info['error'][] = 'Unknown BMP subtype (or not a BMP file)';
			unset($info['fileformat']);
			unset($info['bmp']);
			return false;
		}

		$info['fileformat']                  = 'bmp';
		$info['video']['dataformat']         = 'bmp';
		$info['video']['lossless']           = true;
		$info['video']['pixel_aspect_ratio'] = (float) 1;

		if ($thisfile_bmp['type_os'] == 'OS/2') {

			// OS/2-format BMP
			// http://netghost.narod.ru/gff/graphics/summary/os2bmp.htm

			// DWORD  Size;             /* Size of this structure in bytes */
			// DWORD  Width;            /* Bitmap width in pixels */
			// DWORD  Height;           /* Bitmap height in pixel */
			// WORD   NumPlanes;        /* Number of bit planes (color depth) */
			// WORD   BitsPerPixel;     /* Number of bits per pixel per plane */

			$thisfile_bmp_header_raw['width']          = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
			$offset += 2;
			$thisfile_bmp_header_raw['height']         = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
			$offset += 2;
			$thisfile_bmp_header_raw['planes']         = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
			$offset += 2;
			$thisfile_bmp_header_raw['bits_per_pixel'] = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
			$offset += 2;

			$info['video']['resolution_x']    = $thisfile_bmp_header_raw['width'];
			$info['video']['resolution_y']    = $thisfile_bmp_header_raw['height'];
			$info['video']['codec']           = 'BI_RGB '.$thisfile_bmp_header_raw['bits_per_pixel'].'-bit';
			$info['video']['bits_per_sample'] = $thisfile_bmp_header_raw['bits_per_pixel'];

			if ($thisfile_bmp['type_version'] >= 2) {
				// DWORD  Compression;      /* Bitmap compression scheme */
				// DWORD  ImageDataSize;    /* Size of bitmap data in bytes */
				// DWORD  XResolution;      /* X resolution of display device */
				// DWORD  YResolution;      /* Y resolution of display device */
				// DWORD  ColorsUsed;       /* Number of color table indices used */
				// DWORD  ColorsImportant;  /* Number of important color indices */
				// WORD   Units;            /* Type of units used to measure resolution */
				// WORD   Reserved;         /* Pad structure to 4-byte boundary */
				// WORD   Recording;        /* Recording algorithm */
				// WORD   Rendering;        /* Halftoning algorithm used */
				// DWORD  Size1;            /* Reserved for halftoning algorithm use */
				// DWORD  Size2;            /* Reserved for halftoning algorithm use */
				// DWORD  ColorEncoding;    /* Color model used in bitmap */
				// DWORD  Identifier;       /* Reserved for application use */

				$thisfile_bmp_header_raw['compression']      = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['bmp_data_size']    = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['resolution_h']     = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['resolution_v']     = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['colors_used']      = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['colors_important'] = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['resolution_units'] = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
				$offset += 2;
				$thisfile_bmp_header_raw['reserved1']        = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
				$offset += 2;
				$thisfile_bmp_header_raw['recording']        = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
				$offset += 2;
				$thisfile_bmp_header_raw['rendering']        = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
				$offset += 2;
				$thisfile_bmp_header_raw['size1']            = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['size2']            = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['color_encoding']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['identifier']       = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;

				$thisfile_bmp_header['compression'] = $this->BMPcompressionOS2Lookup($thisfile_bmp_header_raw['compression']);

				$info['video']['codec'] = $thisfile_bmp_header['compression'].' '.$thisfile_bmp_header_raw['bits_per_pixel'].'-bit';
			}

		} elseif ($thisfile_bmp['type_os'] == 'Windows') {

			// Windows-format BMP

			// BITMAPINFOHEADER - [40 bytes] http://msdn.microsoft.com/library/en-us/gdi/bitmaps_1rw2.asp
			// all versions
			// DWORD  biSize;
			// LONG   biWidth;
			// LONG   biHeight;
			// WORD   biPlanes;
			// WORD   biBitCount;
			// DWORD  biCompression;
			// DWORD  biSizeImage;
			// LONG   biXPelsPerMeter;
			// LONG   biYPelsPerMeter;
			// DWORD  biClrUsed;
			// DWORD  biClrImportant;

			// possibly integrate this section and module.audio-video.riff.php::ParseBITMAPINFOHEADER() ?

			$thisfile_bmp_header_raw['width']            = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4), true);
			$offset += 4;
			$thisfile_bmp_header_raw['height']           = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4), true);
			$offset += 4;
			$thisfile_bmp_header_raw['planes']           = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
			$offset += 2;
			$thisfile_bmp_header_raw['bits_per_pixel']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 2));
			$offset += 2;
			$thisfile_bmp_header_raw['compression']      = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
			$offset += 4;
			$thisfile_bmp_header_raw['bmp_data_size']    = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
			$offset += 4;
			$thisfile_bmp_header_raw['resolution_h']     = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4), true);
			$offset += 4;
			$thisfile_bmp_header_raw['resolution_v']     = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4), true);
			$offset += 4;
			$thisfile_bmp_header_raw['colors_used']      = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
			$offset += 4;
			$thisfile_bmp_header_raw['colors_important'] = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
			$offset += 4;

			$thisfile_bmp_header['compression']  = $this->BMPcompressionWindowsLookup($thisfile_bmp_header_raw['compression']);
			$info['video']['resolution_x']    = $thisfile_bmp_header_raw['width'];
			$info['video']['resolution_y']    = $thisfile_bmp_header_raw['height'];
			$info['video']['codec']           = $thisfile_bmp_header['compression'].' '.$thisfile_bmp_header_raw['bits_per_pixel'].'-bit';
			$info['video']['bits_per_sample'] = $thisfile_bmp_header_raw['bits_per_pixel'];

			if (($thisfile_bmp['type_version'] >= 4) || ($thisfile_bmp_header_raw['compression'] == 3)) {
				// should only be v4+, but BMPs with type_version==1 and BI_BITFIELDS compression have been seen
				$BMPheader .= $this->fread(44);

				// BITMAPV4HEADER - [44 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_2k1e.asp
				// Win95+, WinNT4.0+
				// DWORD        bV4RedMask;
				// DWORD        bV4GreenMask;
				// DWORD        bV4BlueMask;
				// DWORD        bV4AlphaMask;
				// DWORD        bV4CSType;
				// CIEXYZTRIPLE bV4Endpoints;
				// DWORD        bV4GammaRed;
				// DWORD        bV4GammaGreen;
				// DWORD        bV4GammaBlue;
				$thisfile_bmp_header_raw['red_mask']     = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['green_mask']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['blue_mask']    = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['alpha_mask']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['cs_type']      = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['ciexyz_red']   =                  substr($BMPheader, $offset, 4);
				$offset += 4;
				$thisfile_bmp_header_raw['ciexyz_green'] =                  substr($BMPheader, $offset, 4);
				$offset += 4;
				$thisfile_bmp_header_raw['ciexyz_blue']  =                  substr($BMPheader, $offset, 4);
				$offset += 4;
				$thisfile_bmp_header_raw['gamma_red']    = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['gamma_green']  = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['gamma_blue']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;

				$thisfile_bmp_header['ciexyz_red']   = getid3_lib::FixedPoint2_30(strrev($thisfile_bmp_header_raw['ciexyz_red']));
				$thisfile_bmp_header['ciexyz_green'] = getid3_lib::FixedPoint2_30(strrev($thisfile_bmp_header_raw['ciexyz_green']));
				$thisfile_bmp_header['ciexyz_blue']  = getid3_lib::FixedPoint2_30(strrev($thisfile_bmp_header_raw['ciexyz_blue']));
			}

			if ($thisfile_bmp['type_version'] >= 5) {
				$BMPheader .= $this->fread(16);

				// BITMAPV5HEADER - [16 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_7c36.asp
				// Win98+, Win2000+
				// DWORD        bV5Intent;
				// DWORD        bV5ProfileData;
				// DWORD        bV5ProfileSize;
				// DWORD        bV5Reserved;
				$thisfile_bmp_header_raw['intent']              = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['profile_data_offset'] = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['profile_data_size']   = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
				$thisfile_bmp_header_raw['reserved3']           = getid3_lib::LittleEndian2Int(substr($BMPheader, $offset, 4));
				$offset += 4;
			}

		} else {

			$info['error'][] = 'Unknown BMP format in header.';
			return false;

		}


		if ($this->ExtractPalette || $this->ExtractData) {
			$PaletteEntries = 0;
			if ($thisfile_bmp_header_raw['bits_per_pixel'] < 16) {
				$PaletteEntries = pow(2, $thisfile_bmp_header_raw['bits_per_pixel']);
			} elseif (isset($thisfile_bmp_header_raw['colors_used']) && ($thisfile_bmp_header_raw['colors_used'] > 0) && ($thisfile_bmp_header_raw['colors_used'] <= 256)) {
				$PaletteEntries = $thisfile_bmp_header_raw['colors_used'];
			}
			if ($PaletteEntries > 0) {
				$BMPpalette = $this->fread(4 * $PaletteEntries);
				$paletteoffset = 0;
				for ($i = 0; $i < $PaletteEntries; $i++) {
					// RGBQUAD          - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_5f8y.asp
					// BYTE    rgbBlue;
					// BYTE    rgbGreen;
					// BYTE    rgbRed;
					// BYTE    rgbReserved;
					$blue  = getid3_lib::LittleEndian2Int(substr($BMPpalette, $paletteoffset++, 1));
					$green = getid3_lib::LittleEndian2Int(substr($BMPpalette, $paletteoffset++, 1));
					$red   = getid3_lib::LittleEndian2Int(substr($BMPpalette, $paletteoffset++, 1));
					if (($thisfile_bmp['type_os'] == 'OS/2') && ($thisfile_bmp['type_version'] == 1)) {
						// no padding byte
					} else {
						$paletteoffset++; // padding byte
					}
					$thisfile_bmp['palette'][$i] = (($red << 16) | ($green << 8) | $blue);
				}
			}
		}

		if ($this->ExtractData) {
			$this->fseek($thisfile_bmp_header_raw['data_offset']);
			$RowByteLength = ceil(($thisfile_bmp_header_raw['width'] * ($thisfile_bmp_header_raw['bits_per_pixel'] / 8)) / 4) * 4; // round up to nearest DWORD boundry
			$BMPpixelData = $this->fread($thisfile_bmp_header_raw['height'] * $RowByteLength);
			$pixeldataoffset = 0;
			$thisfile_bmp_header_raw['compression'] = (isset($thisfile_bmp_header_raw['compression']) ? $thisfile_bmp_header_raw['compression'] : '');
			switch ($thisfile_bmp_header_raw['compression']) {

				case 0: // BI_RGB
					switch ($thisfile_bmp_header_raw['bits_per_pixel']) {
						case 1:
							for ($row = ($thisfile_bmp_header_raw['height'] - 1); $row >= 0; $row--) {
								for ($col = 0; $col < $thisfile_bmp_header_raw['width']; $col = $col) {
									$paletteindexbyte = ord($BMPpixelData{$pixeldataoffset++});
									for ($i = 7; $i >= 0; $i--) {
										$paletteindex = ($paletteindexbyte & (0x01 << $i)) >> $i;
										$thisfile_bmp['data'][$row][$col] = $thisfile_bmp['palette'][$paletteindex];
										$col++;
									}
								}
								while (($pixeldataoffset % 4) != 0) {
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
							break;

						case 4:
							for ($row = ($thisfile_bmp_header_raw['height'] - 1); $row >= 0; $row--) {
								for ($col = 0; $col < $thisfile_bmp_header_raw['width']; $col = $col) {
									$paletteindexbyte = ord($BMPpixelData{$pixeldataoffset++});
									for ($i = 1; $i >= 0; $i--) {
										$paletteindex = ($paletteindexbyte & (0x0F << (4 * $i))) >> (4 * $i);
										$thisfile_bmp['data'][$row][$col] = $thisfile_bmp['palette'][$paletteindex];
										$col++;
									}
								}
								while (($pixeldataoffset % 4) != 0) {
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
							break;

						case 8:
							for ($row = ($thisfile_bmp_header_raw['height'] - 1); $row >= 0; $row--) {
								for ($col = 0; $col < $thisfile_bmp_header_raw['width']; $col++) {
									$paletteindex = ord($BMPpixelData{$pixeldataoffset++});
									$thisfile_bmp['data'][$row][$col] = $thisfile_bmp['palette'][$paletteindex];
								}
								while (($pixeldataoffset % 4) != 0) {
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
							break;

						case 24:
							for ($row = ($thisfile_bmp_header_raw['height'] - 1); $row >= 0; $row--) {
								for ($col = 0; $col < $thisfile_bmp_header_raw['width']; $col++) {
									$thisfile_bmp['data'][$row][$col] = (ord($BMPpixelData{$pixeldataoffset+2}) << 16) | (ord($BMPpixelData{$pixeldataoffset+1}) << 8) | ord($BMPpixelData{$pixeldataoffset});
									$pixeldataoffset += 3;
								}
								while (($pixeldataoffset % 4) != 0) {
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
							break;

						case 32:
							for ($row = ($thisfile_bmp_header_raw['height'] - 1); $row >= 0; $row--) {
								for ($col = 0; $col < $thisfile_bmp_header_raw['width']; $col++) {
									$thisfile_bmp['data'][$row][$col] = (ord($BMPpixelData{$pixeldataoffset+3}) << 24) | (ord($BMPpixelData{$pixeldataoffset+2}) << 16) | (ord($BMPpixelData{$pixeldataoffset+1}) << 8) | ord($BMPpixelData{$pixeldataoffset});
									$pixeldataoffset += 4;
								}
								while (($pixeldataoffset % 4) != 0) {
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
							break;

						case 16:
							// ?
							break;

						default:
							$info['error'][] = 'Unknown bits-per-pixel value ('.$thisfile_bmp_header_raw['bits_per_pixel'].') - cannot read pixel data';
							break;
					}
					break;


				case 1: // BI_RLE8 - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_6x0u.asp
					switch ($thisfile_bmp_header_raw['bits_per_pixel']) {
						case 8:
							$pixelcounter = 0;
							while ($pixeldataoffset < strlen($BMPpixelData)) {
								$firstbyte  = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
								$secondbyte = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
								if ($firstbyte == 0) {

									// escaped/absolute mode - the first byte of the pair can be set to zero to
									// indicate an escape character that denotes the end of a line, the end of
									// a bitmap, or a delta, depending on the value of the second byte.
									switch ($secondbyte) {
										case 0:
											// end of line
											// no need for special processing, just ignore
											break;

										case 1:
											// end of bitmap
											$pixeldataoffset = strlen($BMPpixelData); // force to exit loop just in case
											break;

										case 2:
											// delta - The 2 bytes following the escape contain unsigned values
											// indicating the horizontal and vertical offsets of the next pixel
											// from the current position.
											$colincrement = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
											$rowincrement = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
											$col = ($pixelcounter % $thisfile_bmp_header_raw['width']) + $colincrement;
											$row = ($thisfile_bmp_header_raw['height'] - 1 - (($pixelcounter - $col) / $thisfile_bmp_header_raw['width'])) - $rowincrement;
											$pixelcounter = ($row * $thisfile_bmp_header_raw['width']) + $col;
											break;

										default:
											// In absolute mode, the first byte is zero and the second byte is a
											// value in the range 03H through FFH. The second byte represents the
											// number of bytes that follow, each of which contains the color index
											// of a single pixel. Each run must be aligned on a word boundary.
											for ($i = 0; $i < $secondbyte; $i++) {
												$paletteindex = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
												$col = $pixelcounter % $thisfile_bmp_header_raw['width'];
												$row = $thisfile_bmp_header_raw['height'] - 1 - (($pixelcounter - $col) / $thisfile_bmp_header_raw['width']);
												$thisfile_bmp['data'][$row][$col] = $thisfile_bmp['palette'][$paletteindex];
												$pixelcounter++;
											}
											while (($pixeldataoffset % 2) != 0) {
												// Each run must be aligned on a word boundary.
												$pixeldataoffset++;
											}
											break;
									}

								} else {

									// encoded mode - the first byte specifies the number of consecutive pixels
									// to be drawn using the color index contained in the second byte.
									for ($i = 0; $i < $firstbyte; $i++) {
										$col = $pixelcounter % $thisfile_bmp_header_raw['width'];
										$row = $thisfile_bmp_header_raw['height'] - 1 - (($pixelcounter - $col) / $thisfile_bmp_header_raw['width']);
										$thisfile_bmp['data'][$row][$col] = $thisfile_bmp['palette'][$secondbyte];
										$pixelcounter++;
									}

								}
							}
							break;

						default:
							$info['error'][] = 'Unknown bits-per-pixel value ('.$thisfile_bmp_header_raw['bits_per_pixel'].') - cannot read pixel data';
							break;
					}
					break;



				case 2: // BI_RLE4 - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_6x0u.asp
					switch ($thisfile_bmp_header_raw['bits_per_pixel']) {
						case 4:
							$pixelcounter = 0;
							while ($pixeldataoffset < strlen($BMPpixelData)) {
								$firstbyte  = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
								$secondbyte = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
								if ($firstbyte == 0) {

									// escaped/absolute mode - the first byte of the pair can be set to zero to
									// indicate an escape character that denotes the end of a line, the end of
									// a bitmap, or a delta, depending on the value of the second byte.
									switch ($secondbyte) {
										case 0:
											// end of line
											// no need for special processing, just ignore
											break;

										case 1:
											// end of bitmap
											$pixeldataoffset = strlen($BMPpixelData); // force to exit loop just in case
											break;

										case 2:
											// delta - The 2 bytes following the escape contain unsigned values
											// indicating the horizontal and vertical offsets of the next pixel
											// from the current position.
											$colincrement = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
											$rowincrement = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
											$col = ($pixelcounter % $thisfile_bmp_header_raw['width']) + $colincrement;
											$row = ($thisfile_bmp_header_raw['height'] - 1 - (($pixelcounter - $col) / $thisfile_bmp_header_raw['width'])) - $rowincrement;
											$pixelcounter = ($row * $thisfile_bmp_header_raw['width']) + $col;
											break;

										default:
											// In absolute mode, the first byte is zero. The second byte contains the number
											// of color indexes that follow. Subsequent bytes contain color indexes in their
											// high- and low-order 4 bits, one color index for each pixel. In absolute mode,
											// each run must be aligned on a word boundary.
											unset($paletteindexes);
											for ($i = 0; $i < ceil($secondbyte / 2); $i++) {
												$paletteindexbyte = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset++, 1));
												$paletteindexes[] = ($paletteindexbyte & 0xF0) >> 4;
												$paletteindexes[] = ($paletteindexbyte & 0x0F);
											}
											while (($pixeldataoffset % 2) != 0) {
												// Each run must be aligned on a word boundary.
												$pixeldataoffset++;
											}

											foreach ($paletteindexes as $paletteindex) {
												$col = $pixelcounter % $thisfile_bmp_header_raw['width'];
												$row = $thisfile_bmp_header_raw['height'] - 1 - (($pixelcounter - $col) / $thisfile_bmp_header_raw['width']);
												$thisfile_bmp['data'][$row][$col] = $thisfile_bmp['palette'][$paletteindex];
												$pixelcounter++;
											}
											break;
									}

								} else {

									// encoded mode - the first byte of the pair contains the number of pixels to be
									// drawn using the color indexes in the second byte. The second byte contains two
									// color indexes, one in its high-order 4 bits and one in its low-order 4 bits.
									// The first of the pixels is drawn using the color specified by the high-order
									// 4 bits, the second is drawn using the color in the low-order 4 bits, the third
									// is drawn using the color in the high-order 4 bits, and so on, until all the
									// pixels specified by the first byte have been drawn.
									$paletteindexes[0] = ($secondbyte & 0xF0) >> 4;
									$paletteindexes[1] = ($secondbyte & 0x0F);
									for ($i = 0; $i < $firstbyte; $i++) {
										$col = $pixelcounter % $thisfile_bmp_header_raw['width'];
										$row = $thisfile_bmp_header_raw['height'] - 1 - (($pixelcounter - $col) / $thisfile_bmp_header_raw['width']);
										$thisfile_bmp['data'][$row][$col] = $thisfile_bmp['palette'][$paletteindexes[($i % 2)]];
										$pixelcounter++;
									}

								}
							}
							break;

						default:
							$info['error'][] = 'Unknown bits-per-pixel value ('.$thisfile_bmp_header_raw['bits_per_pixel'].') - cannot read pixel data';
							break;
					}
					break;


				case 3: // BI_BITFIELDS
					switch ($thisfile_bmp_header_raw['bits_per_pixel']) {
						case 16:
						case 32:
							$redshift   = 0;
							$greenshift = 0;
							$blueshift  = 0;
							while ((($thisfile_bmp_header_raw['red_mask'] >> $redshift) & 0x01) == 0) {
								$redshift++;
							}
							while ((($thisfile_bmp_header_raw['green_mask'] >> $greenshift) & 0x01) == 0) {
								$greenshift++;
							}
							while ((($thisfile_bmp_header_raw['blue_mask'] >> $blueshift) & 0x01) == 0) {
								$blueshift++;
							}
							for ($row = ($thisfile_bmp_header_raw['height'] - 1); $row >= 0; $row--) {
								for ($col = 0; $col < $thisfile_bmp_header_raw['width']; $col++) {
									$pixelvalue = getid3_lib::LittleEndian2Int(substr($BMPpixelData, $pixeldataoffset, $thisfile_bmp_header_raw['bits_per_pixel'] / 8));
									$pixeldataoffset += $thisfile_bmp_header_raw['bits_per_pixel'] / 8;

									$red   = intval(round(((($pixelvalue & $thisfile_bmp_header_raw['red_mask'])   >> $redshift)   / ($thisfile_bmp_header_raw['red_mask']   >> $redshift))   * 255));
									$green = intval(round(((($pixelvalue & $thisfile_bmp_header_raw['green_mask']) >> $greenshift) / ($thisfile_bmp_header_raw['green_mask'] >> $greenshift)) * 255));
									$blue  = intval(round(((($pixelvalue & $thisfile_bmp_header_raw['blue_mask'])  >> $blueshift)  / ($thisfile_bmp_header_raw['blue_mask']  >> $blueshift))  * 255));
									$thisfile_bmp['data'][$row][$col] = (($red << 16) | ($green << 8) | ($blue));
								}
								while (($pixeldataoffset % 4) != 0) {
									// lines are padded to nearest DWORD
									$pixeldataoffset++;
								}
							}
							break;

						default:
							$info['error'][] = 'Unknown bits-per-pixel value ('.$thisfile_bmp_header_raw['bits_per_pixel'].') - cannot read pixel data';
							break;
					}
					break;


				default: // unhandled compression type
					$info['error'][] = 'Unknown/unhandled compression type value ('.$thisfile_bmp_header_raw['compression'].') - cannot decompress pixel data';
					break;
			}
		}

		return true;
	}


	public function PlotBMP(&$BMPinfo) {
		$starttime = time();
		if (!isset($BMPinfo['bmp']['data']) || !is_array($BMPinfo['bmp']['data'])) {
			echo 'ERROR: no pixel data<BR>';
			return false;
		}
		set_time_limit(intval(round($BMPinfo['resolution_x'] * $BMPinfo['resolution_y'] / 10000)));
		if ($im = ImageCreateTrueColor($BMPinfo['resolution_x'], $BMPinfo['resolution_y'])) {
			for ($row = 0; $row < $BMPinfo['resolution_y']; $row++) {
				for ($col = 0; $col < $BMPinfo['resolution_x']; $col++) {
					if (isset($BMPinfo['bmp']['data'][$row][$col])) {
						$red   = ($BMPinfo['bmp']['data'][$row][$col] & 0x00FF0000) >> 16;
						$green = ($BMPinfo['bmp']['data'][$row][$col] & 0x0000FF00) >> 8;
						$blue  = ($BMPinfo['bmp']['data'][$row][$col] & 0x000000FF);
						$pixelcolor = ImageColorAllocate($im, $red, $green, $blue);
						ImageSetPixel($im, $col, $row, $pixelcolor);
					} else {
						//echo 'ERROR: no data for pixel '.$row.' x '.$col.'<BR>';
						//return false;
					}
				}
			}
			if (headers_sent()) {
				echo 'plotted '.($BMPinfo['resolution_x'] * $BMPinfo['resolution_y']).' pixels in '.(time() - $starttime).' seconds<BR>';
				ImageDestroy($im);
				exit;
			} else {
				header('Content-type: image/png');
				ImagePNG($im);
				ImageDestroy($im);
				return true;
			}
		}
		return false;
	}

	public function BMPcompressionWindowsLookup($compressionid) {
		static $BMPcompressionWindowsLookup = array(
			0 => 'BI_RGB',
			1 => 'BI_RLE8',
			2 => 'BI_RLE4',
			3 => 'BI_BITFIELDS',
			4 => 'BI_JPEG',
			5 => 'BI_PNG'
		);
		return (isset($BMPcompressionWindowsLookup[$compressionid]) ? $BMPcompressionWindowsLookup[$compressionid] : 'invalid');
	}

	public function BMPcompressionOS2Lookup($compressionid) {
		static $BMPcompressionOS2Lookup = array(
			0 => 'BI_RGB',
			1 => 'BI_RLE8',
			2 => 'BI_RLE4',
			3 => 'Huffman 1D',
			4 => 'BI_RLE24',
		);
		return (isset($BMPcompressionOS2Lookup[$compressionid]) ? $BMPcompressionOS2Lookup[$compressionid] : 'invalid');
	}

}
