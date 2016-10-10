<?php
// $Id: adapter_test.php 1505 2007-04-30 23:39:59Z lastcraft $

$log_directory = dirname(__FILE__)."/../logs";

$heartbeat = new SimpleHeartBeat($log_directory);
echo $heartbeat->view($_SERVER['QUERY_STRING']);

class SimpleHeartBeat {
    public $log_directory;
    public $tests_directory;
    
    function __construct($log_directory) {
        $this->log_directory = $log_directory;
    }
    
    function view($querystring) {
        switch ($querystring) {
            case "last-commits":
                return $this->displayLastCommits();
            case "tests-results":
                return $this->displayTestsResults();
            case "commits":
            default:
                return $this->displaySparkline("commits last week");
        }
    }

    function displayTestsResults() {
        foreach(new DirectoryIterator($this->log_directory) as $node) {
            if (preg_match("/simpletest\..*\.log/", $node->getFilename())) {
                $log = new SimpleHeartBeatLog($node);
                if (!isset($html)) {
                    $html = "<dl>";
                }
                $html .= "<dt>".$log->details()."</dt>";
                $html .= "<dd>".$log->info()."</dd>";
            }
        }
        if (isset($html)) {
            $html .= "</dl>";
        } else {
            $html = $this->dataUnavailable();
        }
        
        return $html;
    }
    
    function displayLastCommits($number=5) {
        $entries = array();
        $xml = simplexml_load_file($this->log_directory."/svn.xml");
        foreach ($xml->logentry as $logentry) {
            $dt = $logentry->msg;
            $dd = $logentry['revision']." - ".$logentry->author. " - ".$logentry->date;
            $entries[] = array('dt' => $dt, 'dd' => $dd);
        }

        if (count($entries) > 0) {
            $html = "<dl>";
            krsort($entries);
	        $i = 0;
	        foreach($entries as $entry) {
	            if ($i < $number) {
	                $i++;
	                $html .= "<dt>".$entry['dt']."</dt>";
	                $html .= "<dd>".$entry['dd']."</dd>";
	            } else {
	                break;
	            }
	        }
	
	        $html .= "</dl>";
        } else {
            $html = $this->dataUnavailable();
        }
        
        return $html;
    }
    
    function displaySparkline($name="commits last week") {
        $method = $this->findMethod($name);
        $data = $this->$method();

        if (is_array($data)) {
	        $html = "<div>";
	        $html .= "<span class=\"sparkline\">";
	        $html .= join(",", $data);
	        $html .= "</span>";
	        $html .= " ".array_pop($data)." ".$name;
	        $html .= "</div>";
        } else {
            $html = $this->dataUnavailable();
        }
        
        return $html;
    }
    
    function dataUnavailable() {
        return "<div><em>data unavailable</em></div>";
    }
    function findMethod($name) {
        switch ($name) {
            default:
                return "commitsPerWeek";
        }
    }
    
    function commitsPerWeek() {
        $data = array();
        $xml = simplexml_load_file($this->log_directory."/svn.xml");
        foreach ($xml->logentry as $logentry) {
            $timestamp = strtotime($logentry->date);
            $weekly = strtotime("last monday", $timestamp);
            if (!isset($data[$weekly])) {
                $data[$weekly] = 0;
            }
            $data[$weekly]++;
        }
        
        $data = $this->normalizeData($data, "week");
        
        return $data;
    }
    
    function normalizeData($data, $period="week") {
        $min = min(array_keys($data));
        $max = max(array_keys($data));
        
        $normalized = array();
        $current = $min;
        while ($current <= $max) {
            $normalized[$current] = 0;
            $current = strtotime("+1 ".$period, $current);
        }
        
        foreach ($data as $timestamp => $value) {
            $normalized[$timestamp] = $value;
        }
        
        return $normalized;
    }
}

class SimpleHeartBeatLog {
    public $node;
    public $content = "";
    
    function __construct($node) {
        $this->node = $node;
        $this->content = file_get_contents($this->node->getPathname());
    }

    function result() {
        if (preg_match("/OK/", $this->content)) {
            return "pass"; 
        } else {
            return "fail";
        }
    }

    function info() {
        return nl2br($this->content);
    }
    
    function details() {
        $details = substr($this->node->getFilename(), 11);
        $details = $this->result(). " with ".substr($details, 0, -4);
        $details .= " - ".date("c", $this->node->getCTime());
        
        return "<div class=\"".$this->result()."\">".$details."</div>";
    }
}

?>