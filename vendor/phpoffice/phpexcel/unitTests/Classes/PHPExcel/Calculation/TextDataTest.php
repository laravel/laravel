<?php


require_once 'testDataFileIterator.php';

class TextDataTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT'))
        {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

    /**
     * @dataProvider providerCHAR
     */
	public function testCHAR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','CHARACTER'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerCHAR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/CHAR.data');
	}

    /**
     * @dataProvider providerCODE
     */
	public function testCODE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','ASCIICODE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerCODE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/CODE.data');
	}

    /**
     * @dataProvider providerCONCATENATE
     */
	public function testCONCATENATE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','CONCATENATE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerCONCATENATE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/CONCATENATE.data');
	}

    /**
     * @dataProvider providerLEFT
     */
	public function testLEFT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','LEFT'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerLEFT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/LEFT.data');
	}

    /**
     * @dataProvider providerMID
     */
	public function testMID()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','MID'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerMID()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/MID.data');
	}

    /**
     * @dataProvider providerRIGHT
     */
	public function testRIGHT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','RIGHT'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerRIGHT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/RIGHT.data');
	}

    /**
     * @dataProvider providerLOWER
     */
	public function testLOWER()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','LOWERCASE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerLOWER()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/LOWER.data');
	}

    /**
     * @dataProvider providerUPPER
     */
	public function testUPPER()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','UPPERCASE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerUPPER()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/UPPER.data');
	}

    /**
     * @dataProvider providerPROPER
     */
	public function testPROPER()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','PROPERCASE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerPROPER()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/PROPER.data');
	}

    /**
     * @dataProvider providerLEN
     */
	public function testLEN()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','STRINGLENGTH'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerLEN()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/LEN.data');
	}

    /**
     * @dataProvider providerSEARCH
     */
	public function testSEARCH()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','SEARCHINSENSITIVE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerSEARCH()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/SEARCH.data');
	}

    /**
     * @dataProvider providerFIND
     */
	public function testFIND()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','SEARCHSENSITIVE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerFIND()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/FIND.data');
	}

    /**
     * @dataProvider providerREPLACE
     */
	public function testREPLACE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','REPLACE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerREPLACE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/REPLACE.data');
	}

    /**
     * @dataProvider providerSUBSTITUTE
     */
	public function testSUBSTITUTE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','SUBSTITUTE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerSUBSTITUTE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/SUBSTITUTE.data');
	}

    /**
     * @dataProvider providerTRIM
     */
	public function testTRIM()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','TRIMSPACES'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerTRIM()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/TRIM.data');
	}

    /**
     * @dataProvider providerCLEAN
     */
	public function testCLEAN()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','TRIMNONPRINTABLE'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerCLEAN()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/CLEAN.data');
	}

    /**
     * @dataProvider providerDOLLAR
     */
	public function testDOLLAR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','DOLLAR'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerDOLLAR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/DOLLAR.data');
	}

    /**
     * @dataProvider providerFIXED
     */
	public function testFIXED()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','FIXEDFORMAT'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerFIXED()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/FIXED.data');
	}

    /**
     * @dataProvider providerT
     */
	public function testT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','RETURNSTRING'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/T.data');
	}

    /**
     * @dataProvider providerTEXT
     */
	public function testTEXT()
	{
		//	Enforce decimal and thousands separator values to UK/US, and currency code to USD
		call_user_func(array('PHPExcel_Shared_String','setDecimalSeparator'),'.');
		call_user_func(array('PHPExcel_Shared_String','setThousandsSeparator'),',');
		call_user_func(array('PHPExcel_Shared_String','setCurrencyCode'),'$');

		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_TextData','TEXTFORMAT'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerTEXT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/TextData/TEXT.data');
	}

}
