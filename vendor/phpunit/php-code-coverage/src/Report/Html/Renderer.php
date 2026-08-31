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

use function array_pop;
use function count;
use function sprintf;
use function str_repeat;
use function substr_count;
use SebastianBergmann\CodeCoverage\Node\AbstractNode;
use SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
use SebastianBergmann\CodeCoverage\Node\File as FileNode;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Version;
use SebastianBergmann\Environment\Runtime;
use SebastianBergmann\Template\Template;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract class Renderer
{
    protected string $templatePath;
    protected string $generator;
    protected string $date;
    protected Thresholds $thresholds;
    protected bool $hasBranchCoverage;
    protected string $version;

    public function __construct(string $templatePath, string $generator, string $date, Thresholds $thresholds, bool $hasBranchCoverage)
    {
        $this->templatePath      = $templatePath;
        $this->generator         = $generator;
        $this->date              = $date;
        $this->thresholds        = $thresholds;
        $this->version           = Version::id();
        $this->hasBranchCoverage = $hasBranchCoverage;
    }

    protected function renderItemTemplate(Template $template, array $data): string
    {
        $numSeparator = '&nbsp;/&nbsp;';

        if (isset($data['numClasses']) && $data['numClasses'] > 0) {
            $classesLevel = $this->colorLevel($data['testedClassesPercent']);

            $classesNumber = $data['numTestedClasses'] . $numSeparator .
                $data['numClasses'];

            $classesBar = $this->coverageBar(
                $data['testedClassesPercent'],
            );
        } else {
            $classesLevel                         = '';
            $classesNumber                        = '0' . $numSeparator . '0';
            $classesBar                           = '';
            $data['testedClassesPercentAsString'] = 'n/a';
        }

        if ($data['numMethods'] > 0) {
            $methodsLevel = $this->colorLevel($data['testedMethodsPercent']);

            $methodsNumber = $data['numTestedMethods'] . $numSeparator .
                $data['numMethods'];

            $methodsBar = $this->coverageBar(
                $data['testedMethodsPercent'],
            );
        } else {
            $methodsLevel                         = '';
            $methodsNumber                        = '0' . $numSeparator . '0';
            $methodsBar                           = '';
            $data['testedMethodsPercentAsString'] = 'n/a';
        }

        if ($data['numExecutableLines'] > 0) {
            $linesLevel = $this->colorLevel($data['linesExecutedPercent']);

            $linesNumber = $data['numExecutedLines'] . $numSeparator .
                $data['numExecutableLines'];

            $linesBar = $this->coverageBar(
                $data['linesExecutedPercent'],
            );
        } else {
            $linesLevel                           = '';
            $linesNumber                          = '0' . $numSeparator . '0';
            $linesBar                             = '';
            $data['linesExecutedPercentAsString'] = 'n/a';
        }

        if ($data['numExecutablePaths'] > 0) {
            $pathsLevel = $this->colorLevel($data['pathsExecutedPercent']);

            $pathsNumber = $data['numExecutedPaths'] . $numSeparator .
                $data['numExecutablePaths'];

            $pathsBar = $this->coverageBar(
                $data['pathsExecutedPercent'],
            );
        } else {
            $pathsLevel                           = '';
            $pathsNumber                          = '0' . $numSeparator . '0';
            $pathsBar                             = '';
            $data['pathsExecutedPercentAsString'] = 'n/a';
        }

        if ($data['numExecutableBranches'] > 0) {
            $branchesLevel = $this->colorLevel($data['branchesExecutedPercent']);

            $branchesNumber = $data['numExecutedBranches'] . $numSeparator .
                $data['numExecutableBranches'];

            $branchesBar = $this->coverageBar(
                $data['branchesExecutedPercent'],
            );
        } else {
            $branchesLevel                           = '';
            $branchesNumber                          = '0' . $numSeparator . '0';
            $branchesBar                             = '';
            $data['branchesExecutedPercentAsString'] = 'n/a';
        }

        $template->setVar(
            [
                'icon'                      => $data['icon'] ?? '',
                'crap'                      => $data['crap'] ?? '',
                'name'                      => $data['name'],
                'lines_bar'                 => $linesBar,
                'lines_executed_percent'    => $data['linesExecutedPercentAsString'],
                'lines_level'               => $linesLevel,
                'lines_number'              => $linesNumber,
                'paths_bar'                 => $pathsBar,
                'paths_executed_percent'    => $data['pathsExecutedPercentAsString'],
                'paths_level'               => $pathsLevel,
                'paths_number'              => $pathsNumber,
                'branches_bar'              => $branchesBar,
                'branches_executed_percent' => $data['branchesExecutedPercentAsString'],
                'branches_level'            => $branchesLevel,
                'branches_number'           => $branchesNumber,
                'methods_bar'               => $methodsBar,
                'methods_tested_percent'    => $data['testedMethodsPercentAsString'],
                'methods_level'             => $methodsLevel,
                'methods_number'            => $methodsNumber,
                'classes_bar'               => $classesBar,
                'classes_tested_percent'    => $data['testedClassesPercentAsString'] ?? '',
                'classes_level'             => $classesLevel,
                'classes_number'            => $classesNumber,
            ],
        );

        return $template->render();
    }

    protected function setCommonTemplateVariables(Template $template, AbstractNode $node): void
    {
        $template->setVar(
            [
                'id'               => $node->id(),
                'full_path'        => $node->pathAsString(),
                'path_to_root'     => $this->pathToRoot($node),
                'breadcrumbs'      => $this->breadcrumbs($node),
                'date'             => $this->date,
                'version'          => $this->version,
                'runtime'          => $this->runtimeString(),
                'generator'        => $this->generator,
                'low_upper_bound'  => $this->thresholds->lowUpperBound(),
                'high_lower_bound' => $this->thresholds->highLowerBound(),
            ],
        );
    }

    protected function breadcrumbs(AbstractNode $node): string
    {
        $breadcrumbs = '';
        $path        = $node->pathAsArray();
        $pathToRoot  = [];
        $max         = count($path);

        if ($node instanceof FileNode) {
            $max--;
        }

        for ($i = 0; $i < $max; $i++) {
            $pathToRoot[] = str_repeat('../', $i);
        }

        foreach ($path as $step) {
            if ($step !== $node) {
                $breadcrumbs .= $this->inactiveBreadcrumb(
                    $step,
                    array_pop($pathToRoot),
                );
            } else {
                $breadcrumbs .= $this->activeBreadcrumb($step);
            }
        }

        return $breadcrumbs;
    }

    protected function activeBreadcrumb(AbstractNode $node): string
    {
        $buffer = sprintf(
            '         <li class="breadcrumb-item active">%s</li>' . "\n",
            $node->name(),
        );

        if ($node instanceof DirectoryNode) {
            $buffer .= '         <li class="breadcrumb-item">(<a href="dashboard.html">Dashboard</a>)</li>' . "\n";
        }

        return $buffer;
    }

    protected function inactiveBreadcrumb(AbstractNode $node, string $pathToRoot): string
    {
        return sprintf(
            '         <li class="breadcrumb-item"><a href="%sindex.html">%s</a></li>' . "\n",
            $pathToRoot,
            $node->name(),
        );
    }

    protected function pathToRoot(AbstractNode $node): string
    {
        $id    = $node->id();
        $depth = substr_count($id, '/');

        if ($id !== 'index' &&
            $node instanceof DirectoryNode) {
            $depth++;
        }

        return str_repeat('../', $depth);
    }

    protected function coverageBar(float $percent): string
    {
        $level = $this->colorLevel($percent);

        $templateName = $this->templatePath . ($this->hasBranchCoverage ? 'coverage_bar_branch.html' : 'coverage_bar.html');
        $template     = new Template(
            $templateName,
            '{{',
            '}}',
        );

        $template->setVar(['level' => $level, 'percent' => sprintf('%.2F', $percent)]);

        return $template->render();
    }

    protected function colorLevel(float $percent): string
    {
        if ($percent <= $this->thresholds->lowUpperBound()) {
            return 'danger';
        }

        if ($percent > $this->thresholds->lowUpperBound() &&
            $percent < $this->thresholds->highLowerBound()) {
            return 'warning';
        }

        return 'success';
    }

    private function runtimeString(): string
    {
        $runtime = new Runtime;

        return sprintf(
            '<a href="%s" target="_top">%s %s</a>',
            $runtime->getVendorUrl(),
            $runtime->getName(),
            $runtime->getVersion(),
        );
    }
}
