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

use Symfony\Component\Translation\Catalogue\DiffOperation;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class DiffOperationTest extends AbstractOperationTest
{
    public function testGetMessagesFromSingleDomain()
    {
        $operation = $this->createOperation(
            new MessageCatalogue('en', array('messages' => array('a' => 'old_a', 'b' => 'old_b'))),
            new MessageCatalogue('en', array('messages' => array('a' => 'new_a', 'c' => 'new_c')))
        );

        $this->assertEquals(
            array('a' => 'old_a', 'c' => 'new_c'),
            $operation->getMessages('messages')
        );

        $this->assertEquals(
            array('c' => 'new_c'),
            $operation->getNewMessages('messages')
        );

        $this->assertEquals(
            array('b' => 'old_b'),
            $operation->getObsoleteMessages('messages')
        );
    }

    public function testGetResultFromSingleDomain()
    {
        $this->assertEquals(
            new MessageCatalogue('en', array(
                'messages' => array('a' => 'old_a', 'c' => 'new_c'),
            )),
            $this->createOperation(
                new MessageCatalogue('en', array('messages' => array('a' => 'old_a', 'b' => 'old_b'))),
                new MessageCatalogue('en', array('messages' => array('a' => 'new_a', 'c' => 'new_c')))
            )->getResult()
        );
    }

    protected function createOperation(MessageCatalogueInterface $source, MessageCatalogueInterface $target)
    {
        return new DiffOperation($source, $target);
    }
}
