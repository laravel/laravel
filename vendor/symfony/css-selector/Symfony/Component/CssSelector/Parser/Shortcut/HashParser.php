<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Parser\Shortcut;

use Symfony\Component\CssSelector\Node\ElementNode;
use Symfony\Component\CssSelector\Node\HashNode;
use Symfony\Component\CssSelector\Node\SelectorNode;
use Symfony\Component\CssSelector\Parser\ParserInterface;

/**
 * CSS selector hash parser shortcut.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class HashParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($source)
    {
        // Matches an optional namespace, optional element, and required id
        // $source = 'test|input#ab6bd_field';
        // $matches = array (size=5)
        //     0 => string 'test:input#ab6bd_field' (length=22)
        //     1 => string 'test:' (length=5)
        //     2 => string 'test' (length=4)
        //     3 => string 'input' (length=5)
        //     4 => string 'ab6bd_field' (length=11)
        if (preg_match('/^(([a-z]+)\|)?([\w-]+|\*)?#([\w-]+)$/i', trim($source), $matches)) {
            return array(
                new SelectorNode(new HashNode(new ElementNode($matches[2] ?: null, $matches[3] ?: null), $matches[4])),
            );
        }

        return array();
    }
}
