<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Filter;

use function end;
use function preg_match;
use function sprintf;
use function str_replace;
use function substr;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\PhptTestCase;
use RecursiveFilterIterator;
use RecursiveIterator;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract class NameFilterIterator extends RecursiveFilterIterator
{
    /**
     * @var non-empty-string
     */
    private readonly string $regularExpression;
    private readonly ?int $dataSetMinimum;
    private readonly ?int $dataSetMaximum;

    /**
     * @param RecursiveIterator<int, Test> $iterator
     * @param non-empty-string             $filter
     */
    public function __construct(RecursiveIterator $iterator, string $filter)
    {
        parent::__construct($iterator);

        $preparedFilter = $this->prepareFilter($filter);

        $this->regularExpression = $preparedFilter['regularExpression'];
        $this->dataSetMinimum    = $preparedFilter['dataSetMinimum'];
        $this->dataSetMaximum    = $preparedFilter['dataSetMaximum'];
    }

    public function accept(): bool
    {
        $test = $this->getInnerIterator()->current();

        if ($test instanceof TestSuite) {
            return true;
        }

        if ($test instanceof PhptTestCase) {
            return false;
        }

        $name = $test::class . '::' . $test->nameWithDataSet();

        $accepted = @preg_match($this->regularExpression, $name, $matches) === 1;

        if ($accepted && isset($this->dataSetMaximum)) {
            $set      = end($matches);
            $accepted = $set >= $this->dataSetMinimum && $set <= $this->dataSetMaximum;
        }

        return $this->doAccept($accepted);
    }

    abstract protected function doAccept(bool $result): bool;

    /**
     * @param non-empty-string $filter
     *
     * @return array{regularExpression: non-empty-string, dataSetMinimum: ?int, dataSetMaximum: ?int}
     */
    private function prepareFilter(string $filter): array
    {
        $dataSetMinimum = null;
        $dataSetMaximum = null;

        if (preg_match('/[a-zA-Z0-9]/', substr($filter, 0, 1)) === 1 || @preg_match($filter, '') === false) {
            // Handles:
            //  * testAssertEqualsSucceeds#4
            //  * testAssertEqualsSucceeds#4-8
            if (preg_match('/^(.*?)#(\d+)(?:-(\d+))?$/', $filter, $matches)) {
                if (isset($matches[3]) && $matches[2] < $matches[3]) {
                    $filter = sprintf(
                        '%s.*with data set #(\d+)$',
                        $matches[1],
                    );

                    $dataSetMinimum = (int) $matches[2];
                    $dataSetMaximum = (int) $matches[3];
                } else {
                    $filter = sprintf(
                        '%s.*with data set #%s$',
                        $matches[1],
                        $matches[2],
                    );
                }
            } // Handles:
            //  * testDetermineJsonError@JSON_ERROR_NONE
            //  * testDetermineJsonError@JSON.*
            elseif (preg_match('/^(.*?)@(.+)$/', $filter, $matches)) {
                $filter = sprintf(
                    '%s.*with data set "%s"$',
                    $matches[1],
                    $matches[2],
                );
            }

            // Escape delimiters in regular expression. Do NOT use preg_quote,
            // to keep magic characters.
            $filter = sprintf(
                '/%s/i',
                str_replace(
                    '/',
                    '\\/',
                    $filter,
                ),
            );
        }

        return [
            'regularExpression' => $filter,
            'dataSetMinimum'    => $dataSetMinimum,
            'dataSetMaximum'    => $dataSetMaximum,
        ];
    }
}
