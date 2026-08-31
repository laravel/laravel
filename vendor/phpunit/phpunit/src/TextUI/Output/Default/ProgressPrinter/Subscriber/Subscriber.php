<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output\Default\ProgressPrinter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract readonly class Subscriber
{
    private ProgressPrinter $printer;

    public function __construct(ProgressPrinter $printer)
    {
        $this->printer = $printer;
    }

    protected function printer(): ProgressPrinter
    {
        return $this->printer;
    }
}
