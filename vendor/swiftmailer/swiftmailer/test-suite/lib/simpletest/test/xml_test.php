<?php
// $Id: xml_test.php 1787 2008-04-26 20:35:39Z pp11 $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../xml.php');
Mock::generate('SimpleScorer');

if (! function_exists('xml_parser_create')) {
    SimpleTest::ignore('TestOfXmlStructureParsing');
    SimpleTest::ignore('TestOfXmlResultsParsing');
}

class TestOfNestingTags extends UnitTestCase {
    function testGroupSize() {
        $nesting = new NestingGroupTag(array('SIZE' => 2));
        $this->assertEqual($nesting->getSize(), 2);
    }
}

class TestOfXmlStructureParsing extends UnitTestCase {
    function testValidXml() {
        $listener = new MockSimpleScorer();
        $listener->expectNever('paintGroupStart');
        $listener->expectNever('paintGroupEnd');
        $listener->expectNever('paintCaseStart');
        $listener->expectNever('paintCaseEnd');
        $parser = new SimpleTestXmlParser($listener);
        $this->assertTrue($parser->parse("<?xml version=\"1.0\"?>\n"));
        $this->assertTrue($parser->parse("<run>\n"));
        $this->assertTrue($parser->parse("</run>\n"));
    }

    function testEmptyGroup() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintGroupStart', array('a_group', 7));
        $listener->expectOnce('paintGroupEnd', array('a_group'));
        $parser = new SimpleTestXmlParser($listener);
        $parser->parse("<?xml version=\"1.0\"?>\n");
        $parser->parse("<run>\n");
        $this->assertTrue($parser->parse("<group size=\"7\">\n"));
        $this->assertTrue($parser->parse("<name>a_group</name>\n"));
        $this->assertTrue($parser->parse("</group>\n"));
        $parser->parse("</run>\n");
    }

    function testEmptyCase() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintCaseStart', array('a_case'));
        $listener->expectOnce('paintCaseEnd', array('a_case'));
        $parser = new SimpleTestXmlParser($listener);
        $parser->parse("<?xml version=\"1.0\"?>\n");
        $parser->parse("<run>\n");
        $this->assertTrue($parser->parse("<case>\n"));
        $this->assertTrue($parser->parse("<name>a_case</name>\n"));
        $this->assertTrue($parser->parse("</case>\n"));
        $parser->parse("</run>\n");
    }

    function testEmptyMethod() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintCaseStart', array('a_case'));
        $listener->expectOnce('paintCaseEnd', array('a_case'));
        $listener->expectOnce('paintMethodStart', array('a_method'));
        $listener->expectOnce('paintMethodEnd', array('a_method'));
        $parser = new SimpleTestXmlParser($listener);
        $parser->parse("<?xml version=\"1.0\"?>\n");
        $parser->parse("<run>\n");
        $parser->parse("<case>\n");
        $parser->parse("<name>a_case</name>\n");
        $this->assertTrue($parser->parse("<test>\n"));
        $this->assertTrue($parser->parse("<name>a_method</name>\n"));
        $this->assertTrue($parser->parse("</test>\n"));
        $parser->parse("</case>\n");
        $parser->parse("</run>\n");
    }

    function testNestedGroup() {
        $listener = new MockSimpleScorer();
        $listener->expectAt(0, 'paintGroupStart', array('a_group', 7));
        $listener->expectAt(1, 'paintGroupStart', array('b_group', 3));
        $listener->expectCallCount('paintGroupStart', 2);
        $listener->expectAt(0, 'paintGroupEnd', array('b_group'));
        $listener->expectAt(1, 'paintGroupEnd', array('a_group'));
        $listener->expectCallCount('paintGroupEnd', 2);

        $parser = new SimpleTestXmlParser($listener);
        $parser->parse("<?xml version=\"1.0\"?>\n");
        $parser->parse("<run>\n");

        $this->assertTrue($parser->parse("<group size=\"7\">\n"));
        $this->assertTrue($parser->parse("<name>a_group</name>\n"));
        $this->assertTrue($parser->parse("<group size=\"3\">\n"));
        $this->assertTrue($parser->parse("<name>b_group</name>\n"));
        $this->assertTrue($parser->parse("</group>\n"));
        $this->assertTrue($parser->parse("</group>\n"));
        $parser->parse("</run>\n");
    }
}

class AnyOldSignal {
    public $stuff = true;
}

class TestOfXmlResultsParsing extends UnitTestCase {

    function sendValidStart(&$parser) {
        $parser->parse("<?xml version=\"1.0\"?>\n");
        $parser->parse("<run>\n");
        $parser->parse("<case>\n");
        $parser->parse("<name>a_case</name>\n");
        $parser->parse("<test>\n");
        $parser->parse("<name>a_method</name>\n");
    }

    function sendValidEnd(&$parser) {
        $parser->parse("</test>\n");
        $parser->parse("</case>\n");
        $parser->parse("</run>\n");
    }

    function testPass() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintPass', array('a_message'));
        $parser = new SimpleTestXmlParser($listener);
        $this->sendValidStart($parser);
        $this->assertTrue($parser->parse("<pass>a_message</pass>\n"));
        $this->sendValidEnd($parser);
    }

    function testFail() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintFail', array('a_message'));
        $parser = new SimpleTestXmlParser($listener);
        $this->sendValidStart($parser);
        $this->assertTrue($parser->parse("<fail>a_message</fail>\n"));
        $this->sendValidEnd($parser);
    }

    function testException() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintError', array('a_message'));
        $parser = new SimpleTestXmlParser($listener);
        $this->sendValidStart($parser);
        $this->assertTrue($parser->parse("<exception>a_message</exception>\n"));
        $this->sendValidEnd($parser);
    }

    function testSkip() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintSkip', array('a_message'));
        $parser = new SimpleTestXmlParser($listener);
        $this->sendValidStart($parser);
        $this->assertTrue($parser->parse("<skip>a_message</skip>\n"));
        $this->sendValidEnd($parser);
    }

    function testSignal() {
        $signal = new AnyOldSignal();
        $signal->stuff = "Hello";
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintSignal', array('a_signal', $signal));
        $parser = new SimpleTestXmlParser($listener);
        $this->sendValidStart($parser);
        $this->assertTrue($parser->parse(
                "<signal type=\"a_signal\"><![CDATA[" .
                serialize($signal) . "]]></signal>\n"));
        $this->sendValidEnd($parser);
    }

    function testMessage() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintMessage', array('a_message'));
        $parser = new SimpleTestXmlParser($listener);
        $this->sendValidStart($parser);
        $this->assertTrue($parser->parse("<message>a_message</message>\n"));
        $this->sendValidEnd($parser);
    }

    function testFormattedMessage() {
        $listener = new MockSimpleScorer();
        $listener->expectOnce('paintFormattedMessage', array("\na\tmessage\n"));
        $parser = new SimpleTestXmlParser($listener);
        $this->sendValidStart($parser);
        $this->assertTrue($parser->parse("<formatted><![CDATA[\na\tmessage\n]]></formatted>\n"));
        $this->sendValidEnd($parser);
    }
}
?>