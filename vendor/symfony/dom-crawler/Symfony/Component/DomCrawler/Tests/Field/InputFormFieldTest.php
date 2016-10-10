<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DomCrawler\Tests\Field;

use Symfony\Component\DomCrawler\Field\InputFormField;

class InputFormFieldTest extends FormFieldTestCase
{
    public function testInitialize()
    {
        $node = $this->createNode('input', '', array('type' => 'text', 'name' => 'name', 'value' => 'value'));
        $field = new InputFormField($node);

        $this->assertEquals('value', $field->getValue(), '->initialize() sets the value of the field to the value attribute value');

        $node = $this->createNode('textarea', '');
        try {
            $field = new InputFormField($node);
            $this->fail('->initialize() throws a \LogicException if the node is not an input');
        } catch (\LogicException $e) {
            $this->assertTrue(true, '->initialize() throws a \LogicException if the node is not an input');
        }

        $node = $this->createNode('input', '', array('type' => 'checkbox'));
        try {
            $field = new InputFormField($node);
            $this->fail('->initialize() throws a \LogicException if the node is a checkbox');
        } catch (\LogicException $e) {
            $this->assertTrue(true, '->initialize() throws a \LogicException if the node is a checkbox');
        }

        $node = $this->createNode('input', '', array('type' => 'file'));
        try {
            $field = new InputFormField($node);
            $this->fail('->initialize() throws a \LogicException if the node is a file');
        } catch (\LogicException $e) {
            $this->assertTrue(true, '->initialize() throws a \LogicException if the node is a file');
        }
    }
}
