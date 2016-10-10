<?php
/**
 *	base include file for SimpleTest PUnit reporter
 *	@package	SimpleTest
 *	@subpackage	Extensions
 *	@version	$Id: webunit_reporter.php 1802 2008-09-08 10:43:58Z maetl_ $
 */

/**
 * @ignore    originally defined in simple_test.php
 */
if (!defined("SIMPLE_TEST")) {
	define("SIMPLE_TEST", "simpletest/");
}
require_once(SIMPLE_TEST . 'runner.php');
require_once(SIMPLE_TEST . 'reporter.php');
/**
 * Main sprintf template for the start of the page.
 * Sequence of parameters is:
 * - title - string
 * - script path - string
 * - script path - string
 * - css path - string
 * - additional css - string
 * - title - string
 * - image path - string
 */
define('SIMPLETEST_WEBUNIT_HEAD', <<<EOS
<html>
<head>
<title>%s</title>
<script type="text/javascript" src="%sx.js"></script>
<script type="text/javascript" src="%swebunit.js"></script>
<link rel="stylesheet" type="text/css" href="%swebunit.css" title="Default"></link>
<style type="text/css">
%s
</style>
</head>
<body>
<div id="wait">
	<h1>&nbsp;Running %s&nbsp;</h1>
	Please wait...<br />
	<img src="%swait.gif" border="0"><br />&nbsp;
</div>
<script type="text/javascript">
wait_start();
</script>
<div id="webunit">
	<div id="run"></div><br />
	<div id="tabs">
		<div id="visible_tab">visible tab content</div>
		&nbsp;&nbsp;<span id="failtab" class="activetab">&nbsp;&nbsp;<a href="javascript:activate_tab('fail');">Fail</a>&nbsp;&nbsp;</span>
		<span id="treetab" class="inactivetab">&nbsp;&nbsp;<a href="javascript:activate_tab('tree');">Tree</a>&nbsp;&nbsp;</span>
	</div>
	<div id="msg">Click on a failed test case method in the tree tab to view output here.</div>
</div>
<div id="fail"></div>
<div id="tree"></div>
<!-- open a new script to capture js vars as the tests run -->
<script type="text/javascript">
layout();

EOS
);

/**
 *	Not used yet.
 *  May be needed for localized styles we need at runtime, not in the stylesheet.
 */
