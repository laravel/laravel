<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use function assert;
use function file_get_contents;
use function libxml_clear_errors;
use function libxml_get_errors;
use function libxml_use_internal_errors;
use DOMDocument;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Validator
{
    public function validate(DOMDocument $document, string $xsdFilename): ValidationResult
    {
        $buffer = file_get_contents($xsdFilename);

        assert($buffer !== false);

        $originalErrorHandling = libxml_use_internal_errors(true);

        $document->schemaValidateSource($buffer);

        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($originalErrorHandling);

        return ValidationResult::fromArray($errors);
    }
}
