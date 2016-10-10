<?php

require_once 'testDataFileIterator.php';

class CalculationTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
    }

    /**
     * @dataProvider providerBinaryComparisonOperation
     */
    public function testBinaryComparisonOperation($formula, $expectedResultExcel, $expectedResultOpenOffice)
    {
        PHPExcel_Calculation_Functions::setCompatibilityMode(PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL);
        $resultExcel = \PHPExcel_Calculation::getInstance()->_calculateFormulaValue($formula);
        $this->assertEquals($expectedResultExcel, $resultExcel, 'should be Excel compatible');

        PHPExcel_Calculation_Functions::setCompatibilityMode(PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = \PHPExcel_Calculation::getInstance()->_calculateFormulaValue($formula);
        $this->assertEquals($expectedResultOpenOffice, $resultOpenOffice, 'should be OpenOffice compatible');
    }

    public function providerBinaryComparisonOperation()
    {
        return new testDataFileIterator('rawTestData/CalculationBinaryComparisonOperation.data');
    }

}
