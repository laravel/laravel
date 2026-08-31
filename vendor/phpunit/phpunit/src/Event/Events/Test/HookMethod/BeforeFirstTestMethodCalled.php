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
use PHPUnit\Event\Code;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class BeforeFirstTestMethodCalled implements Event
{
    private Telemetry\Info $telemetryInfo;

    /**
     * @var class-string
     */
    private string $testClassName;
    private Code\ClassMethod $calledMethod;

    /**
     * @param class-string $testClassName
     */
    public function __construct(Telemetry\Info $telemetryInfo, string $testClassName, Code\ClassMethod $calledMethod)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->testClassName = $testClassName;
        $this->calledMethod  = $calledMethod;
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

    public function calledMethod(): Code\ClassMethod
    {
        return $this->calledMethod;
    }

    public function asString(): string
    {
        return sprintf(
            'Before First Test Method Called (%s::%s)',
            $this->calledMethod->className(),
            $this->calledMethod->methodName(),
        );
    }
}
