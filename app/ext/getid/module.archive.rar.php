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
// module.archive.rar.php                                      //
// module for analyzing RAR files                              //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_rar extends getid3_handler
{

	public $option_use_rar_extension = false;

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'rar';

		if ($this->option_use_rar_extension === true) {
			if (function_exists('rar_open')) {
				if ($rp = rar_open($info['filenamepath'])) {
					$info['rar']['files'] = array();
					$entries = rar_list($rp);
					foreach ($entries as $entry) {
						$info['rar']['files'] = getid3_lib::array_merge_clobber($info['rar']['files'], getid3_lib::CreateDeepArray($entry->getName(), '/', $entry->getUnpackedSize()));
					}
					rar_close($rp);
					return true;
				} else {
					$info['error'][] = 'failed to rar_open('.$info['filename'].')';
				}
			} else {
				$info['error'][] = 'RAR support does not appear to be available in this PHP installation';
			}
		} else {
			$info['error'][] = 'PHP-RAR processing has been disabled (set $getid3_rar->option_use_rar_extension=true to enable)';
		}
		return false;

	}

}
