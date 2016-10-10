<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\XPath;

use Symfony\Component\CssSelector\XPath\Extension\HtmlExtension;
use Symfony\Component\CssSelector\XPath\Translator;

class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider getXpathLiteralTestData */
    public function testXpathLiteral($value, $literal)
    {
        $this->assertEquals($literal, Translator::getXpathLiteral($value));
    }

    /** @dataProvider getCssToXPathTestData */
    public function testCssToXPath($css, $xpath)
    {
        $translator = new Translator();
        $translator->registerExtension(new HtmlExtension($translator));
        $this->assertEquals($xpath, $translator->cssToXPath($css, ''));
    }

    /** @dataProvider getXmlLangTestData */
    public function testXmlLang($css, array $elementsId)
    {
        $translator = new Translator();
        $document = new \SimpleXMLElement(file_get_contents(__DIR__.'/Fixtures/lang.xml'));
        $elements = $document->xpath($translator->cssToXPath($css));
        $this->assertEquals(count($elementsId), count($elements));
        foreach ($elements as $element) {
            $this->assertTrue(in_array($element->attributes()->id, $elementsId));
        }
    }

    /** @dataProvider getHtmlIdsTestData */
    public function testHtmlIds($css, array $elementsId)
    {
        $translator = new Translator();
        $translator->registerExtension(new HtmlExtension($translator));
        $document = new \DOMDocument();
        $document->strictErrorChecking = false;
        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTMLFile(__DIR__.'/Fixtures/ids.html');
        $document = simplexml_import_dom($document);
        $elements = $document->xpath($translator->cssToXPath($css));
        $this->assertCount(count($elementsId), $elementsId);
        foreach ($elements as $element) {
            if (null !== $element->attributes()->id) {
                $this->assertTrue(in_array($element->attributes()->id, $elementsId));
            }
        }
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }

    /** @dataProvider getHtmlShakespearTestData */
    public function testHtmlShakespear($css, $count)
    {
        $translator = new Translator();
        $translator->registerExtension(new HtmlExtension($translator));
        $document = new \DOMDocument();
        $document->strictErrorChecking = false;
        $document->loadHTMLFile(__DIR__.'/Fixtures/shakespear.html');
        $document = simplexml_import_dom($document);
        $bodies = $document->xpath('//body');
        $elements = $bodies[0]->xpath($translator->cssToXPath($css));
        $this->assertEquals($count, count($elements));
    }

    public function getXpathLiteralTestData()
    {
        return array(
            array('foo', "'foo'"),
            array("foo's bar", '"foo\'s bar"'),
            array("foo's \"middle\" bar", 'concat(\'foo\', "\'", \'s "middle" bar\')'),
            array("foo's 'middle' \"bar\"", 'concat(\'foo\', "\'", \'s \', "\'", \'middle\', "\'", \' "bar"\')'),
        );
    }

    public function getCssToXPathTestData()
    {
        return array(
            array('*', "*"),
            array('e', "e"),
            array('*|e', "e"),
            array('e|f', "e:f"),
            array('e[foo]', "e[@foo]"),
            array('e[foo|bar]', "e[@foo:bar]"),
            array('e[foo="bar"]', "e[@foo = 'bar']"),
            array('e[foo~="bar"]', "e[@foo and contains(concat(' ', normalize-space(@foo), ' '), ' bar ')]"),
            array('e[foo^="bar"]', "e[@foo and starts-with(@foo, 'bar')]"),
            array('e[foo$="bar"]', "e[@foo and substring(@foo, string-length(@foo)-2) = 'bar']"),
            array('e[foo*="bar"]', "e[@foo and contains(@foo, 'bar')]"),
            array('e[hreflang|="en"]', "e[@hreflang and (@hreflang = 'en' or starts-with(@hreflang, 'en-'))]"),
            array('e:nth-child(1)', "*/*[name() = 'e' and (position() = 1)]"),
            array('e:nth-last-child(1)', "*/*[name() = 'e' and (position() = last() - 0)]"),
            array('e:nth-last-child(2n+2)', "*/*[name() = 'e' and (last() - position() - 1 >= 0 and (last() - position() - 1) mod 2 = 0)]"),
            array('e:nth-of-type(1)', "*/e[position() = 1]"),
            array('e:nth-last-of-type(1)', "*/e[position() = last() - 0]"),
            array('div e:nth-last-of-type(1) .aclass', "div/descendant-or-self::*/e[position() = last() - 0]/descendant-or-self::*/*[@class and contains(concat(' ', normalize-space(@class), ' '), ' aclass ')]"),
            array('e:first-child', "*/*[name() = 'e' and (position() = 1)]"),
            array('e:last-child', "*/*[name() = 'e' and (position() = last())]"),
            array('e:first-of-type', "*/e[position() = 1]"),
            array('e:last-of-type', "*/e[position() = last()]"),
            array('e:only-child', "*/*[name() = 'e' and (last() = 1)]"),
            array('e:only-of-type', "e[last() = 1]"),
            array('e:empty', "e[not(*) and not(string-length())]"),
            array('e:EmPTY', "e[not(*) and not(string-length())]"),
            array('e:root', "e[not(parent::*)]"),
            array('e:hover', "e[0]"),
            array('e:contains("foo")', "e[contains(string(.), 'foo')]"),
            array('e:ConTains(foo)', "e[contains(string(.), 'foo')]"),
            array('e.warning', "e[@class and contains(concat(' ', normalize-space(@class), ' '), ' warning ')]"),
            array('e#myid', "e[@id = 'myid']"),
            array('e:not(:nth-child(odd))', "e[not(position() - 1 >= 0 and (position() - 1) mod 2 = 0)]"),
            array('e:nOT(*)', "e[0]"),
            array('e f', "e/descendant-or-self::*/f"),
            array('e > f', "e/f"),
            array('e + f', "e/following-sibling::*[name() = 'f' and (position() = 1)]"),
            array('e ~ f', "e/following-sibling::f"),
            array('div#container p', "div[@id = 'container']/descendant-or-self::*/p"),
        );
    }

    public function getXmlLangTestData()
    {
        return array(
            array(':lang("EN")', array('first', 'second', 'third', 'fourth')),
            array(':lang("en-us")', array('second', 'fourth')),
            array(':lang(en-nz)', array('third')),
            array(':lang(fr)', array('fifth')),
            array(':lang(ru)', array('sixth')),
            array(":lang('ZH')", array('eighth')),
            array(':lang(de) :lang(zh)', array('eighth')),
            array(':lang(en), :lang(zh)', array('first', 'second', 'third', 'fourth', 'eighth')),
            array(':lang(es)', array()),
        );
    }

    public function getHtmlIdsTestData()
    {
        return array(
            array('div', array('outer-div', 'li-div', 'foobar-div')),
            array('DIV', array('outer-div', 'li-div', 'foobar-div')),  // case-insensitive in HTML
            array('div div', array('li-div')),
            array('div, div div', array('outer-div', 'li-div', 'foobar-div')),
            array('a[name]', array('name-anchor')),
            array('a[NAme]', array('name-anchor')), // case-insensitive in HTML:
            array('a[rel]', array('tag-anchor', 'nofollow-anchor')),
            array('a[rel="tag"]', array('tag-anchor')),
            array('a[href*="localhost"]', array('tag-anchor')),
            array('a[href*=""]', array()),
            array('a[href^="http"]', array('tag-anchor', 'nofollow-anchor')),
            array('a[href^="http:"]', array('tag-anchor')),
            array('a[href^=""]', array()),
            array('a[href$="org"]', array('nofollow-anchor')),
            array('a[href$=""]', array()),
            array('div[foobar~="bc"]', array('foobar-div')),
            array('div[foobar~="cde"]', array('foobar-div')),
            array('[foobar~="ab bc"]', array('foobar-div')),
            array('[foobar~=""]', array()),
            array('[foobar~=" \t"]', array()),
            array('div[foobar~="cd"]', array()),
            array('*[lang|="En"]', array('second-li')),
            array('[lang|="En-us"]', array('second-li')),
            // Attribute values are case sensitive
            array('*[lang|="en"]', array()),
            array('[lang|="en-US"]', array()),
            array('*[lang|="e"]', array()),
            // ... :lang() is not.
            array(':lang("EN")', array('second-li', 'li-div')),
            array('*:lang(en-US)', array('second-li', 'li-div')),
            array(':lang("e")', array()),
            array('li:nth-child(3)', array('third-li')),
            array('li:nth-child(10)', array()),
            array('li:nth-child(2n)', array('second-li', 'fourth-li', 'sixth-li')),
            array('li:nth-child(even)', array('second-li', 'fourth-li', 'sixth-li')),
            array('li:nth-child(2n+0)', array('second-li', 'fourth-li', 'sixth-li')),
            array('li:nth-child(+2n+1)', array('first-li', 'third-li', 'fifth-li', 'seventh-li')),
            array('li:nth-child(odd)', array('first-li', 'third-li', 'fifth-li', 'seventh-li')),
            array('li:nth-child(2n+4)', array('fourth-li', 'sixth-li')),
            array('li:nth-child(3n+1)', array('first-li', 'fourth-li', 'seventh-li')),
            array('li:nth-child(n)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-child(n-1)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-child(n+1)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-child(n+3)', array('third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-child(-n)', array()),
            array('li:nth-child(-n-1)', array()),
            array('li:nth-child(-n+1)', array('first-li')),
            array('li:nth-child(-n+3)', array('first-li', 'second-li', 'third-li')),
            array('li:nth-last-child(0)', array()),
            array('li:nth-last-child(2n)', array('second-li', 'fourth-li', 'sixth-li')),
            array('li:nth-last-child(even)', array('second-li', 'fourth-li', 'sixth-li')),
            array('li:nth-last-child(2n+2)', array('second-li', 'fourth-li', 'sixth-li')),
            array('li:nth-last-child(n)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-last-child(n-1)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-last-child(n-3)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-last-child(n+1)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li', 'sixth-li', 'seventh-li')),
            array('li:nth-last-child(n+3)', array('first-li', 'second-li', 'third-li', 'fourth-li', 'fifth-li')),
            array('li:nth-last-child(-n)', array()),
            array('li:nth-last-child(-n-1)', array()),
            array('li:nth-last-child(-n+1)', array('seventh-li')),
            array('li:nth-last-child(-n+3)', array('fifth-li', 'sixth-li', 'seventh-li')),
            array('ol:first-of-type', array('first-ol')),
            array('ol:nth-child(1)', array('first-ol')),
            array('ol:nth-of-type(2)', array('second-ol')),
            array('ol:nth-last-of-type(1)', array('second-ol')),
            array('span:only-child', array('foobar-span')),
            array('li div:only-child', array('li-div')),
            array('div *:only-child', array('li-div', 'foobar-span')),
            array('p:only-of-type', array('paragraph')),
            array('a:empty', array('name-anchor')),
            array('a:EMpty', array('name-anchor')),
            array('li:empty', array('third-li', 'fourth-li', 'fifth-li', 'sixth-li')),
            array(':root', array('html')),
            array('html:root', array('html')),
            array('li:root', array()),
            array('* :root', array()),
            array('*:contains("link")', array('html', 'outer-div', 'tag-anchor', 'nofollow-anchor')),
            array(':CONtains("link")', array('html', 'outer-div', 'tag-anchor', 'nofollow-anchor')),
            array('*:contains("LInk")', array()),  // case sensitive
            array('*:contains("e")', array('html', 'nil', 'outer-div', 'first-ol', 'first-li', 'paragraph', 'p-em')),
            array('*:contains("E")', array()),  // case-sensitive
            array('.a', array('first-ol')),
            array('.b', array('first-ol')),
            array('*.a', array('first-ol')),
            array('ol.a', array('first-ol')),
            array('.c', array('first-ol', 'third-li', 'fourth-li')),
            array('*.c', array('first-ol', 'third-li', 'fourth-li')),
            array('ol *.c', array('third-li', 'fourth-li')),
            array('ol li.c', array('third-li', 'fourth-li')),
            array('li ~ li.c', array('third-li', 'fourth-li')),
            array('ol > li.c', array('third-li', 'fourth-li')),
            array('#first-li', array('first-li')),
            array('li#first-li', array('first-li')),
            array('*#first-li', array('first-li')),
            array('li div', array('li-div')),
            array('li > div', array('li-div')),
            array('div div', array('li-div')),
            array('div > div', array()),
            array('div>.c', array('first-ol')),
            array('div > .c', array('first-ol')),
            array('div + div', array('foobar-div')),
            array('a ~ a', array('tag-anchor', 'nofollow-anchor')),
            array('a[rel="tag"] ~ a', array('nofollow-anchor')),
            array('ol#first-ol li:last-child', array('seventh-li')),
            array('ol#first-ol *:last-child', array('li-div', 'seventh-li')),
            array('#outer-div:first-child', array('outer-div')),
            array('#outer-div :first-child', array('name-anchor', 'first-li', 'li-div', 'p-b', 'checkbox-fieldset-disabled', 'area-href')),
            array('a[href]', array('tag-anchor', 'nofollow-anchor')),
            array(':not(*)', array()),
            array('a:not([href])', array('name-anchor')),
            array('ol :Not(li[class])', array('first-li', 'second-li', 'li-div', 'fifth-li', 'sixth-li', 'seventh-li')),
            // HTML-specific
            array(':link', array('link-href', 'tag-anchor', 'nofollow-anchor', 'area-href')),
            array(':visited', array()),
            array(':enabled', array('link-href', 'tag-anchor', 'nofollow-anchor', 'checkbox-unchecked', 'text-checked', 'checkbox-checked', 'area-href')),
            array(':disabled', array('checkbox-disabled', 'checkbox-disabled-checked', 'fieldset', 'checkbox-fieldset-disabled')),
            array(':checked', array('checkbox-checked', 'checkbox-disabled-checked')),
        );
    }

    public function getHtmlShakespearTestData()
    {
        return array(
            array('*', 246),
            array('div:contains(CELIA)', 26),
            array('div:only-child', 22), // ?
            array('div:nth-child(even)', 106),
            array('div:nth-child(2n)', 106),
            array('div:nth-child(odd)', 137),
            array('div:nth-child(2n+1)', 137),
            array('div:nth-child(n)', 243),
            array('div:last-child', 53),
            array('div:first-child', 51),
            array('div > div', 242),
            array('div + div', 190),
            array('div ~ div', 190),
            array('body', 1),
            array('body div', 243),
            array('div', 243),
            array('div div', 242),
            array('div div div', 241),
            array('div, div, div', 243),
            array('div, a, span', 243),
            array('.dialog', 51),
            array('div.dialog', 51),
            array('div .dialog', 51),
            array('div.character, div.dialog', 99),
            array('div.direction.dialog', 0),
            array('div.dialog.direction', 0),
            array('div.dialog.scene', 1),
            array('div.scene.scene', 1),
            array('div.scene .scene', 0),
            array('div.direction .dialog ', 0),
            array('div .dialog .direction', 4),
            array('div.dialog .dialog .direction', 4),
            array('#speech5', 1),
            array('div#speech5', 1),
            array('div #speech5', 1),
            array('div.scene div.dialog', 49),
            array('div#scene1 div.dialog div', 142),
            array('#scene1 #speech1', 1),
            array('div[class]', 103),
            array('div[class=dialog]', 50),
            array('div[class^=dia]', 51),
            array('div[class$=log]', 50),
            array('div[class*=sce]', 1),
            array('div[class|=dialog]', 50), // ? Seems right
            array('div[class!=madeup]', 243), // ? Seems right
            array('div[class~=dialog]', 51), // ? Seems right
        );
    }
}
