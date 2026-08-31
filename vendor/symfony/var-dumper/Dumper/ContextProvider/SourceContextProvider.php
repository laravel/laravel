<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Dumper\ContextProvider;

use Symfony\Component\ErrorHandler\ErrorRenderer\FileLinkFormatter;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;
use Twig\Template;

/**
 * Tries to provide context from sources (class name, file, line, code excerpt, ...).
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
final class SourceContextProvider implements ContextProviderInterface
{
    public function __construct(
        private ?string $charset = null,
        private ?string $projectDir = null,
        private ?FileLinkFormatter $fileLinkFormatter = null,
        private int $limit = 9,
    ) {
    }

    public function getContext(): ?array
    {
        $trace = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT | \DEBUG_BACKTRACE_IGNORE_ARGS, $this->limit);

        $file = $trace[1]['file'];
        $line = $trace[1]['line'];
        $name = '-' === $file || 'Standard input code' === $file ? 'Standard input code' : false;
        $fileExcerpt = false;

        for ($i = 2; $i < $this->limit; ++$i) {
            if (isset($trace[$i]['class'], $trace[$i]['function'])
                && 'dump' === $trace[$i]['function']
                && VarDumper::class === $trace[$i]['class']
            ) {
                $file = $trace[$i]['file'] ?? $file;
                $line = $trace[$i]['line'] ?? $line;

                while (++$i < $this->limit) {
                    if (isset($trace[$i]['function'], $trace[$i]['file']) && empty($trace[$i]['class']) && !str_starts_with($trace[$i]['function'], 'call_user_func')) {
                        $file = $trace[$i]['file'];
                        $line = $trace[$i]['line'];

                        break;
                    } elseif (isset($trace[$i]['object']) && $trace[$i]['object'] instanceof Template) {
                        $template = $trace[$i]['object'];
                        $name = $template->getTemplateName();
                        $src = method_exists($template, 'getSourceContext') ? $template->getSourceContext()->getCode() : (method_exists($template, 'getSource') ? $template->getSource() : false);
                        $info = $template->getDebugInfo();
                        if (isset($info[$trace[$i - 1]['line']])) {
                            $line = $info[$trace[$i - 1]['line']];
                            $file = method_exists($template, 'getSourceContext') ? $template->getSourceContext()->getPath() : null;

                            if ($src) {
                                $src = explode("\n", $src);
                                $fileExcerpt = [];

                                for ($i = max($line - 3, 1), $max = min($line + 3, \count($src)); $i <= $max; ++$i) {
                                    $fileExcerpt[] = '<li'.($i === $line ? ' class="selected"' : '').'><code>'.$this->htmlEncode($src[$i - 1]).'</code></li>';
                                }

                                $fileExcerpt = '<ol start="'.max($line - 3, 1).'">'.implode("\n", $fileExcerpt).'</ol>';
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }

        if (false === $name) {
            $name = str_replace('\\', '/', $file);
            $name = substr($name, strrpos($name, '/') + 1);
        }

        $context = ['name' => $name, 'file' => $file, 'line' => $line];
        $context['file_excerpt'] = $fileExcerpt;

        if (null !== $this->projectDir) {
            $context['project_dir'] = $this->projectDir;
            if (str_starts_with($file, $this->projectDir)) {
                $context['file_relative'] = ltrim(substr($file, \strlen($this->projectDir)), \DIRECTORY_SEPARATOR);
            }
        }

        if ($this->fileLinkFormatter && $fileLink = $this->fileLinkFormatter->format($context['file'], $context['line'])) {
            $context['file_link'] = $fileLink;
        }

        return $context;
    }

    private function htmlEncode(string $s): string
    {
        $html = '';

        $dumper = new HtmlDumper(static function ($line) use (&$html) { $html .= $line; }, $this->charset);
        $dumper->setDumpHeader('');
        $dumper->setDumpBoundaries('', '');

        $cloner = new VarCloner();
        $dumper->dump($cloner->cloneVar($s));

        return substr(strip_tags($html), 1, -1);
    }
}
