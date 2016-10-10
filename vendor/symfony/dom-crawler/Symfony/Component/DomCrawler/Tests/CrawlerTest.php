<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DomCrawler\Tests;

use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $crawler = new Crawler();
        $this->assertCount(0, $crawler, '__construct() returns an empty crawler');

        $crawler = new Crawler(new \DOMNode());
        $this->assertCount(1, $crawler, '__construct() takes a node as a first argument');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::add
     */
    public function testAdd()
    {
        $crawler = new Crawler();
        $crawler->add($this->createDomDocument());
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->add() adds nodes from a \DOMDocument');

        $crawler = new Crawler();
        $crawler->add($this->createNodeList());
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->add() adds nodes from a \DOMNodeList');

        foreach ($this->createNodeList() as $node) {
            $list[] = $node;
        }
        $crawler = new Crawler();
        $crawler->add($list);
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->add() adds nodes from an array of nodes');

        $crawler = new Crawler();
        $crawler->add($this->createNodeList()->item(0));
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->add() adds nodes from an \DOMNode');

        $crawler = new Crawler();
        $crawler->add('<html><body>Foo</body></html>');
        $this->assertEquals('Foo', $crawler->filterXPath('//body')->text(), '->add() adds nodes from a string');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidNode()
    {
        $crawler = new Crawler();
        $crawler->add(1);
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addHtmlContent
     */
    public function testAddHtmlContent()
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent('<html><div class="foo"></html>', 'UTF-8');

        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addHtmlContent() adds nodes from an HTML string');

        $crawler->addHtmlContent('<html><head><base href="http://symfony.com"></head><a href="/contact"></a></html>', 'UTF-8');

        $this->assertEquals('http://symfony.com', $crawler->filterXPath('//base')->attr('href'), '->addHtmlContent() adds nodes from an HTML string');
        $this->assertEquals('http://symfony.com/contact', $crawler->filterXPath('//a')->link()->getUri(), '->addHtmlContent() adds nodes from an HTML string');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addHtmlContent
     */
    public function testAddHtmlContentCharset()
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent('<html><div class="foo">Tiếng Việt</html>', 'UTF-8');

        $this->assertEquals('Tiếng Việt', $crawler->filterXPath('//div')->text());
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addHtmlContent
     */
    public function testAddHtmlContentInvalidBaseTag()
    {
        $crawler = new Crawler(null, 'http://symfony.com');

        $crawler->addHtmlContent('<html><head><base target="_top"></head><a href="/contact"></a></html>', 'UTF-8');

        $this->assertEquals('http://symfony.com/contact', current($crawler->filterXPath('//a')->links())->getUri(), '->addHtmlContent() correctly handles a non-existent base tag href attribute');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addHtmlContent
     */
    public function testAddHtmlContentUnsupportedCharset()
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent(file_get_contents(__DIR__.'/Fixtures/windows-1250.html'), 'Windows-1250');

        $this->assertEquals('Žťčýů', $crawler->filterXPath('//p')->text());
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addHtmlContent
     */
    public function testAddHtmlContentCharsetGbk()
    {
        $crawler = new Crawler();
        //gbk encode of <html><p>中文</p></html>
        $crawler->addHtmlContent(base64_decode('PGh0bWw+PHA+1tDOxDwvcD48L2h0bWw+'), 'gbk');

        $this->assertEquals('中文', $crawler->filterXPath('//p')->text());
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addHtmlContent
     */
    public function testAddHtmlContentWithErrors()
    {
        $internalErrors = libxml_use_internal_errors(true);

        $crawler = new Crawler();
        $crawler->addHtmlContent(<<<EOF
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <nav><a href="#"><a href="#"></nav>
    </body>
</html>
EOF
        , 'UTF-8');

        $errors = libxml_get_errors();
        $this->assertCount(1, $errors);
        $this->assertEquals("Tag nav invalid\n", $errors[0]->message);

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addXmlContent
     */
    public function testAddXmlContent()
    {
        $crawler = new Crawler();
        $crawler->addXmlContent('<html><div class="foo"></div></html>', 'UTF-8');

        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addXmlContent() adds nodes from an XML string');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addXmlContent
     */
    public function testAddXmlContentCharset()
    {
        $crawler = new Crawler();
        $crawler->addXmlContent('<html><div class="foo">Tiếng Việt</div></html>', 'UTF-8');

        $this->assertEquals('Tiếng Việt', $crawler->filterXPath('//div')->text());
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addXmlContent
     */
    public function testAddXmlContentWithErrors()
    {
        $internalErrors = libxml_use_internal_errors(true);

        $crawler = new Crawler();
        $crawler->addXmlContent(<<<EOF
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <nav><a href="#"><a href="#"></nav>
    </body>
</html>
EOF
        , 'UTF-8');

        $this->assertTrue(count(libxml_get_errors()) > 1);

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addContent
     */
    public function testAddContent()
    {
        $crawler = new Crawler();
        $crawler->addContent('<html><div class="foo"></html>', 'text/html; charset=UTF-8');
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addContent() adds nodes from an HTML string');

        $crawler = new Crawler();
        $crawler->addContent('<html><div class="foo"></html>', 'text/html; charset=UTF-8; dir=RTL');
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addContent() adds nodes from an HTML string with extended content type');

        $crawler = new Crawler();
        $crawler->addContent('<html><div class="foo"></html>');
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addContent() uses text/html as the default type');

        $crawler = new Crawler();
        $crawler->addContent('<html><div class="foo"></div></html>', 'text/xml; charset=UTF-8');
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addContent() adds nodes from an XML string');

        $crawler = new Crawler();
        $crawler->addContent('<html><div class="foo"></div></html>', 'text/xml');
        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addContent() adds nodes from an XML string');

        $crawler = new Crawler();
        $crawler->addContent('foo bar', 'text/plain');
        $this->assertCount(0, $crawler, '->addContent() does nothing if the type is not (x|ht)ml');

        $crawler = new Crawler();
        $crawler->addContent('<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><span>中文</span></html>');
        $this->assertEquals('中文', $crawler->filterXPath('//span')->text(), '->addContent() guess wrong charset');

        $crawler = new Crawler();
        $crawler->addContent(mb_convert_encoding('<html><head><meta charset="Shift_JIS"></head><body>日本語</body></html>', 'SJIS', 'UTF-8'));
        $this->assertEquals('日本語', $crawler->filterXPath('//body')->text(), '->addContent() can recognize "Shift_JIS" in html5 meta charset tag');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addDocument
     */
    public function testAddDocument()
    {
        $crawler = new Crawler();
        $crawler->addDocument($this->createDomDocument());

        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addDocument() adds nodes from a \DOMDocument');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addNodeList
     */
    public function testAddNodeList()
    {
        $crawler = new Crawler();
        $crawler->addNodeList($this->createNodeList());

        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addNodeList() adds nodes from a \DOMNodeList');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addNodes
     */
    public function testAddNodes()
    {
        foreach ($this->createNodeList() as $node) {
            $list[] = $node;
        }

        $crawler = new Crawler();
        $crawler->addNodes($list);

        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addNodes() adds nodes from an array of nodes');
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::addNode
     */
    public function testAddNode()
    {
        $crawler = new Crawler();
        $crawler->addNode($this->createNodeList()->item(0));

        $this->assertEquals('foo', $crawler->filterXPath('//div')->attr('class'), '->addNode() adds nodes from an \DOMNode');
    }

    public function testClear()
    {
        $crawler = new Crawler(new \DOMNode());
        $crawler->clear();
        $this->assertCount(0, $crawler, '->clear() removes all the nodes from the crawler');
    }

    public function testEq()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//li');
        $this->assertNotSame($crawler, $crawler->eq(0), '->eq() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->eq() returns a new instance of a crawler');

        $this->assertEquals('Two', $crawler->eq(1)->text(), '->eq() returns the nth node of the list');
        $this->assertCount(0, $crawler->eq(100), '->eq() returns an empty crawler if the nth node does not exist');
    }

    public function testEach()
    {
        $data = $this->createTestCrawler()->filterXPath('//ul[1]/li')->each(function ($node, $i) {
            return $i.'-'.$node->text();
        });

        $this->assertEquals(array('0-One', '1-Two', '2-Three'), $data, '->each() executes an anonymous function on each node of the list');
    }

    public function testReduce()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//ul[1]/li');
        $nodes = $crawler->reduce(function ($node, $i) {
            return $i == 1 ? false : true;
        });
        $this->assertNotSame($nodes, $crawler, '->reduce() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $nodes, '->reduce() returns a new instance of a crawler');

        $this->assertCount(2, $nodes, '->reduce() filters the nodes in the list');
    }

    public function testAttr()
    {
        $this->assertEquals('first', $this->createTestCrawler()->filterXPath('//li')->attr('class'), '->attr() returns the attribute of the first element of the node list');

        try {
            $this->createTestCrawler()->filterXPath('//ol')->attr('class');
            $this->fail('->attr() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->attr() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testMissingAttrValueIsNull()
    {
        $crawler = new Crawler();
        $crawler->addContent('<html><div non-empty-attr="sample value" empty-attr=""></div></html>', 'text/html; charset=UTF-8');
        $div = $crawler->filterXPath('//div');

        $this->assertEquals('sample value', $div->attr('non-empty-attr'), '->attr() reads non-empty attributes correctly');
        $this->assertEquals('', $div->attr('empty-attr'), '->attr() reads empty attributes correctly');
        $this->assertNull($div->attr('missing-attr'), '->attr() reads missing attributes correctly');
    }

    public function testText()
    {
        $this->assertEquals('One', $this->createTestCrawler()->filterXPath('//li')->text(), '->text() returns the node value of the first element of the node list');

        try {
            $this->createTestCrawler()->filterXPath('//ol')->text();
            $this->fail('->text() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->text() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testHtml()
    {
        $this->assertEquals('<img alt="Bar">', $this->createTestCrawler()->filterXPath('//a[5]')->html());
        $this->assertEquals('<input type="text" value="TextValue" name="TextName"><input type="submit" value="FooValue" name="FooName" id="FooId"><input type="button" value="BarValue" name="BarName" id="BarId"><button value="ButtonValue" name="ButtonName" id="ButtonId"></button>'
            , trim($this->createTestCrawler()->filterXPath('//form[@id="FooFormId"]')->html()));

        try {
            $this->createTestCrawler()->filterXPath('//ol')->html();
            $this->fail('->html() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->html() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testExtract()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//ul[1]/li');

        $this->assertEquals(array('One', 'Two', 'Three'), $crawler->extract('_text'), '->extract() returns an array of extracted data from the node list');
        $this->assertEquals(array(array('One', 'first'), array('Two', ''), array('Three', '')), $crawler->extract(array('_text', 'class')), '->extract() returns an array of extracted data from the node list');

        $this->assertEquals(array(), $this->createTestCrawler()->filterXPath('//ol')->extract('_text'), '->extract() returns an empty array if the node list is empty');
    }

    public function testFilterXpathComplexQueries()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//body');

        $this->assertCount(0, $crawler->filterXPath('/input'));
        $this->assertCount(0, $crawler->filterXPath('/body'));
        $this->assertCount(1, $crawler->filterXPath('/_root/body'));
        $this->assertCount(1, $crawler->filterXPath('./body'));
        $this->assertCount(1, $crawler->filterXPath('.//body'));
        $this->assertCount(5, $crawler->filterXPath('.//input'));
        $this->assertCount(4, $crawler->filterXPath('//form')->filterXPath('//button | //input'));
        $this->assertCount(1, $crawler->filterXPath('body'));
        $this->assertCount(6, $crawler->filterXPath('//button | //input'));
        $this->assertCount(1, $crawler->filterXPath('//body'));
        $this->assertCount(1, $crawler->filterXPath('descendant-or-self::body'));
        $this->assertCount(1, $crawler->filterXPath('//div[@id="parent"]')->filterXPath('./div'), 'A child selection finds only the current div');
        $this->assertCount(3, $crawler->filterXPath('//div[@id="parent"]')->filterXPath('descendant::div'), 'A descendant selector matches the current div and its child');
        $this->assertCount(3, $crawler->filterXPath('//div[@id="parent"]')->filterXPath('//div'), 'A descendant selector matches the current div and its child');
        $this->assertCount(5, $crawler->filterXPath('(//a | //div)//img'));
        $this->assertCount(7, $crawler->filterXPath('((//a | //div)//img | //ul)'));
        $this->assertCount(7, $crawler->filterXPath('( ( //a | //div )//img | //ul )'));
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::filterXPath
     */
    public function testFilterXPath()
    {
        $crawler = $this->createTestCrawler();
        $this->assertNotSame($crawler, $crawler->filterXPath('//li'), '->filterXPath() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->filterXPath() returns a new instance of a crawler');

        $crawler = $this->createTestCrawler()->filterXPath('//ul');
        $this->assertCount(6, $crawler->filterXPath('//li'), '->filterXPath() filters the node list with the XPath expression');

        $crawler = $this->createTestCrawler();
        $this->assertCount(3, $crawler->filterXPath('//body')->filterXPath('//button')->parents(), '->filterXpath() preserves parents when chained');
    }

    public function testFilterXPathWithDefaultNamespace()
    {
        $crawler = $this->createTestXmlCrawler()->filterXPath('//default:entry/default:id');
        $this->assertCount(1, $crawler, '->filterXPath() automatically registers a namespace');
        $this->assertSame('tag:youtube.com,2008:video:kgZRZmEc9j4', $crawler->text());
    }

    public function testFilterXPathWithCustomDefaultNamespace()
    {
        $crawler = $this->createTestXmlCrawler();
        $crawler->setDefaultNamespacePrefix('x');
        $crawler = $crawler->filterXPath('//x:entry/x:id');

        $this->assertCount(1, $crawler, '->filterXPath() lets to override the default namespace prefix');
        $this->assertSame('tag:youtube.com,2008:video:kgZRZmEc9j4', $crawler->text());
    }

    public function testFilterXPathWithNamespace()
    {
        $crawler = $this->createTestXmlCrawler()->filterXPath('//yt:accessControl');
        $this->assertCount(2, $crawler, '->filterXPath() automatically registers a namespace');
    }

    public function testFilterXPathWithMultipleNamespaces()
    {
        $crawler = $this->createTestXmlCrawler()->filterXPath('//media:group/yt:aspectRatio');
        $this->assertCount(1, $crawler, '->filterXPath() automatically registers multiple namespaces');
        $this->assertSame('widescreen', $crawler->text());
    }

    public function testFilterXPathWithManuallyRegisteredNamespace()
    {
        $crawler = $this->createTestXmlCrawler();
        $crawler->registerNamespace('m', 'http://search.yahoo.com/mrss/');

        $crawler = $crawler->filterXPath('//m:group/yt:aspectRatio');
        $this->assertCount(1, $crawler, '->filterXPath() uses manually registered namespace');
        $this->assertSame('widescreen', $crawler->text());
    }

    public function testFilterXPathWithAnUrl()
    {
        $crawler = $this->createTestXmlCrawler();

        $crawler = $crawler->filterXPath('//media:category[@scheme="http://gdata.youtube.com/schemas/2007/categories.cat"]');
        $this->assertCount(1, $crawler);
        $this->assertSame('Music', $crawler->text());
    }

    public function testFilterXPathWithFakeRoot()
    {
        $crawler = $this->createTestCrawler();
        $this->assertCount(0, $crawler->filterXPath('.'), '->filterXPath() returns an empty result if the XPath references the fake root node');
        $this->assertCount(0, $crawler->filterXPath('/_root'), '->filterXPath() returns an empty result if the XPath references the fake root node');
        $this->assertCount(0, $crawler->filterXPath('self::*'), '->filterXPath() returns an empty result if the XPath references the fake root node');
        $this->assertCount(0, $crawler->filterXPath('self::_root'), '->filterXPath() returns an empty result if the XPath references the fake root node');
    }

    public function testFilterXPathWithAncestorAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//form');

        $this->assertCount(0, $crawler->filterXPath('ancestor::*'), 'The fake root node has no ancestor nodes');
    }

    public function testFilterXPathWithAncestorOrSelfAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//form');

        $this->assertCount(0, $crawler->filterXPath('ancestor-or-self::*'), 'The fake root node has no ancestor nodes');
    }

    public function testFilterXPathWithAttributeAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//form');

        $this->assertCount(0, $crawler->filterXPath('attribute::*'), 'The fake root node has no attribute nodes');
    }

    public function testFilterXPathWithAttributeAxisAfterElementAxis()
    {
        $this->assertCount(3, $this->createTestCrawler()->filterXPath('//form/button/attribute::*'), '->filterXPath() handles attribute axes properly when they are preceded by an element filtering axis');
    }

    public function testFilterXPathWithChildAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//div[@id="parent"]');

        $this->assertCount(1, $crawler->filterXPath('child::div'), 'A child selection finds only the current div');
    }

    public function testFilterXPathWithFollowingAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//a');

        $this->assertCount(0, $crawler->filterXPath('following::div'), 'The fake root node has no following nodes');
    }

    public function testFilterXPathWithFollowingSiblingAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//a');

        $this->assertCount(0, $crawler->filterXPath('following-sibling::div'), 'The fake root node has no following nodes');
    }

    public function testFilterXPathWithNamespaceAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//button');

        $this->assertCount(0, $crawler->filterXPath('namespace::*'), 'The fake root node has no namespace nodes');
    }

    public function testFilterXPathWithNamespaceAxisAfterElementAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//div[@id="parent"]/namespace::*');

        $this->assertCount(0, $crawler->filterXPath('namespace::*'), 'Namespace axes cannot be requested');
    }

    public function testFilterXPathWithParentAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//button');

        $this->assertCount(0, $crawler->filterXPath('parent::*'), 'The fake root node has no parent nodes');
    }

    public function testFilterXPathWithPrecedingAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//form');

        $this->assertCount(0, $crawler->filterXPath('preceding::*'), 'The fake root node has no preceding nodes');
    }

    public function testFilterXPathWithPrecedingSiblingAxis()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//form');

        $this->assertCount(0, $crawler->filterXPath('preceding-sibling::*'), 'The fake root node has no preceding nodes');
    }

    public function testFilterXPathWithSelfAxes()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//a');

        $this->assertCount(0, $crawler->filterXPath('self::a'), 'The fake root node has no "real" element name');
        $this->assertCount(0, $crawler->filterXPath('self::a/img'), 'The fake root node has no "real" element name');
        $this->assertCount(9, $crawler->filterXPath('self::*/a'));
    }

    /**
     * @covers Symfony\Component\DomCrawler\Crawler::filter
     */
    public function testFilter()
    {
        $crawler = $this->createTestCrawler();
        $this->assertNotSame($crawler, $crawler->filter('li'), '->filter() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->filter() returns a new instance of a crawler');

        $crawler = $this->createTestCrawler()->filter('ul');

        $this->assertCount(6, $crawler->filter('li'), '->filter() filters the node list with the CSS selector');
    }

    public function testFilterWithDefaultNamespace()
    {
        $crawler = $this->createTestXmlCrawler()->filter('default|entry default|id');
        $this->assertCount(1, $crawler, '->filter() automatically registers namespaces');
        $this->assertSame('tag:youtube.com,2008:video:kgZRZmEc9j4', $crawler->text());
    }

    public function testFilterWithNamespace()
    {
        CssSelector::disableHtmlExtension();

        $crawler = $this->createTestXmlCrawler()->filter('yt|accessControl');
        $this->assertCount(2, $crawler, '->filter() automatically registers namespaces');
    }

    public function testFilterWithMultipleNamespaces()
    {
        CssSelector::disableHtmlExtension();

        $crawler = $this->createTestXmlCrawler()->filter('media|group yt|aspectRatio');
        $this->assertCount(1, $crawler, '->filter() automatically registers namespaces');
        $this->assertSame('widescreen', $crawler->text());
    }

    public function testFilterWithDefaultNamespaceOnly()
    {
        $crawler = new Crawler('<?xml version="1.0" encoding="UTF-8"?>
            <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                <url>
                    <loc>http://localhost/foo</loc>
                    <changefreq>weekly</changefreq>
                    <priority>0.5</priority>
                    <lastmod>2012-11-16</lastmod>
               </url>
               <url>
                    <loc>http://localhost/bar</loc>
                    <changefreq>weekly</changefreq>
                    <priority>0.5</priority>
                    <lastmod>2012-11-16</lastmod>
                </url>
            </urlset>
        ');

        $this->assertEquals(2, $crawler->filter('url')->count());
    }

    public function testSelectLink()
    {
        $crawler = $this->createTestCrawler();
        $this->assertNotSame($crawler, $crawler->selectLink('Foo'), '->selectLink() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->selectLink() returns a new instance of a crawler');

        $this->assertCount(1, $crawler->selectLink('Fabien\'s Foo'), '->selectLink() selects links by the node values');
        $this->assertCount(1, $crawler->selectLink('Fabien\'s Bar'), '->selectLink() selects links by the alt attribute of a clickable image');

        $this->assertCount(2, $crawler->selectLink('Fabien"s Foo'), '->selectLink() selects links by the node values');
        $this->assertCount(2, $crawler->selectLink('Fabien"s Bar'), '->selectLink() selects links by the alt attribute of a clickable image');

        $this->assertCount(1, $crawler->selectLink('\' Fabien"s Foo'), '->selectLink() selects links by the node values');
        $this->assertCount(1, $crawler->selectLink('\' Fabien"s Bar'), '->selectLink() selects links by the alt attribute of a clickable image');

        $this->assertCount(4, $crawler->selectLink('Foo'), '->selectLink() selects links by the node values');
        $this->assertCount(4, $crawler->selectLink('Bar'), '->selectLink() selects links by the node values');
    }

    public function testSelectButton()
    {
        $crawler = $this->createTestCrawler();
        $this->assertNotSame($crawler, $crawler->selectButton('FooValue'), '->selectButton() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->selectButton() returns a new instance of a crawler');

        $this->assertEquals(1, $crawler->selectButton('FooValue')->count(), '->selectButton() selects buttons');
        $this->assertEquals(1, $crawler->selectButton('FooName')->count(), '->selectButton() selects buttons');
        $this->assertEquals(1, $crawler->selectButton('FooId')->count(), '->selectButton() selects buttons');

        $this->assertEquals(1, $crawler->selectButton('BarValue')->count(), '->selectButton() selects buttons');
        $this->assertEquals(1, $crawler->selectButton('BarName')->count(), '->selectButton() selects buttons');
        $this->assertEquals(1, $crawler->selectButton('BarId')->count(), '->selectButton() selects buttons');

        $this->assertEquals(1, $crawler->selectButton('FooBarValue')->count(), '->selectButton() selects buttons with form attribute too');
        $this->assertEquals(1, $crawler->selectButton('FooBarName')->count(), '->selectButton() selects buttons with form attribute too');
    }

    public function testSelectButtonWithSingleQuotesInNameAttribute()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<body>
    <div id="action">
        <a href="/index.php?r=site/login">Login</a>
    </div>
    <form id="login-form" action="/index.php?r=site/login" method="post">
        <button type="submit" name="Click 'Here'">Submit</button>
    </form>
</body>
</html>
HTML;

        $crawler = new Crawler($html);

        $this->assertCount(1, $crawler->selectButton('Click \'Here\''));
    }

    public function testSelectButtonWithDoubleQuotesInNameAttribute()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<body>
    <div id="action">
        <a href="/index.php?r=site/login">Login</a>
    </div>
    <form id="login-form" action="/index.php?r=site/login" method="post">
        <button type="submit" name='Click "Here"'>Submit</button>
    </form>
</body>
</html>
HTML;

        $crawler = new Crawler($html);

        $this->assertCount(1, $crawler->selectButton('Click "Here"'));
    }

    public function testLink()
    {
        $crawler = $this->createTestCrawler('http://example.com/bar/')->selectLink('Foo');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Link', $crawler->link(), '->link() returns a Link instance');

        $this->assertEquals('POST', $crawler->link('post')->getMethod(), '->link() takes a method as its argument');

        $crawler = $this->createTestCrawler('http://example.com/bar')->selectLink('GetLink');
        $this->assertEquals('http://example.com/bar?get=param', $crawler->link()->getUri(), '->link() returns a Link instance');

        try {
            $this->createTestCrawler()->filterXPath('//ol')->link();
            $this->fail('->link() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->link() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testSelectLinkAndLinkFiltered()
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<body>
    <div id="action">
        <a href="/index.php?r=site/login">Login</a>
    </div>
    <form id="login-form" action="/index.php?r=site/login" method="post">
        <button type="submit">Submit</button>
    </form>
</body>
</html>
HTML;

        $crawler = new Crawler($html);
        $filtered = $crawler->filterXPath("descendant-or-self::*[@id = 'login-form']");

        $this->assertCount(0, $filtered->selectLink('Login'));
        $this->assertCount(1, $filtered->selectButton('Submit'));

        $filtered = $crawler->filterXPath("descendant-or-self::*[@id = 'action']");

        $this->assertCount(1, $filtered->selectLink('Login'));
        $this->assertCount(0, $filtered->selectButton('Submit'));

        $this->assertCount(1, $crawler->selectLink('Login')->selectLink('Login'));
        $this->assertCount(1, $crawler->selectButton('Submit')->selectButton('Submit'));
    }

    public function testChaining()
    {
        $crawler = new Crawler('<div name="a"><div name="b"><div name="c"></div></div></div>');

        $this->assertEquals('a', $crawler->filterXPath('//div')->filterXPath('div')->filterXPath('div')->attr('name'));
    }

    public function testLinks()
    {
        $crawler = $this->createTestCrawler('http://example.com/bar/')->selectLink('Foo');
        $this->assertInternalType('array', $crawler->links(), '->links() returns an array');

        $this->assertCount(4, $crawler->links(), '->links() returns an array');
        $links = $crawler->links();
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Link', $links[0], '->links() returns an array of Link instances');

        $this->assertEquals(array(), $this->createTestCrawler()->filterXPath('//ol')->links(), '->links() returns an empty array if the node selection is empty');
    }

    public function testForm()
    {
        $testCrawler = $this->createTestCrawler('http://example.com/bar/');
        $crawler = $testCrawler->selectButton('FooValue');
        $crawler2 = $testCrawler->selectButton('FooBarValue');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Form', $crawler->form(), '->form() returns a Form instance');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Form', $crawler2->form(), '->form() returns a Form instance');

        $this->assertEquals($crawler->form()->getFormNode()->getAttribute('id'), $crawler2->form()->getFormNode()->getAttribute('id'), '->form() works on elements with form attribute');

        $this->assertEquals(array('FooName' => 'FooBar', 'TextName' => 'TextValue', 'FooTextName' => 'FooTextValue'), $crawler->form(array('FooName' => 'FooBar'))->getValues(), '->form() takes an array of values to submit as its first argument');
        $this->assertEquals(array('FooName' => 'FooValue', 'TextName' => 'TextValue', 'FooTextName' => 'FooTextValue'), $crawler->form()->getValues(), '->getValues() returns correct form values');
        $this->assertEquals(array('FooBarName' => 'FooBarValue', 'TextName' => 'TextValue', 'FooTextName' => 'FooTextValue'), $crawler2->form()->getValues(), '->getValues() returns correct form values');

        try {
            $this->createTestCrawler()->filterXPath('//ol')->form();
            $this->fail('->form() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->form() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testLast()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//ul[1]/li');
        $this->assertNotSame($crawler, $crawler->last(), '->last() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->last() returns a new instance of a crawler');

        $this->assertEquals('Three', $crawler->last()->text());
    }

    public function testFirst()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//li');
        $this->assertNotSame($crawler, $crawler->first(), '->first() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->first() returns a new instance of a crawler');

        $this->assertEquals('One', $crawler->first()->text());
    }

    public function testSiblings()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//li')->eq(1);
        $this->assertNotSame($crawler, $crawler->siblings(), '->siblings() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->siblings() returns a new instance of a crawler');

        $nodes = $crawler->siblings();
        $this->assertEquals(2, $nodes->count());
        $this->assertEquals('One', $nodes->eq(0)->text());
        $this->assertEquals('Three', $nodes->eq(1)->text());

        $nodes = $this->createTestCrawler()->filterXPath('//li')->eq(0)->siblings();
        $this->assertEquals(2, $nodes->count());
        $this->assertEquals('Two', $nodes->eq(0)->text());
        $this->assertEquals('Three', $nodes->eq(1)->text());

        try {
            $this->createTestCrawler()->filterXPath('//ol')->siblings();
            $this->fail('->siblings() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->siblings() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testNextAll()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//li')->eq(1);
        $this->assertNotSame($crawler, $crawler->nextAll(), '->nextAll() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->nextAll() returns a new instance of a crawler');

        $nodes = $crawler->nextAll();
        $this->assertEquals(1, $nodes->count());
        $this->assertEquals('Three', $nodes->eq(0)->text());

        try {
            $this->createTestCrawler()->filterXPath('//ol')->nextAll();
            $this->fail('->nextAll() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->nextAll() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testPreviousAll()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//li')->eq(2);
        $this->assertNotSame($crawler, $crawler->previousAll(), '->previousAll() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->previousAll() returns a new instance of a crawler');

        $nodes = $crawler->previousAll();
        $this->assertEquals(2, $nodes->count());
        $this->assertEquals('Two', $nodes->eq(0)->text());

        try {
            $this->createTestCrawler()->filterXPath('//ol')->previousAll();
            $this->fail('->previousAll() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->previousAll() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testChildren()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//ul');
        $this->assertNotSame($crawler, $crawler->children(), '->children() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->children() returns a new instance of a crawler');

        $nodes = $crawler->children();
        $this->assertEquals(3, $nodes->count());
        $this->assertEquals('One', $nodes->eq(0)->text());
        $this->assertEquals('Two', $nodes->eq(1)->text());
        $this->assertEquals('Three', $nodes->eq(2)->text());

        try {
            $this->createTestCrawler()->filterXPath('//ol')->children();
            $this->fail('->children() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->children() throws an \InvalidArgumentException if the node list is empty');
        }

        try {
            $crawler = new Crawler('<p></p>');
            $crawler->filter('p')->children();
            $this->assertTrue(true, '->children() does not trigger a notice if the node has no children');
        } catch (\PHPUnit_Framework_Error_Notice $e) {
            $this->fail('->children() does not trigger a notice if the node has no children');
        }
    }

    public function testParents()
    {
        $crawler = $this->createTestCrawler()->filterXPath('//li[1]');
        $this->assertNotSame($crawler, $crawler->parents(), '->parents() returns a new instance of a crawler');
        $this->assertInstanceOf('Symfony\\Component\\DomCrawler\\Crawler', $crawler, '->parents() returns a new instance of a crawler');

        $nodes = $crawler->parents();
        $this->assertEquals(3, $nodes->count());

        $nodes = $this->createTestCrawler()->filterXPath('//html')->parents();
        $this->assertEquals(0, $nodes->count());

        try {
            $this->createTestCrawler()->filterXPath('//ol')->parents();
            $this->fail('->parents() throws an \InvalidArgumentException if the node list is empty');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, '->parents() throws an \InvalidArgumentException if the node list is empty');
        }
    }

    public function testBaseTag()
    {
        $crawler = new Crawler('<html><base href="http://base.com"><a href="link"></a></html>');
        $this->assertEquals('http://base.com/link', $crawler->filterXPath('//a')->link()->getUri());

        $crawler = new Crawler('<html><base href="//base.com"><a href="link"></a></html>', 'https://domain.com');
        $this->assertEquals('https://base.com/link', $crawler->filterXPath('//a')->link()->getUri(), '<base> tag can use a schema-less URL');

        $crawler = new Crawler('<html><base href="path/"><a href="link"></a></html>', 'https://domain.com');
        $this->assertEquals('https://domain.com/path/link', $crawler->filterXPath('//a')->link()->getUri(), '<base> tag can set a path');
    }

    public function createTestCrawler($uri = null)
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('
            <html>
                <body>
                    <a href="foo">Foo</a>
                    <a href="/foo">   Fabien\'s Foo   </a>
                    <a href="/foo">Fabien"s Foo</a>
                    <a href="/foo">\' Fabien"s Foo</a>

                    <a href="/bar"><img alt="Bar"/></a>
                    <a href="/bar"><img alt="   Fabien\'s Bar   "/></a>
                    <a href="/bar"><img alt="Fabien&quot;s Bar"/></a>
                    <a href="/bar"><img alt="\' Fabien&quot;s Bar"/></a>

                    <a href="?get=param">GetLink</a>

                    <form action="foo" id="FooFormId">
                        <input type="text" value="TextValue" name="TextName" />
                        <input type="submit" value="FooValue" name="FooName" id="FooId" />
                        <input type="button" value="BarValue" name="BarName" id="BarId" />
                        <button value="ButtonValue" name="ButtonName" id="ButtonId" />
                    </form>

                    <input type="submit" value="FooBarValue" name="FooBarName" form="FooFormId" />
                    <input type="text" value="FooTextValue" name="FooTextName" form="FooFormId" />

                    <ul class="first">
                        <li class="first">One</li>
                        <li>Two</li>
                        <li>Three</li>
                    </ul>
                    <ul>
                        <li>One Bis</li>
                        <li>Two Bis</li>
                        <li>Three Bis</li>
                    </ul>
                    <div id="parent">
                        <div id="child"></div>
                        <div id="child2" xmlns:foo="http://example.com"></div>
                    </div>
                    <div id="sibling"><img /></div>
                </body>
            </html>
        ');

        return new Crawler($dom, $uri);
    }

    protected function createTestXmlCrawler($uri = null)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:yt="http://gdata.youtube.com/schemas/2007">
                <id>tag:youtube.com,2008:video:kgZRZmEc9j4</id>
                <yt:accessControl action="comment" permission="allowed"/>
                <yt:accessControl action="videoRespond" permission="moderated"/>
                <media:group>
                    <media:title type="plain">Chordates - CrashCourse Biology #24</media:title>
                    <yt:aspectRatio>widescreen</yt:aspectRatio>
                </media:group>
                <media:category label="Music" scheme="http://gdata.youtube.com/schemas/2007/categories.cat">Music</media:category>
            </entry>';

        return new Crawler($xml, $uri);
    }

    protected function createDomDocument()
    {
        $dom = new \DOMDocument();
        $dom->loadXML('<html><div class="foo"></div></html>');

        return $dom;
    }

    protected function createNodeList()
    {
        $dom = new \DOMDocument();
        $dom->loadXML('<html><div class="foo"></div></html>');
        $domxpath = new \DOMXPath($dom);

        return $domxpath->query('//div');
    }
}
