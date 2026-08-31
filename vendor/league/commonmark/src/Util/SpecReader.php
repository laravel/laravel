<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

use League\CommonMark\Exception\IOException;

/**
 * Reads in a CommonMark spec document and extracts the input/output examples for testing against them
 */
final class SpecReader
{
    private function __construct()
    {
    }

    /**
     * @return iterable<string, array{input: string, output: string, type: string, section: string, number: int}>
     */
    public static function read(string $data): iterable
    {
        // Normalize newlines for platform independence
        $data = \preg_replace('/\r\n?/', "\n", $data);
        \assert($data !== null);
        $data = \preg_replace('/<!-- END TESTS -->.*$/', '', $data);
        \assert($data !== null);
        \preg_match_all('/^`{32} (example ?\w*)\n([\s\S]*?)^\.\n([\s\S]*?)^`{32}$|^#{1,6} *(.*)$/m', $data, $matches, PREG_SET_ORDER);

        $currentSection = 'Example';
        $exampleNumber  = 0;

        foreach ($matches as $match) {
            \assert(isset($match[1], $match[2], $match[3]));
            if (isset($match[4])) {
                $currentSection = $match[4];
                continue;
            }

            yield \trim($currentSection . ' #' . $exampleNumber) => [
                'input'   => \str_replace('→', "\t", $match[2]),
                'output'  => \str_replace('→', "\t", $match[3]),
                'type'    => $match[1],
                'section' => $currentSection,
                'number'  => $exampleNumber++,
            ];
        }
    }

    /**
     * @return iterable<string, array{input: string, output: string, type: string, section: string, number: int}>
     *
     * @throws IOException if the file cannot be loaded
     */
    public static function readFile(string $filename): iterable
    {
        if (($data = \file_get_contents($filename)) === false) {
            throw new IOException(\sprintf('Failed to load spec from %s', $filename));
        }

        return self::read($data);
    }
}
