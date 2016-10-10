<?php


require_once 'testDataFileIterator.php';

class FunctionsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT'))
        {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testDUMMY()
	{
		$result = PHPExcel_Calculation_Functions::DUMMY();
		$this->assertEquals('#Not Yet Implemented', $result);
	}

	public function testDIV0()
	{
		$result = PHPExcel_Calculation_Functions::DIV0();
		$this->assertEquals('#DIV/0!', $result);
	}

	public function testNA()
	{
		$result = PHPExcel_Calculation_Functions::NA();
		$this->assertEquals('#N/A', $result);
	}

	public function testNaN()
	{
		$result = PHPExcel_Calculation_Functions::NaN();
		$this->assertEquals('#NUM!', $result);
	}

	public function testNAME()
	{
		$result = PHPExcel_Calculation_Functions::NAME();
		$this->assertEquals('#NAME?', $result);
	}

	public function testREF()
	{
		$result = PHPExcel_Calculation_Functions::REF();
		$this->assertEquals('#REF!', $result);
	}

	public function testNULL()
	{
		$result = PHPExcel_Calculation_Functions::NULL();
		$this->assertEquals('#NULL!', $result);
	}

	public function testVALUE()
	{
		$result = PHPExcel_Calculation_Functions::VALUE();
		$this->assertEquals('#VALUE!', $result);
	}

    /**
     * @dataProvider providerIS_BLANK
     */
	public function testIS_BLANK()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_BLANK'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_BLANK()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_BLANK.data');
	}

    /**
     * @dataProvider providerIS_ERR
     */
	public function testIS_ERR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_ERR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_ERR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_ERR.data');
	}

    /**
     * @dataProvider providerIS_ERROR
     */
	public function testIS_ERROR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_ERROR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_ERROR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_ERROR.data');
	}

    /**
     * @dataProvider providerERROR_TYPE
     */
	public function testERROR_TYPE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','ERROR_TYPE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerERROR_TYPE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/ERROR_TYPE.data');
	}

    /**
     * @dataProvider providerIS_LOGICAL
     */
	public function testIS_LOGICAL()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_LOGICAL'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_LOGICAL()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_LOGICAL.data');
	}

    /**
     * @dataProvider providerIS_NA
     */
	public function testIS_NA()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_NA'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_NA()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_NA.data');
	}

    /**
     * @dataProvider providerIS_NUMBER
     */
	public function testIS_NUMBER()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_NUMBER'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_NUMBER()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_NUMBER.data');
	}

    /**
     * @dataProvider providerIS_TEXT
     */
	public function testIS_TEXT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_TEXT'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_TEXT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_TEXT.data');
	}

    /**
     * @dataProvider providerIS_NONTEXT
     */
	public function testIS_NONTEXT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_NONTEXT'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_NONTEXT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_NONTEXT.data');
	}

    /**
     * @dataProvider providerIS_EVEN
     */
	public function testIS_EVEN()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_EVEN'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_EVEN()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_EVEN.data');
	}

    /**
     * @dataProvider providerIS_ODD
     */
	public function testIS_ODD()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','IS_ODD'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerIS_ODD()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/IS_ODD.data');
	}

    /**
     * @dataProvider providerTYPE
     */
	public function testTYPE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','TYPE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerTYPE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/TYPE.data');
	}

    /**
     * @dataProvider providerN
     */
	public function testN()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Functions','N'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerN()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Functions/N.data');
	}

}
