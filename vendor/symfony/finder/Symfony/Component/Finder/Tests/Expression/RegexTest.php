<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\Expression;

use Symfony\Component\Finder\Expression\Expression;

class RegexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getHasFlagsData
     */
    public function testHasFlags($regex, $start, $end)
    {
        $expr = new Expression($regex);

        $this->assertEquals($start, $expr->getRegex()->hasStartFlag());
        $this->assertEquals($end,   $expr->getRegex()->hasEndFlag());
    }

    /**
     * @dataProvider getHasJokersData
     */
    public function testHasJokers($regex, $start, $end)
    {
        $expr = new Expression($regex);

        $this->assertEquals($start, $expr->getRegex()->hasStartJoker());
        $this->assertEquals($end,   $expr->getRegex()->hasEndJoker());
    }

    /**
     * @dataProvider getSetFlagsData
     */
    public function testSetFlags($regex, $start, $end, $expected)
    {
        $expr = new Expression($regex);
        $expr->getRegex()->setStartFlag($start)->setEndFlag($end);

        $this->assertEquals($expected, $expr->render());
    }

    /**
     * @dataProvider getSetJokersData
     */
    public function testSetJokers($regex, $start, $end, $expected)
    {
        $expr = new Expression($regex);
        $expr->getRegex()->setStartJoker($start)->setEndJoker($end);

        $this->assertEquals($expected, $expr->render());
    }

    public function testOptions()
    {
        $expr = new Expression('~abc~is');
        $expr->getRegex()->removeOption('i')->addOption('m');

        $this->assertEquals('~abc~sm', $expr->render());
    }

    public function testMixFlagsAndJokers()
    {
        $expr = new Expression('~^.*abc.*$~is');

        $expr->getRegex()->setStartFlag(false)->setEndFlag(false)->setStartJoker(false)->setEndJoker(false);
        $this->assertEquals('~abc~is', $expr->render());

        $expr->getRegex()->setStartFlag(true)->setEndFlag(true)->setStartJoker(true)->setEndJoker(true);
        $this->assertEquals('~^.*abc.*$~is', $expr->render());
    }

    /**
     * @dataProvider getReplaceJokersTestData
     */
    public function testReplaceJokers($regex, $expected)
    {
        $expr = new Expression($regex);
        $expr = $expr->getRegex()->replaceJokers('@');

        $this->assertEquals($expected, $expr->renderPattern());
    }

    public function getHasFlagsData()
    {
        return array(
            array('~^abc~', true, false),
            array('~abc$~', false, true),
            array('~abc~', false, false),
            array('~^abc$~', true, true),
            array('~^abc\\$~', true, false),
        );
    }

    public function getHasJokersData()
    {
        return array(
            array('~.*abc~', true, false),
            array('~abc.*~', false, true),
            array('~abc~', false, false),
            array('~.*abc.*~', true, true),
            array('~.*abc\\.*~', true, false),
        );
    }

    public function getSetFlagsData()
    {
        return array(
            array('~abc~', true, false, '~^abc~'),
            array('~abc~', false, true, '~abc$~'),
            array('~abc~', false, false, '~abc~'),
            array('~abc~', true, true, '~^abc$~'),
        );
    }

    public function getSetJokersData()
    {
        return array(
            array('~abc~', true, false, '~.*abc~'),
            array('~abc~', false, true, '~abc.*~'),
            array('~abc~', false, false, '~abc~'),
            array('~abc~', true, true, '~.*abc.*~'),
        );
    }

    public function getReplaceJokersTestData()
    {
        return array(
            array('~.abc~', '@abc'),
            array('~\\.abc~', '\\.abc'),
            array('~\\\\.abc~', '\\\\@abc'),
            array('~\\\\\\.abc~', '\\\\\\.abc'),
        );
    }
}
