<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Processor;

class PsrLogMessageProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getPairs
     */
    public function testReplacement($val, $expected)
    {
        $proc = new PsrLogMessageProcessor;

        $message = $proc(array(
            'message' => '{foo}',
            'context' => array('foo' => $val),
        ));
        $this->assertEquals($expected, $message['message']);
    }

    public function getPairs()
    {
        return array(
            array('foo',    'foo'),
            array('3',      '3'),
            array(3,        '3'),
            array(null,     ''),
            array(true,     '1'),
            array(false,    ''),
            array(new \stdClass, '[object stdClass]'),
            array(array(), '[array]'),
        );
    }
}
