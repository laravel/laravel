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

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * XliffFileDumper generates xliff files from a message catalogue.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
class XliffFileDumper extends FileDumper
{
    public function __construct(
        private string $extension = 'xlf',
    ) {
    }

    public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []): string
    {
        $xliffVersion = '1.2';
        if (\array_key_exists('xliff_version', $options)) {
            $xliffVersion = $options['xliff_version'];
        }

        if (\array_key_exists('default_locale', $options)) {
            $defaultLocale = $options['default_locale'];
        } else {
            $defaultLocale = \Locale::getDefault();
        }

        if ('1.2' === $xliffVersion) {
            return $this->dumpXliff1($defaultLocale, $messages, $domain, $options);
        }
        if ('2.0' === $xliffVersion) {
            return $this->dumpXliff2($defaultLocale, $messages, $domain);
        }

        throw new InvalidArgumentException(\sprintf('No support implemented for dumping XLIFF version "%s".', $xliffVersion));
    }

    protected function getExtension(): string
    {
        return $this->extension;
    }

    private function dumpXliff1(string $defaultLocale, MessageCatalogue $messages, ?string $domain, array $options = []): string
    {
        $toolInfo = ['tool-id' => 'symfony', 'tool-name' => 'Symfony'];
        if (\array_key_exists('tool_info', $options)) {
            $toolInfo = array_merge($toolInfo, $options['tool_info']);
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $xliff = $dom->appendChild($dom->createElement('xliff'));
        $xliff->setAttribute('version', '1.2');
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');

        $xliffFile = $xliff->appendChild($dom->createElement('file'));
        $xliffFile->setAttribute('source-language', str_replace('_', '-', $defaultLocale));
        $xliffFile->setAttribute('target-language', str_replace('_', '-', $messages->getLocale()));
        $xliffFile->setAttribute('datatype', 'plaintext');
        $xliffFile->setAttribute('original', 'file.ext');

        $xliffHead = $xliffFile->appendChild($dom->createElement('header'));
        $xliffTool = $xliffHead->appendChild($dom->createElement('tool'));
        foreach ($toolInfo as $id => $value) {
            $xliffTool->setAttribute($id, $value);
        }

        if ($catalogueMetadata = $messages->getCatalogueMetadata('', $domain) ?? []) {
            $xliffPropGroup = $xliffHead->appendChild($dom->createElement('prop-group'));
            foreach ($catalogueMetadata as $key => $value) {
                $xliffProp = $xliffPropGroup->appendChild($dom->createElement('prop'));
                $xliffProp->setAttribute('prop-type', $key);
                $xliffProp->appendChild($dom->createTextNode($value));
            }
        }

        $xliffBody = $xliffFile->appendChild($dom->createElement('body'));
        foreach ($messages->all($domain) as $source => $target) {
            $translation = $dom->createElement('trans-unit');

            $translation->setAttribute('id', strtr(substr(base64_encode(hash('xxh128', $source, true)), 0, 7), '/+', '._'));
            $translation->setAttribute('resname', $source);

            $s = $translation->appendChild($dom->createElement('source'));
            $s->appendChild($dom->createTextNode($source));

            // Does the target contain characters requiring a CDATA section?
            $text = 1 === preg_match('/[&<>]/', $target) ? $dom->createCDATASection($target) : $dom->createTextNode($target);

            $targetElement = $dom->createElement('target');
            $metadata = $messages->getMetadata($source, $domain);
            if ($this->hasMetadataArrayInfo('target-attributes', $metadata)) {
                foreach ($metadata['target-attributes'] as $name => $value) {
                    $targetElement->setAttribute($name, $value);
                }
            }
            $t = $translation->appendChild($targetElement);
            $t->appendChild($text);

            if ($this->hasMetadataArrayInfo('notes', $metadata)) {
                foreach ($metadata['notes'] as $note) {
                    if (!isset($note['content'])) {
                        continue;
                    }

                    $n = $translation->appendChild($dom->createElement('note'));
                    $n->appendChild($dom->createTextNode($note['content']));

                    if (isset($note['priority'])) {
                        $n->setAttribute('priority', $note['priority']);
                    }

                    if (isset($note['from'])) {
                        $n->setAttribute('from', $note['from']);
                    }
                }
            }

            $xliffBody->appendChild($translation);
        }

        return $dom->saveXML();
    }

    private function dumpXliff2(string $defaultLocale, MessageCatalogue $messages, ?string $domain): string
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $xliff = $dom->appendChild($dom->createElement('xliff'));
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:2.0');
        $xliff->setAttribute('version', '2.0');
        $xliff->setAttribute('srcLang', str_replace('_', '-', $defaultLocale));
        $xliff->setAttribute('trgLang', str_replace('_', '-', $messages->getLocale()));

        $xliffFile = $xliff->appendChild($dom->createElement('file'));
        if (str_ends_with($domain, MessageCatalogue::INTL_DOMAIN_SUFFIX)) {
            $xliffFile->setAttribute('id', substr($domain, 0, -\strlen(MessageCatalogue::INTL_DOMAIN_SUFFIX)).'.'.$messages->getLocale());
        } else {
            $xliffFile->setAttribute('id', $domain.'.'.$messages->getLocale());
        }

        if ($catalogueMetadata = $messages->getCatalogueMetadata('', $domain) ?? []) {
            $xliff->setAttribute('xmlns:mda', 'urn:oasis:names:tc:xliff:metadata:2.0');
            $xliffMetadata = $xliffFile->appendChild($dom->createElement('mda:metadata'));
            $xliffMetaGroup = $xliffMetadata->appendChild($dom->createElement('mda:metaGroup'));
            foreach ($catalogueMetadata as $key => $value) {
                $xliffMeta = $xliffMetaGroup->appendChild($dom->createElement('mda:meta'));
                $xliffMeta->setAttribute('type', $key);
                $xliffMeta->appendChild($dom->createTextNode($value));
            }
        }

        foreach ($messages->all($domain) as $source => $target) {
            $translation = $dom->createElement('unit');
            $translation->setAttribute('id', strtr(substr(base64_encode(hash('xxh128', $source, true)), 0, 7), '/+', '._'));

            if (\strlen($source) <= 80) {
                $translation->setAttribute('name', $source);
            }

            $metadata = $messages->getMetadata($source, $domain);

            // Add notes section
            if ($this->hasMetadataArrayInfo('notes', $metadata) && $metadata['notes']) {
                $notesElement = $dom->createElement('notes');
                foreach ($metadata['notes'] as $note) {
                    $n = $dom->createElement('note');
                    $n->appendChild($dom->createTextNode($note['content'] ?? ''));
                    unset($note['content']);

                    foreach ($note as $name => $value) {
                        $n->setAttribute($name, $value);
                    }
                    $notesElement->appendChild($n);
                }
                $translation->appendChild($notesElement);
            }

            $segment = $translation->appendChild($dom->createElement('segment'));

            if ($this->hasMetadataArrayInfo('segment-attributes', $metadata)) {
                foreach ($metadata['segment-attributes'] as $name => $value) {
                    $segment->setAttribute($name, $value);
                }
            }

            $s = $segment->appendChild($dom->createElement('source'));
            $s->appendChild($dom->createTextNode($source));

            // Does the target contain characters requiring a CDATA section?
            $text = 1 === preg_match('/[&<>]/', $target) ? $dom->createCDATASection($target) : $dom->createTextNode($target);

            $targetElement = $dom->createElement('target');
            if ($this->hasMetadataArrayInfo('target-attributes', $metadata)) {
                foreach ($metadata['target-attributes'] as $name => $value) {
                    $targetElement->setAttribute($name, $value);
                }
            }
            $t = $segment->appendChild($targetElement);
            $t->appendChild($text);

            $xliffFile->appendChild($translation);
        }

        return $dom->saveXML();
    }

    private function hasMetadataArrayInfo(string $key, ?array $metadata = null): bool
    {
        return is_iterable($metadata[$key] ?? null);
    }
}
