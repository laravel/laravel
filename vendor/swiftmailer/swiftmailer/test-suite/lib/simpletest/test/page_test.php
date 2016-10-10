<?php
// $Id: page_test.php 1782 2008-04-25 17:09:06Z pp11 $
require_once(dirname(__FILE__) . '/../autorun.php');
require_once(dirname(__FILE__) . '/../expectation.php');
require_once(dirname(__FILE__) . '/../http.php');
require_once(dirname(__FILE__) . '/../page.php');
require_once(dirname(__FILE__) . '/../parser.php');
Mock::generate('SimpleHtmlSaxParser');
Mock::generate('SimplePage');
Mock::generate('SimpleHttpResponse');
Mock::generate('SimpleHttpHeaders');
Mock::generate('SimplePageBuilder');
Mock::generatePartial(
        'SimplePageBuilder',
        'PartialSimplePageBuilder',
        array('createPage', 'createParser'));

class TestOfPageBuilder extends UnitTestCase {

    function testLink() {
        $tag = new SimpleAnchorTag(array('href' => 'http://somewhere'));
        $tag->addContent('Label');

        $page = new MockSimplePage();
        $page->expect('acceptTag', array($tag));
        $page->expectCallCount('acceptTag', 1);

        $builder = new PartialSimplePageBuilder();
        $builder->returns('createPage', $page);
        $builder->returns('createParser', new MockSimpleHtmlSaxParser());
        $builder->__construct();

        $builder->parse(new MockSimpleHttpResponse());
        $this->assertTrue($builder->startElement(
                'a',
                array('href' => 'http://somewhere')));
        $this->assertTrue($builder->addContent('Label'));
        $this->assertTrue($builder->endElement('a'));
    }

    function testLinkWithId() {
        $tag = new SimpleAnchorTag(array("href" => "http://somewhere", "id" => "44"));
        $tag->addContent("Label");

        $page = new MockSimplePage();
        $page->expect("acceptTag", array($tag));
        $page->expectCallCount("acceptTag", 1);

        $builder = new PartialSimplePageBuilder();
        $builder->returns('createPage', $page);
        $builder->returns('createParser', new MockSimpleHtmlSaxParser());
        $builder->__construct();

        $builder->parse(new MockSimpleHttpResponse());
        $this->assertTrue($builder->startElement(
                "a",
                array("href" => "http://somewhere", "id" => "44")));
        $this->assertTrue($builder->addContent("Label"));
        $this->assertTrue($builder->endElement("a"));
    }

    function testLinkExtraction() {
        $tag = new SimpleAnchorTag(array("href" => "http://somewhere"));
        $tag->addContent("Label");

        $page = new MockSimplePage();
        $page->expect("acceptTag", array($tag));
        $page->expectCallCount("acceptTag", 1);

        $builder = new PartialSimplePageBuilder();
        $builder->returns('createPage', $page);
        $builder->returns('createParser', new MockSimpleHtmlSaxParser());
        $builder->__construct();

        $builder->parse(new MockSimpleHttpResponse());
        $this->assertTrue($builder->addContent("Starting stuff"));
        $this->assertTrue($builder->startElement(
                "a",
                array("href" => "http://somewhere")));
        $this->assertTrue($builder->addContent("Label"));
        $this->assertTrue($builder->endElement("a"));
        $this->assertTrue($builder->addContent("Trailing stuff"));
    }

    function testMultipleLinks() {
        $a1 = new SimpleAnchorTag(array("href" => "http://somewhere"));
        $a1->addContent("1");

        $a2 = new SimpleAnchorTag(array("href" => "http://elsewhere"));
        $a2->addContent("2");

        $page = new MockSimplePage();
        $page->expectAt(0, "acceptTag", array($a1));
        $page->expectAt(1, "acceptTag", array($a2));
        $page->expectCallCount("acceptTag", 2);

        $builder = new PartialSimplePageBuilder();
        $builder->returns('createPage', $page);
        $builder->returns('createParser', new MockSimpleHtmlSaxParser());
        $builder->__construct();

        $builder->parse(new MockSimpleHttpResponse());
        $builder->startElement("a", array("href" => "http://somewhere"));
        $builder->addContent("1");
        $builder->endElement("a");
        $builder->addContent("Padding");
        $builder->startElement("a", array("href" => "http://elsewhere"));
        $builder->addContent("2");
        $builder->endElement("a");
    }

