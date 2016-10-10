<?php

/**
 * PHPExcel_Shared_Excel5
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
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Shared_Excel5
{
    /**
     * Get the width of a column in pixels. We use the relationship y = ceil(7x) where
     * x is the width in intrinsic Excel units (measuring width in number of normal characters)
     * This holds for Arial 10
     *
     * @param PHPExcel_Worksheet $sheet The sheet
     * @param string $col The column
     * @return integer The width in pixels
    */
    public static function sizeCol($sheet, $col = 'A')
    {
        // default font of the workbook
        $font = $sheet->getParent()->getDefaultStyle()->getFont();

        $columnDimensions = $sheet->getColumnDimensions();

        // first find the true column width in pixels (uncollapsed and unhidden)
        if (isset($columnDimensions[$col]) and $columnDimensions[$col]->getWidth() != -1) {
            // then we have column dimension with explicit width
            $columnDimension = $columnDimensions[$col];
            $width = $columnDimension->getWidth();
            $pixelWidth = PHPExcel_Shared_Drawing::cellDimensionToPixels($width, $font);
        } elseif ($sheet->getDefaultColumnDimension()->getWidth() != -1) {
            // then we have default column dimension with explicit width
            $defaultColumnDimension = $sheet->getDefaultColumnDimension();
            $width = $defaultColumnDimension->getWidth();
            $pixelWidth = PHPExcel_Shared_Drawing::cellDimensionToPixels($width, $font);
        } else {
            // we don't even have any default column dimension. Width depends on default font
            $pixelWidth = PHPExcel_Shared_Font::getDefaultColumnWidthByFont($font, true);
        }

        // now find the effective column width in pixels
        if (isset($columnDimensions[$col]) and !$columnDimensions[$col]->getVisible()) {
            $effectivePixelWidth = 0;
        } else {
            $effectivePixelWidth = $pixelWidth;
        }

        return $effectivePixelWidth;
    }

    /**
     * Convert the height of a cell from user's units to pixels. By interpolation
     * the relationship is: y = 4/3x. If the height hasn't been set by the user we
     * use the default value. If the row is hidden we use a value of zero.
     *
     * @param PHPExcel_Worksheet $sheet The sheet
     * @param integer $row The row index (1-based)
     * @return integer The width in pixels
     */
    public static function sizeRow($sheet, $row = 1)
    {
        // default font of the workbook
        $font = $sheet->getParent()->getDefaultStyle()->getFont();

        $rowDimensions = $sheet->getRowDimensions();

        // first find the true row height in pixels (uncollapsed and unhidden)
        if (isset($rowDimensions[$row]) and $rowDimensions[$row]->getRowHeight() != -1) {
            // then we have a row dimension
            $rowDimension = $rowDimensions[$row];
            $rowHeight = $rowDimension->getRowHeight();
            $pixelRowHeight = (int) ceil(4 * $rowHeight / 3); // here we assume Arial 10
        } elseif ($sheet->getDefaultRowDimension()->getRowHeight() != -1) {
            // then we have a default row dimension with explicit height
            $defaultRowDimension = $sheet->getDefaultRowDimension();
            $rowHeight = $defaultRowDimension->getRowHeight();
            $pixelRowHeight = PHPExcel_Shared_Drawing::pointsToPixels($rowHeight);
        } else {
            // we don't even have any default row dimension. Height depends on default font
            $pointRowHeight = PHPExcel_Shared_Font::getDefaultRowHeightByFont($font);
            $pixelRowHeight = PHPExcel_Shared_Font::fontSizeToPixels($pointRowHeight);
        }

        // now find the effective row height in pixels
        if (isset($rowDimensions[$row]) and !$rowDimensions[$row]->getVisible()) {
            $effectivePixelRowHeight = 0;
        } else {
            $effectivePixelRowHeight = $pixelRowHeight;
        }

        return $effectivePixelRowHeight;
    }

    /**
     * Get the horizontal distance in pixels between two anchors
     * The distanceX is found as sum of all the spanning columns widths minus correction for the two offsets
     *
     * @param PHPExcel_Worksheet $sheet
     * @param string $startColumn
     * @param integer $startOffsetX Offset within start cell measured in 1/1024 of the cell width
     * @param string $endColumn
     * @param integer $endOffsetX Offset within end cell measured in 1/1024 of the cell width
     * @return integer Horizontal measured in pixels
     */
    public static function getDistanceX(PHPExcel_Worksheet $sheet, $startColumn = 'A', $startOffsetX = 0, $endColumn = 'A', $endOffsetX = 0)
    {
        $distanceX = 0;

        // add the widths of the spanning columns
        $startColumnIndex = PHPExcel_Cell::columnIndexFromString($startColumn) - 1; // 1-based
        $endColumnIndex = PHPExcel_Cell::columnIndexFromString($endColumn) - 1; // 1-based
        for ($i = $startColumnIndex; $i <= $endColumnIndex; ++$i) {
            $distanceX += self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($i));
        }

        // correct for offsetX in startcell
        $distanceX -= (int) floor(self::sizeCol($sheet, $startColumn) * $startOffsetX / 1024);

        // correct for offsetX in endcell
        $distanceX -= (int) floor(self::sizeCol($sheet, $endColumn) * (1 - $endOffsetX / 1024));

        return $distanceX;
    }

    /**
     * Get the vertical distance in pixels between two anchors
     * The distanceY is found as sum of all the spanning rows minus two offsets
     *
     * @param PHPExcel_Worksheet $sheet
     * @param integer $startRow (1-based)
     * @param integer $startOffsetY Offset within start cell measured in 1/256 of the cell height
     * @param integer $endRow (1-based)
     * @param integer $endOffsetY Offset within end cell measured in 1/256 of the cell height
     * @return integer Vertical distance measured in pixels
     */
    public static function getDistanceY(PHPExcel_Worksheet $sheet, $startRow = 1, $startOffsetY = 0, $endRow = 1, $endOffsetY = 0)
    {
        $distanceY = 0;

        // add the widths of the spanning rows
        for ($row = $startRow; $row <= $endRow; ++$row) {
            $distanceY += self::sizeRow($sheet, $row);
        }

        // correct for offsetX in startcell
        $distanceY -= (int) floor(self::sizeRow($sheet, $startRow) * $startOffsetY / 256);

        // correct for offsetX in endcell
        $distanceY -= (int) floor(self::sizeRow($sheet, $endRow) * (1 - $endOffsetY / 256));

        return $distanceY;
    }

    /**
     * Convert 1-cell anchor coordinates to 2-cell anchor coordinates
     * This function is ported from PEAR Spreadsheet_Writer_Excel with small modifications
     *
     * Calculate the vertices that define the position of the image as required by
     * the OBJ record.
     *
     *         +------------+------------+
     *         |     A      |      B     |
     *   +-----+------------+------------+
     *   |     |(x1,y1)     |            |
     *   |  1  |(A1)._______|______      |
     *   |     |    |              |     |
     *   |     |    |              |     |
     *   +-----+----|    BITMAP    |-----+
     *   |     |    |              |     |
     *   |  2  |    |______________.     |
     *   |     |            |        (B2)|
     *   |     |            |     (x2,y2)|
     *   +---- +------------+------------+
     *
     * Example of a bitmap that covers some of the area from cell A1 to cell B2.
     *
     * Based on the width and height of the bitmap we need to calculate 8 vars:
     *     $col_start, $row_start, $col_end, $row_end, $x1, $y1, $x2, $y2.
     * The width and height of the cells are also variable and have to be taken into
     * account.
     * The values of $col_start and $row_start are passed in from the calling
     * function. The values of $col_end and $row_end are calculated by subtracting
     * the width and height of the bitmap from the width and height of the
     * underlying cells.
     * The vertices are expressed as a percentage of the underlying cell width as
     * follows (rhs values are in pixels):
     *
     *       x1 = X / W *1024
     *       y1 = Y / H *256
     *       x2 = (X-1) / W *1024
     *       y2 = (Y-1) / H *256
     *
     *       Where:  X is distance from the left side of the underlying cell
     *               Y is distance from the top of the underlying cell
     *               W is the width of the cell
     *               H is the height of the cell
     *
     * @param PHPExcel_Worksheet $sheet
     * @param string $coordinates E.g. 'A1'
     * @param integer $offsetX Horizontal offset in pixels
     * @param integer $offsetY Vertical offset in pixels
     * @param integer $width Width in pixels
     * @param integer $height Height in pixels
     * @return array
     */
    public static function oneAnchor2twoAnchor($sheet, $coordinates, $offsetX, $offsetY, $width, $height)
    {
        list($column, $row) = PHPExcel_Cell::coordinateFromString($coordinates);
        $col_start = PHPExcel_Cell::columnIndexFromString($column) - 1;
        $row_start = $row - 1;

        $x1 = $offsetX;
        $y1 = $offsetY;

        // Initialise end cell to the same as the start cell
        $col_end    = $col_start;  // Col containing lower right corner of object
        $row_end    = $row_start;  // Row containing bottom right corner of object

        // Zero the specified offset if greater than the cell dimensions
        if ($x1 >= self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_start))) {
            $x1 = 0;
        }
        if ($y1 >= self::sizeRow($sheet, $row_start + 1)) {
            $y1 = 0;
        }

        $width      = $width  + $x1 -1;
        $height     = $height + $y1 -1;

        // Subtract the underlying cell widths to find the end cell of the image
        while ($width >= self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end))) {
            $width -= self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end));
            ++$col_end;
        }

        // Subtract the underlying cell heights to find the end cell of the image
        while ($height >= self::sizeRow($sheet, $row_end + 1)) {
            $height -= self::sizeRow($sheet, $row_end + 1);
            ++$row_end;
        }

        // Bitmap isn't allowed to start or finish in a hidden cell, i.e. a cell
        // with zero height or width.
        if (self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_start)) == 0) {
            return;
        }
        if (self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end))   == 0) {
            return;
        }
        if (self::sizeRow($sheet, $row_start + 1) == 0) {
            return;
        }
        if (self::sizeRow($sheet, $row_end + 1)   == 0) {
            return;
        }

        // Convert the pixel values to the percentage value expected by Excel
        $x1 = $x1     / self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_start))   * 1024;
        $y1 = $y1     / self::sizeRow($sheet, $row_start + 1)   *  256;
        $x2 = ($width + 1)  / self::sizeCol($sheet, PHPExcel_Cell::stringFromColumnIndex($col_end))     * 1024; // Distance to right side of object
        $y2 = ($height + 1) / self::sizeRow($sheet, $row_end + 1)     *  256; // Distance to bottom of object

        $startCoordinates = PHPExcel_Cell::stringFromColumnIndex($col_start) . ($row_start + 1);
        $endCoordinates = PHPExcel_Cell::stringFromColumnIndex($col_end) . ($row_end + 1);

        $twoAnchor = array(
            'startCoordinates' => $startCoordinates,
            'startOffsetX' => $x1,
            'startOffsetY' => $y1,
            'endCoordinates' => $endCoordinates,
            'endOffsetX' => $x2,
            'endOffsetY' => $y2,
        );

        return  $twoAnchor;
    }
}
