<?php
/**
 *  base include file for SimpleTest
 *  @package    SimpleTest
 *  @subpackage UnitTester
 *  @version    $Id: xml.php 1787 2008-04-26 20:35:39Z pp11 $
 */

/**#@+
 *  include other SimpleTest class files
 */
require_once(dirname(__FILE__) . '/scorer.php');
/**#@-*/

/**
 *    Creates the XML needed for remote communication
 *    by SimpleTest.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class XmlReporter extends SimpleReporter {
    private $indent;
    private $namespace;

    /**
     *    Sets up indentation and namespace.
     *    @param string $namespace        Namespace to add to each tag.
     *    @param string $indent           Indenting to add on each nesting.
     *    @access public
     */
    function __construct($namespace = false, $indent = '  ') {
        parent::__construct();
        $this->namespace = ($namespace ? $namespace . ':' : '');
        $this->indent = $indent;
    }

    /**
     *    Calculates the pretty printing indent level
     *    from the current level of nesting.
     *    @param integer $offset  Extra indenting level.
     *    @return string          Leading space.
     *    @access protected
     */
    protected function getIndent($offset = 0) {
        return str_repeat(
                $this->indent,
                count($this->getTestList()) + $offset);
    }

    /**
     *    Converts character string to parsed XML
     *    entities string.
     *    @param string text        Unparsed character data.
     *    @return string            Parsed character data.
     *    @access public
     */
    function toParsedXml($text) {
        return str_replace(
                array('&', '<', '>', '"', '\''),
                array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;'),
                $text);
    }

    /**
     *    Paints the start of a group test.
     *    @param string $test_name   Name of test that is starting.
     *    @param integer $size       Number of test cases starting.
     *    @access public
     */
    function paintGroupStart($test_name, $size) {
        parent::paintGroupStart($test_name, $size);
        print $this->getIndent();
        print "<" . $this->namespace . "group size=\"$size\">\n";
        print $this->getIndent(1);
        print "<" . $this->namespace . "name>" .
                $this->toParsedXml($test_name) .
                "</" . $this->namespace . "name>\n";
    }

    /**
     *    Paints the end of a group test.
     *    @param string $test_name   Name of test that is ending.
     *    @access public
     */
    function paintGroupEnd($test_name) {
        print $this->getIndent();
        print "</" . $this->namespace . "group>\n";
        parent::paintGroupEnd($test_name);
    }

    /**
     *    Paints the start of a test case.
     *    @param string $test_name   Name of test that is starting.
     *    @access public
     */
    function paintCaseStart($test_name) {
        parent::paintCaseStart($test_name);
        print $this->getIndent();
        print "<" . $this->namespace . "case>\n";
        print $this->getIndent(1);
        print "<" . $this->namespace . "name>" .
                $this->toParsedXml($test_name) .
                "</" . $this->namespace . "name>\n";
    }

    /**
     *    Paints the end of a test case.
     *    @param string $test_name   Name of test that is ending.
     *    @access public
     */
    function paintCaseEnd($test_name) {
        print $this->getIndent();
        print "</" . $this->namespace . "case>\n";
        parent::paintCaseEnd($test_name);
    }

    /**
     *    Paints the start of a test method.
     *    @param string $test_name   Name of test that is starting.
     *    @access public
     */
    function paintMethodStart($test_name) {
        parent::paintMethodStart($test_name);
        print $this->getIndent();
        print "<" . $this->namespace . "test>\n";
        print $this->getIndent(1);
        print "<" . $this->namespace . "name>" .
                $this->toParsedXml($test_name) .
                "</" . $this->namespace . "name>\n";
    }

    /**
     *    Paints the end of a test method.
     *    @param string $test_name   Name of test that is ending.
     *    @param integer $progress   Number of test cases ending.
     *    @access public
     */
    function paintMethodEnd($test_name) {
        print $this->getIndent();
        print "</" . $this->namespace . "test>\n";
        parent::paintMethodEnd($test_name);
    }

    /**
     *    Paints pass as XML.
     *    @param string $message        Message to encode.
     *    @access public
     */
    function paintPass($message) {
        parent::paintPass($message);
        print $this->getIndent(1);
        print "<" . $this->namespace . "pass>";
        print $this->toParsedXml($message);
        print "</" . $this->namespace . "pass>\n";
    }

    /**
     *    Paints failure as XML.
     *    @param string $message        Message to encode.
     *    @access public
     */
    function paintFail($message) {
        parent::paintFail($message);
        print $this->getIndent(1);
        print "<" . $this->namespace . "fail>";
        print $this->toParsedXml($message);
        print "</" . $this->namespace . "fail>\n";
    }

    /**
     *    Paints error as XML.
     *    @param string $message        Message to encode.
     *    @access public
     */
    function paintError($message) {
        parent::paintError($message);
        print $this->getIndent(1);
        print "<" . $this->namespace . "exception>";
        print $this->toParsedXml($message);
        print "</" . $this->namespace . "exception>\n";
    }

    /**
     *    Paints exception as XML.
     *    @param Exception $exception    Exception to encode.
     *    @access public
     */
    function paintException($exception) {
        parent::paintException($exception);
        print $this->getIndent(1);
        print "<" . $this->namespace . "exception>";
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        print $this->toParsedXml($message);
        print "</" . $this->namespace . "exception>\n";
    }

    /**
     *    Paints the skipping message and tag.
     *    @param string $message        Text to display in skip tag.
     *    @access public
     */
    function paintSkip($message) {
        parent::paintSkip($message);
        print $this->getIndent(1);
        print "<" . $this->namespace . "skip>";
        print $this->toParsedXml($message);
        print "</" . $this->namespace . "skip>\n";
    }

    /**
     *    Paints a simple supplementary message.
     *    @param string $message        Text to display.
     *    @access public
     */
    function paintMessage($message) {
        parent::paintMessage($message);
        print $this->getIndent(1);
        print "<" . $this->namespace . "message>";
        print $this->toParsedXml($message);
        print "</" . $this->namespace . "message>\n";
    }

    /**
     *    Paints a formatted ASCII message such as a
     *    privateiable dump.
     *    @param string $message        Text to display.
     *    @access public
     */
    function paintFormattedMessage($message) {
        parent::paintFormattedMessage($message);
        print $this->getIndent(1);
        print "<" . $this->namespace . "formatted>";
        print "<![CDATA[$message]]>";
        print "</" . $this->namespace . "formatted>\n";
    }

    /**
     *    Serialises the event object.
     *    @param string $type        Event type as text.
     *    @param mixed $payload      Message or object.
     *    @access public
     */
    function paintSignal($type, $payload) {
        parent::paintSignal($type, $payload);
        print $this->getIndent(1);
        print "<" . $this->namespace . "signal type=\"$type\">";
        print "<![CDATA[" . serialize($payload) . "]]>";
        print "</" . $this->namespace . "signal>\n";
    }

    /**
     *    Paints the test document header.
     *    @param string $test_name     First test top level
     *                                 to start.
     *    @access public
     *    @abstract
     */
    function paintHeader($test_name) {
        if (! SimpleReporter::inCli()) {
            header('Content-type: text/xml');
        }
        print "<?xml version=\"1.0\"";
        if ($this->namespace) {
            print " xmlns:" . $this->namespace .
                    "=\"www.lastcraft.com/SimpleTest/Beta3/Report\"";
        }
        print "?>\n";
        print "<" . $this->namespace . "run>\n";
    }

    /**
     *    Paints the test document footer.
     *    @param string $test_name        The top level test.
     *    @access public
     *    @abstract
     */
    function paintFooter($test_name) {
        print "</" . $this->namespace . "run>\n";
    }
}