    function testTitle() {
        $tag = new SimpleTitleTag(array());
        $tag->addContent("HereThere");

        $page = new MockSimplePage();
        $page->expect("acceptTag", array($tag));
        $page->expectCallCount("acceptTag", 1);

        $builder = new PartialSimplePageBuilder();
        $builder->returns('createPage', $page);
        $builder->returns('createParser', new MockSimpleHtmlSaxParser());
        $builder->__construct();

        $builder->parse(new MockSimpleHttpResponse());
        $builder->startElement("title", array());
        $builder->addContent("Here");
        $builder->addContent("There");
        $builder->endElement("title");
    }

    function testForm() {
        $page = new MockSimplePage();
        $page->expectOnce("acceptFormStart", array(new SimpleFormTag(array())));
        $page->expectOnce("acceptFormEnd", array());

        $builder = new PartialSimplePageBuilder();
        $builder->returns('createPage', $page);
        $builder->returns('createParser', new MockSimpleHtmlSaxParser());
        $builder->__construct();

        $builder->parse(new MockSimpleHttpResponse());
        $builder->startElement("form", array());
        $builder->addContent("Stuff");
        $builder->endElement("form");
    }
}

class TestOfPageParsing extends UnitTestCase {

    function testParseMechanics() {
        $parser = new MockSimpleHtmlSaxParser();
        $parser->expectOnce('parse', array('stuff'));

        $page = new MockSimplePage();
        $page->expectOnce('acceptPageEnd');

        $builder = new PartialSimplePageBuilder();
        $builder->returns('createPage', $page);
        $builder->returns('createParser', $parser);
        $builder->__construct();

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent', 'stuff');
        $builder->parse($response);
    }
}

class TestOfPageInterface extends UnitTestCase {

    function testInterfaceOnEmptyPage() {
        $page = new SimplePage();
        $this->assertEqual($page->getTransportError(), 'No page fetched yet');
        $this->assertIdentical($page->getRaw(), false);
        $this->assertIdentical($page->getHeaders(), false);
        $this->assertIdentical($page->getMimeType(), false);
        $this->assertIdentical($page->getResponseCode(), false);
        $this->assertIdentical($page->getAuthentication(), false);
        $this->assertIdentical($page->getRealm(), false);
        $this->assertFalse($page->hasFrames());
        $this->assertIdentical($page->getUrls(), array());
        $this->assertIdentical($page->getTitle(), false);
    }
}

class TestOfPageHeaders extends UnitTestCase {

    function testUrlAccessor() {
        $headers = new MockSimpleHttpHeaders();

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getHeaders', $headers);
        $response->setReturnValue('getMethod', 'POST');
        $response->setReturnValue('getUrl', new SimpleUrl('here'));
        $response->setReturnValue('getRequestData', array('a' => 'A'));

        $page = new SimplePage($response);
        $this->assertEqual($page->getMethod(), 'POST');
        $this->assertEqual($page->getUrl(), new SimpleUrl('here'));
        $this->assertEqual($page->getRequestData(), array('a' => 'A'));
    }

    function testTransportError() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getError', 'Ouch');

        $page = new SimplePage($response);
        $this->assertEqual($page->getTransportError(), 'Ouch');
    }

    function testHeadersAccessor() {
        $headers = new MockSimpleHttpHeaders();
        $headers->setReturnValue('getRaw', 'My: Headers');

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getHeaders', $headers);

        $page = new SimplePage($response);
        $this->assertEqual($page->getHeaders(), 'My: Headers');
    }

    function testMimeAccessor() {
        $headers = new MockSimpleHttpHeaders();
        $headers->setReturnValue('getMimeType', 'text/html');

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getHeaders', $headers);

        $page = new SimplePage($response);
        $this->assertEqual($page->getMimeType(), 'text/html');
    }

    function testResponseAccessor() {
        $headers = new MockSimpleHttpHeaders();
        $headers->setReturnValue('getResponseCode', 301);

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getHeaders', $headers);

        $page = new SimplePage($response);
        $this->assertIdentical($page->getResponseCode(), 301);
    }

    function testAuthenticationAccessors() {
        $headers = new MockSimpleHttpHeaders();
        $headers->setReturnValue('getAuthentication', 'Basic');
        $headers->setReturnValue('getRealm', 'Secret stuff');

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getHeaders', $headers);

        $page = new SimplePage($response);
        $this->assertEqual($page->getAuthentication(), 'Basic');
        $this->assertEqual($page->getRealm(), 'Secret stuff');
    }
}

