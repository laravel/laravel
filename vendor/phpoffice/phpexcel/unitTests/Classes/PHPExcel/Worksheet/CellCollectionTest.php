<?php

class CellCollectionTest extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		if (!defined('PHPEXCEL_ROOT'))
		{
			define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
		}
		require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}


	public function testCacheLastCell()
	{
		$methods = PHPExcel_CachedObjectStorageFactory::getCacheStorageMethods();
		foreach ($methods as $method) {
			PHPExcel_CachedObjectStorageFactory::initialize($method);
			$workbook = new PHPExcel();
			$cells = array('A1', 'A2');
			$worksheet = $workbook->getActiveSheet();
			$worksheet->setCellValue('A1', 1);
			$worksheet->setCellValue('A2', 2);
			$this->assertEquals($cells, $worksheet->getCellCollection(), "Cache method \"$method\".");
			PHPExcel_CachedObjectStorageFactory::finalize();
		}
	}

}