/**
 *    Accumulator for incoming tag. Holds the
 *    incoming test structure information for
 *    later dispatch to the reporter.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NestingXmlTag {
    private $name;
    private $attributes;

    /**
     *    Sets the basic test information except
     *    the name.
     *    @param hash $attributes   Name value pairs.
     *    @access public
     */
    function NestingXmlTag($attributes) {
        $this->name = false;
        $this->attributes = $attributes;
    }

    /**
     *    Sets the test case/method name.
     *    @param string $name        Name of test.
     *    @access public
     */
    function setName($name) {
        $this->name = $name;
    }

    /**
     *    Accessor for name.
     *    @return string        Name of test.
     *    @access public
     */
    function getName() {
        return $this->name;
    }

    /**
     *    Accessor for attributes.
     *    @return hash        All attributes.
     *    @access protected
     */
    protected function getAttributes() {
        return $this->attributes;
    }
}

/**
 *    Accumulator for incoming method tag. Holds the
 *    incoming test structure information for
 *    later dispatch to the reporter.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NestingMethodTag extends NestingXmlTag {

    /**
     *    Sets the basic test information except
     *    the name.
     *    @param hash $attributes   Name value pairs.
     *    @access public
     */
    function NestingMethodTag($attributes) {
        $this->NestingXmlTag($attributes);
    }

    /**
     *    Signals the appropriate start event on the
     *    listener.
     *    @param SimpleReporter $listener    Target for events.
     *    @access public
     */
    function paintStart(&$listener) {
        $listener->paintMethodStart($this->getName());
    }

    /**
     *    Signals the appropriate end event on the
     *    listener.
     *    @param SimpleReporter $listener    Target for events.
     *    @access public
     */
    function paintEnd(&$listener) {
        $listener->paintMethodEnd($this->getName());
    }
}

