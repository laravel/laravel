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
// module.misc.pdf.php                                         //
// module for analyzing PDF files                              //
// dependencies: NONE                                          //
//                                                            ///
/////////////////////////////////////////////////////////////////


class getid3_pdf extends getid3_handler
{

	public function Analyze() {
		$info = &$this->getid3->info;

		$info['fileformat'] = 'pdf';

		$info['error'][] = 'PDF parsing not enabled in this version of getID3() ['.$this->getid3->version().']';
		return false;

	}

}
