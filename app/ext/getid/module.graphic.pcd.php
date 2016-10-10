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
// module.graphic.pcd.php                                      //
// module for analyzing PhotoCD (PCD) Image files              //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_pcd extends getid3_handler
{
	public $ExtractData = 0;

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat']          = 'pcd';
		$info['video']['dataformat'] = 'pcd';
		$info['video']['lossless']   = false;


		$this->fseek($info['avdataoffset'] + 72);

		$PCDflags = $this->fread(1);
		$PCDisVertical = ((ord($PCDflags) & 0x01) ? true : false);


		if ($PCDisVertical) {
			$info['video']['resolution_x'] = 3072;
			$info['video']['resolution_y'] = 2048;
		} else {
			$info['video']['resolution_x'] = 2048;
			$info['video']['resolution_y'] = 3072;
		}


		if ($this->ExtractData > 3) {

			$info['error'][] = 'Cannot extract PSD image data for detail levels above BASE (level-3) because encrypted with Kodak-proprietary compression/encryption.';

		} elseif ($this->ExtractData > 0) {

			$PCD_levels[1] = array( 192,  128, 0x02000); // BASE/16
			$PCD_levels[2] = array( 384,  256, 0x0B800); // BASE/4
			$PCD_levels[3] = array( 768,  512, 0x30000); // BASE
			//$PCD_levels[4] = array(1536, 1024,    ??); // BASE*4  - encrypted with Kodak-proprietary compression/encryption
			//$PCD_levels[5] = array(3072, 2048,    ??); // BASE*16 - encrypted with Kodak-proprietary compression/encryption
			//$PCD_levels[6] = array(6144, 4096,    ??); // BASE*64 - encrypted with Kodak-proprietary compression/encryption; PhotoCD-Pro only

			list($PCD_width, $PCD_height, $PCD_dataOffset) = $PCD_levels[3];

			$this->fseek($info['avdataoffset'] + $PCD_dataOffset);

			for ($y = 0; $y < $PCD_height; $y += 2) {
				// The image-data of these subtypes start at the respective offsets of 02000h, 0b800h and 30000h.
				// To decode the YcbYr to the more usual RGB-code, three lines of data have to be read, each
				// consisting of w bytes, where w is the width of the image-subtype. The first w bytes and
				// the first half of the third w bytes contain data for the first RGB-line, the second w bytes
				// and the second half of the third w bytes contain data for a second RGB-line.

				$PCD_data_Y1 = $this->fread($PCD_width);
				$PCD_data_Y2 = $this->fread($PCD_width);
				$PCD_data_Cb = $this->fread(intval(round($PCD_width / 2)));
				$PCD_data_Cr = $this->fread(intval(round($PCD_width / 2)));

				for ($x = 0; $x < $PCD_width; $x++) {
					if ($PCDisVertical) {
						$info['pcd']['data'][$PCD_width - $x][$y]     = $this->YCbCr2RGB(ord($PCD_data_Y1{$x}), ord($PCD_data_Cb{floor($x / 2)}), ord($PCD_data_Cr{floor($x / 2)}));
						$info['pcd']['data'][$PCD_width - $x][$y + 1] = $this->YCbCr2RGB(ord($PCD_data_Y2{$x}), ord($PCD_data_Cb{floor($x / 2)}), ord($PCD_data_Cr{floor($x / 2)}));
					} else {
						$info['pcd']['data'][$y][$x]                  = $this->YCbCr2RGB(ord($PCD_data_Y1{$x}), ord($PCD_data_Cb{floor($x / 2)}), ord($PCD_data_Cr{floor($x / 2)}));
						$info['pcd']['data'][$y + 1][$x]              = $this->YCbCr2RGB(ord($PCD_data_Y2{$x}), ord($PCD_data_Cb{floor($x / 2)}), ord($PCD_data_Cr{floor($x / 2)}));
					}
				}
			}

			// Example for plotting extracted data
			//getid3_lib::IncludeDependency(GETID3_INCLUDEPATH.'module.audio.ac3.php', __FILE__, true);
			//if ($PCDisVertical) {
			//	$BMPinfo['resolution_x'] = $PCD_height;
			//	$BMPinfo['resolution_y'] = $PCD_width;
			//} else {
			//	$BMPinfo['resolution_x'] = $PCD_width;
			//	$BMPinfo['resolution_y'] = $PCD_height;
			//}
			//$BMPinfo['bmp']['data'] = $info['pcd']['data'];
			//getid3_bmp::PlotBMP($BMPinfo);
			//exit;

		}

	}

	public function YCbCr2RGB($Y, $Cb, $Cr) {
		static $YCbCr_constants = array();
		if (empty($YCbCr_constants)) {
			$YCbCr_constants['red']['Y']    =  0.0054980 * 256;
			$YCbCr_constants['red']['Cb']   =  0.0000000 * 256;
			$YCbCr_constants['red']['Cr']   =  0.0051681 * 256;
			$YCbCr_constants['green']['Y']  =  0.0054980 * 256;
			$YCbCr_constants['green']['Cb'] = -0.0015446 * 256;
			$YCbCr_constants['green']['Cr'] = -0.0026325 * 256;
			$YCbCr_constants['blue']['Y']   =  0.0054980 * 256;
			$YCbCr_constants['blue']['Cb']  =  0.0079533 * 256;
			$YCbCr_constants['blue']['Cr']  =  0.0000000 * 256;
		}

		$RGBcolor = array('red'=>0, 'green'=>0, 'blue'=>0);
		foreach ($RGBcolor as $rgbname => $dummy) {
			$RGBcolor[$rgbname] = max(0,
										min(255,
											intval(
												round(
													($YCbCr_constants[$rgbname]['Y'] * $Y) +
													($YCbCr_constants[$rgbname]['Cb'] * ($Cb - 156)) +
													($YCbCr_constants[$rgbname]['Cr'] * ($Cr - 137))
												)
											)
										)
									);
		}
		return (($RGBcolor['red'] * 65536) + ($RGBcolor['green'] * 256) + $RGBcolor['blue']);
	}

}