/**
 *    Accumulator for incoming case tag. Holds the
 *    incoming test structure information for
 *    later dispatch to the reporter.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NestingCaseTag extends NestingXmlTag {

    /**
     *    Sets the basic test information except
     *    the name.
     *    @param hash $attributes   Name value pairs.
     *    @access public
     */
    function NestingCaseTag($attributes) {
        $this->NestingXmlTag($attributes);
    }

    /**
     *    Signals the appropriate start event on the
     *    listener.
     *    @param SimpleReporter $listener    Target for events.
     *    @access public
     */
    function paintStart(&$listener) {
        $listener->paintCaseStart($this->getName());
    }

    /**
     *    Signals the appropriate end event on the
     *    listener.
     *    @param SimpleReporter $listener    Target for events.
     *    @access public
     */
    function paintEnd(&$listener) {
        $listener->paintCaseEnd($this->getName());
    }
}

/**
 *    Accumulator for incoming group tag. Holds the
 *    incoming test structure information for
 *    later dispatch to the reporter.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class NestingGroupTag extends NestingXmlTag {

    /**
     *    Sets the basic test information except
     *    the name.
     *    @param hash $attributes   Name value pairs.
     *    @access public
     */
    function NestingGroupTag($attributes) {
        $this->NestingXmlTag($attributes);
    }

    /**
     *    Signals the appropriate start event on the
     *    listener.
     *    @param SimpleReporter $listener    Target for events.
     *    @access public
     */
    function paintStart(&$listener) {
        $listener->paintGroupStart($this->getName(), $this->getSize());
    }

    /**
     *    Signals the appropriate end event on the
     *    listener.
     *    @param SimpleReporter $listener    Target for events.
     *    @access public
     */
    function paintEnd(&$listener) {
        $listener->paintGroupEnd($this->getName());
    }

    /**
     *    The size in the attributes.
     *    @return integer     Value of size attribute or zero.
     *    @access public
     */
    function getSize() {
        $attributes = $this->getAttributes();
        if (isset($attributes['SIZE'])) {
            return (integer)$attributes['SIZE'];
        }
        return 0;
    }
}

