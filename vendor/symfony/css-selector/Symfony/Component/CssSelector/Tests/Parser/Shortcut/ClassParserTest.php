<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Tests\Parser\Shortcut;

use Symfony\Component\CssSelector\Node\SelectorNode;
use Symfony\Component\CssSelector\Parser\Shortcut\ClassParser;

/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class ClassParserTest extends \PHPUnit_Framework_TestCase
{
    /** @dataProvider getParseTestData */
    public function testParse($source, $representation)
    {
        $parser = new ClassParser();
        $selectors = $parser->parse($source);
        $this->assertCount(1, $selectors);

        /** @var SelectorNode $selector */
        $selector = $selectors[0];
        $this->assertEquals($representation, (string) $selector->getTree());
    }

    public function getParseTestData()
    {
        return array(
            array('.testclass', 'Class[Element[*].testclass]'),
            array('testel.testclass', 'Class[Element[testel].testclass]'),
            array('testns|.testclass', 'Class[Element[testns|*].testclass]'),
            array('testns|*.testclass', 'Class[Element[testns|*].testclass]'),
            array('testns|testel.testclass', 'Class[Element[testns|testel].testclass]'),
        );
    }
}
