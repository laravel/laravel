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
// module.misc.msoffice.php                                    //
// module for analyzing MS Office (.doc, .xls, etc) files      //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_msoffice extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$this->fseek($info['avdataoffset']);
		$DOCFILEheader = $this->fread(8);
		$magic = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";
		if (substr($DOCFILEheader, 0, 8) != $magic) {
			$info['error'][] = 'Expecting "'.getid3_lib::PrintHexBytes($magic).'" at '.$info['avdataoffset'].', found '.getid3_lib::PrintHexBytes(substr($DOCFILEheader, 0, 8)).' instead.';
			return false;
		}
		$info['fileformat'] = 'msoffice';

$info['error'][] = 'MS Office (.doc, .xls, etc) parsing not enabled in this version of getID3() ['.$this->getid3->version().']';
return false;

	}

}