class TestOfHtmlPage extends UnitTestCase {

    function testRawAccessor() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent', 'Raw HTML');

        $page = new SimplePage($response);
        $this->assertEqual($page->getRaw(), 'Raw HTML');
    }

    function testTextAccessor() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent', '<b>Some</b> &quot;messy&quot; HTML');

        $page = new SimplePage($response);
        $this->assertEqual($page->getText(), 'Some "messy" HTML');
    }

    function testNoLinks() {
        $page = new SimplePage(new MockSimpleHttpResponse());
        $this->assertIdentical($page->getUrls(), array());
        $this->assertIdentical($page->getUrlsByLabel('Label'), array());
    }

    function testAddAbsoluteLink() {
        $link = new SimpleAnchorTag(array('href' => 'http://somewhere.com'));
        $link->addContent('Label');
        $page = new SimplePage(new MockSimpleHttpResponse());
        $page->AcceptTag($link);
        $this->assertEqual(
                $page->getUrlsByLabel('Label'),
                array(new SimpleUrl('http://somewhere.com')));
    }

    function testAddStrictRelativeLink() {
        $link = new SimpleAnchorTag(array('href' => './somewhere.php'));
        $link->addContent('Label');
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = new SimplePage($response);
        $page->AcceptTag($link);
        $this->assertEqual(
                $page->getUrlsByLabel('Label'),
                array(new SimpleUrl('http://host/somewhere.php')));
    }

    function testAddBareRelativeLink() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = new SimplePage($response);
        $page->AcceptTag(new SimpleAnchorTag(array('href' => 'somewhere.php')));
        $this->assertIdentical($page->getUrls(), array('http://host/somewhere.php'));
    }

    function testAddRelativeLinkWithBaseTag() {
        $link = new SimpleAnchorTag(array('href' => 'somewhere.php'));
        $link->addContent('Label');
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = new SimplePage($response);
        $page->AcceptTag($link);        
        $base = new SimpleBaseTag(array('href' => 'www.lastcraft.com/stuff/'));
        $page->AcceptTag($base);
        $this->assertEqual(
                $page->getUrlsByLabel('Label'),
                array(new SimpleUrl('www.lastcraft.com/stuff/somewhere.php')));
    }

    function testAddAbsoluteLinkWithBaseTag() {
        $link = new SimpleAnchorTag(array('href' => 'http://here.com/somewhere.php'));
        $link->addContent('Label');
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = new SimplePage($response);
        $page->AcceptTag($link);        
        $base = new SimpleBaseTag(array('href' => 'www.lastcraft.com/stuff/'));
        $page->AcceptTag($base);
        $this->assertEqual(
                $page->getUrlsByLabel('Label'),
                array(new SimpleUrl('http://here.com/somewhere.php')));
    }

    function testLinkIds() {
        $link = new SimpleAnchorTag(array('href' => './somewhere.php', 'id' => 33));
        $link->addContent('Label');

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));

        $page = new SimplePage($response);
        $page->AcceptTag($link);

        $this->assertEqual(
                $page->getUrlsByLabel('Label'),
                array(new SimpleUrl('http://host/somewhere.php')));
        $this->assertFalse($page->getUrlById(0));
        $this->assertEqual(
                $page->getUrlById(33),
                new SimpleUrl('http://host/somewhere.php'));
    }

    function testFindLinkWithNormalisation() {
        $link = new SimpleAnchorTag(array('href' => './somewhere.php', 'id' => 33));
        $link->addContent(' <em>Long &amp; thin</em> ');

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));

        $page = new SimplePage($response);
        $page->AcceptTag($link);

        $this->assertEqual(
                $page->getUrlsByLabel('Long & thin'),
                array(new SimpleUrl('http://host/somewhere.php')));
    }

    function testFindLinkWithImage() {
        $link = new SimpleAnchorTag(array('href' => './somewhere.php', 'id' => 33));
        $link->addContent('<img src="pic.jpg" alt="&lt;A picture&gt;">');

        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));

        $page = new SimplePage($response);
        $page->AcceptTag($link);

        $this->assertEqual(
                $page->getUrlsByLabel('<A picture>'),
                array(new SimpleUrl('http://host/somewhere.php')));
    }

    function testTitleSetting() {
        $title = new SimpleTitleTag(array());
        $title->addContent('Title');
        $page = new SimplePage(new MockSimpleHttpResponse());
        $page->AcceptTag($title);
        $this->assertEqual($page->getTitle(), 'Title');
    }

    function testFramesetAbsence() {
        $url = new SimpleUrl('here');
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', $url);
        $page = new SimplePage($response);
        $this->assertFalse($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), false);
    }

    function testHasEmptyFrameset() {
        $page = new SimplePage(new MockSimpleHttpResponse());
        $page->acceptFramesetStart(new SimpleTag('frameset', array()));
        $page->acceptFramesetEnd();
        $this->assertTrue($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), array());
    }

    function testFramesInPage() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://here'));

        $page = new SimplePage($response);
        $page->acceptFrame(new SimpleFrameTag(array('src' => '1.html')));
        $page->acceptFramesetStart(new SimpleTag('frameset', array()));
        $page->acceptFrame(new SimpleFrameTag(array('src' => '2.html')));
        $page->acceptFrame(new SimpleFrameTag(array('src' => '3.html')));
        $page->acceptFramesetEnd();
        $page->acceptFrame(new SimpleFrameTag(array('src' => '4.html')));

        $this->assertTrue($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), array(
                1 => new SimpleUrl('http://here/2.html'),
                2 => new SimpleUrl('http://here/3.html')));
    }

    function testNamedFramesInPage() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://here'));

        $page = new SimplePage($response);
        $page->acceptFramesetStart(new SimpleTag('frameset', array()));
        $page->acceptFrame(new SimpleFrameTag(array('src' => '1.html')));
        $page->acceptFrame(new SimpleFrameTag(array('src' => '2.html', 'name' => 'A')));
        $page->acceptFrame(new SimpleFrameTag(array('src' => '3.html', 'name' => 'B')));
        $page->acceptFrame(new SimpleFrameTag(array('src' => '4.html')));
        $page->acceptFramesetEnd();

        $this->assertTrue($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), array(
                1 => new SimpleUrl('http://here/1.html'),
                'A' => new SimpleUrl('http://here/2.html'),
                'B' => new SimpleUrl('http://here/3.html'),
                4 => new SimpleUrl('http://here/4.html')));
    }
    
    function testRelativeFramesRespectBaseTag() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getUrl', new SimpleUrl('http://here.com/'));
        $page = new SimplePage($response);

        $base = new SimpleBaseTag(array('href' => 'https://there.com/stuff/'));
        $page->AcceptTag($base);

        $page->acceptFramesetStart(new SimpleTag('frameset', array()));
        $page->acceptFrame(new SimpleFrameTag(array('src' => '1.html')));
        $page->acceptFramesetEnd();
        $this->assertIdentical(
                $page->getFrameset(),
                array(1 => new SimpleUrl('https://there.com/stuff/1.html')));
    }
}

