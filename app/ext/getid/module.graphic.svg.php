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
// module.graphic.svg.php                                      //
// module for analyzing SVG Image files                        //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_svg extends getid3_handler
{


	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);

		$SVGheader = $this->fread(4096);
		if (preg_match('#\<\?xml([^\>]+)\?\>#i', $SVGheader, $matches)) {
			$info['svg']['xml']['raw'] = $matches;
		}
		if (preg_match('#\<\!DOCTYPE([^\>]+)\>#i', $SVGheader, $matches)) {
			$info['svg']['doctype']['raw'] = $matches;
		}
		if (preg_match('#\<svg([^\>]+)\>#i', $SVGheader, $matches)) {
			$info['svg']['svg']['raw'] = $matches;
		}
		if (isset($info['svg']['svg']['raw'])) {

			$sections_to_fix = array('xml', 'doctype', 'svg');
			foreach ($sections_to_fix as $section_to_fix) {
				if (!isset($info['svg'][$section_to_fix])) {
					continue;
				}
				$section_data = array();
				while (preg_match('/ "([^"]+)"/', $info['svg'][$section_to_fix]['raw'][1], $matches)) {
					$section_data[] = $matches[1];
					$info['svg'][$section_to_fix]['raw'][1] = str_replace($matches[0], '', $info['svg'][$section_to_fix]['raw'][1]);
				}
				while (preg_match('/([^\s]+)="([^"]+)"/', $info['svg'][$section_to_fix]['raw'][1], $matches)) {
					$section_data[] = $matches[0];
					$info['svg'][$section_to_fix]['raw'][1] = str_replace($matches[0], '', $info['svg'][$section_to_fix]['raw'][1]);
				}
				$section_data = array_merge($section_data, preg_split('/[\s,]+/', $info['svg'][$section_to_fix]['raw'][1]));
				foreach ($section_data as $keyvaluepair) {
					$keyvaluepair = trim($keyvaluepair);
					if ($keyvaluepair) {
						$keyvalueexploded = explode('=', $keyvaluepair);
						$key   = (isset($keyvalueexploded[0]) ? $keyvalueexploded[0] : '');
						$value = (isset($keyvalueexploded[1]) ? $keyvalueexploded[1] : '');
						$info['svg'][$section_to_fix]['sections'][$key] = trim($value, '"');
					}
				}
			}

			$info['fileformat']                  = 'svg';
			$info['video']['dataformat']         = 'svg';
			$info['video']['lossless']           = true;
			//$info['video']['bits_per_sample']    = 24;
			$info['video']['pixel_aspect_ratio'] = (float) 1;

			if (!empty($info['svg']['svg']['sections']['width'])) {
				$info['svg']['width']  = intval($info['svg']['svg']['sections']['width']);
			}
			if (!empty($info['svg']['svg']['sections']['height'])) {
				$info['svg']['height'] = intval($info['svg']['svg']['sections']['height']);
			}
			if (!empty($info['svg']['svg']['sections']['version'])) {
				$info['svg']['version'] = $info['svg']['svg']['sections']['version'];
			}
			if (!isset($info['svg']['version']) && isset($info['svg']['doctype']['sections'])) {
				foreach ($info['svg']['doctype']['sections'] as $key => $value) {
					if (preg_match('#//W3C//DTD SVG ([0-9\.]+)//#i', $key, $matches)) {
						$info['svg']['version'] = $matches[1];
						break;
					}
				}
			}

			if (!empty($info['svg']['width'])) {
				$info['video']['resolution_x'] = $info['svg']['width'];
			}
			if (!empty($info['svg']['height'])) {
				$info['video']['resolution_y'] = $info['svg']['height'];
			}

			return true;
		}
		$info['error'][] = 'Did not find expected <svg> tag';
		return false;
	}

}