define('SIMPLETEST_WEBUNIT_CSS', '/* this space reseved for future use */');

    /**
     *    Sample minimal test displayer. Generates only
     *    failure messages and a pass count.
	 *	  @package SimpleTest
	 *	  @subpackage UnitTester
     */
    class WebUnitReporter extends SimpleReporter {
    	/**
    	 *    @var string Base directory for PUnit script, images and style sheets.
    	 *    Needs to be a relative path from where the test scripts are run 
    	 *    (and obviously, visible in the document root).
    	 */
    	var $path;
        
        /**
         *    Does nothing yet. The first output will
         *    be sent on the first test start. For use
         *    by a web browser.
         *    @access public
         */
        function WebUnitReporter($path='../ui/') {
            $this->SimpleReporter();
            $this->path = $path;
        }
        
        /**
         *    Paints the top of the web page setting the
         *    title to the name of the starting test.
         *    @param string $test_name      Name class of test.
         *    @access public
         */
        function paintHeader($test_name) {
            $this->sendNoCacheHeaders();
            echo sprintf(
            	SIMPLETEST_WEBUNIT_HEAD
            	,$test_name
            	,$this->path.'js/'
            	,$this->path.'js/'
            	,$this->path.'css/'
            	,$this->_getCss()
            	,$test_name
            	,$this->path.'img/'
            	);
            flush();
        }
        
        /**
         *    Send the headers necessary to ensure the page is
         *    reloaded on every request. Otherwise you could be
         *    scratching your head over out of date test data.
         *    @access public
         */
        function sendNoCacheHeaders() {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }
        
        /**
         *    Paints the CSS. Add additional styles here.
         *    @return string            CSS code as text.
         *    @access protected
         */
        function _getCss() {
            return SIMPLETEST_WEBUNIT_CSS;
        }
        
        /**
         *    Paints the end of the test with a summary of
         *    the passes and failures.
         *    @param string $test_name        Name class of test.
         *    @access public
         */
        function paintFooter($test_name) {
            echo 'make_tree();</script>'.$this->outputScript("xHide('wait');");
            $colour = ($this->getFailCount() + $this->getExceptionCount() > 0 ? "red" : "green");
            $content = "<h1>$test_name</h1>\n";
            $content .= "<div style=\"";
            $content .= "padding: 8px; margin-top: 1em; background-color: $colour; color: white;";
            $content .= "\">";
            $content .= $this->getTestCaseProgress() . "/" . $this->getTestCaseCount();
            $content .= " test cases complete:\n";
            $content .= "<strong>" . $this->getPassCount() . "</strong> passes, ";
            $content .= "<strong>" . $this->getFailCount() . "</strong> fails and ";
            $content .= "<strong>" . $this->getExceptionCount() . "</strong> exceptions.";
            $content .= "</div>\n";

			echo $this->outputScript('foo = "'.$this->toJsString($content).'";'."\nset_div_content('run', foo);");
            echo "\n</body>\n</html>\n";
        }
        
        
        /**
         *    Paints formatted text such as dumped variables.
         *    @param string $message        Text to show.
         *    @access public
         */
        function paintFormattedMessage($message) {
           echo "add_log(\"".$this->toJsString("<pre>$message</pre>", true)."\");\n";
        }
        
        /**
         *    Paints the start of a group test. Will also paint
         *    the page header and footer if this is the
         *    first test. Will stash the size if the first
         *    start.
         *    @param string $test_name   Name of test that is starting.
         *    @param integer $size       Number of test cases starting.
         *    @access public
         */
        function paintGroupStart($test_name, $size) {
             Parent::paintGroupStart($test_name, $size);
             echo "add_group('$test_name');\n";
        }
 
         /**
          *    Paints the start of a test case. Will also paint
          *    the page header and footer if this is the
          *    first test. Will stash the size if the first
          *    start.
          *    @param string $test_name   Name of test that is starting.
          *    @access public
          */
         function paintCaseStart($test_name) {
             Parent::paintCaseStart($test_name);
             echo "add_case('$test_name');\n";
         }


         /**
          *    Paints the start of a test method.
          *    @param string $test_name   Name of test that is starting.
          *    @access public
          */
         function paintMethodStart($test_name) {
             Parent::paintMethodStart($test_name);
             echo "add_method('$test_name');\n";
         }

         /**
          *    Paints the end of a test method.
          *    @param string $test_name   Name of test that is ending.
          *    @access public
          */
         function paintMethodEnd($test_name) {
             Parent::paintMethodEnd($test_name);
         }

         /**
          *    Paints the test failure with a breadcrumbs
          *    trail of the nesting test suites below the
          *    top level test.
          *    @param string $message    Failure message displayed in
          *                               the context of the other tests.
          *    @access public
          */
         function paintFail($message) {
             parent::paintFail($message);
             $msg = "<span class=\"fail\">Fail</span>: ";
             $breadcrumb = $this->getTestList();
             array_shift($breadcrumb);
             $msg .= implode("-&gt;", $breadcrumb);
             $msg .= "-&gt;" . htmlentities($message) . "<br />";
             echo "add_fail('$msg');\n";
         }

        /**
         *    Paints a PHP error or exception.
         *    @param string $message        Message is ignored.
         *    @access public
         *    @abstract
         */
        function paintException($message) {
            parent::paintException($message);
            $msg = "<span class=\"fail\">Exception</span>: ";
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            $msg .= implode("-&gt;", $breadcrumb);
            $msg .= "-&gt;<strong>" . htmlentities($message) . "</strong><br />";
            echo "add_fail('$msg');\n";
        }
 
        /**
		 * Returns the script passed in wrapped in script tags.
		 *
		 * @param	string	$script		the script to output
		 * @return	string	the script wrapped with script tags
		 */
		function outputScript($script)
		{
			return "<script type=\"text/javascript\">\n".$script."\n</script>\n";
		}
		
        
        /**
		 *	Transform a string into a format acceptable to JavaScript
		 *  @param string $str	the string to transform
		 *	@return	string
		 */
		function toJsString($str, $preserveCr=false) {
			$cr = ($preserveCr) ? '\\n' : '';
			return str_replace(
				array('"'
					,"\n")
				,array('\"'
					,"$cr\"\n\t+\"")
				,$str
				);
		}
    }
    
?>