class TestOfFormsCreatedFromEventStream extends UnitTestCase {

    function testFormCanBeSubmitted() {
        $page = new SimplePage(new MockSimpleHttpResponse());
        $page->acceptFormStart(
                new SimpleFormTag(array('method' => 'GET', 'action' => 'here.php')));
        $page->AcceptTag(
                new SimpleSubmitTag(array('type' => 'submit', 'name' => 's')));
        $page->acceptFormEnd();
        $form = &$page->getFormBySubmit(new SimpleByLabel('Submit'));
        $this->assertEqual(
                $form->submitButton(new SimpleByLabel('Submit')),
                new SimpleGetEncoding(array('s' => 'Submit')));
    }

    function testInputFieldCanBeReadBack() {
        $page = new SimplePage(new MockSimpleHttpResponse());
        $page->acceptFormStart(
                new SimpleFormTag(array("method" => "GET", "action" => "here.php")));
        $page->AcceptTag(
                new SimpleTextTag(array("type" => "text", "name" => "a", "value" => "A")));
        $page->AcceptTag(
                new SimpleSubmitTag(array("type" => "submit", "name" => "s")));
        $page->acceptFormEnd();
        $this->assertEqual($page->getField(new SimpleByName('a')), 'A');
    }

    function testInputFieldCanBeReadBackByLabel() {
        $label = new SimpleLabelTag(array());
        $page = new SimplePage(new MockSimpleHttpResponse());
        $page->acceptFormStart(
                new SimpleFormTag(array("method" => "GET", "action" => "here.php")));
        $page->acceptLabelStart($label);
        $label->addContent('l');
        $page->AcceptTag(
                new SimpleTextTag(array("type" => "text", "name" => "a", "value" => "A")));
        $page->acceptLabelEnd();
        $page->AcceptTag(
                new SimpleSubmitTag(array("type" => "submit", "name" => "s")));
        $page->acceptFormEnd();
        $this->assertEqual($page->getField(new SimpleByLabel('l')), 'A');
    }
}

