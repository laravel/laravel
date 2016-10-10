<?php
// $Id: frames_test.php 1787 2008-04-26 20:35:39Z pp11 $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../tag.php');
require_once(dirname(__FILE__) . '/../page.php');
require_once(dirname(__FILE__) . '/../frames.php');
Mock::generate('SimplePage');
Mock::generate('SimpleForm');

class TestOfFrameset extends UnitTestCase {

    function testTitleReadFromFramesetPage() {
        $page = new MockSimplePage();
        $page->setReturnValue('getTitle', 'This page');
        $frameset = new SimpleFrameset($page);
        $this->assertEqual($frameset->getTitle(), 'This page');
    }

    function TestHeadersReadFromFramesetByDefault() {
        $page = new MockSimplePage();
        $page->setReturnValue('getHeaders', 'Header: content');
        $page->setReturnValue('getMimeType', 'text/xml');
        $page->setReturnValue('getResponseCode', 401);
        $page->setReturnValue('getTransportError', 'Could not parse headers');
        $page->setReturnValue('getAuthentication', 'Basic');
        $page->setReturnValue('getRealm', 'Safe place');

        $frameset = new SimpleFrameset($page);

        $this->assertIdentical($frameset->getHeaders(), 'Header: content');
        $this->assertIdentical($frameset->getMimeType(), 'text/xml');
        $this->assertIdentical($frameset->getResponseCode(), 401);
        $this->assertIdentical($frameset->getTransportError(), 'Could not parse headers');
        $this->assertIdentical($frameset->getAuthentication(), 'Basic');
        $this->assertIdentical($frameset->getRealm(), 'Safe place');
    }

    function testEmptyFramesetHasNoContent() {
        $page = new MockSimplePage();
        $page->setReturnValue('getRaw', 'This content');
        $frameset = new SimpleFrameset($page);
        $this->assertEqual($frameset->getRaw(), '');
    }

    function testRawContentIsFromOnlyFrame() {
        $page = new MockSimplePage();
        $page->expectNever('getRaw');

        $frame = new MockSimplePage();
        $frame->setReturnValue('getRaw', 'Stuff');

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame);
        $this->assertEqual($frameset->getRaw(), 'Stuff');
    }

    function testRawContentIsFromAllFrames() {
        $page = new MockSimplePage();
        $page->expectNever('getRaw');

        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getRaw', 'Stuff1');

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getRaw', 'Stuff2');

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1);
        $frameset->addFrame($frame2);
        $this->assertEqual($frameset->getRaw(), 'Stuff1Stuff2');
    }

    function testTextContentIsFromOnlyFrame() {
        $page = new MockSimplePage();
        $page->expectNever('getText');

        $frame = new MockSimplePage();
        $frame->setReturnValue('getText', 'Stuff');

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame);
        $this->assertEqual($frameset->getText(), 'Stuff');
    }

    function testTextContentIsFromAllFrames() {
        $page = new MockSimplePage();
        $page->expectNever('getText');

        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getText', 'Stuff1');

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getText', 'Stuff2');

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1);
        $frameset->addFrame($frame2);
        $this->assertEqual($frameset->getText(), 'Stuff1 Stuff2');
    }

    function testFieldFoundIsFirstInFramelist() {
        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getField', null);
        $frame1->expectOnce('getField', array(new SimpleByName('a')));

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getField', 'A');
        $frame2->expectOnce('getField', array(new SimpleByName('a')));

        $frame3 = new MockSimplePage();
        $frame3->expectNever('getField');

        $page = new MockSimplePage();
        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1);
        $frameset->addFrame($frame2);
        $frameset->addFrame($frame3);
        $this->assertIdentical($frameset->getField(new SimpleByName('a')), 'A');
    }

    function testFrameReplacementByIndex() {
        $page = new MockSimplePage();
        $page->expectNever('getRaw');

        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getRaw', 'Stuff1');

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getRaw', 'Stuff2');

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1);
        $frameset->setFrame(array(1), $frame2);
        $this->assertEqual($frameset->getRaw(), 'Stuff2');
    }

    function testFrameReplacementByName() {
        $page = new MockSimplePage();
        $page->expectNever('getRaw');

        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getRaw', 'Stuff1');

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getRaw', 'Stuff2');

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1, 'a');
        $frameset->setFrame(array('a'), $frame2);
        $this->assertEqual($frameset->getRaw(), 'Stuff2');
    }
}

class TestOfFrameNavigation extends UnitTestCase {

