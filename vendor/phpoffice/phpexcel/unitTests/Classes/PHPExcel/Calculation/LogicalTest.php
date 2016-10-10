<?php


require_once 'testDataFileIterator.php';

class LogicalTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT'))
        {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testTRUE()
	{
		$result = PHPExcel_Calculation_Logical::TRUE();
		$this->assertEquals(TRUE, $result);
	}

	public function testFALSE()
	{
		$result = PHPExcel_Calculation_Logical::FALSE();
		$this->assertEquals(FALSE, $result);
	}

    /**
     * @dataProvider providerAND
     */
	public function testAND()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','LOGICAL_AND'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerAND()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Logical/AND.data');
	}

    /**
     * @dataProvider providerOR
     */
	public function testOR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','LOGICAL_OR'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerOR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Logical/OR.data');
	}

    /**
     * @dataProvider providerNOT
     */
    public function testNOT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','NOT'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerNOT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Logical/NOT.data');
    }

    /**
     * @dataProvider providerIF
     */
    public function testIF()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','STATEMENT_IF'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerIF()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Logical/IF.data');
	}

    /**
     * @dataProvider providerIFERROR
     */
    public function testIFERROR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','IFERROR'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerIFERROR()
    {
        return new testDataFileIterator('rawTestData/Calculation/Logical/IFERROR.data');
	}

}
