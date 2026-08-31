<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI;

use PHPUnit\TestRunner\TestResult\TestResult;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ShellExitCodeCalculator
{
    private const SUCCESS_EXIT   = 0;
    private const FAILURE_EXIT   = 1;
    private const EXCEPTION_EXIT = 2;

    public function calculate(Configuration $configuration, TestResult $result): int
    {
        $failOnDeprecation        = false;
        $failOnPhpunitDeprecation = false;
        $failOnPhpunitWarning     = false;
        $failOnEmptyTestSuite     = false;
        $failOnIncomplete         = false;
        $failOnNotice             = false;
        $failOnRisky              = false;
        $failOnSkipped            = false;
        $failOnWarning            = false;

        if ($configuration->failOnAllIssues()) {
            $failOnDeprecation        = true;
            $failOnPhpunitDeprecation = true;
            $failOnPhpunitWarning     = true;
            $failOnEmptyTestSuite     = true;
            $failOnIncomplete         = true;
            $failOnNotice             = true;
            $failOnRisky              = true;
            $failOnSkipped            = true;
            $failOnWarning            = true;
        }

        if ($configuration->failOnDeprecation()) {
            $failOnDeprecation = true;
        }

        if ($configuration->doNotFailOnDeprecation()) {
            $failOnDeprecation = false;
        }

        if ($configuration->failOnPhpunitDeprecation()) {
            $failOnPhpunitDeprecation = true;
        }

        if ($configuration->doNotFailOnPhpunitDeprecation()) {
            $failOnPhpunitDeprecation = false;
        }

        if ($configuration->failOnPhpunitWarning()) {
            $failOnPhpunitWarning = true;
        }

        if ($configuration->doNotFailOnPhpunitWarning()) {
            $failOnPhpunitWarning = false;
        }

        if ($configuration->failOnEmptyTestSuite()) {
            $failOnEmptyTestSuite = true;
        }

        if ($configuration->doNotFailOnEmptyTestSuite()) {
            $failOnEmptyTestSuite = false;
        }

        if ($configuration->failOnIncomplete()) {
            $failOnIncomplete = true;
        }

        if ($configuration->doNotFailOnIncomplete()) {
            $failOnIncomplete = false;
        }

        if ($configuration->failOnNotice()) {
            $failOnNotice = true;
        }

        if ($configuration->doNotFailOnNotice()) {
            $failOnNotice = false;
        }

        if ($configuration->failOnRisky()) {
            $failOnRisky = true;
        }

        if ($configuration->doNotFailOnRisky()) {
            $failOnRisky = false;
        }

        if ($configuration->failOnSkipped()) {
            $failOnSkipped = true;
        }

        if ($configuration->doNotFailOnSkipped()) {
            $failOnSkipped = false;
        }

        if ($configuration->failOnWarning()) {
            $failOnWarning = true;
        }

        if ($configuration->doNotFailOnWarning()) {
            $failOnWarning = false;
        }

        $returnCode = self::FAILURE_EXIT;

        if ($result->wasSuccessful()) {
            $returnCode = self::SUCCESS_EXIT;
        }

        if ($failOnEmptyTestSuite && !$result->hasTests()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnDeprecation && $result->hasPhpOrUserDeprecations()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnPhpunitDeprecation && $result->hasPhpunitDeprecations()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnPhpunitWarning && $result->hasPhpunitWarnings()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnIncomplete && $result->hasIncompleteTests()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnNotice && $result->hasNotices()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnRisky && $result->hasRiskyTests()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnSkipped && $result->hasSkippedTests()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($failOnWarning && $result->hasWarnings()) {
            $returnCode = self::FAILURE_EXIT;
        }

        if ($result->hasErrors()) {
            $returnCode = self::EXCEPTION_EXIT;
        }

        return $returnCode;
    }
}
