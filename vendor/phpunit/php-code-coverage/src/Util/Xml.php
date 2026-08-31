<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Util;

use const PHP_EOL;
use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use DOMDocument;
use SebastianBergmann\CodeCoverage\XmlException;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final readonly class Xml
{
    /**
     * @throws XmlException
     *
     * @see https://bugs.php.net/bug.php?id=79191
     */
    public static function asString(DOMDocument $document): string
    {
        $xmlErrorHandling = libxml_use_internal_errors(true);

        $document->formatOutput       = true;
        $document->preserveWhiteSpace = false;

        $buffer = $document->saveXML();

        if ($buffer === false) {
            $message = 'Unable to generate the XML';

            foreach (libxml_get_errors() as $error) {
                $message .= PHP_EOL . $error->message;
            }

            throw new XmlException($message);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($xmlErrorHandling);

        return $buffer;
    }
}
