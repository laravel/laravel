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

use PHPUnit\Util\Xml\XmlException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
abstract readonly class SchemaDetectionResult
{
    /**
     * @phpstan-assert-if-true SuccessfulSchemaDetectionResult $this
     */
    public function detected(): bool
    {
        return false;
    }

    /**
     * @throws XmlException
     */
    public function version(): string
    {
        throw new XmlException('No supported schema was detected');
    }
}
