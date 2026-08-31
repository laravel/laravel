<?php

namespace Illuminate\Foundation\Exceptions\Renderer\Mappers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ViewException;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;

/*
 * This file contains parts of https://github.com/spatie/laravel-ignition.
 *
 * (c) Spatie <info@spatie.be>
 *
 * For the full copyright and license information, please review its LICENSE:
 *
 * The MIT License (MIT)
 *
 * Copyright (c) Spatie <info@spatie.be>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class BladeMapper
{
    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $factory;

    /**
     * The Blade compiler instance.
     *
     * @var \Illuminate\View\Compilers\BladeCompiler
     */
    protected $bladeCompiler;

    /**
     * Create a new Blade mapper instance.
     *
     * @param  \Illuminate\Contracts\View\Factory  $factory
     * @param  \Illuminate\View\Compilers\BladeCompiler  $bladeCompiler
     */
    public function __construct(Factory $factory, BladeCompiler $bladeCompiler)
    {
        $this->factory = $factory;
        $this->bladeCompiler = $bladeCompiler;
    }

    /**
     * Map cached view paths to their original paths.
     *
     * @param  \Symfony\Component\ErrorHandler\Exception\FlattenException  $exception
     * @return \Symfony\Component\ErrorHandler\Exception\FlattenException
     */
    public function map(FlattenException $exception)
    {
        while ($exception->getClass() === ViewException::class) {
            if (($previous = $exception->getPrevious()) === null) {
                break;
            }

            $exception = $previous;
        }

        $trace = (new Collection($exception->getTrace()))
            ->map(function ($frame) {
                if ($originalPath = $this->findCompiledView((string) Arr::get($frame, 'file', ''))) {
                    $frame['file'] = $originalPath;
                    $frame['line'] = $this->detectLineNumber($frame['file'], $frame['line']);
                }

                return $frame;
            })->toArray();

        return tap($exception, fn () => (fn () => $this->trace = $trace)->call($exception));
    }

    /**
     * Find the compiled view file for the given compiled path.
     *
     * @param  string  $compiledPath
     * @return string|null
     */
    protected function findCompiledView(string $compiledPath)
    {
        return once(fn () => $this->getKnownPaths())[$compiledPath] ?? null;
    }

    /**
     * Get the list of known paths from the compiler engine.
     *
     * @return array<string, string>
     */
    protected function getKnownPaths()
    {
        $compilerEngineReflection = new ReflectionClass(
            $bladeCompilerEngine = $this->factory->getEngineResolver()->resolve('blade'),
        );

        if (! $compilerEngineReflection->hasProperty('lastCompiled') && $compilerEngineReflection->hasProperty('engine')) {
            $compilerEngine = $compilerEngineReflection->getProperty('engine');
            $compilerEngine = $compilerEngine->getValue($bladeCompilerEngine);
            $lastCompiled = new ReflectionProperty($compilerEngine, 'lastCompiled');
            $lastCompiled = $lastCompiled->getValue($compilerEngine);
        } else {
            $lastCompiled = $compilerEngineReflection->getProperty('lastCompiled');
            $lastCompiled = $lastCompiled->getValue($bladeCompilerEngine);
        }

        $knownPaths = [];
        foreach ($lastCompiled as $lastCompiledPath) {
            $compiledPath = $bladeCompilerEngine->getCompiler()->getCompiledPath($lastCompiledPath);

            $knownPaths[realpath($compiledPath ?? $lastCompiledPath)] = realpath($lastCompiledPath);
        }

        return $knownPaths;
    }

    /**
     * Filter out the view data that should not be shown in the exception report.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function filterViewData(array $data)
    {
        return array_filter($data, function ($value, $key) {
            if ($key === 'app') {
                return ! $value instanceof Application;
            }

            return $key !== '__env';
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Detect the line number in the original blade file.
     *
     * @param  string  $filename
     * @param  int  $compiledLineNumber
     * @return int
     */
    protected function detectLineNumber(string $filename, int $compiledLineNumber)
    {
        $map = $this->compileSourcemap((string) file_get_contents($filename));

        return $this->findClosestLineNumberMapping($map, $compiledLineNumber);
    }

    /**
     * Compile the source map for the given blade file.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileSourcemap(string $value)
    {
        try {
            $value = $this->addEchoLineNumbers($value);
            $value = $this->addStatementLineNumbers($value);
            $value = $this->addBladeComponentLineNumbers($value);

            $value = $this->bladeCompiler->compileString($value);

            return $this->trimEmptyLines($value);
        } catch (Throwable $e) {
            report($e);

            return $value;
        }
    }

    /**
     * Add line numbers to echo statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function addEchoLineNumbers(string $value)
    {
        $echoPairs = [['{{', '}}'], ['{{{', '}}}'], ['{!!', '!!}']];

        foreach ($echoPairs as $pair) {
            // Matches {{ $value }}, {!! $value !!} and  {{{ $value }}} depending on $pair
            $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $pair[0], $pair[1]);

            if (preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
                foreach (array_reverse($matches[0]) as $match) {
                    $position = mb_strlen(substr($value, 0, $match[1]));

                    $value = $this->insertLineNumberAtPosition($position, $value);
                }
            }
        }

        return $value;
    }

    /**
     * Add line numbers to blade statements.
     *
     * @param  string  $value
     * @return string
     */
    protected function addStatementLineNumbers(string $value)
    {
        $shouldInsertLineNumbers = preg_match_all(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            $value,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if ($shouldInsertLineNumbers) {
            foreach (array_reverse($matches[0]) as $match) {
                $position = mb_strlen(substr($value, 0, $match[1]));

                $value = $this->insertLineNumberAtPosition($position, $value);
            }
        }

        return $value;
    }

    /**
     * Add line numbers to blade components.
     *
     * @param  string  $value
     * @return string
     */
    protected function addBladeComponentLineNumbers(string $value)
    {
        $shouldInsertLineNumbers = preg_match_all(
            '/<\s*x[-:]([\w\-:.]*)/mx',
            $value,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if ($shouldInsertLineNumbers) {
            foreach (array_reverse($matches[0]) as $match) {
                $position = mb_strlen(substr($value, 0, $match[1]));

                $value = $this->insertLineNumberAtPosition($position, $value);
            }
        }

        return $value;
    }

    /**
     * Insert a line number at the given position.
     *
     * @param  int  $position
     * @param  string  $value
     * @return string
     */
    protected function insertLineNumberAtPosition(int $position, string $value)
    {
        $before = mb_substr($value, 0, $position);

        $lineNumber = count(explode("\n", $before));

        return mb_substr($value, 0, $position)."|---LINE:{$lineNumber}---|".mb_substr($value, $position);
    }

    /**
     * Trim empty lines from the given value.
     *
     * @param  string  $value
     * @return string
     */
    protected function trimEmptyLines(string $value)
    {
        $value = preg_replace('/^\|---LINE:([0-9]+)---\|$/m', '', $value);

        return ltrim((string) $value, PHP_EOL);
    }

    /**
     * Find the closest line number mapping in the given source map.
     *
     * @param  string  $map
     * @param  int  $compiledLineNumber
     * @return int
     */
    protected function findClosestLineNumberMapping(string $map, int $compiledLineNumber)
    {
        $map = explode("\n", $map);

        $maxDistance = 20;

        $pattern = '/\|---LINE:(?P<line>[0-9]+)---\|/m';

        $lineNumberToCheck = $compiledLineNumber - 1;

        while (true) {
            if ($lineNumberToCheck < $compiledLineNumber - $maxDistance) {
                return min($compiledLineNumber, count($map));
            }

            if (preg_match($pattern, $map[$lineNumberToCheck] ?? '', $matches)) {
                return (int) $matches['line'];
            }

            $lineNumberToCheck--;
        }
    }
}
