<?php
// $Id: adapter_test.php 1505 2007-04-30 23:39:59Z lastcraft $

$target = dirname(__FILE__)."/public_html/logs";
$integration = new SimpleIntegration($target);

$cli_directory = dirname(__FILE__)."/binaries/";
$test = dirname(__FILE__)."/working-copies/simpletest/test/all_tests.php";
$integration->updateTestLogs($cli_directory, $test);

$working_copy = dirname(__FILE__)."/working-copies/simpletest";
$binary = "svn";
$integration->updateSvnLog($working_copy, $binary);

class SimpleIntegration {
    public $target_directory;
    
    function __construct($target_directory="") {
        $this->target_directory = $target_directory;
    }
    
    function updateTestLogs($cli_directory, $test_file) {
        foreach(new DirectoryIterator($cli_directory) as $node) {
            if ($node->isDir() and !$node->isDot()) {
                $bin = $node->getPathname()."/bin/php";
                $result = shell_exec($bin." ".$test_file);
                
                $result_file = $this->target_directory."/simpletest.".$node->getFilename().".log";
                file_put_contents($result_file, $result);
            }
        }
    }
    
    function updateSvnLog($working_copy, $binary="svn") {
        $start = date("Y-m-d", strtotime('-1year'));
        $command = $binary." log --xml --revision {".$start."}:HEAD ".$working_copy." > ".$this->target_directory."/svn.xml";
        return exec($command);
    }
}

?>