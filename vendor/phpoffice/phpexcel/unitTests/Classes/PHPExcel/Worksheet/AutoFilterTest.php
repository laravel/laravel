<?php


class AutoFilterTest extends PHPUnit_Framework_TestCase
{
	private $_testInitialRange = 'H2:O256';

	private $_testAutoFilterObject;


    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');

        $this->_mockWorksheetObject = $this->getMockBuilder('PHPExcel_Worksheet')
        	->disableOriginalConstructor()
        	->getMock();
        $this->_mockCacheController = $this->getMockBuilder('PHPExcel_CachedObjectStorage_Memory')
        	->disableOriginalConstructor()
        	->getMock();
        $this->_mockWorksheetObject->expects($this->any())
            ->method('getCellCacheController')
            ->will($this->returnValue($this->_mockCacheController));

		$this->_testAutoFilterObject = new PHPExcel_Worksheet_AutoFilter(
			$this->_testInitialRange,
			$this->_mockWorksheetObject
		);
    }

	public function testToString()
	{
		$expectedResult = $this->_testInitialRange;

		//	magic __toString should return the active autofilter range
		$result = $this->_testAutoFilterObject;
		$this->assertEquals($expectedResult, $result);
	}

	public function testGetParent()
	{
		$result = $this->_testAutoFilterObject->getParent();
		$this->assertInstanceOf('PHPExcel_Worksheet', $result);
	}

	public function testSetParent()
	{
		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterObject->setParent($this->_mockWorksheetObject);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);
	}

	public function testGetRange()
	{
		$expectedResult = $this->_testInitialRange;

		//	Result should be the active autofilter range
		$result = $this->_testAutoFilterObject->getRange();
		$this->assertEquals($expectedResult, $result);
	}

	public function testSetRange()
	{
		$ranges = array('G1:J512' => 'Worksheet1!G1:J512',
						'K1:N20' => 'K1:N20'
					   );

		foreach($ranges as $actualRange => $fullRange) {
			//	Setters return the instance to implement the fluent interface
			$result = $this->_testAutoFilterObject->setRange($fullRange);
			$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);

			//	Result should be the new autofilter range
			$result = $this->_testAutoFilterObject->getRange();
			$this->assertEquals($actualRange, $result);
		}
	}

	public function testClearRange()
	{
		$expectedResult = '';

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterObject->setRange();
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);

		//	Result should be a clear range
		$result = $this->_testAutoFilterObject->getRange();
		$this->assertEquals($expectedResult, $result);
	}

    /**
     * @expectedException PHPExcel_Exception
     */
	public function testSetRangeInvalidRange()
	{
		$expectedResult = 'A1';

		$result = $this->_testAutoFilterObject->setRange($expectedResult);
	}

	public function testGetColumnsEmpty()
	{
		//	There should be no columns yet defined
		$result = $this->_testAutoFilterObject->getColumns();
		$this->assertInternalType('array', $result);
		$this->assertEquals(0, count($result));
	}

	public function testGetColumnOffset()
	{
		$columnIndexes = array(	'H' => 0,
								'K' => 3,
								'M' => 5
							  );

		//	If we request a specific column by its column ID, we should get an
		//	integer returned representing the column offset within the range
		foreach($columnIndexes as $columnIndex => $columnOffset) {
			$result = $this->_testAutoFilterObject->getColumnOffset($columnIndex);
			$this->assertEquals($columnOffset, $result);
		}
	}

    /**
     * @expectedException PHPExcel_Exception
     */
    public function testGetInvalidColumnOffset()
	{
		$invalidColumn = 'G';

		$result = $this->_testAutoFilterObject->getColumnOffset($invalidColumn);
	}

	public function testSetColumnWithString()
	{
		$expectedResult = 'L';

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterObject->setColumn($expectedResult);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);

		$result = $this->_testAutoFilterObject->getColumns();
		//	Result should be an array of PHPExcel_Worksheet_AutoFilter_Column
		//	objects for each column we set indexed by the column ID
		$this->assertInternalType('array', $result);
		$this->assertEquals(1, count($result));
		$this->assertArrayHasKey($expectedResult,$result);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result[$expectedResult]);
	}

    /**
     * @expectedException PHPExcel_Exception
     */
    public function testSetInvalidColumnWithString()
	{
		$invalidColumn = 'A';

		$result = $this->_testAutoFilterObject->setColumn($invalidColumn);
	}

	public function testSetColumnWithColumnObject()
	{
		$expectedResult = 'M';
		$columnObject = new PHPExcel_Worksheet_AutoFilter_Column($expectedResult);

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterObject->setColumn($columnObject);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);

		$result = $this->_testAutoFilterObject->getColumns();
		//	Result should be an array of PHPExcel_Worksheet_AutoFilter_Column
		//	objects for each column we set indexed by the column ID
		$this->assertInternalType('array', $result);
		$this->assertEquals(1, count($result));
		$this->assertArrayHasKey($expectedResult,$result);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result[$expectedResult]);
	}

    /**
     * @expectedException PHPExcel_Exception
     */
    public function testSetInvalidColumnWithObject()
	{
		$invalidColumn = 'E';
		$columnObject = new PHPExcel_Worksheet_AutoFilter_Column($invalidColumn);

		$result = $this->_testAutoFilterObject->setColumn($invalidColumn);
	}

    /**
     * @expectedException PHPExcel_Exception
     */
    public function testSetColumnWithInvalidDataType()
	{
		$invalidColumn = 123.456;
		$columnObject = new PHPExcel_Worksheet_AutoFilter_Column($invalidColumn);

		$result = $this->_testAutoFilterObject->setColumn($invalidColumn);
	}

	public function testGetColumns()
	{
		$columnIndexes = array('L','M');

		foreach($columnIndexes as $columnIndex) {
			$this->_testAutoFilterObject->setColumn($columnIndex);
		}

		$result = $this->_testAutoFilterObject->getColumns();
		//	Result should be an array of PHPExcel_Worksheet_AutoFilter_Column
		//	objects for each column we set indexed by the column ID
		$this->assertInternalType('array', $result);
		$this->assertEquals(count($columnIndexes), count($result));
		foreach($columnIndexes as $columnIndex) {
			$this->assertArrayHasKey($columnIndex,$result);
			$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result[$columnIndex]);
		}
	}

	public function testGetColumn()
	{
		$columnIndexes = array('L','M');

		foreach($columnIndexes as $columnIndex) {
			$this->_testAutoFilterObject->setColumn($columnIndex);
		}

		//	If we request a specific column by its column ID, we should
		//	get a PHPExcel_Worksheet_AutoFilter_Column object returned
		foreach($columnIndexes as $columnIndex) {
			$result = $this->_testAutoFilterObject->getColumn($columnIndex);
			$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
		}
	}

	public function testGetColumnByOffset()
	{
		$columnIndexes = array(	0 => 'H',
								3 => 'K',
								5 => 'M'
							  );

		//	If we request a specific column by its offset, we should
		//	get a PHPExcel_Worksheet_AutoFilter_Column object returned
		foreach($columnIndexes as $columnIndex => $columnID) {
			$result = $this->_testAutoFilterObject->getColumnByOffset($columnIndex);
			$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
			$this->assertEquals($result->getColumnIndex(),$columnID);
		}
	}

	public function testGetColumnIfNotSet()
	{
		//	If we request a specific column by its column ID, we should
		//	get a PHPExcel_Worksheet_AutoFilter_Column object returned
		$result = $this->_testAutoFilterObject->getColumn('K');
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
	}

    /**
     * @expectedException PHPExcel_Exception
     */
	public function testGetColumnWithoutRangeSet()
	{
		//	Clear the range
		$result = $this->_testAutoFilterObject->setRange();

		$result = $this->_testAutoFilterObject->getColumn('A');
	}

	public function testClearRangeWithExistingColumns()
	{
		$expectedResult = '';

		$columnIndexes = array('L','M','N');
		foreach($columnIndexes as $columnIndex) {
			$this->_testAutoFilterObject->setColumn($columnIndex);
		}

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterObject->setRange();
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);

		//	Range should be cleared
		$result = $this->_testAutoFilterObject->getRange();
		$this->assertEquals($expectedResult, $result);

		//	Column array should be cleared
		$result = $this->_testAutoFilterObject->getColumns();
		$this->assertInternalType('array', $result);
		$this->assertEquals(0, count($result));
	}

	public function testSetRangeWithExistingColumns()
	{
		$expectedResult = 'G1:J512';

		//	These columns should be retained
		$columnIndexes1 = array('I','J');
		foreach($columnIndexes1 as $columnIndex) {
			$this->_testAutoFilterObject->setColumn($columnIndex);
		}
		//	These columns should be discarded
		$columnIndexes2 = array('K','L','M');
		foreach($columnIndexes2 as $columnIndex) {
			$this->_testAutoFilterObject->setColumn($columnIndex);
		}

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterObject->setRange($expectedResult);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);

		//	Range should be correctly set
		$result = $this->_testAutoFilterObject->getRange();
		$this->assertEquals($expectedResult, $result);

		//	Only columns that existed in the original range and that
		//		still fall within the new range should be retained
		$result = $this->_testAutoFilterObject->getColumns();
		$this->assertInternalType('array', $result);
		$this->assertEquals(count($columnIndexes1), count($result));
	}

	public function testClone()
	{
		$columnIndexes = array('L','M');

		foreach($columnIndexes as $columnIndex) {
			$this->_testAutoFilterObject->setColumn($columnIndex);
		}

		$result = clone $this->_testAutoFilterObject;
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);
	}

}
