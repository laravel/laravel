<?php


require_once 'testDataFileIterator.php';

class FontTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testGetAutoSizeMethod()
	{
		$expectedResult = PHPExcel_Shared_Font::AUTOSIZE_METHOD_APPROX;

		$result = call_user_func(array('PHPExcel_Shared_Font','getAutoSizeMethod'));
		$this->assertEquals($expectedResult, $result);
	}

	public function testSetAutoSizeMethod()
	{
		$autosizeMethodValues = array(
			PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT,
			PHPExcel_Shared_Font::AUTOSIZE_METHOD_APPROX,
		);

		foreach($autosizeMethodValues as $autosizeMethodValue) {
			$result = call_user_func(array('PHPExcel_Shared_Font','setAutoSizeMethod'),$autosizeMethodValue);
			$this->assertTrue($result);
		}
	}

    public function testSetAutoSizeMethodWithInvalidValue()
	{
		$unsupportedAutosizeMethod = 'guess';

		$result = call_user_func(array('PHPExcel_Shared_Font','setAutoSizeMethod'),$unsupportedAutosizeMethod);
		$this->assertFalse($result);
	}

    /**
     * @dataProvider providerFontSizeToPixels
     */
	public function testFontSizeToPixels()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_Font','fontSizeToPixels'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerFontSizeToPixels()
    {
    	return new testDataFileIterator('rawTestData/Shared/FontSizeToPixels.data');
	}

    /**
     * @dataProvider providerInchSizeToPixels
     */
	public function testInchSizeToPixels()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_Font','inchSizeToPixels'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerInchSizeToPixels()
    {
    	return new testDataFileIterator('rawTestData/Shared/InchSizeToPixels.data');
	}

    /**
     * @dataProvider providerCentimeterSizeToPixels
     */
	public function testCentimeterSizeToPixels()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_Font','centimeterSizeToPixels'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerCentimeterSizeToPixels()
    {
    	return new testDataFileIterator('rawTestData/Shared/CentimeterSizeToPixels.data');
	}

}
