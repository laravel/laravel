<?php


require_once 'testDataFileIterator.php';

class DateTimeTest extends PHPUnit_Framework_TestCase
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
     * @dataProvider providerDATE
     */
	public function testDATE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DATE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDATE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DATE.data');
	}

	public function testDATEtoPHP()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC);
		$result = PHPExcel_Calculation_DateTime::DATE(2012,1,31);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$this->assertEquals(1327968000, $result, NULL, 1E-8);
	}

	public function testDATEtoPHPObject()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT);
		$result = PHPExcel_Calculation_DateTime::DATE(2012,1,31);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result,'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'),'31-Jan-2012');
	}

	public function testDATEwith1904Calendar()
	{
		PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_MAC_1904);
		$result = PHPExcel_Calculation_DateTime::DATE(1918,11,11);
		PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900);
        $this->assertEquals($result,5428);
	}

	public function testDATEwith1904CalendarError()
	{
		PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_MAC_1904);
		$result = PHPExcel_Calculation_DateTime::DATE(1901,1,31);
		PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900);
        $this->assertEquals($result,'#NUM!');
	}

    /**
     * @dataProvider providerDATEVALUE
     */
	public function testDATEVALUE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DATEVALUE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDATEVALUE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DATEVALUE.data');
	}

	public function testDATEVALUEtoPHP()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC);
		$result = PHPExcel_Calculation_DateTime::DATEVALUE('2012-1-31');
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$this->assertEquals(1327968000, $result, NULL, 1E-8);
	}

	public function testDATEVALUEtoPHPObject()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT);
		$result = PHPExcel_Calculation_DateTime::DATEVALUE('2012-1-31');
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result,'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'),'31-Jan-2012');
	}

    /**
     * @dataProvider providerYEAR
     */
	public function testYEAR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','YEAR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerYEAR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/YEAR.data');
	}

    /**
     * @dataProvider providerMONTH
     */
	public function testMONTH()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','MONTHOFYEAR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerMONTH()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/MONTH.data');
	}

    /**
     * @dataProvider providerWEEKNUM
     */
	public function testWEEKNUM()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','WEEKOFYEAR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerWEEKNUM()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/WEEKNUM.data');
	}

    /**
     * @dataProvider providerWEEKDAY
     */
	public function testWEEKDAY()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DAYOFWEEK'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerWEEKDAY()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/WEEKDAY.data');
	}

    /**
     * @dataProvider providerDAY
     */
	public function testDAY()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DAYOFMONTH'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDAY()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DAY.data');
	}

    /**
     * @dataProvider providerTIME
     */
	public function testTIME()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','TIME'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerTIME()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/TIME.data');
	}

	public function testTIMEtoPHP()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC);
		$result = PHPExcel_Calculation_DateTime::TIME(7,30,20);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$this->assertEquals(27020, $result, NULL, 1E-8);
	}

	public function testTIMEtoPHPObject()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT);
		$result = PHPExcel_Calculation_DateTime::TIME(7,30,20);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result,'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('H:i:s'),'07:30:20');
	}

    /**
     * @dataProvider providerTIMEVALUE
     */
	public function testTIMEVALUE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','TIMEVALUE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerTIMEVALUE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/TIMEVALUE.data');
	}

	public function testTIMEVALUEtoPHP()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC);
		$result = PHPExcel_Calculation_DateTime::TIMEVALUE('7:30:20');
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$this->assertEquals(23420, $result, NULL, 1E-8);
	}

	public function testTIMEVALUEtoPHPObject()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT);
		$result = PHPExcel_Calculation_DateTime::TIMEVALUE('7:30:20');
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result,'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('H:i:s'),'07:30:20');
	}

    /**
     * @dataProvider providerHOUR
     */
	public function testHOUR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','HOUROFDAY'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerHOUR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/HOUR.data');
	}

    /**
     * @dataProvider providerMINUTE
     */
	public function testMINUTE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','MINUTEOFHOUR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerMINUTE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/MINUTE.data');
	}

    /**
     * @dataProvider providerSECOND
     */
	public function testSECOND()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','SECONDOFMINUTE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerSECOND()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/SECOND.data');
	}

    /**
     * @dataProvider providerNETWORKDAYS
     */
	public function testNETWORKDAYS()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','NETWORKDAYS'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerNETWORKDAYS()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/NETWORKDAYS.data');
	}

    /**
     * @dataProvider providerWORKDAY
     */
	public function testWORKDAY()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','WORKDAY'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerWORKDAY()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/WORKDAY.data');
	}

    /**
     * @dataProvider providerEDATE
     */
	public function testEDATE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','EDATE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerEDATE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/EDATE.data');
	}

	public function testEDATEtoPHP()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC);
		$result = PHPExcel_Calculation_DateTime::EDATE('2012-1-26',-1);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$this->assertEquals(1324857600, $result, NULL, 1E-8);
	}

	public function testEDATEtoPHPObject()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT);
		$result = PHPExcel_Calculation_DateTime::EDATE('2012-1-26',-1);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result,'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'),'26-Dec-2011');
	}

    /**
     * @dataProvider providerEOMONTH
     */
	public function testEOMONTH()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','EOMONTH'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerEOMONTH()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/EOMONTH.data');
	}

	public function testEOMONTHtoPHP()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC);
		$result = PHPExcel_Calculation_DateTime::EOMONTH('2012-1-26',-1);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
		$this->assertEquals(1325289600, $result, NULL, 1E-8);
	}

	public function testEOMONTHtoPHPObject()
	{
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT);
		$result = PHPExcel_Calculation_DateTime::EOMONTH('2012-1-26',-1);
		PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
        //    Must return an object...
        $this->assertTrue(is_object($result));
        //    ... of the correct type
        $this->assertTrue(is_a($result,'DateTime'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'),'31-Dec-2011');
	}

    /**
     * @dataProvider providerDATEDIF
     */
	public function testDATEDIF()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DATEDIF'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDATEDIF()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DATEDIF.data');
	}

    /**
     * @dataProvider providerDAYS360
     */
	public function testDAYS360()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DAYS360'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDAYS360()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DAYS360.data');
	}

    /**
     * @dataProvider providerYEARFRAC
     */
	public function testYEARFRAC()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','YEARFRAC'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerYEARFRAC()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/YEARFRAC.data');
	}

}
