<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Test;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MockObjectFromWsdlCreated implements Event
{
    private Telemetry\Info $telemetryInfo;
    private string $wsdlFile;

    /**
     * @var class-string
     */
    private string $originalClassName;

    /**
     * @var class-string
     */
    private string $mockClassName;

    /**
     * @var list<string>
     */
    private array $methods;
    private bool $callOriginalConstructor;

    /**
     * @var list<mixed>
     */
    private array $options;

    /**
     * @param class-string $originalClassName
     * @param class-string $mockClassName
     * @param list<string> $methods
     * @param list<mixed>  $options
     */
    public function __construct(Telemetry\Info $telemetryInfo, string $wsdlFile, string $originalClassName, string $mockClassName, array $methods, bool $callOriginalConstructor, array $options)
    {
        $this->telemetryInfo           = $telemetryInfo;
        $this->wsdlFile                = $wsdlFile;
        $this->originalClassName       = $originalClassName;
        $this->mockClassName           = $mockClassName;
        $this->methods                 = $methods;
        $this->callOriginalConstructor = $callOriginalConstructor;
        $this->options                 = $options;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function wsdlFile(): string
    {
        return $this->wsdlFile;
    }

    /**
     * @return class-string
     */
    public function originalClassName(): string
    {
        return $this->originalClassName;
    }

    /**
     * @return class-string
     */
    public function mockClassName(): string
    {
        return $this->mockClassName;
    }

    /**
     * @return list<string>
     */
    public function methods(): array
    {
        return $this->methods;
    }

    public function callOriginalConstructor(): bool
    {
        return $this->callOriginalConstructor;
    }

    /**
     * @return list<mixed>
     */
    public function options(): array
    {
        return $this->options;
    }

    public function asString(): string
    {
        return sprintf(
            'Mock Object Created (%s)',
            $this->wsdlFile,
        );
    }
}
