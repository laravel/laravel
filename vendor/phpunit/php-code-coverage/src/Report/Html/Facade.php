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

use const DIRECTORY_SEPARATOR;
use function copy;
use function date;
use function dirname;
use function str_ends_with;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\FileCouldNotBeWrittenException;
use SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
use SebastianBergmann\CodeCoverage\Report\Thresholds;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\Template\Exception;
use SebastianBergmann\Template\Template;

final class Facade
{
    private readonly string $templatePath;
    private readonly string $generator;
    private readonly Colors $colors;
    private readonly Thresholds $thresholds;
    private readonly CustomCssFile $customCssFile;

    public function __construct(string $generator = '', ?Colors $colors = null, ?Thresholds $thresholds = null, ?CustomCssFile $customCssFile = null)
    {
        $this->generator     = $generator;
        $this->colors        = $colors ?? Colors::default();
        $this->thresholds    = $thresholds ?? Thresholds::default();
        $this->customCssFile = $customCssFile ?? CustomCssFile::default();
        $this->templatePath  = __DIR__ . '/Renderer/Template/';
    }

    public function process(CodeCoverage $coverage, string $target): void
    {
        $target = $this->directory($target);
        $report = $coverage->getReport();
        $date   = date('D M j G:i:s T Y');

        $dashboard = new Dashboard(
            $this->templatePath,
            $this->generator,
            $date,
            $this->thresholds,
            $coverage->collectsBranchAndPathCoverage(),
        );

        $directory = new Directory(
            $this->templatePath,
            $this->generator,
            $date,
            $this->thresholds,
            $coverage->collectsBranchAndPathCoverage(),
        );

        $file = new File(
            $this->templatePath,
            $this->generator,
            $date,
            $this->thresholds,
            $coverage->collectsBranchAndPathCoverage(),
        );

        $directory->render($report, $target . 'index.html');
        $dashboard->render($report, $target . 'dashboard.html');

        foreach ($report as $node) {
            $id = $node->id();

            if ($node instanceof DirectoryNode) {
                Filesystem::createDirectory($target . $id);

                $directory->render($node, $target . $id . '/index.html');
                $dashboard->render($node, $target . $id . '/dashboard.html');
            } else {
                $dir = dirname($target . $id);

                Filesystem::createDirectory($dir);

                $file->render($node, $target . $id);
            }
        }

        $this->copyFiles($target);
        $this->renderCss($target);
    }

    private function copyFiles(string $target): void
    {
        $dir = $this->directory($target . '_css');

        copy($this->templatePath . 'css/bootstrap.min.css', $dir . 'bootstrap.min.css');
        copy($this->templatePath . 'css/nv.d3.min.css', $dir . 'nv.d3.min.css');
        copy($this->customCssFile->path(), $dir . 'custom.css');
        copy($this->templatePath . 'css/octicons.css', $dir . 'octicons.css');

        $dir = $this->directory($target . '_icons');
        copy($this->templatePath . 'icons/file-code.svg', $dir . 'file-code.svg');
        copy($this->templatePath . 'icons/file-directory.svg', $dir . 'file-directory.svg');

        $dir = $this->directory($target . '_js');
        copy($this->templatePath . 'js/bootstrap.bundle.min.js', $dir . 'bootstrap.bundle.min.js');
        copy($this->templatePath . 'js/d3.min.js', $dir . 'd3.min.js');
        copy($this->templatePath . 'js/jquery.min.js', $dir . 'jquery.min.js');
        copy($this->templatePath . 'js/nv.d3.min.js', $dir . 'nv.d3.min.js');
        copy($this->templatePath . 'js/file.js', $dir . 'file.js');
    }

    private function renderCss(string $target): void
    {
        $template = new Template($this->templatePath . 'css/style.css', '{{', '}}');

        $template->setVar(
            [
                'success-low'    => $this->colors->successLow(),
                'success-medium' => $this->colors->successMedium(),
                'success-high'   => $this->colors->successHigh(),
                'warning'        => $this->colors->warning(),
                'danger'         => $this->colors->danger(),
            ],
        );

        try {
            $template->renderTo($this->directory($target . '_css') . 'style.css');
        } catch (Exception $e) {
            throw new FileCouldNotBeWrittenException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }

    private function directory(string $directory): string
    {
        if (!str_ends_with($directory, DIRECTORY_SEPARATOR)) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        Filesystem::createDirectory($directory);

        return $directory;
    }
}
