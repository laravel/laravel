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
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Shared_Drawing
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Drawing
{
    /**
     * Convert pixels to EMU
     *
     * @param     int $pValue    Value in pixels
     * @return     int            Value in EMU
     */
    public static function pixelsToEMU($pValue = 0)
    {
        return round($pValue * 9525);
    }

    /**
     * Convert EMU to pixels
     *
     * @param     int $pValue    Value in EMU
     * @return     int            Value in pixels
     */
    public static function EMUToPixels($pValue = 0)
    {
        if ($pValue != 0) {
            return round($pValue / 9525);
        } else {
            return 0;
        }
    }

    /**
     * Convert pixels to column width. Exact algorithm not known.
     * By inspection of a real Excel file using Calibri 11, one finds 1000px ~ 142.85546875
     * This gives a conversion factor of 7. Also, we assume that pixels and font size are proportional.
     *
     * @param     int $pValue    Value in pixels
     * @param     PHPExcel_Style_Font $pDefaultFont    Default font of the workbook
     * @return     int            Value in cell dimension
     */
    public static function pixelsToCellDimension($pValue = 0, PHPExcel_Style_Font $pDefaultFont)
    {
        // Font name and size
        $name = $pDefaultFont->getName();
        $size = $pDefaultFont->getSize();

        if (isset(PHPExcel_Shared_Font::$defaultColumnWidths[$name][$size])) {
            // Exact width can be determined
            $colWidth = $pValue * PHPExcel_Shared_Font::$defaultColumnWidths[$name][$size]['width'] / PHPExcel_Shared_Font::$defaultColumnWidths[$name][$size]['px'];
        } else {
            // We don't have data for this particular font and size, use approximation by
            // extrapolating from Calibri 11
            $colWidth = $pValue * 11 * PHPExcel_Shared_Font::$defaultColumnWidths['Calibri'][11]['width'] / PHPExcel_Shared_Font::$defaultColumnWidths['Calibri'][11]['px'] / $size;
        }

        return $colWidth;
    }

    /**
     * Convert column width from (intrinsic) Excel units to pixels
     *
     * @param     float    $pValue        Value in cell dimension
     * @param     PHPExcel_Style_Font $pDefaultFont    Default font of the workbook
     * @return     int        Value in pixels
     */
    public static function cellDimensionToPixels($pValue = 0, PHPExcel_Style_Font $pDefaultFont)
    {
        // Font name and size
        $name = $pDefaultFont->getName();
        $size = $pDefaultFont->getSize();

        if (isset(PHPExcel_Shared_Font::$defaultColumnWidths[$name][$size])) {
            // Exact width can be determined
            $colWidth = $pValue * PHPExcel_Shared_Font::$defaultColumnWidths[$name][$size]['px'] / PHPExcel_Shared_Font::$defaultColumnWidths[$name][$size]['width'];
        } else {
            // We don't have data for this particular font and size, use approximation by
            // extrapolating from Calibri 11
            $colWidth = $pValue * $size * PHPExcel_Shared_Font::$defaultColumnWidths['Calibri'][11]['px'] / PHPExcel_Shared_Font::$defaultColumnWidths['Calibri'][11]['width'] / 11;
        }

        // Round pixels to closest integer
        $colWidth = (int) round($colWidth);

        return $colWidth;
    }

    /**
     * Convert pixels to points
     *
     * @param     int $pValue    Value in pixels
     * @return     int            Value in points
     */
    public static function pixelsToPoints($pValue = 0)
    {
        return $pValue * 0.67777777;
    }

    /**
     * Convert points to pixels
     *
     * @param     int $pValue    Value in points
     * @return     int            Value in pixels
     */
    public static function pointsToPixels($pValue = 0)
    {
        if ($pValue != 0) {
            return (int) ceil($pValue * 1.333333333);
        } else {
            return 0;
        }
    }

    /**
     * Convert degrees to angle
     *
     * @param     int $pValue    Degrees
     * @return     int            Angle
     */
    public static function degreesToAngle($pValue = 0)
    {
        return (int)round($pValue * 60000);
    }

    /**
     * Convert angle to degrees
     *
     * @param     int $pValue    Angle
     * @return     int            Degrees
     */
    public static function angleToDegrees($pValue = 0)
    {
        if ($pValue != 0) {
            return round($pValue / 60000);
        } else {
            return 0;
        }
    }

    /**
     * Create a new image from file. By alexander at alexauto dot nl
     *
     * @link http://www.php.net/manual/en/function.imagecreatefromwbmp.php#86214
     * @param string $filename Path to Windows DIB (BMP) image
     * @return resource
     */
    public static function imagecreatefrombmp($p_sFile)
    {
        //    Load the image into a string
        $file = fopen($p_sFile, "rb");
        $read = fread($file, 10);
        while (!feof($file) && ($read<>"")) {
            $read .= fread($file, 1024);
        }

        $temp = unpack("H*", $read);
        $hex = $temp[1];
        $header = substr($hex, 0, 108);

        //    Process the header
        //    Structure: http://www.fastgraph.com/help/bmp_header_format.html
        if (substr($header, 0, 4)=="424d") {
            //    Cut it in parts of 2 bytes
            $header_parts = str_split($header, 2);

            //    Get the width        4 bytes
            $width = hexdec($header_parts[19].$header_parts[18]);

            //    Get the height        4 bytes
            $height = hexdec($header_parts[23].$header_parts[22]);

            //    Unset the header params
            unset($header_parts);
        }

        //    Define starting X and Y
        $x = 0;
        $y = 1;

        //    Create newimage
        $image = imagecreatetruecolor($width, $height);

        //    Grab the body from the image
        $body = substr($hex, 108);

        //    Calculate if padding at the end-line is needed
        //    Divided by two to keep overview.
        //    1 byte = 2 HEX-chars
        $body_size = (strlen($body)/2);
        $header_size = ($width*$height);

        //    Use end-line padding? Only when needed
        $usePadding = ($body_size>($header_size*3)+4);

        //    Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
        //    Calculate the next DWORD-position in the body
        for ($i = 0; $i < $body_size; $i += 3) {
            //    Calculate line-ending and padding
            if ($x >= $width) {
                // If padding needed, ignore image-padding
                // Shift i to the ending of the current 32-bit-block
                if ($usePadding) {
                    $i += $width%4;
                }

                //    Reset horizontal position
                $x = 0;

                //    Raise the height-position (bottom-up)
                $y++;

                //    Reached the image-height? Break the for-loop
                if ($y > $height) {
                    break;
                }
            }

            // Calculation of the RGB-pixel (defined as BGR in image-data)
            // Define $i_pos as absolute position in the body
            $i_pos    = $i * 2;
            $r        = hexdec($body[$i_pos+4].$body[$i_pos+5]);
            $g        = hexdec($body[$i_pos+2].$body[$i_pos+3]);
            $b        = hexdec($body[$i_pos].$body[$i_pos+1]);

            // Calculate and draw the pixel
            $color    = imagecolorallocate($image, $r, $g, $b);
            imagesetpixel($image, $x, $height-$y, $color);

            // Raise the horizontal position
            $x++;
        }

        // Unset the body / free the memory
        unset($body);

        //    Return image-object
        return $image;
    }
}
