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

use PHPUnit\Util\Xml\Loader;
use PHPUnit\Util\Xml\XmlException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class SchemaDetector
{
    /**
     * @throws XmlException
     */
    public function detect(string $filename): SchemaDetectionResult
    {
        $document = (new Loader)->loadFile($filename);

        $schemaFinder = new SchemaFinder;

        foreach ($schemaFinder->available() as $candidate) {
            $schema = (new SchemaFinder)->find($candidate);

            if (!(new Validator)->validate($document, $schema)->hasValidationErrors()) {
                return new SuccessfulSchemaDetectionResult($candidate);
            }
        }

        return new FailedSchemaDetectionResult;
    }
}
