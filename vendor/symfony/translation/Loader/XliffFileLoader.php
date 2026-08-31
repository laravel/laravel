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

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Util\Exception\InvalidXmlException;
use Symfony\Component\Config\Util\Exception\XmlParsingException;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Exception\RuntimeException;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Util\XliffUtils;

/**
 * XliffFileLoader loads translations from XLIFF files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class XliffFileLoader implements LoaderInterface
{
    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        if (!class_exists(XmlUtils::class)) {
            throw new RuntimeException('Loading translations from the Xliff format requires the Symfony Config component.');
        }

        if (!$this->isXmlString($resource)) {
            if (!stream_is_local($resource)) {
                throw new InvalidResourceException(\sprintf('This is not a local file "%s".', $resource));
            }

            if (!file_exists($resource)) {
                throw new NotFoundResourceException(\sprintf('File "%s" not found.', $resource));
            }

            if (!is_file($resource)) {
                throw new InvalidResourceException(\sprintf('This is neither a file nor an XLIFF string "%s".', $resource));
            }
        }

        try {
            if ($this->isXmlString($resource)) {
                $dom = XmlUtils::parse($resource);
            } else {
                $dom = XmlUtils::loadFile($resource);
            }
        } catch (\InvalidArgumentException|XmlParsingException|InvalidXmlException $e) {
            throw new InvalidResourceException(\sprintf('Unable to load "%s": ', $resource).$e->getMessage(), $e->getCode(), $e);
        }

        if ($errors = XliffUtils::validateSchema($dom)) {
            throw new InvalidResourceException(\sprintf('Invalid resource provided: "%s"; Errors: ', $resource).XliffUtils::getErrorsAsString($errors));
        }

        $catalogue = new MessageCatalogue($locale);
        $this->extract($dom, $catalogue, $domain);

        if (is_file($resource) && class_exists(FileResource::class)) {
            $catalogue->addResource(new FileResource($resource));
        }

        return $catalogue;
    }

    private function extract(\DOMDocument $dom, MessageCatalogue $catalogue, string $domain): void
    {
        $xliffVersion = XliffUtils::getVersionNumber($dom);

        if ('1.2' === $xliffVersion) {
            $this->extractXliff1($dom, $catalogue, $domain);
        }

        if ('2.0' === $xliffVersion) {
            $this->extractXliff2($dom, $catalogue, $domain);
        }
    }

    /**
     * Extract messages and metadata from DOMDocument into a MessageCatalogue.
     */
    private function extractXliff1(\DOMDocument $dom, MessageCatalogue $catalogue, string $domain): void
    {
        $xml = simplexml_import_dom($dom);
        $encoding = $dom->encoding ? strtoupper($dom->encoding) : null;

        $namespace = 'urn:oasis:names:tc:xliff:document:1.2';
        $xml->registerXPathNamespace('xliff', $namespace);

        foreach ($xml->xpath('//xliff:file') as $file) {
            $fileAttributes = $file->attributes();

            $file->registerXPathNamespace('xliff', $namespace);

            foreach ($file->xpath('.//xliff:prop') as $prop) {
                $catalogue->setCatalogueMetadata($prop->attributes()['prop-type'], (string) $prop, $domain);
            }

            foreach ($file->xpath('.//xliff:trans-unit') as $translation) {
                $attributes = $translation->attributes();

                if (!(isset($attributes['resname']) || isset($translation->source))) {
                    continue;
                }

                $source = (string) (isset($attributes['resname']) && $attributes['resname'] ? $attributes['resname'] : $translation->source);

                if (isset($translation->target)
                    && 'needs-translation' === (string) $translation->target->attributes()['state']
                    && \in_array((string) $translation->target, [$source, (string) $translation->source], true)
                ) {
                    continue;
                }

                // If the xlf file has another encoding specified, try to convert it because
                // simple_xml will always return utf-8 encoded values
                $target = $this->utf8ToCharset((string) ($translation->target ?? $translation->source), $encoding);

                $catalogue->set($source, $target, $domain);

                $metadata = [
                    'source' => (string) $translation->source,
                    'file' => [
                        'original' => (string) $fileAttributes['original'],
                    ],
                ];
                if ($notes = $this->parseNotesMetadata($translation->note, $encoding)) {
                    $metadata['notes'] = $notes;
                }

                if (isset($translation->target) && $translation->target->attributes()) {
                    $metadata['target-attributes'] = [];
                    foreach ($translation->target->attributes() as $key => $value) {
                        $metadata['target-attributes'][$key] = (string) $value;
                    }
                }

                if (isset($attributes['id'])) {
                    $metadata['id'] = (string) $attributes['id'];
                }

                $catalogue->setMetadata($source, $metadata, $domain);
            }
        }
    }

    private function extractXliff2(\DOMDocument $dom, MessageCatalogue $catalogue, string $domain): void
    {
        $xml = simplexml_import_dom($dom);
        $encoding = $dom->encoding ? strtoupper($dom->encoding) : null;

        $xml->registerXPathNamespace('mda', 'urn:oasis:names:tc:xliff:metadata:2.0');

        foreach ($xml->xpath('//mda:meta') as $meta) {
            $catalogue->setCatalogueMetadata($meta->attributes()['type'] ?? '', (string) $meta, $domain);
        }

        $xml->registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:2.0');

        foreach ($xml->xpath('//xliff:unit') as $unit) {
            foreach ($unit->segment as $segment) {
                $attributes = $unit->attributes();
                $source = $attributes['name'] ?? $segment->source;

                // If the xlf file has another encoding specified, try to convert it because
                // simple_xml will always return utf-8 encoded values
                $target = $this->utf8ToCharset((string) ($segment->target ?? $segment->source), $encoding);

                $catalogue->set((string) $source, $target, $domain);

                $metadata = [];
                if ($segment->attributes()) {
                    $metadata['segment-attributes'] = [];
                    foreach ($segment->attributes() as $key => $value) {
                        $metadata['segment-attributes'][$key] = (string) $value;
                    }
                }

                if (isset($segment->target) && $segment->target->attributes()) {
                    $metadata['target-attributes'] = [];
                    foreach ($segment->target->attributes() as $key => $value) {
                        $metadata['target-attributes'][$key] = (string) $value;
                    }
                }

                if (isset($unit->notes)) {
                    $metadata['notes'] = [];
                    foreach ($unit->notes->note as $noteNode) {
                        $note = [];
                        foreach ($noteNode->attributes() as $key => $value) {
                            $note[$key] = (string) $value;
                        }
                        $note['content'] = (string) $noteNode;
                        $metadata['notes'][] = $note;
                    }
                }

                $catalogue->setMetadata((string) $source, $metadata, $domain);
            }
        }
    }

    /**
     * Convert a UTF8 string to the specified encoding.
     */
    private function utf8ToCharset(string $content, ?string $encoding = null): string
    {
        if ('UTF-8' !== $encoding && $encoding) {
            return mb_convert_encoding($content, $encoding, 'UTF-8');
        }

        return $content;
    }

    private function parseNotesMetadata(?\SimpleXMLElement $noteElement = null, ?string $encoding = null): array
    {
        $notes = [];

        if (null === $noteElement) {
            return $notes;
        }

        /** @var \SimpleXMLElement $xmlNote */
        foreach ($noteElement as $xmlNote) {
            $noteAttributes = $xmlNote->attributes();
            $note = ['content' => $this->utf8ToCharset((string) $xmlNote, $encoding)];
            if (isset($noteAttributes['priority'])) {
                $note['priority'] = (int) $noteAttributes['priority'];
            }

            if (isset($noteAttributes['from'])) {
                $note['from'] = (string) $noteAttributes['from'];
            }

            $notes[] = $note;
        }

        return $notes;
    }

    private function isXmlString(string $resource): bool
    {
        return str_starts_with($resource, '<?xml');
    }
}
