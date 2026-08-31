<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Logging\TestDox;

use function sprintf;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class HtmlRenderer
{
    /**
     * @var string
     */
    private const PAGE_HEADER = <<<'EOT'
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Test Documentation</title>
        <style>
            body {
                text-rendering: optimizeLegibility;
                font-family: Source SansSerif Pro, Arial, sans-serif;
                font-variant-ligatures: common-ligatures;
                font-kerning: normal;
                margin-left: 2rem;
                background-color: #fff;
                color: #000;
            }

            body > ul > li {
                font-size: larger;
            }

            h2 {
                font-size: larger;
                text-decoration-line: underline;
                text-decoration-thickness: 2px;
                margin: 0;
                padding: 0.5rem 0;
            }

            ul {
                list-style: none;
                margin: 0 0 2rem;
                padding: 0 0 0 1rem;
                text-indent: -1rem;
            }

            .success:before {
                color: #4e9a06;
                content: '✓';
                padding-right: 0.5rem;
            }

            .defect {
                color: #a40000;
            }

            .defect:before {
                color: #a40000;
                content: '✗';
                padding-right: 0.5rem;
            }
        </style>
    </head>
    <body>
EOT;

    /**
     * @var string
     */
    private const CLASS_HEADER = <<<'EOT'

        <h2>%s</h2>
        <ul>

EOT;

    /**
     * @var string
     */
    private const CLASS_FOOTER = <<<'EOT'
        </ul>
EOT;

    /**
     * @var string
     */
    private const PAGE_FOOTER = <<<'EOT'

    </body>
</html>
EOT;

    /**
     * @param array<string, TestResultCollection> $tests
     */
    public function render(array $tests): string
    {
        $buffer = self::PAGE_HEADER;

        foreach ($tests as $prettifiedClassName => $_tests) {
            $buffer .= sprintf(
                self::CLASS_HEADER,
                $prettifiedClassName,
            );

            foreach ($this->reduce($_tests) as $prettifiedMethodName => $outcome) {
                $buffer .= sprintf(
                    "            <li class=\"%s\">%s</li>\n",
                    $outcome,
                    $prettifiedMethodName,
                );
            }

            $buffer .= self::CLASS_FOOTER;
        }

        return $buffer . self::PAGE_FOOTER;
    }

    /**
     * @return array<string, 'defect'|'success'>
     */
    private function reduce(TestResultCollection $tests): array
    {
        $result = [];

        foreach ($tests as $test) {
            $prettifiedMethodName = $test->test()->testDox()->prettifiedMethodName();

            if (!isset($result[$prettifiedMethodName])) {
                $result[$prettifiedMethodName] = $test->status()->isSuccess() ? 'success' : 'defect';

                continue;
            }

            if ($test->status()->isSuccess()) {
                continue;
            }

            $result[$prettifiedMethodName] = 'defect';
        }

        return $result;
    }
}