    function testStartsWithoutFrameFocus() {
        $page = new MockSimplePage();
        $frameset = new SimpleFrameset($page);
        $frameset->addFrame(new MockSimplePage());
        $this->assertFalse($frameset->getFrameFocus());
    }

    function testCanFocusOnSingleFrame() {
        $page = new MockSimplePage();
        $page->expectNever('getRaw');

        $frame = new MockSimplePage();
        $frame->setReturnValue('getFrameFocus', array());
        $frame->setReturnValue('getRaw', 'Stuff');

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame);

        $this->assertFalse($frameset->setFrameFocusByIndex(0));
        $this->assertTrue($frameset->setFrameFocusByIndex(1));
        $this->assertEqual($frameset->getRaw(), 'Stuff');
        $this->assertFalse($frameset->setFrameFocusByIndex(2));
        $this->assertIdentical($frameset->getFrameFocus(), array(1));
    }

    function testContentComesFromFrameInFocus() {
        $page = new MockSimplePage();

        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getRaw', 'Stuff1');
        $frame1->setReturnValue('getFrameFocus', array());

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getRaw', 'Stuff2');
        $frame2->setReturnValue('getFrameFocus', array());

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1);
        $frameset->addFrame($frame2);

        $this->assertTrue($frameset->setFrameFocusByIndex(1));
        $this->assertEqual($frameset->getFrameFocus(), array(1));
        $this->assertEqual($frameset->getRaw(), 'Stuff1');

        $this->assertTrue($frameset->setFrameFocusByIndex(2));
        $this->assertEqual($frameset->getFrameFocus(), array(2));
        $this->assertEqual($frameset->getRaw(), 'Stuff2');

        $this->assertFalse($frameset->setFrameFocusByIndex(3));
        $this->assertEqual($frameset->getFrameFocus(), array(2));

        $frameset->clearFrameFocus();
        $this->assertEqual($frameset->getRaw(), 'Stuff1Stuff2');
    }

    function testCanFocusByName() {
        $page = new MockSimplePage();

        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getRaw', 'Stuff1');
        $frame1->setReturnValue('getFrameFocus', array());

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getRaw', 'Stuff2');
        $frame2->setReturnValue('getFrameFocus', array());

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1, 'A');
        $frameset->addFrame($frame2, 'B');

        $this->assertTrue($frameset->setFrameFocus('A'));
        $this->assertEqual($frameset->getFrameFocus(), array('A'));
        $this->assertEqual($frameset->getRaw(), 'Stuff1');

        $this->assertTrue($frameset->setFrameFocusByIndex(2));
        $this->assertEqual($frameset->getFrameFocus(), array('B'));
        $this->assertEqual($frameset->getRaw(), 'Stuff2');

        $this->assertFalse($frameset->setFrameFocus('z'));

        $frameset->clearFrameFocus();
        $this->assertEqual($frameset->getRaw(), 'Stuff1Stuff2');
    }
}

class TestOfFramesetPageInterface extends UnitTestCase {
    private $page_interface;
    private $frameset_interface;

    function __construct() {
        parent::__construct();
        $this->page_interface = $this->getPageMethods();
        $this->frameset_interface = $this->getFramesetMethods();
    }

    function assertListInAnyOrder($list, $expected) {
        sort($list);
        sort($expected);
        $this->assertEqual($list, $expected);
    }

    private function getPageMethods() {
        $methods = array();
        foreach (get_class_methods('SimplePage') as $method) {
            if (strtolower($method) == strtolower('SimplePage')) {
                continue;
            }
            if (strtolower($method) == strtolower('getFrameset')) {
                continue;
            }
            if (strncmp($method, '_', 1) == 0) {
                continue;
            }
            if (strncmp($method, 'accept', 6) == 0) {
                continue;
            }
            $methods[] = $method;
        }
        return $methods;
    }

    private function getFramesetMethods() {
        $methods = array();
        foreach (get_class_methods('SimpleFrameset') as $method) {
            if (strtolower($method) == strtolower('SimpleFrameset')) {
                continue;
            }
            if (strncmp($method, '_', 1) == 0) {
                continue;
            }
            if (strncmp($method, 'add', 3) == 0) {
                continue;
            }
            $methods[] = $method;
        }
        return $methods;
    }

    function testFramsetHasPageInterface() {
        $difference = array();
        foreach ($this->page_interface as $method) {
            if (! in_array($method, $this->frameset_interface)) {
                $this->fail("No [$method] in Frameset class");
                return;
            }
        }
        $this->pass('Frameset covers Page interface');
    }

