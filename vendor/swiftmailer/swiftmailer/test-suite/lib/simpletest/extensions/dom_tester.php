<?php

/**
 *	@package	SimpleTest
 *	@subpackage	Extensions
 *  @author     Perrick Penet <perrick@noparking.net>
 *	@version	$Id: dom_tester.php 1804 2008-09-08 13:16:44Z pp11 $
 */

/**#@+
 * include SimpleTest files
 */
require_once dirname(__FILE__).'/../web_tester.php';
require_once dirname(__FILE__).'/dom_tester/css_selector.php';
/**#@-*/

/**
 * CssSelectorExpectation
 * 
 * Create a CSS Selector expectactation
 * 
 * @param DomDocument $_dom
 * @param string $_selector
 * @param array $_value
 * 
 */
class CssSelectorExpectation extends SimpleExpectation {
    var $_dom;
    var $_selector;
    var $_value;
    
    /**
     *    Sets the dom tree and the css selector to compare against
     *    @param mixed $dom          Dom tree to search into.
     *    @param mixed $selector     Css selector to match element.
     *    @param string $message     Customised message on failure.
     *    @access public
     */
    function CssSelectorExpectation($dom, $selector, $message = '%s') {
        $this->SimpleExpectation($message);
        $this->_dom = $dom;
        $this->_selector = $selector;
        
        $css_selector = new CssSelector($this->_dom);
        $this->_value = $css_selector->getTexts($this->_selector);
    }
    
    /**
     *    Tests the expectation. True if it matches the
     *    held value.
     *    @param mixed $compare        Comparison value.
     *    @return boolean              True if correct.
     *    @access public
     */
    function test($compare) {
            return (($this->_value == $compare) && ($compare == $this->_value));
    }
    
    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        $dumper = &$this->_getDumper();
        if (is_array($compare)) {
            sort($compare);
        }
        if ($this->test($compare)) {
            return "CSS selector expectation [" . $dumper->describeValue($this->_value) . "]".
            		" using [" . $dumper->describeValue($this->_selector) . "]";
        } else {
            return "CSS selector expectation [" . $dumper->describeValue($this->_value) . "]".
            		" using [" . $dumper->describeValue($this->_selector) . "]".
            		" fails with [" .
                    $dumper->describeValue($compare) . "] " .
                    $dumper->describeDifference($this->_value, $compare);
        }
    }
}

/**
 * DomTestCase
 * 
 * Extend Web test case with DOM related assertions,
 * CSS selectors in particular
 * 
 * @param DomDocument $dom
 * 
 */
class DomTestCase extends WebTestCase {
	var $dom;

    function assertElementsBySelector($selector, $elements, $message = '%s') {
		$this->dom = new DomDocument('1.0', 'utf-8');
		$this->dom->validateOnParse = true;
		$this->dom->loadHTML($this->_browser->getContent());

        return $this->assert(
                new CssSelectorExpectation($this->dom, $selector),
                $elements,
                $message);
    }
    
	function getElementsBySelector($selector) {
		$this->dom = new DomDocument('1.0', 'utf-8');
		$this->dom->validateOnParse = true;
		$this->dom->loadHTML($this->_browser->getContent());
		
		$css_selector = new CssSelectorExpectation($this->dom, $selector);
		return $css_selector->_value;
	}
}

?>
