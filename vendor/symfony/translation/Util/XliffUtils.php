<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Util;

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\InvalidResourceException;

/**
 * Provides some utility methods for XLIFF translation files, such as validating
 * their contents according to the XSD schema.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class XliffUtils
{
    /**
     * Gets xliff file version based on the root "version" attribute.
     *
     * Defaults to 1.2 for backwards compatibility.
     *
     * @throws InvalidArgumentException
     */
    public static function getVersionNumber(\DOMDocument $dom): string
    {
        foreach ($dom->getElementsByTagName('xliff') as $xliff) {
            $version = $xliff->attributes->getNamedItem('version');
            if ($version) {
                return $version->nodeValue;
            }

            $namespace = $xliff->attributes->getNamedItem('xmlns');
            if ($namespace) {
                if (0 !== substr_compare('urn:oasis:names:tc:xliff:document:', $namespace->nodeValue, 0, 34)) {
                    throw new InvalidArgumentException(\sprintf('Not a valid XLIFF namespace "%s".', $namespace));
                }

                return substr($namespace, 34);
            }
        }

        // Falls back to v1.2
        return '1.2';
    }

    /**
     * Validates and parses the given file into a DOMDocument.
     *
     * @throws InvalidResourceException
     */
    public static function validateSchema(\DOMDocument $dom): array
    {
        $xliffVersion = static::getVersionNumber($dom);
        $internalErrors = libxml_use_internal_errors(true);
        if ($shouldEnable = self::shouldEnableEntityLoader()) {
            $disableEntities = libxml_disable_entity_loader(false);
        }
        try {
            $isValid = @$dom->schemaValidateSource(self::getSchema($xliffVersion));
            if (!$isValid) {
                return self::getXmlErrors($internalErrors);
            }
        } finally {
            if ($shouldEnable) {
                libxml_disable_entity_loader($disableEntities);
            }
        }

        $dom->normalizeDocument();

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return [];
    }

    public static function getErrorsAsString(array $xmlErrors): string
    {
        $errorsAsString = '';

        foreach ($xmlErrors as $error) {
            $errorsAsString .= \sprintf("[%s %s] %s (in %s - line %d, column %d)\n",
                \LIBXML_ERR_WARNING === $error['level'] ? 'WARNING' : 'ERROR',
                $error['code'],
                $error['message'],
                $error['file'],
                $error['line'],
                $error['column']
            );
        }

        return $errorsAsString;
    }

    private static function shouldEnableEntityLoader(): bool
    {
        static $dom, $schema;
        if (null === $dom) {
            $dom = new \DOMDocument();
            $dom->loadXML('<?xml version="1.0"?><test/>');

            $tmpfile = tempnam(sys_get_temp_dir(), 'symfony');
            register_shutdown_function(static function () use ($tmpfile) {
                @unlink($tmpfile);
            });
            $schema = '<?xml version="1.0" encoding="utf-8"?>
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <xsd:include schemaLocation="'.self::getFileUrl($tmpfile).'" />
</xsd:schema>';
            file_put_contents($tmpfile, '<?xml version="1.0" encoding="utf-8"?>
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <xsd:element name="test" type="testType" />
  <xsd:complexType name="testType"/>
</xsd:schema>');
        }

        return !@$dom->schemaValidateSource($schema);
    }

    private static function getFileUrl(string $path): string
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $parts = explode('/', str_replace('\\', '/', $path));
            $drive = array_shift($parts).'/';
        } else {
            $parts = explode('/', $path);
            $drive = '';
        }

        return 'file:///'.$drive.implode('/', array_map('rawurlencode', $parts));
    }

    private static function getSchema(string $xliffVersion): string
    {
        if ('1.2' === $xliffVersion) {
            $schemaSource = file_get_contents(__DIR__.'/../Resources/schemas/xliff-core-1.2-transitional.xsd');
            $xmlUri = 'http://www.w3.org/2001/xml.xsd';
        } elseif ('2.0' === $xliffVersion) {
            $schemaSource = file_get_contents(__DIR__.'/../Resources/schemas/xliff-core-2.0.xsd');
            $xmlUri = 'informativeCopiesOf3rdPartySchemas/w3c/xml.xsd';
        } else {
            throw new InvalidArgumentException(\sprintf('No support implemented for loading XLIFF version "%s".', $xliffVersion));
        }

        return self::fixXmlLocation($schemaSource, $xmlUri);
    }

    /**
     * Internally changes the URI of a dependent xsd to be loaded locally.
     */
    private static function fixXmlLocation(string $schemaSource, string $xmlUri): string
    {
        $path = __DIR__.'/../Resources/schemas/xml.xsd';

        if (0 === stripos($path, 'phar://')) {
            if ($tmpfile = tempnam(sys_get_temp_dir(), 'symfony')) {
                copy($path, $tmpfile);
                $newPath = self::getFileUrl($tmpfile);
            } else {
                $parts = explode('/', '\\' === \DIRECTORY_SEPARATOR ? str_replace('\\', '/', $path) : $path);
                array_shift($parts);
                $drive = '\\' === \DIRECTORY_SEPARATOR ? array_shift($parts).'/' : '';
                $newPath = 'phar:///'.$drive.implode('/', array_map('rawurlencode', $parts));
            }
        } else {
            $newPath = self::getFileUrl($path);
        }

        return str_replace($xmlUri, $newPath, $schemaSource);
    }

    /**
     * Returns the XML errors of the internal XML parser.
     */
    private static function getXmlErrors(bool $internalErrors): array
    {
        $errors = [];
        foreach (libxml_get_errors() as $error) {
            $errors[] = [
                'level' => \LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                'code' => $error->code,
                'message' => trim($error->message),
                'file' => $error->file ?: 'n/a',
                'line' => $error->line,
                'column' => $error->column,
            ];
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return $errors;
    }
}
