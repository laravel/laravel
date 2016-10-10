<?php


require_once 'testDataFileIterator.php';

class FileTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testGetUseUploadTempDirectory()
	{
		$expectedResult = FALSE;

		$result = call_user_func(array('PHPExcel_Shared_File','getUseUploadTempDirectory'));
		$this->assertEquals($expectedResult, $result);
	}

	public function testSetUseUploadTempDirectory()
	{
		$useUploadTempDirectoryValues = array(
			TRUE,
			FALSE,
		);

		foreach($useUploadTempDirectoryValues as $useUploadTempDirectoryValue) {
			call_user_func(array('PHPExcel_Shared_File','setUseUploadTempDirectory'),$useUploadTempDirectoryValue);

			$result = call_user_func(array('PHPExcel_Shared_File','getUseUploadTempDirectory'));
			$this->assertEquals($useUploadTempDirectoryValue, $result);
		}
	}
}
