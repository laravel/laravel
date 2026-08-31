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

use const PHP_EOL;
use function sprintf;
use PHPUnit\Event\Code\ClassMethod;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class DataProviderMethodFinished implements Event
{
    private Telemetry\Info $telemetryInfo;
    private ClassMethod $testMethod;

    /**
     * @var list<ClassMethod>
     */
    private array $calledMethods;

    public function __construct(Telemetry\Info $telemetryInfo, ClassMethod $testMethod, ClassMethod ...$calledMethods)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->testMethod    = $testMethod;
        $this->calledMethods = $calledMethods;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function testMethod(): ClassMethod
    {
        return $this->testMethod;
    }

    /**
     * @return list<ClassMethod>
     */
    public function calledMethods(): array
    {
        return $this->calledMethods;
    }

    public function asString(): string
    {
        $buffer = sprintf(
            'Data Provider Method Finished for %s::%s:',
            $this->testMethod->className(),
            $this->testMethod->methodName(),
        );

        foreach ($this->calledMethods as $calledMethod) {
            $buffer .= sprintf(
                PHP_EOL . '- %s::%s',
                $calledMethod->className(),
                $calledMethod->methodName(),
            );
        }

        return $buffer;
    }
}
