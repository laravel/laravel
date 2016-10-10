<?php
/**
 *	extension file for SimpleTest
 *  @package        SimpleTest
 *  @subpackage     Extensions
 *	@version	$Id: jquery.php 1802 2008-09-08 10:43:58Z maetl_ $
 */
require_once dirname(__FILE__) . '/../treemap_reporter.php';

/**
 * outputs <ul> representing treemap of test report,
 * and attaches jQuery Treemap to render results.
 *
 *  @package        SimpleTest
 *  @subpackage     Extensions
 */
class JqueryTreemapReporter extends TreemapReporter {

	function _getCss() {
		$css = ".treemapView { color:white; }
				.treemapCell {background-color:green;font-size:10px;font-family:Arial;}
  				.treemapHead {cursor:pointer;background-color:#B34700}
				.treemapCell.selected, .treemapCell.selected .treemapCell.selected {background-color:#FFCC80}
  				.treemapCell.selected .treemapCell {background-color:#FF9900}
  				.treemapCell.selected .treemapHead {background-color:#B36B00}
  				.transfer {border:1px solid black}";
		return $css;
	}

	function paintResultsHeader() {
		$title = $this->_reporter->getTitle();
		echo "<html><head>";
		echo "<title>{$title}</title>";
		echo "<style type=\"text/css\">" . $this->_getCss() . "</style>";
		echo "<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-latest.js\"></script>";
		echo "<script type=\"text/javascript\" src=\"http://www.fbtools.com/jquery/treemap/treemap.js\"></script>";
		echo "<script type=\"text/javascript\">\n";
		echo "	window.onload = function() { jQuery(\"ul\").treemap(800,600,{getData:getDataFromUL}); };
					function getDataFromUL(el) {
					 var data = [];
					 jQuery(\"li\",el).each(function(){
					   var item = jQuery(this);
					   var row = [item.find(\"span.desc\").html(),item.find(\"span.data\").html()];
					   data.push(row);
					 });
					 return data;
					}";
		echo "</script></head>";
		echo "<body><ul>";
	}
	
	function paintRectangleStart($node) {
		echo "<li><span class=\"desc\">". basename($node->getDescription()) . "</span>";
		echo "<span class=\"data\">" . $node->getTotalSize() . "</span>";
	}
	
	function paintRectangleEnd() {}
	
	function paintResultsFooter() {
		echo "</ul></body>";
		echo "</html>";
	}
	
	function divideMapNodes($map) {
		foreach($map->getChildren() as $node) {
			if (!$node->isLeaf()) {
				$this->paintRectangleStart($node);
				$this->divideMapNodes($node);
			}
		}
	}

}

?>