<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Html;

use const ENT_COMPAT;
use const ENT_HTML401;
use const ENT_SUBSTITUTE;
use const T_ABSTRACT;
use const T_ARRAY;
use const T_AS;
use const T_BREAK;
use const T_CALLABLE;
use const T_CASE;
use const T_CATCH;
use const T_CLASS;
use const T_CLONE;
use const T_COMMENT;
use const T_CONST;
use const T_CONTINUE;
use const T_DECLARE;
use const T_DEFAULT;
use const T_DO;
use const T_DOC_COMMENT;
use const T_ECHO;
use const T_ELSE;
use const T_ELSEIF;
use const T_EMPTY;
use const T_ENDDECLARE;
use const T_ENDFOR;
use const T_ENDFOREACH;
use const T_ENDIF;
use const T_ENDSWITCH;
use const T_ENDWHILE;
use const T_ENUM;
use const T_EVAL;
use const T_EXIT;
use const T_EXTENDS;
use const T_FINAL;
use const T_FINALLY;
use const T_FN;
use const T_FOR;
use const T_FOREACH;
use const T_FUNCTION;
use const T_GLOBAL;
use const T_GOTO;
use const T_HALT_COMPILER;
use const T_IF;
use const T_IMPLEMENTS;
use const T_INCLUDE;
use const T_INCLUDE_ONCE;
use const T_INLINE_HTML;
use const T_INSTANCEOF;
use const T_INSTEADOF;
use const T_INTERFACE;
use const T_ISSET;
use const T_LIST;
use const T_MATCH;
use const T_NAMESPACE;
use const T_NEW;
use const T_PRINT;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_READONLY;
use const T_REQUIRE;
use const T_REQUIRE_ONCE;
use const T_RETURN;
use const T_STATIC;
use const T_SWITCH;
use const T_THROW;
use const T_TRAIT;
use const T_TRY;
use const T_UNSET;
use const T_USE;
use const T_VAR;
use const T_WHILE;
use const T_YIELD;
use const T_YIELD_FROM;
use const TOKEN_PARSE;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_pop;
use function array_unique;
use function count;
use function explode;
use function file_get_contents;
use function htmlspecialchars;
use function is_string;
use function ksort;
use function range;
use function sort;
use function sprintf;
use function str_ends_with;
use function str_replace;
use function token_get_all;
use function trim;
use SebastianBergmann\CodeCoverage\FileCouldNotBeWrittenException;
use SebastianBergmann\CodeCoverage\Node\File as FileNode;
use SebastianBergmann\CodeCoverage\Util\Percentage;
use SebastianBergmann\Template\Exception;
use SebastianBergmann\Template\Template;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class File extends Renderer
{
    /**
     * @var array<int,true>
     */
    private const KEYWORD_TOKENS = [
        T_ABSTRACT      => true,
        T_ARRAY         => true,
        T_AS            => true,
        T_BREAK         => true,
        T_CALLABLE      => true,
        T_CASE          => true,
        T_CATCH         => true,
        T_CLASS         => true,
        T_CLONE         => true,
        T_CONST         => true,
        T_CONTINUE      => true,
        T_DECLARE       => true,
        T_DEFAULT       => true,
        T_DO            => true,
        T_ECHO          => true,
        T_ELSE          => true,
        T_ELSEIF        => true,
        T_EMPTY         => true,
        T_ENDDECLARE    => true,
        T_ENDFOR        => true,
        T_ENDFOREACH    => true,
        T_ENDIF         => true,
        T_ENDSWITCH     => true,
        T_ENDWHILE      => true,
        T_ENUM          => true,
        T_EVAL          => true,
        T_EXIT          => true,
        T_EXTENDS       => true,
        T_FINAL         => true,
        T_FINALLY       => true,
        T_FN            => true,
        T_FOR           => true,
        T_FOREACH       => true,
        T_FUNCTION      => true,
        T_GLOBAL        => true,
        T_GOTO          => true,
        T_HALT_COMPILER => true,
        T_IF            => true,
        T_IMPLEMENTS    => true,
        T_INCLUDE       => true,
        T_INCLUDE_ONCE  => true,
        T_INSTANCEOF    => true,
        T_INSTEADOF     => true,
        T_INTERFACE     => true,
        T_ISSET         => true,
        T_LIST          => true,
        T_MATCH         => true,
        T_NAMESPACE     => true,
        T_NEW           => true,
        T_PRINT         => true,
        T_PRIVATE       => true,
        T_PROTECTED     => true,
        T_PUBLIC        => true,
        T_READONLY      => true,
        T_REQUIRE       => true,
        T_REQUIRE_ONCE  => true,
        T_RETURN        => true,
        T_STATIC        => true,
        T_SWITCH        => true,
        T_THROW         => true,
        T_TRAIT         => true,
        T_TRY           => true,
        T_UNSET         => true,
        T_USE           => true,
        T_VAR           => true,
        T_WHILE         => true,
        T_YIELD         => true,
        T_YIELD_FROM    => true,
    ];
    private static array $formattedSourceCache = [];
    private int $htmlSpecialCharsFlags         = ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE;

    public function render(FileNode $node, string $file): void
    {
        $templateName = $this->templatePath . ($this->hasBranchCoverage ? 'file_branch.html' : 'file.html');
        $template     = new Template($templateName, '{{', '}}');
        $this->setCommonTemplateVariables($template, $node);

        $template->setVar(
            [
                'items'     => $this->renderItems($node),
                'lines'     => $this->renderSourceWithLineCoverage($node),
                'legend'    => '<p><span class="legend covered-by-small-tests">Covered by small (and larger) tests</span><span class="legend covered-by-medium-tests">Covered by medium (and large) tests</span><span class="legend covered-by-large-tests">Covered by large tests (and tests of unknown size)</span><span class="legend not-covered">Not covered</span><span class="legend not-coverable">Not coverable</span></p>',
                'structure' => '',
            ],
        );

        try {
            $template->renderTo($file . '.html');
        } catch (Exception $e) {
            throw new FileCouldNotBeWrittenException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        if ($this->hasBranchCoverage) {
            $template->setVar(
                [
                    'items'     => $this->renderItems($node),
                    'lines'     => $this->renderSourceWithBranchCoverage($node),
                    'legend'    => '<p><span class="success"><strong>Fully covered</strong></span><span class="warning"><strong>Partially covered</strong></span><span class="danger"><strong>Not covered</strong></span></p>',
                    'structure' => $this->renderBranchStructure($node),
                ],
            );

            try {
                $template->renderTo($file . '_branch.html');
            } catch (Exception $e) {
                throw new FileCouldNotBeWrittenException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e,
                );
            }

            $template->setVar(
                [
                    'items'     => $this->renderItems($node),
                    'lines'     => $this->renderSourceWithPathCoverage($node),
                    'legend'    => '<p><span class="success"><strong>Fully covered</strong></span><span class="warning"><strong>Partially covered</strong></span><span class="danger"><strong>Not covered</strong></span></p>',
                    'structure' => $this->renderPathStructure($node),
                ],
            );

            try {
                $template->renderTo($file . '_path.html');
            } catch (Exception $e) {
                throw new FileCouldNotBeWrittenException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e,
                );
            }
        }
    }

    private function renderItems(FileNode $node): string
    {
        $templateName = $this->templatePath . ($this->hasBranchCoverage ? 'file_item_branch.html' : 'file_item.html');
        $template     = new Template($templateName, '{{', '}}');

        $methodTemplateName = $this->templatePath . ($this->hasBranchCoverage ? 'method_item_branch.html' : 'method_item.html');
        $methodItemTemplate = new Template(
            $methodTemplateName,
            '{{',
            '}}',
        );

        $items = $this->renderItemTemplate(
            $template,
            [
                'name'                            => 'Total',
                'numClasses'                      => $node->numberOfClassesAndTraits(),
                'numTestedClasses'                => $node->numberOfTestedClassesAndTraits(),
                'numMethods'                      => $node->numberOfFunctionsAndMethods(),
                'numTestedMethods'                => $node->numberOfTestedFunctionsAndMethods(),
                'linesExecutedPercent'            => $node->percentageOfExecutedLines()->asFloat(),
                'linesExecutedPercentAsString'    => $node->percentageOfExecutedLines()->asString(),
                'numExecutedLines'                => $node->numberOfExecutedLines(),
                'numExecutableLines'              => $node->numberOfExecutableLines(),
                'branchesExecutedPercent'         => $node->percentageOfExecutedBranches()->asFloat(),
                'branchesExecutedPercentAsString' => $node->percentageOfExecutedBranches()->asString(),
                'numExecutedBranches'             => $node->numberOfExecutedBranches(),
                'numExecutableBranches'           => $node->numberOfExecutableBranches(),
                'pathsExecutedPercent'            => $node->percentageOfExecutedPaths()->asFloat(),
                'pathsExecutedPercentAsString'    => $node->percentageOfExecutedPaths()->asString(),
                'numExecutedPaths'                => $node->numberOfExecutedPaths(),
                'numExecutablePaths'              => $node->numberOfExecutablePaths(),
                'testedMethodsPercent'            => $node->percentageOfTestedFunctionsAndMethods()->asFloat(),
                'testedMethodsPercentAsString'    => $node->percentageOfTestedFunctionsAndMethods()->asString(),
                'testedClassesPercent'            => $node->percentageOfTestedClassesAndTraits()->asFloat(),
                'testedClassesPercentAsString'    => $node->percentageOfTestedClassesAndTraits()->asString(),
                'crap'                            => '<abbr title="Change Risk Anti-Patterns (CRAP) Index">CRAP</abbr>',
            ],
        );

        $items .= $this->renderFunctionItems(
            $node->functions(),
            $methodItemTemplate,
        );

        $items .= $this->renderTraitOrClassItems(
            $node->traits(),
            $template,
            $methodItemTemplate,
        );

        $items .= $this->renderTraitOrClassItems(
            $node->classes(),
            $template,
            $methodItemTemplate,
        );

        return $items;
    }

    private function renderTraitOrClassItems(array $items, Template $template, Template $methodItemTemplate): string
    {
        $buffer = '';

        if (empty($items)) {
            return $buffer;
        }

        foreach ($items as $name => $item) {
            $numMethods       = 0;
            $numTestedMethods = 0;

            foreach ($item['methods'] as $method) {
                if ($method['executableLines'] > 0) {
                    $numMethods++;

                    if ($method['executedLines'] === $method['executableLines']) {
                        $numTestedMethods++;
                    }
                }
            }

            if ($item['executableLines'] > 0) {
                $numClasses                   = 1;
                $numTestedClasses             = $numTestedMethods === $numMethods ? 1 : 0;
                $linesExecutedPercentAsString = Percentage::fromFractionAndTotal(
                    $item['executedLines'],
                    $item['executableLines'],
                )->asString();
                $branchesExecutedPercentAsString = Percentage::fromFractionAndTotal(
                    $item['executedBranches'],
                    $item['executableBranches'],
                )->asString();
                $pathsExecutedPercentAsString = Percentage::fromFractionAndTotal(
                    $item['executedPaths'],
                    $item['executablePaths'],
                )->asString();
            } else {
                $numClasses                      = 0;
                $numTestedClasses                = 0;
                $linesExecutedPercentAsString    = 'n/a';
                $branchesExecutedPercentAsString = 'n/a';
                $pathsExecutedPercentAsString    = 'n/a';
            }

            $testedMethodsPercentage = Percentage::fromFractionAndTotal(
                $numTestedMethods,
                $numMethods,
            );

            $testedClassesPercentage = Percentage::fromFractionAndTotal(
                $numTestedMethods === $numMethods ? 1 : 0,
                1,
            );

            $buffer .= $this->renderItemTemplate(
                $template,
                [
                    'name'                 => $this->abbreviateClassName($name),
                    'numClasses'           => $numClasses,
                    'numTestedClasses'     => $numTestedClasses,
                    'numMethods'           => $numMethods,
                    'numTestedMethods'     => $numTestedMethods,
                    'linesExecutedPercent' => Percentage::fromFractionAndTotal(
                        $item['executedLines'],
                        $item['executableLines'],
                    )->asFloat(),
                    'linesExecutedPercentAsString' => $linesExecutedPercentAsString,
                    'numExecutedLines'             => $item['executedLines'],
                    'numExecutableLines'           => $item['executableLines'],
                    'branchesExecutedPercent'      => Percentage::fromFractionAndTotal(
                        $item['executedBranches'],
                        $item['executableBranches'],
                    )->asFloat(),
                    'branchesExecutedPercentAsString' => $branchesExecutedPercentAsString,
                    'numExecutedBranches'             => $item['executedBranches'],
                    'numExecutableBranches'           => $item['executableBranches'],
                    'pathsExecutedPercent'            => Percentage::fromFractionAndTotal(
                        $item['executedPaths'],
                        $item['executablePaths'],
                    )->asFloat(),
                    'pathsExecutedPercentAsString' => $pathsExecutedPercentAsString,
                    'numExecutedPaths'             => $item['executedPaths'],
                    'numExecutablePaths'           => $item['executablePaths'],
                    'testedMethodsPercent'         => $testedMethodsPercentage->asFloat(),
                    'testedMethodsPercentAsString' => $testedMethodsPercentage->asString(),
                    'testedClassesPercent'         => $testedClassesPercentage->asFloat(),
                    'testedClassesPercentAsString' => $testedClassesPercentage->asString(),
                    'crap'                         => $item['crap'],
                ],
            );

            foreach ($item['methods'] as $method) {
                $buffer .= $this->renderFunctionOrMethodItem(
                    $methodItemTemplate,
                    $method,
                    '&nbsp;',
                );
            }
        }

        return $buffer;
    }

    private function renderFunctionItems(array $functions, Template $template): string
    {
        if (empty($functions)) {
            return '';
        }

        $buffer = '';

        foreach ($functions as $function) {
            $buffer .= $this->renderFunctionOrMethodItem(
                $template,
                $function,
            );
        }

        return $buffer;
    }

    private function renderFunctionOrMethodItem(Template $template, array $item, string $indent = ''): string
    {
        $numMethods       = 0;
        $numTestedMethods = 0;

        if ($item['executableLines'] > 0) {
            $numMethods = 1;

            if ($item['executedLines'] === $item['executableLines']) {
                $numTestedMethods = 1;
            }
        }

        $executedLinesPercentage = Percentage::fromFractionAndTotal(
            $item['executedLines'],
            $item['executableLines'],
        );

        $executedBranchesPercentage = Percentage::fromFractionAndTotal(
            $item['executedBranches'],
            $item['executableBranches'],
        );

        $executedPathsPercentage = Percentage::fromFractionAndTotal(
            $item['executedPaths'],
            $item['executablePaths'],
        );

        $testedMethodsPercentage = Percentage::fromFractionAndTotal(
            $numTestedMethods,
            1,
        );

        return $this->renderItemTemplate(
            $template,
            [
                'name' => sprintf(
                    '%s<a href="#%d"><abbr title="%s">%s</abbr></a>',
                    $indent,
                    $item['startLine'],
                    htmlspecialchars($item['signature'], $this->htmlSpecialCharsFlags),
                    $item['functionName'] ?? $item['methodName'],
                ),
                'numMethods'                      => $numMethods,
                'numTestedMethods'                => $numTestedMethods,
                'linesExecutedPercent'            => $executedLinesPercentage->asFloat(),
                'linesExecutedPercentAsString'    => $executedLinesPercentage->asString(),
                'numExecutedLines'                => $item['executedLines'],
                'numExecutableLines'              => $item['executableLines'],
                'branchesExecutedPercent'         => $executedBranchesPercentage->asFloat(),
                'branchesExecutedPercentAsString' => $executedBranchesPercentage->asString(),
                'numExecutedBranches'             => $item['executedBranches'],
                'numExecutableBranches'           => $item['executableBranches'],
                'pathsExecutedPercent'            => $executedPathsPercentage->asFloat(),
                'pathsExecutedPercentAsString'    => $executedPathsPercentage->asString(),
                'numExecutedPaths'                => $item['executedPaths'],
                'numExecutablePaths'              => $item['executablePaths'],
                'testedMethodsPercent'            => $testedMethodsPercentage->asFloat(),
                'testedMethodsPercentAsString'    => $testedMethodsPercentage->asString(),
                'crap'                            => $item['crap'],
            ],
        );
    }

    private function renderSourceWithLineCoverage(FileNode $node): string
    {
        $linesTemplate      = new Template($this->templatePath . 'lines.html.dist', '{{', '}}');
        $singleLineTemplate = new Template($this->templatePath . 'line.html.dist', '{{', '}}');

        $coverageData = $node->lineCoverageData();
        $testData     = $node->testData();
        $codeLines    = $this->loadFile($node->pathAsString());
        $lines        = '';
        $i            = 1;

        foreach ($codeLines as $line) {
            $trClass        = '';
            $popoverContent = '';
            $popoverTitle   = '';

            if (array_key_exists($i, $coverageData)) {
                $numTests = ($coverageData[$i] ? count($coverageData[$i]) : 0);

                if ($coverageData[$i] === null) {
                    $trClass = 'warning';
                } elseif ($numTests === 0) {
                    $trClass = 'danger';
                } else {
                    if ($numTests > 1) {
                        $popoverTitle = $numTests . ' tests cover line ' . $i;
                    } else {
                        $popoverTitle = '1 test covers line ' . $i;
                    }

                    $lineCss        = 'covered-by-large-tests';
                    $popoverContent = '<ul>';

                    foreach ($coverageData[$i] as $test) {
                        if ($lineCss === 'covered-by-large-tests' && $testData[$test]['size'] === 'medium') {
                            $lineCss = 'covered-by-medium-tests';
                        } elseif ($testData[$test]['size'] === 'small') {
                            $lineCss = 'covered-by-small-tests';
                        }

                        $popoverContent .= $this->createPopoverContentForTest($test, $testData[$test]);
                    }

                    $popoverContent .= '</ul>';
                    $trClass = $lineCss . ' popin';
                }
            }

            $popover = '';

            if (!empty($popoverTitle)) {
                $popover = sprintf(
                    ' data-bs-title="%s" data-bs-content="%s" data-bs-placement="top" data-bs-html="true"',
                    $popoverTitle,
                    htmlspecialchars($popoverContent, $this->htmlSpecialCharsFlags),
                );
            }

            $lines .= $this->renderLine($singleLineTemplate, $i, $line, $trClass, $popover);

            $i++;
        }

        $linesTemplate->setVar(['lines' => $lines]);

        return $linesTemplate->render();
    }

    private function renderSourceWithBranchCoverage(FileNode $node): string
    {
        $linesTemplate      = new Template($this->templatePath . 'lines.html.dist', '{{', '}}');
        $singleLineTemplate = new Template($this->templatePath . 'line.html.dist', '{{', '}}');

        $functionCoverageData = $node->functionCoverageData();
        $testData             = $node->testData();
        $codeLines            = $this->loadFile($node->pathAsString());

        $lineData = [];

        /** @var int $line */
        foreach (array_keys($codeLines) as $line) {
            $lineData[$line + 1] = [
                'includedInBranches'    => 0,
                'includedInHitBranches' => 0,
                'tests'                 => [],
            ];
        }

        foreach ($functionCoverageData as $method) {
            foreach ($method['branches'] as $branch) {
                foreach (range($branch['line_start'], $branch['line_end']) as $line) {
                    if (!isset($lineData[$line])) { // blank line at end of file is sometimes included here
                        continue;
                    }

                    $lineData[$line]['includedInBranches']++;

                    if ($branch['hit']) {
                        $lineData[$line]['includedInHitBranches']++;
                        $lineData[$line]['tests'] = array_unique(array_merge($lineData[$line]['tests'], $branch['hit']));
                    }
                }
            }
        }

        $lines = '';
        $i     = 1;

        /** @var string $line */
        foreach ($codeLines as $line) {
            $trClass = '';
            $popover = '';

            if ($lineData[$i]['includedInBranches'] > 0) {
                $lineCss = 'success';

                if ($lineData[$i]['includedInHitBranches'] === 0) {
                    $lineCss = 'danger';
                } elseif ($lineData[$i]['includedInHitBranches'] !== $lineData[$i]['includedInBranches']) {
                    $lineCss = 'warning';
                }

                $popoverContent = '<ul>';

                if (count($lineData[$i]['tests']) === 1) {
                    $popoverTitle = '1 test covers line ' . $i;
                } else {
                    $popoverTitle = count($lineData[$i]['tests']) . ' tests cover line ' . $i;
                }
                $popoverTitle .= '. These are covering ' . $lineData[$i]['includedInHitBranches'] . ' out of the ' . $lineData[$i]['includedInBranches'] . ' code branches.';

                foreach ($lineData[$i]['tests'] as $test) {
                    $popoverContent .= $this->createPopoverContentForTest($test, $testData[$test]);
                }

                $popoverContent .= '</ul>';
                $trClass = $lineCss . ' popin';

                $popover = sprintf(
                    ' data-bs-title="%s" data-bs-content="%s" data-bs-placement="top" data-bs-html="true"',
                    $popoverTitle,
                    htmlspecialchars($popoverContent, $this->htmlSpecialCharsFlags),
                );
            }

            $lines .= $this->renderLine($singleLineTemplate, $i, $line, $trClass, $popover);

            $i++;
        }

        $linesTemplate->setVar(['lines' => $lines]);

        return $linesTemplate->render();
    }

    private function renderSourceWithPathCoverage(FileNode $node): string
    {
        $linesTemplate      = new Template($this->templatePath . 'lines.html.dist', '{{', '}}');
        $singleLineTemplate = new Template($this->templatePath . 'line.html.dist', '{{', '}}');

        $functionCoverageData = $node->functionCoverageData();
        $testData             = $node->testData();
        $codeLines            = $this->loadFile($node->pathAsString());

        $lineData = [];

        /** @var int $line */
        foreach (array_keys($codeLines) as $line) {
            $lineData[$line + 1] = [
                'includedInPaths'    => [],
                'includedInHitPaths' => [],
                'tests'              => [],
            ];
        }

        foreach ($functionCoverageData as $method) {
            foreach ($method['paths'] as $pathId => $path) {
                foreach ($path['path'] as $branchTaken) {
                    foreach (range($method['branches'][$branchTaken]['line_start'], $method['branches'][$branchTaken]['line_end']) as $line) {
                        if (!isset($lineData[$line])) {
                            continue;
                        }
                        $lineData[$line]['includedInPaths'][] = $pathId;

                        if ($path['hit']) {
                            $lineData[$line]['includedInHitPaths'][] = $pathId;
                            $lineData[$line]['tests']                = array_unique(array_merge($lineData[$line]['tests'], $path['hit']));
                        }
                    }
                }
            }
        }

        $lines = '';
        $i     = 1;

        /** @var string $line */
        foreach ($codeLines as $line) {
            $trClass                 = '';
            $popover                 = '';
            $includedInPathsCount    = count(array_unique($lineData[$i]['includedInPaths']));
            $includedInHitPathsCount = count(array_unique($lineData[$i]['includedInHitPaths']));

            if ($includedInPathsCount > 0) {
                $lineCss = 'success';

                if ($includedInHitPathsCount === 0) {
                    $lineCss = 'danger';
                } elseif ($includedInHitPathsCount !== $includedInPathsCount) {
                    $lineCss = 'warning';
                }

                $popoverContent = '<ul>';

                if (count($lineData[$i]['tests']) === 1) {
                    $popoverTitle = '1 test covers line ' . $i;
                } else {
                    $popoverTitle = count($lineData[$i]['tests']) . ' tests cover line ' . $i;
                }
                $popoverTitle .= '. These are covering ' . $includedInHitPathsCount . ' out of the ' . $includedInPathsCount . ' code paths.';

                foreach ($lineData[$i]['tests'] as $test) {
                    $popoverContent .= $this->createPopoverContentForTest($test, $testData[$test]);
                }

                $popoverContent .= '</ul>';
                $trClass = $lineCss . ' popin';

                $popover = sprintf(
                    ' data-bs-title="%s" data-bs-content="%s" data-bs-placement="top" data-bs-html="true"',
                    $popoverTitle,
                    htmlspecialchars($popoverContent, $this->htmlSpecialCharsFlags),
                );
            }

            $lines .= $this->renderLine($singleLineTemplate, $i, $line, $trClass, $popover);

            $i++;
        }

        $linesTemplate->setVar(['lines' => $lines]);

        return $linesTemplate->render();
    }

    private function renderBranchStructure(FileNode $node): string
    {
        $branchesTemplate = new Template($this->templatePath . 'branches.html.dist', '{{', '}}');

        $coverageData = $node->functionCoverageData();
        $testData     = $node->testData();
        $codeLines    = $this->loadFile($node->pathAsString());
        $branches     = '';

        ksort($coverageData);

        foreach ($coverageData as $methodName => $methodData) {
            if (!$methodData['branches']) {
                continue;
            }

            $branchStructure = '';

            foreach ($methodData['branches'] as $branch) {
                $branchStructure .= $this->renderBranchLines($branch, $codeLines, $testData);
            }

            if ($branchStructure !== '') { // don't show empty branches
                $branches .= '<h5 class="structure-heading"><a name="' . htmlspecialchars($methodName, $this->htmlSpecialCharsFlags) . '">' . $this->abbreviateMethodName($methodName) . '</a></h5>' . "\n";
                $branches .= $branchStructure;
            }
        }

        $branchesTemplate->setVar(['branches' => $branches]);

        return $branchesTemplate->render();
    }

    private function renderBranchLines(array $branch, array $codeLines, array $testData): string
    {
        $linesTemplate      = new Template($this->templatePath . 'lines.html.dist', '{{', '}}');
        $singleLineTemplate = new Template($this->templatePath . 'line.html.dist', '{{', '}}');

        $lines = '';

        $branchLines = range($branch['line_start'], $branch['line_end']);
        sort($branchLines); // sometimes end_line < start_line

        /** @var int $line */
        foreach ($branchLines as $line) {
            if (!isset($codeLines[$line])) { // blank line at end of file is sometimes included here
                continue;
            }

            $popoverContent = '';
            $popoverTitle   = '';

            $numTests = count($branch['hit']);

            if ($numTests === 0) {
                $trClass = 'danger';
            } else {
                $lineCss        = 'covered-by-large-tests';
                $popoverContent = '<ul>';

                if ($numTests > 1) {
                    $popoverTitle = $numTests . ' tests cover this branch';
                } else {
                    $popoverTitle = '1 test covers this branch';
                }

                foreach ($branch['hit'] as $test) {
                    if ($lineCss === 'covered-by-large-tests' && $testData[$test]['size'] === 'medium') {
                        $lineCss = 'covered-by-medium-tests';
                    } elseif ($testData[$test]['size'] === 'small') {
                        $lineCss = 'covered-by-small-tests';
                    }

                    $popoverContent .= $this->createPopoverContentForTest($test, $testData[$test]);
                }
                $trClass = $lineCss . ' popin';
            }

            $popover = '';

            if (!empty($popoverTitle)) {
                $popover = sprintf(
                    ' data-bs-title="%s" data-bs-content="%s" data-bs-placement="top" data-bs-html="true"',
                    $popoverTitle,
                    htmlspecialchars($popoverContent, $this->htmlSpecialCharsFlags),
                );
            }

            $lines .= $this->renderLine($singleLineTemplate, $line, $codeLines[$line - 1], $trClass, $popover);
        }

        if ($lines === '') {
            return '';
        }

        $linesTemplate->setVar(['lines' => $lines]);

        return $linesTemplate->render();
    }

    private function renderPathStructure(FileNode $node): string
    {
        $pathsTemplate = new Template($this->templatePath . 'paths.html.dist', '{{', '}}');

        $coverageData = $node->functionCoverageData();
        $testData     = $node->testData();
        $codeLines    = $this->loadFile($node->pathAsString());
        $paths        = '';

        ksort($coverageData);

        foreach ($coverageData as $methodName => $methodData) {
            if (!$methodData['paths']) {
                continue;
            }

            $pathStructure = '';

            if (count($methodData['paths']) > 100) {
                $pathStructure .= '<p>' . count($methodData['paths']) . ' is too many paths to sensibly render, consider refactoring your code to bring this number down.</p>';

                continue;
            }

            foreach ($methodData['paths'] as $path) {
                $pathStructure .= $this->renderPathLines($path, $methodData['branches'], $codeLines, $testData);
            }

            if ($pathStructure !== '') {
                $paths .= '<h5 class="structure-heading"><a name="' . htmlspecialchars($methodName, $this->htmlSpecialCharsFlags) . '">' . $this->abbreviateMethodName($methodName) . '</a></h5>' . "\n";
                $paths .= $pathStructure;
            }
        }

        $pathsTemplate->setVar(['paths' => $paths]);

        return $pathsTemplate->render();
    }

    private function renderPathLines(array $path, array $branches, array $codeLines, array $testData): string
    {
        $linesTemplate      = new Template($this->templatePath . 'lines.html.dist', '{{', '}}');
        $singleLineTemplate = new Template($this->templatePath . 'line.html.dist', '{{', '}}');

        $lines = '';
        $first = true;

        foreach ($path['path'] as $branchId) {
            if ($first) {
                $first = false;
            } else {
                $lines .= '    <tr><td colspan="2">&nbsp;</td></tr>' . "\n";
            }

            $branchLines = range($branches[$branchId]['line_start'], $branches[$branchId]['line_end']);
            sort($branchLines); // sometimes end_line < start_line

            /** @var int $line */
            foreach ($branchLines as $line) {
                if (!isset($codeLines[$line])) { // blank line at end of file is sometimes included here
                    continue;
                }

                $popoverContent = '';
                $popoverTitle   = '';

                $numTests = count($path['hit']);

                if ($numTests === 0) {
                    $trClass = 'danger';
                } else {
                    $lineCss        = 'covered-by-large-tests';
                    $popoverContent = '<ul>';

                    if ($numTests > 1) {
                        $popoverTitle = $numTests . ' tests cover this path';
                    } else {
                        $popoverTitle = '1 test covers this path';
                    }

                    foreach ($path['hit'] as $test) {
                        if ($lineCss === 'covered-by-large-tests' && $testData[$test]['size'] === 'medium') {
                            $lineCss = 'covered-by-medium-tests';
                        } elseif ($testData[$test]['size'] === 'small') {
                            $lineCss = 'covered-by-small-tests';
                        }

                        $popoverContent .= $this->createPopoverContentForTest($test, $testData[$test]);
                    }

                    $trClass = $lineCss . ' popin';
                }

                $popover = '';

                if (!empty($popoverTitle)) {
                    $popover = sprintf(
                        ' data-bs-title="%s" data-bs-content="%s" data-bs-placement="top" data-bs-html="true"',
                        $popoverTitle,
                        htmlspecialchars($popoverContent, $this->htmlSpecialCharsFlags),
                    );
                }

                $lines .= $this->renderLine($singleLineTemplate, $line, $codeLines[$line - 1], $trClass, $popover);
            }
        }

        if ($lines === '') {
            return '';
        }

        $linesTemplate->setVar(['lines' => $lines]);

        return $linesTemplate->render();
    }

    private function renderLine(Template $template, int $lineNumber, string $lineContent, string $class, string $popover): string
    {
        $template->setVar(
            [
                'lineNumber'  => $lineNumber,
                'lineContent' => $lineContent,
                'class'       => $class,
                'popover'     => $popover,
            ],
        );

        return $template->render();
    }

    private function loadFile(string $file): array
    {
        if (isset(self::$formattedSourceCache[$file])) {
            return self::$formattedSourceCache[$file];
        }

        $buffer              = file_get_contents($file);
        $tokens              = token_get_all($buffer, TOKEN_PARSE);
        $result              = [''];
        $i                   = 0;
        $stringFlag          = false;
        $fileEndsWithNewLine = str_ends_with($buffer, "\n");

        unset($buffer);

        foreach ($tokens as $j => $token) {
            if (is_string($token)) {
                if ($token === '"' && $tokens[$j - 1] !== '\\') {
                    $result[$i] .= sprintf(
                        '<span class="string">%s</span>',
                        htmlspecialchars($token, $this->htmlSpecialCharsFlags),
                    );

                    $stringFlag = !$stringFlag;
                } else {
                    $result[$i] .= sprintf(
                        '<span class="keyword">%s</span>',
                        htmlspecialchars($token, $this->htmlSpecialCharsFlags),
                    );
                }

                continue;
            }

            [$token, $value] = $token;

            $value = str_replace(
                ["\t", ' '],
                ['&nbsp;&nbsp;&nbsp;&nbsp;', '&nbsp;'],
                htmlspecialchars($value, $this->htmlSpecialCharsFlags),
            );

            if ($value === "\n") {
                $result[++$i] = '';
            } else {
                $lines = explode("\n", $value);

                foreach ($lines as $jj => $line) {
                    $line = trim($line);

                    if ($line !== '') {
                        if ($stringFlag) {
                            $colour = 'string';
                        } else {
                            $colour = 'default';

                            if ($this->isInlineHtml($token)) {
                                $colour = 'html';
                            } elseif ($this->isComment($token)) {
                                $colour = 'comment';
                            } elseif ($this->isKeyword($token)) {
                                $colour = 'keyword';
                            }
                        }

                        $result[$i] .= sprintf(
                            '<span class="%s">%s</span>',
                            $colour,
                            $line,
                        );
                    }

                    if (isset($lines[$jj + 1])) {
                        $result[++$i] = '';
                    }
                }
            }
        }

        if ($fileEndsWithNewLine) {
            unset($result[count($result) - 1]);
        }

        self::$formattedSourceCache[$file] = $result;

        return $result;
    }

    private function abbreviateClassName(string $className): string
    {
        $tmp = explode('\\', $className);

        if (count($tmp) > 1) {
            $className = sprintf(
                '<abbr title="%s">%s</abbr>',
                $className,
                array_pop($tmp),
            );
        }

        return $className;
    }

    private function abbreviateMethodName(string $methodName): string
    {
        $parts = explode('->', $methodName);

        if (count($parts) === 2) {
            return $this->abbreviateClassName($parts[0]) . '->' . $parts[1];
        }

        return $methodName;
    }

    private function createPopoverContentForTest(string $test, array $testData): string
    {
        $testCSS = '';

        switch ($testData['status']) {
            case 'success':
                $testCSS = match ($testData['size']) {
                    'small'  => ' class="covered-by-small-tests"',
                    'medium' => ' class="covered-by-medium-tests"',
                    // no break
                    default => ' class="covered-by-large-tests"',
                };

                break;

            case 'failure':
                $testCSS = ' class="danger"';

                break;
        }

        return sprintf(
            '<li%s>%s</li>',
            $testCSS,
            htmlspecialchars($test, $this->htmlSpecialCharsFlags),
        );
    }

    private function isComment(int $token): bool
    {
        return $token === T_COMMENT || $token === T_DOC_COMMENT;
    }

    private function isInlineHtml(int $token): bool
    {
        return $token === T_INLINE_HTML;
    }

    private function isKeyword(int $token): bool
    {
        return isset(self::KEYWORD_TOKENS[$token]);
    }
}
