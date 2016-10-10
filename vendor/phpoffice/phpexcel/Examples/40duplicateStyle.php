<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();
$worksheet = $objPHPExcel->getActiveSheet();

echo date('H:i:s') , " Create styles array" , EOL;
$styles = array();
for ($i = 0; $i < 10; $i++) {
    $style = new PHPExcel_Style();
    $style->getFont()->setSize($i + 4);
    $styles[] = $style;
}

echo date('H:i:s') , " Add data (begin)" , EOL;
$t = microtime(true);
for ($col = 0; $col < 50; $col++) {
    for ($row = 0; $row < 100; $row++) {
        $str = ($row + $col);
        $style = $styles[$row % 10];
        $coord = PHPExcel_Cell::stringFromColumnIndex($col) . ($row + 1);
        $worksheet->setCellValue($coord, $str);
        $worksheet->duplicateStyle($style, $coord);
    }
}
$d = microtime(true) - $t;
echo date('H:i:s') , " Add data (end), time: " . round($d, 2) . " s", EOL;


echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;


echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

echo date('H:i:s') , " Done writing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;
