<?php


class ColumnTest extends PHPUnit_Framework_TestCase
{
	private $_testInitialColumn = 'H';

	private $_testAutoFilterColumnObject;

	private $_mockAutoFilterObject;

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');

        $this->_mockAutoFilterObject = $this->getMockBuilder('PHPExcel_Worksheet_AutoFilter')
        	->disableOriginalConstructor()
        	->getMock();

        $this->_mockAutoFilterObject->expects($this->any())
        	->method('testColumnInRange')
        	->will($this->returnValue(3));

		$this->_testAutoFilterColumnObject = new PHPExcel_Worksheet_AutoFilter_Column(
			$this->_testInitialColumn,
			$this->_mockAutoFilterObject
		);
    }

	public function testGetColumnIndex()
	{
		$result = $this->_testAutoFilterColumnObject->getColumnIndex();
		$this->assertEquals($this->_testInitialColumn, $result);
	}

	public function testSetColumnIndex()
	{
		$expectedResult = 'L';

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterColumnObject->setColumnIndex($expectedResult);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);

		$result = $this->_testAutoFilterColumnObject->getColumnIndex();
		$this->assertEquals($expectedResult, $result);
	}

	public function testGetParent()
	{
		$result = $this->_testAutoFilterColumnObject->getParent();
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter', $result);
	}

	public function testSetParent()
	{
		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterColumnObject->setParent($this->_mockAutoFilterObject);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
	}

	public function testGetFilterType()
	{
		$result = $this->_testAutoFilterColumnObject->getFilterType();
		$this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER, $result);
	}

	public function testSetFilterType()
	{
		$result = $this->_testAutoFilterColumnObject->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);

		$result = $this->_testAutoFilterColumnObject->getFilterType();
		$this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER, $result);
	}

    /**
     * @expectedException PHPExcel_Exception
     */
	public function testSetInvalidFilterTypeThrowsException()
	{
		$expectedResult = 'Unfiltered';

		$result = $this->_testAutoFilterColumnObject->setFilterType($expectedResult);
	}

	public function testGetJoin()
	{
		$result = $this->_testAutoFilterColumnObject->getJoin();
		$this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_OR, $result);
	}

	public function testSetJoin()
	{
		$result = $this->_testAutoFilterColumnObject->setJoin(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);

		$result = $this->_testAutoFilterColumnObject->getJoin();
		$this->assertEquals(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND, $result);
	}

    /**
     * @expectedException PHPExcel_Exception
     */
	public function testSetInvalidJoinThrowsException()
	{
		$expectedResult = 'Neither';

		$result = $this->_testAutoFilterColumnObject->setJoin($expectedResult);
	}

	public function testSetAttributes()
	{
		$attributeSet = array(	'val' => 100,
								'maxVal' => 200
							 );

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterColumnObject->setAttributes($attributeSet);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
	}

	public function testGetAttributes()
	{
		$attributeSet = array(	'val' => 100,
								'maxVal' => 200
							 );

		$this->_testAutoFilterColumnObject->setAttributes($attributeSet);

		$result = $this->_testAutoFilterColumnObject->getAttributes();
		$this->assertTrue(is_array($result));
		$this->assertEquals(count($attributeSet), count($result));
	}

	public function testSetAttribute()
	{
		$attributeSet = array(	'val' => 100,
								'maxVal' => 200
							 );

		foreach($attributeSet as $attributeName => $attributeValue) {
			//	Setters return the instance to implement the fluent interface
			$result = $this->_testAutoFilterColumnObject->setAttribute($attributeName,$attributeValue);
			$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
		}
	}

	public function testGetAttribute()
	{
		$attributeSet = array(	'val' => 100,
								'maxVal' => 200
							 );

		$this->_testAutoFilterColumnObject->setAttributes($attributeSet);

		foreach($attributeSet as $attributeName => $attributeValue) {
			$result = $this->_testAutoFilterColumnObject->getAttribute($attributeName);
			$this->assertEquals($attributeValue, $result);
		}
		$result = $this->_testAutoFilterColumnObject->getAttribute('nonExistentAttribute');
		$this->assertNull($result);
	}

	public function testClone()
	{
		$result = clone $this->_testAutoFilterColumnObject;
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
	}

}
