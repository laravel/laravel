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
use PHPUnit\Event\Code;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class AfterLastTestMethodFinished implements Event
{
    private Telemetry\Info $telemetryInfo;

    /**
     * @var class-string
     */
    private string $testClassName;

    /**
     * @var list<Code\ClassMethod>
     */
    private array $calledMethods;

    /**
     * @param class-string $testClassName
     */
    public function __construct(Telemetry\Info $telemetryInfo, string $testClassName, Code\ClassMethod ...$calledMethods)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->testClassName = $testClassName;
        $this->calledMethods = $calledMethods;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    /**
     * @return class-string
     */
    public function testClassName(): string
    {
        return $this->testClassName;
    }

    /**
     * @return list<Code\ClassMethod>
     */
    public function calledMethods(): array
    {
        return $this->calledMethods;
    }

    public function asString(): string
    {
        $buffer = 'After Last Test Method Finished:';

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
