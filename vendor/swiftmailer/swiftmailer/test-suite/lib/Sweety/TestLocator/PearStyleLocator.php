<?php

require_once 'Sweety/TestLocator.php';

class Sweety_TestLocator_PearStyleLocator implements Sweety_TestLocator
{
  
  private $_testCache = array();
  
  public function getTests($dirs = array())
  {
    return $this->_findTestCases($dirs);
  }
  
  public function includeTest($testCase)
  {
    $file = str_replace('_', '/', $testCase) . '.php';
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $dir)
    {
      if (is_file($dir . '/' . $file))
      {
        require_once $dir . '/' . $file;
        return true;
      }
    }
    
    return false;
  }
  
  protected function _findTestCases($dirs = array(), $prepend = '')
  {
    $ret = array();
    
    foreach ($dirs as $dir)
    {
      if (array_key_exists($dir, $this->_testCache))
      {
        $ret += $this->_testCache[$dir];
        continue;
      }
      
      $this->_testCache[$dir] = array();
      
      $handle = opendir($dir);
      while (false !== $file = readdir($handle))
      {
        if (substr($file, 0, 1) != '.' && is_dir($dir . '/' . $file))
        {
          foreach ($this->_findTestCases(
            array($dir . '/' . $file), $prepend . $file . '_') as $add)
          {
            $this->_testCache[$dir][] = $add;
            $ret[] = $add;
          }
        }
        elseif (substr($file, -4) == '.php')
        { 
          $className = $prepend . basename($file, '.php');
          $this->_testCache[$dir][] = $className;
          $ret[] = $className;
        }
      }
      closedir($handle);
    }
    
    sort($ret);
    
    return $ret;
  }
  
}