/**
 *    Parser for importing the output of the XmlReporter.
 *    Dispatches that output to another reporter.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class SimpleTestXmlParser {
    private $listener;
    private $expat;
    private $tag_stack;
    private $in_content_tag;
    private $content;
    private $attributes;

    /**
     *    Loads a listener with the SimpleReporter
     *    interface.
     *    @param SimpleReporter $listener   Listener of tag events.
     *    @access public
     */
    function SimpleTestXmlParser(&$listener) {
        $this->listener = &$listener;
        $this->expat = &$this->createParser();
        $this->tag_stack = array();
        $this->in_content_tag = false;
        $this->content = '';
        $this->attributes = array();
    }

    /**
     *    Parses a block of XML sending the results to
     *    the listener.
     *    @param string $chunk        Block of text to read.
     *    @return boolean             True if valid XML.
     *    @access public
     */
    function parse($chunk) {
        if (! xml_parse($this->expat, $chunk)) {
            trigger_error('XML parse error with ' .
                    xml_error_string(xml_get_error_code($this->expat)));
            return false;
        }
        return true;
    }

    /**
     *    Sets up expat as the XML parser.
     *    @return resource        Expat handle.
     *    @access protected
     */
    protected function &createParser() {
        $expat = xml_parser_create();
        xml_set_object($expat, $this);
        xml_set_element_handler($expat, 'startElement', 'endElement');
        xml_set_character_data_handler($expat, 'addContent');
        xml_set_default_handler($expat, 'defaultContent');
        return $expat;
    }

    /**
     *    Opens a new test nesting level.
     *    @return NestedXmlTag     The group, case or method tag
     *                             to start.
     *    @access private
     */
    protected function pushNestingTag($nested) {
        array_unshift($this->tag_stack, $nested);
    }

    /**
     *    Accessor for current test structure tag.
     *    @return NestedXmlTag     The group, case or method tag
     *                             being parsed.
     *    @access private
     */
    protected function &getCurrentNestingTag() {
        return $this->tag_stack[0];
    }

    /**
     *    Ends a nesting tag.
     *    @return NestedXmlTag     The group, case or method tag
     *                             just finished.
     *    @access private
     */
    protected function popNestingTag() {
        return array_shift($this->tag_stack);
    }

    /**
     *    Test if tag is a leaf node with only text content.
     *    @param string $tag        XML tag name.
     *    @return @boolean          True if leaf, false if nesting.
     *    @private
     */
    protected function isLeaf($tag) {
        return in_array($tag, array(
                'NAME', 'PASS', 'FAIL', 'EXCEPTION', 'SKIP', 'MESSAGE', 'FORMATTED', 'SIGNAL'));
    }

    /**
     *    Handler for start of event element.
     *    @param resource $expat     Parser handle.
     *    @param string $tag         Element name.
     *    @param hash $attributes    Name value pairs.
     *                               Attributes without content
     *                               are marked as true.
     *    @access protected
     */
    protected function startElement($expat, $tag, $attributes) {
        $this->attributes = $attributes;
        if ($tag == 'GROUP') {
            $this->pushNestingTag(new NestingGroupTag($attributes));
        } elseif ($tag == 'CASE') {
            $this->pushNestingTag(new NestingCaseTag($attributes));
        } elseif ($tag == 'TEST') {
            $this->pushNestingTag(new NestingMethodTag($attributes));
        } elseif ($this->isLeaf($tag)) {
            $this->in_content_tag = true;
            $this->content = '';
        }
    }

    /**
     *    End of element event.
     *    @param resource $expat     Parser handle.
     *    @param string $tag         Element name.
     *    @access protected
     */
    protected function endElement($expat, $tag) {
        $this->in_content_tag = false;
        if (in_array($tag, array('GROUP', 'CASE', 'TEST'))) {
            $nesting_tag = $this->popNestingTag();
            $nesting_tag->paintEnd($this->listener);
        } elseif ($tag == 'NAME') {
            $nesting_tag = &$this->getCurrentNestingTag();
            $nesting_tag->setName($this->content);
            $nesting_tag->paintStart($this->listener);
        } elseif ($tag == 'PASS') {
            $this->listener->paintPass($this->content);
        } elseif ($tag == 'FAIL') {
            $this->listener->paintFail($this->content);
        } elseif ($tag == 'EXCEPTION') {
            $this->listener->paintError($this->content);
        } elseif ($tag == 'SKIP') {
            $this->listener->paintSkip($this->content);
        } elseif ($tag == 'SIGNAL') {
            $this->listener->paintSignal(
                    $this->attributes['TYPE'],
                    unserialize($this->content));
        } elseif ($tag == 'MESSAGE') {
            $this->listener->paintMessage($this->content);
        } elseif ($tag == 'FORMATTED') {
            $this->listener->paintFormattedMessage($this->content);
        }
    }

    /**
     *    Content between start and end elements.
     *    @param resource $expat     Parser handle.
     *    @param string $text        Usually output messages.
     *    @access protected
     */
    protected function addContent($expat, $text) {
        if ($this->in_content_tag) {
            $this->content .= $text;
        }
        return true;
    }

    /**
     *    XML and Doctype handler. Discards all such content.
     *    @param resource $expat     Parser handle.
     *    @param string $default     Text of default content.
     *    @access protected
     */
    protected function defaultContent($expat, $default) {
    }
}
?>
