<?php


class DataSeriesValuesTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT'))
        {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testSetDataType()
	{
		$dataTypeValues = array(
			'Number',
			'String'
		);

		$testInstance = new PHPExcel_Chart_DataSeriesValues;

		foreach($dataTypeValues as $dataTypeValue) {
			$result = $testInstance->setDataType($dataTypeValue);
			$this->assertTrue($result instanceof PHPExcel_Chart_DataSeriesValues);
		}
	}

	public function testSetInvalidDataTypeThrowsException()
	{
		$testInstance = new PHPExcel_Chart_DataSeriesValues;

		try {
			$result = $testInstance->setDataType('BOOLEAN');
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'Invalid datatype for chart data series values');
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function testGetDataType()
	{
		$dataTypeValue = 'String';

		$testInstance = new PHPExcel_Chart_DataSeriesValues;
		$setValue = $testInstance->setDataType($dataTypeValue);

		$result = $testInstance->getDataType();
		$this->assertEquals($dataTypeValue,$result);
	}

}
