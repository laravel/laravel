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

use Symfony\Component\DomCrawler\Field\TextareaFormField;

class TextareaFormFieldTest extends FormFieldTestCase
{
    public function testInitialize()
    {
        $node = $this->createNode('textarea', 'foo bar');
        $field = new TextareaFormField($node);

        $this->assertEquals('foo bar', $field->getValue(), '->initialize() sets the value of the field to the textarea node value');

        $node = $this->createNode('input', '');
        try {
            $field = new TextareaFormField($node);
            $this->fail('->initialize() throws a \LogicException if the node is not a textarea');
        } catch (\LogicException $e) {
            $this->assertTrue(true, '->initialize() throws a \LogicException if the node is not a textarea');
        }

        // Ensure that valid HTML can be used on a textarea.
        $node = $this->createNode('textarea', 'foo bar <h1>Baz</h1>');
        $field = new TextareaFormField($node);

        $this->assertEquals('foo bar <h1>Baz</h1>', $field->getValue(), '->initialize() sets the value of the field to the textarea node value');

        // Ensure that we don't do any DOM manipulation/validation by passing in
        // "invalid" HTML.
        $node = $this->createNode('textarea', 'foo bar <h1>Baz</h2>');
        $field = new TextareaFormField($node);

        $this->assertEquals('foo bar <h1>Baz</h2>', $field->getValue(), '->initialize() sets the value of the field to the textarea node value');
    }
}
