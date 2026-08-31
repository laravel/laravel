<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Command;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Result
{
    public const SUCCESS   = 0;
    public const FAILURE   = 1;
    public const EXCEPTION = 2;
    public const CRASH     = 255;
    private string $output;
    private int $shellExitCode;

    public static function from(string $output = '', int $shellExitCode = self::SUCCESS): self
    {
        return new self($output, $shellExitCode);
    }

    private function __construct(string $output, int $shellExitCode)
    {
        $this->output        = $output;
        $this->shellExitCode = $shellExitCode;
    }

    public function output(): string
    {
        return $this->output;
    }

    public function shellExitCode(): int
    {
        return $this->shellExitCode;
    }
}
