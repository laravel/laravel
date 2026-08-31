<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

use PhpParser\Parser;
use PhpParser\ParserFactory as PhpParserFactory;

/**
 * Parser factory to abstract over PHP Parser library versions.
 */
class ParserFactory
{
    /**
     * New parser instance.
     */
    public function createParser(): Parser
    {
        $factory = new PhpParserFactory();

        if (!\method_exists($factory, 'createForHostVersion')) {
            return $factory->create(PhpParserFactory::ONLY_PHP7);
        }

        return $factory->createForHostVersion();
    }
}
