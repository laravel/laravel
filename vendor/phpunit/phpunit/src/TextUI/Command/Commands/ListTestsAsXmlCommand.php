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

use const PHP_EOL;
use function assert;
use function file_put_contents;
use function ksort;
use function sprintf;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;
use ReflectionClass;
use XMLWriter;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ListTestsAsXmlCommand implements Command
{
    /**
     * @var list<PhptTestCase|TestCase>
     */
    private array $tests;
    private string $filename;

    /**
     * @param list<PhptTestCase|TestCase> $tests
     */
    public function __construct(array $tests, string $filename)
    {
        $this->tests    = $tests;
        $this->filename = $filename;
    }

    public function execute(): Result
    {
        $writer = new XMLWriter;

        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument();

        $writer->startElement('testSuite');
        $writer->writeAttribute('xmlns', 'https://xml.phpunit.de/testSuite');

        $writer->startElement('tests');

        $currentTestClass = null;
        $groups           = [];

        foreach ($this->tests as $test) {
            if ($test instanceof TestCase) {
                foreach ($test->groups() as $group) {
                    if (!isset($groups[$group])) {
                        $groups[$group] = [];
                    }

                    $groups[$group][] = $test->valueObjectForEvents()->id();
                }

                if ($test::class !== $currentTestClass) {
                    if ($currentTestClass !== null) {
                        $writer->endElement();
                    }

                    $file = (new ReflectionClass($test))->getFileName();

                    assert($file !== false);

                    $writer->startElement('testClass');
                    $writer->writeAttribute('name', $test::class);
                    $writer->writeAttribute('file', $file);

                    $currentTestClass = $test::class;
                }

                $writer->startElement('testMethod');
                $writer->writeAttribute('id', $test->valueObjectForEvents()->id());
                $writer->writeAttribute('name', $test->valueObjectForEvents()->methodName());
                $writer->endElement();

                continue;
            }

            if ($currentTestClass !== null) {
                $writer->endElement();

                $currentTestClass = null;
            }

            $writer->startElement('phpt');
            $writer->writeAttribute('file', $test->getName());
            $writer->endElement();
        }

        if ($currentTestClass !== null) {
            $writer->endElement();
        }

        $writer->endElement();

        ksort($groups);

        $writer->startElement('groups');

        foreach ($groups as $groupName => $testIds) {
            $writer->startElement('group');
            $writer->writeAttribute('name', (string) $groupName);

            foreach ($testIds as $testId) {
                $writer->startElement('test');
                $writer->writeAttribute('id', $testId);
                $writer->endElement();
            }

            $writer->endElement();
        }

        $writer->endElement();
        $writer->endElement();

        file_put_contents($this->filename, $writer->outputMemory());

        return Result::from(
            sprintf(
                'Wrote list of tests that would have been run to %s' . PHP_EOL,
                $this->filename,
            ),
        );
    }
}