    function testHeadersReadFromFrameIfInFocus() {
        $frame = new MockSimplePage();
        $frame->setReturnValue('getUrl', new SimpleUrl('http://localhost/stuff'));

        $frame->setReturnValue('getRequest', 'POST stuff');
        $frame->setReturnValue('getMethod', 'POST');
        $frame->setReturnValue('getRequestData', array('a' => 'A'));
        $frame->setReturnValue('getHeaders', 'Header: content');
        $frame->setReturnValue('getMimeType', 'text/xml');
        $frame->setReturnValue('getResponseCode', 401);
        $frame->setReturnValue('getTransportError', 'Could not parse headers');
        $frame->setReturnValue('getAuthentication', 'Basic');
        $frame->setReturnValue('getRealm', 'Safe place');

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame);
        $frameset->setFrameFocusByIndex(1);

        $url = new SimpleUrl('http://localhost/stuff');
        $url->setTarget(1);
        $this->assertIdentical($frameset->getUrl(), $url);

        $this->assertIdentical($frameset->getRequest(), 'POST stuff');
        $this->assertIdentical($frameset->getMethod(), 'POST');
        $this->assertIdentical($frameset->getRequestData(), array('a' => 'A'));
        $this->assertIdentical($frameset->getHeaders(), 'Header: content');
        $this->assertIdentical($frameset->getMimeType(), 'text/xml');
        $this->assertIdentical($frameset->getResponseCode(), 401);
        $this->assertIdentical($frameset->getTransportError(), 'Could not parse headers');
        $this->assertIdentical($frameset->getAuthentication(), 'Basic');
        $this->assertIdentical($frameset->getRealm(), 'Safe place');
    }

    function testUrlsComeFromBothFrames() {
        $page = new MockSimplePage();
        $page->expectNever('getUrls');

        $frame1 = new MockSimplePage();
        $frame1->setReturnValue(
                'getUrls',
                array('http://www.lastcraft.com/', 'http://myserver/'));

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue(
                'getUrls',
                array('http://www.lastcraft.com/', 'http://test/'));

        $frameset = new SimpleFrameset($page);
        $frameset->addFrame($frame1);
        $frameset->addFrame($frame2);
        $this->assertListInAnyOrder(
                $frameset->getUrls(),
                array('http://www.lastcraft.com/', 'http://myserver/', 'http://test/'));
    }

    function testLabelledUrlsComeFromBothFrames() {
        $frame1 = new MockSimplePage();
        $frame1->setReturnValue(
                'getUrlsByLabel',
                array(new SimpleUrl('goodbye.php')),
                array('a'));

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue(
                'getUrlsByLabel',
                array(new SimpleUrl('hello.php')),
                array('a'));

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame1);
        $frameset->addFrame($frame2, 'Two');

        $expected1 = new SimpleUrl('goodbye.php');
        $expected1->setTarget(1);
        $expected2 = new SimpleUrl('hello.php');
        $expected2->setTarget('Two');
        $this->assertEqual(
                $frameset->getUrlsByLabel('a'),
                array($expected1, $expected2));
    }

    function testUrlByIdComesFromFirstFrameToRespond() {
        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getUrlById', new SimpleUrl('four.php'), array(4));
        $frame1->setReturnValue('getUrlById', false, array(5));

        $frame2 = new MockSimplePage();
        $frame2->setReturnValue('getUrlById', false, array(4));
        $frame2->setReturnValue('getUrlById', new SimpleUrl('five.php'), array(5));

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame1);
        $frameset->addFrame($frame2);

        $four = new SimpleUrl('four.php');
        $four->setTarget(1);
        $this->assertEqual($frameset->getUrlById(4), $four);
        $five = new SimpleUrl('five.php');
        $five->setTarget(2);
        $this->assertEqual($frameset->getUrlById(5), $five);
    }

    function testReadUrlsFromFrameInFocus() {
        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getUrls', array('a'));
        $frame1->setReturnValue('getUrlsByLabel', array(new SimpleUrl('l')));
        $frame1->setReturnValue('getUrlById', new SimpleUrl('i'));

        $frame2 = new MockSimplePage();
        $frame2->expectNever('getUrls');
        $frame2->expectNever('getUrlsByLabel');
        $frame2->expectNever('getUrlById');

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame1, 'A');
        $frameset->addFrame($frame2, 'B');
        $frameset->setFrameFocus('A');

        $this->assertIdentical($frameset->getUrls(), array('a'));
        $expected = new SimpleUrl('l');
        $expected->setTarget('A');
        $this->assertIdentical($frameset->getUrlsByLabel('label'), array($expected));
        $expected = new SimpleUrl('i');
        $expected->setTarget('A');
        $this->assertIdentical($frameset->getUrlById(99), $expected);
    }

    function testReadFrameTaggedUrlsFromFrameInFocus() {
        $frame = new MockSimplePage();

        $by_label = new SimpleUrl('l');
        $by_label->setTarget('L');
        $frame->setReturnValue('getUrlsByLabel', array($by_label));

        $by_id = new SimpleUrl('i');
        $by_id->setTarget('I');
        $frame->setReturnValue('getUrlById', $by_id);

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame, 'A');
        $frameset->setFrameFocus('A');

        $this->assertIdentical($frameset->getUrlsByLabel('label'), array($by_label));
        $this->assertIdentical($frameset->getUrlById(99), $by_id);
    }

    function testFindingFormsById() {
        $frame = new MockSimplePage();
        $form = new MockSimpleForm();
        $frame->returns('getFormById', $form, array('a'));

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame(new MockSimplePage(), 'A');
        $frameset->addFrame($frame, 'B');
        $this->assertSame($frameset->getFormById('a'), $form);

        $frameset->setFrameFocus('A');
        $this->assertNull($frameset->getFormById('a'));

        $frameset->setFrameFocus('B');
        $this->assertSame($frameset->getFormById('a'), $form);
    }

    function testFindingFormsBySubmit() {
        $frame = new MockSimplePage();
        $form = new MockSimpleForm();
        $frame->returns(
                'getFormBySubmit',
                $form,
                array(new SimpleByLabel('a')));

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame(new MockSimplePage(), 'A');
        $frameset->addFrame($frame, 'B');
        $this->assertSame($frameset->getFormBySubmit(new SimpleByLabel('a')), $form);

        $frameset->setFrameFocus('A');
        $this->assertNull($frameset->getFormBySubmit(new SimpleByLabel('a')));

        $frameset->setFrameFocus('B');
        $this->assertSame($frameset->getFormBySubmit(new SimpleByLabel('a')), $form);
    }

    function testFindingFormsByImage() {
        $frame = new MockSimplePage();
        $form = new MockSimpleForm();
        $frame->returns(
                'getFormByImage',
                $form,
                array(new SimpleByLabel('a')));

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame(new MockSimplePage(), 'A');
        $frameset->addFrame($frame, 'B');
        $this->assertSame($frameset->getFormByImage(new SimpleByLabel('a')), $form);

        $frameset->setFrameFocus('A');
        $this->assertNull($frameset->getFormByImage(new SimpleByLabel('a')));

        $frameset->setFrameFocus('B');
        $this->assertSame($frameset->getFormByImage(new SimpleByLabel('a')), $form);
    }

    function testSettingAllFrameFieldsWhenNoFrameFocus() {
        $frame1 = new MockSimplePage();
        $frame1->expectOnce('setField', array(new SimpleById(22), 'A'));

        $frame2 = new MockSimplePage();
        $frame2->expectOnce('setField', array(new SimpleById(22), 'A'));

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame1, 'A');
        $frameset->addFrame($frame2, 'B');
        $frameset->setField(new SimpleById(22), 'A');
    }

    function testOnlySettingFieldFromFocusedFrame() {
        $frame1 = new MockSimplePage();
        $frame1->expectOnce('setField', array(new SimpleByLabelOrName('a'), 'A'));

        $frame2 = new MockSimplePage();
        $frame2->expectNever('setField');

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame1, 'A');
        $frameset->addFrame($frame2, 'B');
        $frameset->setFrameFocus('A');
        $frameset->setField(new SimpleByLabelOrName('a'), 'A');
    }

    function testOnlyGettingFieldFromFocusedFrame() {
        $frame1 = new MockSimplePage();
        $frame1->setReturnValue('getField', 'f', array(new SimpleByName('a')));

        $frame2 = new MockSimplePage();
        $frame2->expectNever('getField');

        $frameset = new SimpleFrameset(new MockSimplePage());
        $frameset->addFrame($frame1, 'A');
        $frameset->addFrame($frame2, 'B');
        $frameset->setFrameFocus('A');
        $this->assertIdentical($frameset->getField(new SimpleByName('a')), 'f');
    }
}
?>