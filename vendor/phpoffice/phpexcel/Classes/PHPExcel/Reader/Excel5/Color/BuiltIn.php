<?php

class PHPExcel_Reader_Excel5_Color_BuiltIn
{
    protected static $map = array(
        0x00 => '000000',
        0x01 => 'FFFFFF',
        0x02 => 'FF0000',
        0x03 => '00FF00',
        0x04 => '0000FF',
        0x05 => 'FFFF00',
        0x06 => 'FF00FF',
        0x07 => '00FFFF',
        0x40 => '000000', // system window text color
        0x41 => 'FFFFFF', // system window background color
    );

    /**
     * Map built-in color to RGB value
     *
     * @param int $color Indexed color
     * @return array
     */
    public static function lookup($color)
    {
        if (isset(self::$map[$color])) {
            return array('rgb' => self::$map[$color]);
        }
        return array('rgb' => '000000');
    }
}