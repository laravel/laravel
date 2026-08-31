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

use PHPUnit\TextUI\Help;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ShowHelpCommand implements Command
{
    private int $shellExitCode;

    public function __construct(int $shellExitCode)
    {
        $this->shellExitCode = $shellExitCode;
    }

    public function execute(): Result
    {
        return Result::from(
            (new Help)->generate(),
            $this->shellExitCode,
        );
    }
}
