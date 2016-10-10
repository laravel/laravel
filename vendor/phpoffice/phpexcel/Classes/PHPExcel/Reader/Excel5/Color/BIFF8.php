<?php

class PHPExcel_Reader_Excel5_Color_BIFF8
{
    protected static $map = array(
        0x08 => '000000',
        0x09 => 'FFFFFF',
        0x0A => 'FF0000',
        0x0B => '00FF00',
        0x0C => '0000FF',
        0x0D => 'FFFF00',
        0x0E => 'FF00FF',
        0x0F => '00FFFF',
        0x10 => '800000',
        0x11 => '008000',
        0x12 => '000080',
        0x13 => '808000',
        0x14 => '800080',
        0x15 => '008080',
        0x16 => 'C0C0C0',
        0x17 => '808080',
        0x18 => '9999FF',
        0x19 => '993366',
        0x1A => 'FFFFCC',
        0x1B => 'CCFFFF',
        0x1C => '660066',
        0x1D => 'FF8080',
        0x1E => '0066CC',
        0x1F => 'CCCCFF',
        0x20 => '000080',
        0x21 => 'FF00FF',
        0x22 => 'FFFF00',
        0x23 => '00FFFF',
        0x24 => '800080',
        0x25 => '800000',
        0x26 => '008080',
        0x27 => '0000FF',
        0x28 => '00CCFF',
        0x29 => 'CCFFFF',
        0x2A => 'CCFFCC',
        0x2B => 'FFFF99',
        0x2C => '99CCFF',
        0x2D => 'FF99CC',
        0x2E => 'CC99FF',
        0x2F => 'FFCC99',
        0x30 => '3366FF',
        0x31 => '33CCCC',
        0x32 => '99CC00',
        0x33 => 'FFCC00',
        0x34 => 'FF9900',
        0x35 => 'FF6600',
        0x36 => '666699',
        0x37 => '969696',
        0x38 => '003366',
        0x39 => '339966',
        0x3A => '003300',
        0x3B => '333300',
        0x3C => '993300',
        0x3D => '993366',
        0x3E => '333399',
        0x3F => '333333',
    );

   /**
     * Map color array from BIFF8 built-in color index
     *
     * @param int $color
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