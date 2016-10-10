<?php


require_once 'testDataFileIterator.php';

class PasswordHasherTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

    /**
     * @dataProvider providerHashPassword
     */
	public function testHashPassword()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_PasswordHasher','hashPassword'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerHashPassword()
    {
    	return new testDataFileIterator('rawTestData/Shared/PasswordHashes.data');
	}

}
