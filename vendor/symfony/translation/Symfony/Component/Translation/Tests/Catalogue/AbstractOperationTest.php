<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Tests\Catalogue;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

abstract class AbstractOperationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEmptyDomains()
    {
        $this->assertEquals(
            array(),
            $this->createOperation(
                new MessageCatalogue('en'),
                new MessageCatalogue('en')
            )->getDomains()
        );
    }

    public function testGetMergedDomains()
    {
        $this->assertEquals(
            array('a', 'b', 'c'),
            $this->createOperation(
                new MessageCatalogue('en', array('a' => array(), 'b' => array())),
                new MessageCatalogue('en', array('b' => array(), 'c' => array()))
            )->getDomains()
        );
    }

    public function testGetMessagesFromUnknownDomain()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->createOperation(
            new MessageCatalogue('en'),
            new MessageCatalogue('en')
        )->getMessages('domain');
    }

    public function testGetEmptyMessages()
    {
        $this->assertEquals(
            array(),
            $this->createOperation(
                new MessageCatalogue('en', array('a' => array())),
                new MessageCatalogue('en')
            )->getMessages('a')
        );
    }

    public function testGetEmptyResult()
    {
        $this->assertEquals(
            new MessageCatalogue('en'),
            $this->createOperation(
                new MessageCatalogue('en'),
                new MessageCatalogue('en')
            )->getResult()
        );
    }

    abstract protected function createOperation(MessageCatalogueInterface $source, MessageCatalogueInterface $target);
}
