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

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @immutable
 */
final readonly class SuccessfulSchemaDetectionResult extends SchemaDetectionResult
{
    /**
     * @var non-empty-string
     */
    private string $version;

    /**
     * @param non-empty-string $version
     */
    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function detected(): bool
    {
        return true;
    }

    /**
     * @throws void
     *
     * @return non-empty-string
     */
    public function version(): string
    {
        return $this->version;
    }
}
