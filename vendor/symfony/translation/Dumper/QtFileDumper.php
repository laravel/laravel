<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * QtFileDumper generates ts files from a message catalogue.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class QtFileDumper extends FileDumper
{
    public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $ts = $dom->appendChild($dom->createElement('TS'));
        $context = $ts->appendChild($dom->createElement('context'));
        $context->appendChild($dom->createElement('name', $domain));

        foreach ($messages->all($domain) as $source => $target) {
            $message = $context->appendChild($dom->createElement('message'));
            $metadata = $messages->getMetadata($source, $domain);
            if (isset($metadata['sources'])) {
                foreach ((array) $metadata['sources'] as $location) {
                    $loc = explode(':', $location, 2);
                    $location = $message->appendChild($dom->createElement('location'));
                    $location->setAttribute('filename', $loc[0]);
                    if (isset($loc[1])) {
                        $location->setAttribute('line', $loc[1]);
                    }
                }
            }
            $message->appendChild($dom->createElement('source', $source));
            $message->appendChild($dom->createElement('translation', $target));
        }

        return $dom->saveXML();
    }

    protected function getExtension(): string
    {
        return 'ts';
    }
}
