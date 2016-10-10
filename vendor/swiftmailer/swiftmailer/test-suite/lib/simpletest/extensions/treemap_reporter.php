<?php
/**
 *	Extension file for SimpleTest
 *  @package        SimpleTest
 *  @subpackage     Extensions
 *	@version	$Id: treemap_reporter.php 1802 2008-09-08 10:43:58Z maetl_ $
 */
require_once(dirname(__FILE__) . '/../scorer.php');
require_once(dirname(__FILE__) . '/treemap_reporter/treemap_recorder.php');

/**
 * Constructs and renders a treemap visualization of a test run
 *
 * @package SimpleTest
 * @subpackage Extensions
 */
class TreemapReporter extends SimpleReporterDecorator {

	function TreemapReporter() {
		$this->SimpleReporterDecorator(new TreemapRecorder());
	}

	/**
	 * basic CSS for floating nested divs
	 * @todo checkout some weird border bugs
	 */
	function _getCss() {
		$css = ".pass{background-color:green;}.fail{background-color:red;}";
		$css .= "body {background-color:white;margin:0;padding:1em;}";
		$css .= "div{float:right;margin:0;color:black;}";
		$css .= "div{border-left:1px solid white;border-bottom:1px solid white;}";
		$css .= "h1 {font:normal 1.8em Arial;color:black;margin:0 0 0.3em 0.1em;}";
		$css .= ".clear { clear:both; }";
		return $css;
	}	
	
	/**
	 * paints the HTML header and sets up results
	 */
	function paintResultsHeader() {
		$title = $this->_reporter->getTitle();
		echo "<html><head>";
		echo "<title>{$title}</title>";
		echo "<style type=\"text/css\">" . $this->_getCss() . "</style>";
		echo "</head><body>";
		echo "<h1>{$title}</h1>";
	}	
	
	/**
	 * places a clearing break below the end of the test nodes
	 */
	function paintResultsFooter() {
		echo "<br clear=\"all\">";
		echo "</body></html>";
	}
	 
	/**
	 * paints start tag for div representing a test node
	 */
	function paintRectangleStart($node, $horiz, $vert) {
		$name = $node->getName();
		$description = $node->getDescription();
		$status = $node->getStatus();
		echo "<div title=\"$name: $description\" class=\"$status\" style=\"width:{$horiz}%;height:{$vert}%\">";
	}
	
	/**
	 * paints end tag for test node div
	 */
	function paintRectangleEnd() {
		echo "</div>";
	}	
	
	/**
	 * paints wrapping treemap divs
	 * @todo how to configure aspect and other parameters?
	 */
	function paintFooter($group) {
		$aspect = 1;
		$this->paintResultsHeader();
		$this->paintRectangleStart($this->_reporter->getGraph(), 100, 100);
		$this->divideMapNodes($this->_reporter->getGraph(), $aspect);
		$this->paintRectangleEnd();
		$this->paintResultsFooter();
	}
	
	/**
	 * divides the test results based on a slice and dice algorithm
	 *
	 * @param TreemapNode $map sorted 
	 * @param boolean $aspect flips the aspect between horizontal and vertical
	 * @private
	 */
	function divideMapNodes($map, $aspect) {
		$aspect = !$aspect;
		$divisions = $map->getSize();
		$total = $map->getTotalSize();
		foreach($map->getChildren() as $node) {
			if (!$node->isLeaf()) {
				$dist = $node->getTotalSize() / $total * 100;
			} else {
				$dist = 1 / $total * 100;
			}
			if ($aspect) {
				$horiz = $dist;
				$vert = 100;
			} else {
				$horiz = 100;
				$vert = $dist;
			}
			$this->paintRectangleStart($node, $horiz, $vert);
			$this->divideMapNodes($node, $aspect);
			$this->paintRectangleEnd();
		}
	}
	
	function paintGroupEnd($group) {
		$this->_reporter->paintGroupEnd($group);
		if ($this->_reporter->isComplete()) {
			$this->paintFooter($group);
		}
	}
	
}

?>