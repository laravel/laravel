<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Writer_Excel2007_Comments
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Comments extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Write comments to XML format
	 *
	 * @param 	PHPExcel_Worksheet				$pWorksheet
	 * @return 	string 								XML Output
	 * @throws 	PHPExcel_Writer_Exception
	 */
	public function writeComments(PHPExcel_Worksheet $pWorksheet = null)
	{
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');

  		// Comments cache
  		$comments	= $pWorksheet->getComments();

  		// Authors cache
  		$authors	= array();
  		$authorId	= 0;
		foreach ($comments as $comment) {
			if (!isset($authors[$comment->getAuthor()])) {
				$authors[$comment->getAuthor()] = $authorId++;
			}
		}

		// comments
		$objWriter->startElement('comments');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

			// Loop through authors
			$objWriter->startElement('authors');
			foreach ($authors as $author => $index) {
				$objWriter->writeElement('author', $author);
			}
			$objWriter->endElement();

			// Loop through comments
			$objWriter->startElement('commentList');
			foreach ($comments as $key => $value) {
				$this->_writeComment($objWriter, $key, $value, $authors);
			}
			$objWriter->endElement();

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write comment to XML format
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 			XML Writer
	 * @param	string							$pCellReference		Cell reference
	 * @param 	PHPExcel_Comment				$pComment			Comment
	 * @param	array							$pAuthors			Array of authors
	 * @throws 	PHPExcel_Writer_Exception
	 */
	public function _writeComment(PHPExcel_Shared_XMLWriter $objWriter = null, $pCellReference = 'A1', PHPExcel_Comment $pComment = null, $pAuthors = null)
	{
		// comment
		$objWriter->startElement('comment');
		$objWriter->writeAttribute('ref', 		$pCellReference);
		$objWriter->writeAttribute('authorId', 	$pAuthors[$pComment->getAuthor()]);

			// text
			$objWriter->startElement('text');
			$this->getParentWriter()->getWriterPart('stringtable')->writeRichText($objWriter, $pComment->getText());
			$objWriter->endElement();

		$objWriter->endElement();
	}

	/**
	 * Write VML comments to XML format
	 *
	 * @param 	PHPExcel_Worksheet				$pWorksheet
	 * @return 	string 								XML Output
	 * @throws 	PHPExcel_Writer_Exception
	 */
	public function writeVMLComments(PHPExcel_Worksheet $pWorksheet = null)
	{
		// Create XML writer
		$objWriter = null;
		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		} else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		// XML header
		$objWriter->startDocument('1.0','UTF-8','yes');

  		// Comments cache
  		$comments	= $pWorksheet->getComments();

		// xml
		$objWriter->startElement('xml');
		$objWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
		$objWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
		$objWriter->writeAttribute('xmlns:x', 'urn:schemas-microsoft-com:office:excel');

			// o:shapelayout
			$objWriter->startElement('o:shapelayout');
			$objWriter->writeAttribute('v:ext', 		'edit');

				// o:idmap
				$objWriter->startElement('o:idmap');
				$objWriter->writeAttribute('v:ext', 	'edit');
				$objWriter->writeAttribute('data', 		'1');
				$objWriter->endElement();

			$objWriter->endElement();

			// v:shapetype
			$objWriter->startElement('v:shapetype');
			$objWriter->writeAttribute('id', 		'_x0000_t202');
			$objWriter->writeAttribute('coordsize', '21600,21600');
			$objWriter->writeAttribute('o:spt', 	'202');
			$objWriter->writeAttribute('path', 		'm,l,21600r21600,l21600,xe');

				// v:stroke
				$objWriter->startElement('v:stroke');
				$objWriter->writeAttribute('joinstyle', 	'miter');
				$objWriter->endElement();

				// v:path
				$objWriter->startElement('v:path');
				$objWriter->writeAttribute('gradientshapeok', 	't');
				$objWriter->writeAttribute('o:connecttype', 	'rect');
				$objWriter->endElement();

			$objWriter->endElement();

			// Loop through comments
			foreach ($comments as $key => $value) {
				$this->_writeVMLComment($objWriter, $key, $value);
			}

		$objWriter->endElement();

		// Return
		return $objWriter->getData();
	}

	/**
	 * Write VML comment to XML format
	 *
	 * @param 	PHPExcel_Shared_XMLWriter		$objWriter 			XML Writer
	 * @param	string							$pCellReference		Cell reference
	 * @param 	PHPExcel_Comment				$pComment			Comment
	 * @throws 	PHPExcel_Writer_Exception
	 */
	public function _writeVMLComment(PHPExcel_Shared_XMLWriter $objWriter = null, $pCellReference = 'A1', PHPExcel_Comment $pComment = null)
	{
 		// Metadata
 		list($column, $row) = PHPExcel_Cell::coordinateFromString($pCellReference);
 		$column = PHPExcel_Cell::columnIndexFromString($column);
 		$id = 1024 + $column + $row;
 		$id = substr($id, 0, 4);

		// v:shape
		$objWriter->startElement('v:shape');
		$objWriter->writeAttribute('id', 			'_x0000_s' . $id);
		$objWriter->writeAttribute('type', 			'#_x0000_t202');
		$objWriter->writeAttribute('style', 		'position:absolute;margin-left:' . $pComment->getMarginLeft() . ';margin-top:' . $pComment->getMarginTop() . ';width:' . $pComment->getWidth() . ';height:' . $pComment->getHeight() . ';z-index:1;visibility:' . ($pComment->getVisible() ? 'visible' : 'hidden'));
		$objWriter->writeAttribute('fillcolor', 	'#' . $pComment->getFillColor()->getRGB());
		$objWriter->writeAttribute('o:insetmode', 	'auto');

			// v:fill
			$objWriter->startElement('v:fill');
			$objWriter->writeAttribute('color2', 		'#' . $pComment->getFillColor()->getRGB());
			$objWriter->endElement();

			// v:shadow
			$objWriter->startElement('v:shadow');
			$objWriter->writeAttribute('on', 			't');
			$objWriter->writeAttribute('color', 		'black');
			$objWriter->writeAttribute('obscured', 		't');
			$objWriter->endElement();

			// v:path
			$objWriter->startElement('v:path');
			$objWriter->writeAttribute('o:connecttype', 'none');
			$objWriter->endElement();

			// v:textbox
			$objWriter->startElement('v:textbox');
			$objWriter->writeAttribute('style', 'mso-direction-alt:auto');

				// div
				$objWriter->startElement('div');
				$objWriter->writeAttribute('style', 'text-align:left');
				$objWriter->endElement();

			$objWriter->endElement();

			// x:ClientData
			$objWriter->startElement('x:ClientData');
			$objWriter->writeAttribute('ObjectType', 'Note');

				// x:MoveWithCells
				$objWriter->writeElement('x:MoveWithCells', '');

				// x:SizeWithCells
				$objWriter->writeElement('x:SizeWithCells', '');

				// x:Anchor
				//$objWriter->writeElement('x:Anchor', $column . ', 15, ' . ($row - 2) . ', 10, ' . ($column + 4) . ', 15, ' . ($row + 5) . ', 18');

				// x:AutoFill
				$objWriter->writeElement('x:AutoFill', 'False');

				// x:Row
				$objWriter->writeElement('x:Row', ($row - 1));

				// x:Column
				$objWriter->writeElement('x:Column', ($column - 1));

			$objWriter->endElement();

		$objWriter->endElement();
	}
}
