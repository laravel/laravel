<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
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
 * @package    PHPExcel_Writer_OpenDocument
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_OpenDocument_Cell_Comment
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_OpenDocument
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @author     Alexander Pervakov <frost-nzcr4@jagmort.com>
 */
class PHPExcel_Writer_OpenDocument_Cell_Comment
{
    public static function write(PHPExcel_Shared_XMLWriter $objWriter, PHPExcel_Cell $cell)
    {
        $comments = $cell->getWorksheet()->getComments();
        if (!isset($comments[$cell->getCoordinate()])) {
            return;
        }
        $comment = $comments[$cell->getCoordinate()];

        $objWriter->startElement('office:annotation');
            //$objWriter->writeAttribute('draw:style-name', 'gr1');
            //$objWriter->writeAttribute('draw:text-style-name', 'P1');
            $objWriter->writeAttribute('svg:width', $comment->getWidth());
            $objWriter->writeAttribute('svg:height', $comment->getHeight());
            $objWriter->writeAttribute('svg:x', $comment->getMarginLeft());
            $objWriter->writeAttribute('svg:y', $comment->getMarginTop());
            //$objWriter->writeAttribute('draw:caption-point-x', $comment->getMarginLeft());
            //$objWriter->writeAttribute('draw:caption-point-y', $comment->getMarginTop());
                $objWriter->writeElement('dc:creator', $comment->getAuthor());
                // TODO: Not realized in PHPExcel_Comment yet.
                //$objWriter->writeElement('dc:date', $comment->getDate());
                $objWriter->writeElement('text:p', $comment->getText()->getPlainText());
                    //$objWriter->writeAttribute('draw:text-style-name', 'P1');
        $objWriter->endElement();
    }
}