class TestOfPageScraping extends UnitTestCase {

    function parse($response) {
        $builder = new SimplePageBuilder();
        $page = $builder->parse($response);
        return $page;
    }

    function testEmptyPage() {
        $page = new SimplePage(new MockSimpleHttpResponse());
        $this->assertIdentical($page->getUrls(), array());
        $this->assertIdentical($page->getTitle(), false);
    }

    function testUninterestingPage() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent', '<html><body><p>Stuff</p></body></html>');
        $page = $this->parse($response);
        $this->assertIdentical($page->getUrls(), array());
    }

    function testLinksPage() {
        $raw = '<html>';
        $raw .= '<a href="there.html">There</a>';
        $raw .= '<a href="http://there.com/that.html" id="0">That page</a>';
        $raw .= '</html>';
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent', $raw);
        $response->setReturnValue('getUrl', new SimpleUrl('http://www.here.com/a/index.html'));
        $page = $this->parse($response);
        $this->assertIdentical(
                $page->getUrls(),
                array('http://www.here.com/a/there.html', 'http://there.com/that.html'));
        $this->assertIdentical(
                $page->getUrlsByLabel('There'),
                array(new SimpleUrl('http://www.here.com/a/there.html')));
        $this->assertEqual(
                $page->getUrlById('0'),
                new SimpleUrl('http://there.com/that.html'));
    }

    function testTitle() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent', '<html><head><title>Me</title></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getTitle(), 'Me');
    }

    function testNastyTitle() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue(
                'getContent',
                '<html><head><Title> <b>Me&amp;Me </TITLE></b></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getTitle(), "Me&Me");
    }

    function testCompleteForm() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<input type="text" name="here" value="Hello">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByName('here')), "Hello");
    }

    function testUnclosedForm() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<input type="text" name="here" value="Hello">' .
                '</head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByName('here')), "Hello");
    }

    function testEmptyFrameset() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue(
                'getContent',
                '<html><frameset></frameset></html>');
        $page = $this->parse($response);
        $this->assertTrue($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), array());
    }

    function testSingleFrame() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue(
                'getContent',
                '<html><frameset><frame src="a.html"></frameset></html>');
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = $this->parse($response);
        $this->assertTrue($page->hasFrames());
        $this->assertIdentical(
                $page->getFrameset(),
                array(1 => new SimpleUrl('http://host/a.html')));
    }

    function testSingleFrameInNestedFrameset() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><frameset><frameset>' .
                '<frame src="a.html">' .
                '</frameset></frameset></html>');
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = $this->parse($response);
        $this->assertTrue($page->hasFrames());
        $this->assertIdentical(
                $page->getFrameset(),
                array(1 => new SimpleUrl('http://host/a.html')));
    }

    function testFrameWithNoSource() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue(
                'getContent',
                '<html><frameset><frame></frameset></html>');
        $page = $this->parse($response);
        $this->assertTrue($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), array());
    }

    function testFramesCollectedWithNestedFramesetTags() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><frameset>' .
                '<frame src="a.html">' .
                '<frameset><frame src="b.html"></frameset>' .
                '<frame src="c.html">' .
                '</frameset></html>');
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = $this->parse($response);
        $this->assertTrue($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), array(
                1 => new SimpleUrl('http://host/a.html'),
                2 => new SimpleUrl('http://host/b.html'),
                3 => new SimpleUrl('http://host/c.html')));
    }

    function testNamedFrames() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><frameset>' .
                '<frame src="a.html">' .
                '<frame name="_one" src="b.html">' .
                '<frame src="c.html">' .
                '<frame src="d.html" name="_two">' .
                '</frameset></html>');
        $response->setReturnValue('getUrl', new SimpleUrl('http://host/'));
        $page = $this->parse($response);
        $this->assertTrue($page->hasFrames());
        $this->assertIdentical($page->getFrameset(), array(
                1 => new SimpleUrl('http://host/a.html'),
                '_one' => new SimpleUrl('http://host/b.html'),
                3 => new SimpleUrl('http://host/c.html'),
                '_two' => new SimpleUrl('http://host/d.html')));
    }

    function testFindFormByLabel() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue(
                'getContent',
                '<html><head><form><input type="submit"></form></head></html>');
        $page = $this->parse($response);
        $this->assertNull($page->getFormBySubmit(new SimpleByLabel('submit')));
        $this->assertNull($page->getFormBySubmit(new SimpleByName('submit')));
        $this->assertIsA(
                $page->getFormBySubmit(new SimpleByLabel('Submit')),
                'SimpleForm');
    }

    function testConfirmSubmitAttributesAreCaseSensitive() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue(
                'getContent',
                '<html><head><FORM><INPUT TYPE="SUBMIT" NAME="S" VALUE="S"></FORM></head></html>');
        $page = $this->parse($response);
        $this->assertIsA(
                $page->getFormBySubmit(new SimpleByName('S')),
                'SimpleForm');
        $this->assertIsA(
                $page->getFormBySubmit(new SimpleByLabel('S')),
                'SimpleForm');
    }

    function testFindFormByImage() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<input type="image" id=100 alt="Label" name="me">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertIsA(
                $page->getFormByImage(new SimpleByLabel('Label')),
                'SimpleForm');
        $this->assertIsA(
                $page->getFormByImage(new SimpleByName('me')),
                'SimpleForm');
        $this->assertIsA(
                $page->getFormByImage(new SimpleById(100)),
                'SimpleForm');
    }

    function testFindFormByButtonTag() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<button type="submit" name="b" value="B">BBB</button>' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertNull($page->getFormBySubmit(new SimpleByLabel('b')));
        $this->assertNull($page->getFormBySubmit(new SimpleByLabel('B')));
        $this->assertIsA(
                $page->getFormBySubmit(new SimpleByName('b')),
                'SimpleForm');
        $this->assertIsA(
                $page->getFormBySubmit(new SimpleByLabel('BBB')),
                'SimpleForm');
    }

    function testFindFormById() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue(
                'getContent',
                '<html><head><form id="55"><input type="submit"></form></head></html>');
        $page = $this->parse($response);
        $this->assertNull($page->getFormById(54));
        $this->assertIsA($page->getFormById(55), 'SimpleForm');
    }

    function testReadingTextField() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<input type="text" name="a">' .
                '<input type="text" name="b" value="bbb" id=3>' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertNull($page->getField(new SimpleByName('missing')));
        $this->assertIdentical($page->getField(new SimpleByName('a')), '');
        $this->assertIdentical($page->getField(new SimpleByName('b')), 'bbb');
    }

    function testReadingTextFieldIsCaseInsensitive() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><FORM>' .
                '<INPUT TYPE="TEXT" NAME="a">' .
                '<INPUT TYPE="TEXT" NAME="b" VALUE="bbb" id=3>' .
                '</FORM></head></html>');
        $page = $this->parse($response);
        $this->assertNull($page->getField(new SimpleByName('missing')));
        $this->assertIdentical($page->getField(new SimpleByName('a')), '');
        $this->assertIdentical($page->getField(new SimpleByName('b')), 'bbb');
    }

    function testSettingTextField() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<input type="text" name="a">' .
                '<input type="text" name="b" id=3>' .
                '<input type="submit">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertTrue($page->setField(new SimpleByName('a'), 'aaa'));
        $this->assertEqual($page->getField(new SimpleByName('a')), 'aaa');
        $this->assertTrue($page->setField(new SimpleById(3), 'bbb'));
        $this->assertEqual($page->getField(new SimpleBYId(3)), 'bbb');
        $this->assertFalse($page->setField(new SimpleByName('z'), 'zzz'));
        $this->assertNull($page->getField(new SimpleByName('z')));
    }

    function testSettingTextFieldByEnclosingLabel() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<label>Stuff' .
                '<input type="text" name="a" value="A">' .
                '</label>' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByName('a')), 'A');
        $this->assertEqual($page->getField(new SimpleByLabel('Stuff')), 'A');
        $this->assertTrue($page->setField(new SimpleByLabel('Stuff'), 'aaa'));
        $this->assertEqual($page->getField(new SimpleByLabel('Stuff')), 'aaa');
    }

    function testGettingTextFieldByEnclosingLabelWithConflictingOtherFields() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<label>Stuff' .
                '<input type="text" name="a" value="A">' .
                '</label>' .
                '<input type="text" name="b" value="B">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByName('a')), 'A');
        $this->assertEqual($page->getField(new SimpleByName('b')), 'B');
        $this->assertEqual($page->getField(new SimpleByLabel('Stuff')), 'A');
    }

    function testSettingTextFieldByExternalLabel() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<label for="aaa">Stuff</label>' .
                '<input id="aaa" type="text" name="a" value="A">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByLabel('Stuff')), 'A');
        $this->assertTrue($page->setField(new SimpleByLabel('Stuff'), 'aaa'));
        $this->assertEqual($page->getField(new SimpleByLabel('Stuff')), 'aaa');
    }

    function testReadingTextArea() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<textarea name="a">aaa</textarea>' .
                '<input type="submit">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByName('a')), 'aaa');
    }

    function testSettingTextArea() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<textarea name="a">aaa</textarea>' .
                '<input type="submit">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertTrue($page->setField(new SimpleByName('a'), 'AAA'));
        $this->assertEqual($page->getField(new SimpleByName('a')), 'AAA');
    }

    function testSettingSelectionField() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<select name="a">' .
                '<option>aaa</option>' .
                '<option selected>bbb</option>' .
                '</select>' .
                '<input type="submit">' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByName('a')), 'bbb');
        $this->assertFalse($page->setField(new SimpleByName('a'), 'ccc'));
        $this->assertTrue($page->setField(new SimpleByName('a'), 'aaa'));
        $this->assertEqual($page->getField(new SimpleByName('a')), 'aaa');
    }

    function testSettingSelectionFieldByEnclosingLabel() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<label>Stuff' .
                '<select name="a"><option selected>A</option><option>B</option></select>' .
                '</label>' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByLabel('Stuff')), 'A');
        $this->assertTrue($page->setField(new SimpleByLabel('Stuff'), 'B'));
        $this->assertEqual($page->getField(new SimpleByLabel('Stuff')), 'B');
    }

    function testSettingRadioButtonByEnclosingLabel() {
        $response = new MockSimpleHttpResponse();
        $response->setReturnValue('getContent',
                '<html><head><form>' .
                '<label>A<input type="radio" name="r" value="a" checked></label>' .
                '<label>B<input type="radio" name="r" value="b"></label>' .
                '</form></head></html>');
        $page = $this->parse($response);
        $this->assertEqual($page->getField(new SimpleByLabel('A')), 'a');
        $this->assertTrue($page->setField(new SimpleBylabel('B'), 'b'));
        $this->assertEqual($page->getField(new SimpleByLabel('B')), 'b');
    }
}
?>