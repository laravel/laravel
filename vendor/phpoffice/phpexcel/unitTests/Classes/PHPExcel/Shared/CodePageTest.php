<?php


require_once 'testDataFileIterator.php';

class CodePageTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

    /**
     * @dataProvider providerCodePage
     */
	public function testCodePageNumberToName()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_CodePage','NumberToName'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerCodePage()
    {
    	return new testDataFileIterator('rawTestData/Shared/CodePage.data');
	}

    public function testNumberToNameWithInvalidCodePage()
	{
		$invalidCodePage = 12345;
		try {
			$result = call_user_func(array('PHPExcel_Shared_CodePage','NumberToName'),$invalidCodePage);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'Unknown codepage: 12345');
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

    public function testNumberToNameWithUnsupportedCodePage()
	{
		$unsupportedCodePage = 720;
		try {
			$result = call_user_func(array('PHPExcel_Shared_CodePage','NumberToName'),$unsupportedCodePage);
		} catch (Exception $e) {
			$this->assertEquals($e->getMessage(), 'Code page 720 not supported.');
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

}
