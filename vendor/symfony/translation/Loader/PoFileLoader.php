<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Loader;

/**
 * @copyright Copyright (c) 2010, Union of RAD https://github.com/UnionOfRAD/lithium
 * @copyright Copyright (c) 2012, Clemens Tolboom
 */
class PoFileLoader extends FileLoader
{
    /**
     * Parses portable object (PO) format.
     *
     * From https://www.gnu.org/software/gettext/manual/gettext.html#PO-Files
     * we should be able to parse files having:
     *
     * white-space
     * #  translator-comments
     * #. extracted-comments
     * #: reference...
     * #, flag...
     * #| msgid previous-untranslated-string
     * msgid untranslated-string
     * msgstr translated-string
     *
     * extra or different lines are:
     *
     * #| msgctxt previous-context
     * #| msgid previous-untranslated-string
     * msgctxt context
     *
     * #| msgid previous-untranslated-string-singular
     * #| msgid_plural previous-untranslated-string-plural
     * msgid untranslated-string-singular
     * msgid_plural untranslated-string-plural
     * msgstr[0] translated-string-case-0
     * ...
     * msgstr[N] translated-string-case-n
     *
     * The definition states:
     * - white-space and comments are optional.
     * - msgid "" that an empty singleline defines a header.
     *
     * This parser sacrifices some features of the reference implementation the
     * differences to that implementation are as follows.
     * - No support for comments spanning multiple lines.
     * - Translator and extracted comments are treated as being the same type.
     * - Message IDs are allowed to have other encodings as just US-ASCII.
     *
     * Items with an empty id are ignored.
     */
    protected function loadResource(string $resource): array
    {
        $stream = fopen($resource, 'r');

        $defaults = [
            'ids' => [],
            'translated' => null,
        ];

        $messages = [];
        $item = $defaults;
        $flags = [];

        while ($line = fgets($stream)) {
            $line = trim($line);

            if ('' === $line) {
                // Whitespace indicated current item is done
                if (!\in_array('fuzzy', $flags, true)) {
                    $this->addMessage($messages, $item);
                }
                $item = $defaults;
                $flags = [];
            } elseif (str_starts_with($line, '#,')) {
                $flags = array_map('trim', explode(',', substr($line, 2)));
            } elseif (str_starts_with($line, 'msgid "')) {
                // We start a new msg so save previous
                // TODO: this fails when comments or contexts are added
                $this->addMessage($messages, $item);
                $item = $defaults;
                $item['ids']['singular'] = substr($line, 7, -1);
            } elseif (str_starts_with($line, 'msgstr "')) {
                $item['translated'] = substr($line, 8, -1);
            } elseif ('"' === $line[0]) {
                $continues = isset($item['translated']) ? 'translated' : 'ids';

                if (\is_array($item[$continues])) {
                    end($item[$continues]);
                    $item[$continues][key($item[$continues])] .= substr($line, 1, -1);
                } else {
                    $item[$continues] .= substr($line, 1, -1);
                }
            } elseif (str_starts_with($line, 'msgid_plural "')) {
                $item['ids']['plural'] = substr($line, 14, -1);
            } elseif (str_starts_with($line, 'msgstr[')) {
                $size = strpos($line, ']');
                $item['translated'][(int) substr($line, 7, 1)] = substr($line, $size + 3, -1);
            }
        }
        // save last item
        if (!\in_array('fuzzy', $flags, true)) {
            $this->addMessage($messages, $item);
        }
        fclose($stream);

        return $messages;
    }

    /**
     * Save a translation item to the messages.
     *
     * A .po file could contain by error missing plural indexes. We need to
     * fix these before saving them.
     */
    private function addMessage(array &$messages, array $item): void
    {
        if (!empty($item['ids']['singular'])) {
            $id = stripcslashes($item['ids']['singular']);
            if (isset($item['ids']['plural'])) {
                $id .= '|'.stripcslashes($item['ids']['plural']);
            }

            $translated = (array) $item['translated'];
            // PO are by definition indexed so sort by index.
            ksort($translated);
            // Make sure every index is filled.
            end($translated);
            $count = key($translated);
            // Fill missing spots with '-'.
            $empties = array_fill(0, $count + 1, '-');
            $translated += $empties;
            ksort($translated);

            $messages[$id] = stripcslashes(implode('|', $translated));
        }
    }
}
