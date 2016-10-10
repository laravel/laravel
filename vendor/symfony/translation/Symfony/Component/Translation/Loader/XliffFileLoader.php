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

use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Config\Resource\FileResource;

/**
 * XliffFileLoader loads translations from XLIFF files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class XliffFileLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        if (!stream_is_local($resource)) {
            throw new InvalidResourceException(sprintf('This is not a local file "%s".', $resource));
        }

        if (!file_exists($resource)) {
            throw new NotFoundResourceException(sprintf('File "%s" not found.', $resource));
        }

        list($xml, $encoding) = $this->parseFile($resource);
        $xml->registerXPathNamespace('xliff', 'urn:oasis:names:tc:xliff:document:1.2');

        $catalogue = new MessageCatalogue($locale);
        foreach ($xml->xpath('//xliff:trans-unit') as $translation) {
            $attributes = $translation->attributes();

            if (!(isset($attributes['resname']) || isset($translation->source)) || !isset($translation->target)) {
                continue;
            }

            $source = isset($attributes['resname']) && $attributes['resname'] ? $attributes['resname'] : $translation->source;
            $target = (string) $translation->target;

            // If the xlf file has another encoding specified, try to convert it because
            // simple_xml will always return utf-8 encoded values
            if ('UTF-8' !== $encoding && !empty($encoding)) {
                if (function_exists('mb_convert_encoding')) {
                    $target = mb_convert_encoding($target, $encoding, 'UTF-8');
                } elseif (function_exists('iconv')) {
                    $target = iconv('UTF-8', $encoding, $target);
                } else {
                    throw new \RuntimeException('No suitable convert encoding function (use UTF-8 as your encoding or install the iconv or mbstring extension).');
                }
            }

            $catalogue->set((string) $source, $target, $domain);
        }
        $catalogue->addResource(new FileResource($resource));

        return $catalogue;
    }

    /**
     * Validates and parses the given file into a SimpleXMLElement
     *
     * @param string $file
     *
     * @throws \RuntimeException
     *
     * @return \SimpleXMLElement
     *
     * @throws InvalidResourceException
     */
    private function parseFile($file)
    {
        try {
            $dom = XmlUtils::loadFile($file);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidResourceException(sprintf('Unable to load "%s": %s', $file, $e->getMessage()), $e->getCode(), $e);
        }

        $internalErrors = libxml_use_internal_errors(true);

        $location = str_replace('\\', '/', __DIR__).'/schema/dic/xliff-core/xml.xsd';
        $parts = explode('/', $location);
        if (0 === stripos($location, 'phar://')) {
            $tmpfile = tempnam(sys_get_temp_dir(), 'sf2');
            if ($tmpfile) {
                copy($location, $tmpfile);
                $parts = explode('/', str_replace('\\', '/', $tmpfile));
            }
        }
        $drive = '\\' === DIRECTORY_SEPARATOR ? array_shift($parts).'/' : '';
        $location = 'file:///'.$drive.implode('/', array_map('rawurlencode', $parts));

        $source = file_get_contents(__DIR__.'/schema/dic/xliff-core/xliff-core-1.2-strict.xsd');
        $source = str_replace('http://www.w3.org/2001/xml.xsd', $location, $source);

        if (!@$dom->schemaValidateSource($source)) {
            throw new InvalidResourceException(implode("\n", $this->getXmlErrors($internalErrors)));
        }

        $dom->normalizeDocument();

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return array(simplexml_import_dom($dom), strtoupper($dom->encoding));
    }

    /**
     * Returns the XML errors of the internal XML parser
     *
     * @param bool $internalErrors
     *
     * @return array An array of errors
     */
    private function getXmlErrors($internalErrors)
    {
        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf('[%s %s] %s (in %s - line %d, column %d)',
                LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                $error->code,
                trim($error->message),
                $error->file ? $error->file : 'n/a',
                $error->line,
                $error->column
            );
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return $errors;
    }
}
