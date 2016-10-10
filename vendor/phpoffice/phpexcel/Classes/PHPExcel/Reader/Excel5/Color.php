<?php

class PHPExcel_Reader_Excel5_Color
{
    /**
     * Read color
     *
     * @param int $color Indexed color
     * @param array $palette Color palette
     * @return array RGB color value, example: array('rgb' => 'FF0000')
     */
    public static function map($color, $palette, $version)
    {
        if ($color <= 0x07 || $color >= 0x40) {
            // special built-in color
            return PHPExcel_Reader_Excel5_Color_BuiltIn::lookup($color);
        } elseif (isset($palette) && isset($palette[$color - 8])) {
            // palette color, color index 0x08 maps to pallete index 0
            return $palette[$color - 8];
        } else {
            // default color table
            if ($version == PHPExcel_Reader_Excel5::XLS_BIFF8) {
                return PHPExcel_Reader_Excel5_Color_BIFF8::lookup($color);
            } else {
                // BIFF5
                return PHPExcel_Reader_Excel5_Color_BIFF5::lookup($color);
            }
        }

        return $color;
    }
}