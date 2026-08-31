<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Node;

use function array_merge;
use function assert;
use function count;
use IteratorAggregate;
use RecursiveIteratorIterator;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type LinesOfCodeType from \SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser
 */
final class Directory extends AbstractNode implements IteratorAggregate
{
    /**
     * @var list<Directory|File>
     */
    private array $children = [];

    /**
     * @var list<Directory>
     */
    private array $directories = [];

    /**
     * @var list<File>
     */
    private array $files      = [];
    private ?array $classes   = null;
    private ?array $traits    = null;
    private ?array $functions = null;

    /**
     * @var null|LinesOfCodeType
     */
    private ?array $linesOfCode        = null;
    private int $numFiles              = -1;
    private int $numExecutableLines    = -1;
    private int $numExecutedLines      = -1;
    private int $numExecutableBranches = -1;
    private int $numExecutedBranches   = -1;
    private int $numExecutablePaths    = -1;
    private int $numExecutedPaths      = -1;
    private int $numClasses            = -1;
    private int $numTestedClasses      = -1;
    private int $numTraits             = -1;
    private int $numTestedTraits       = -1;
    private int $numMethods            = -1;
    private int $numTestedMethods      = -1;
    private int $numFunctions          = -1;
    private int $numTestedFunctions    = -1;

    public function count(): int
    {
        if ($this->numFiles === -1) {
            $this->numFiles = 0;

            foreach ($this->children as $child) {
                $this->numFiles += count($child);
            }
        }

        return $this->numFiles;
    }

    public function getIterator(): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new Iterator($this),
            RecursiveIteratorIterator::SELF_FIRST,
        );
    }

    public function addDirectory(string $name): self
    {
        $directory = new self($name, $this);

        assert($directory instanceof self);

        $this->children[]    = $directory;
        $this->directories[] = &$this->children[count($this->children) - 1];

        return $directory;
    }

    public function addFile(File $file): void
    {
        $this->children[] = $file;
        $this->files[]    = &$this->children[count($this->children) - 1];

        $this->numExecutableLines = -1;
        $this->numExecutedLines   = -1;
    }

    public function directories(): array
    {
        return $this->directories;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function children(): array
    {
        return $this->children;
    }

    public function classes(): array
    {
        if ($this->classes === null) {
            $this->classes = [];

            foreach ($this->children as $child) {
                $this->classes = array_merge(
                    $this->classes,
                    $child->classes(),
                );
            }
        }

        return $this->classes;
    }

    public function traits(): array
    {
        if ($this->traits === null) {
            $this->traits = [];

            foreach ($this->children as $child) {
                $this->traits = array_merge(
                    $this->traits,
                    $child->traits(),
                );
            }
        }

        return $this->traits;
    }

    public function functions(): array
    {
        if ($this->functions === null) {
            $this->functions = [];

            foreach ($this->children as $child) {
                $this->functions = array_merge(
                    $this->functions,
                    $child->functions(),
                );
            }
        }

        return $this->functions;
    }

    /**
     * @return LinesOfCodeType
     */
    public function linesOfCode(): array
    {
        if ($this->linesOfCode === null) {
            $this->linesOfCode = [
                'linesOfCode'           => 0,
                'commentLinesOfCode'    => 0,
                'nonCommentLinesOfCode' => 0,
            ];

            foreach ($this->children as $child) {
                $childLinesOfCode = $child->linesOfCode();

                $this->linesOfCode['linesOfCode']           += $childLinesOfCode['linesOfCode'];
                $this->linesOfCode['commentLinesOfCode']    += $childLinesOfCode['commentLinesOfCode'];
                $this->linesOfCode['nonCommentLinesOfCode'] += $childLinesOfCode['nonCommentLinesOfCode'];
            }
        }

        return $this->linesOfCode;
    }

    public function numberOfExecutableLines(): int
    {
        if ($this->numExecutableLines === -1) {
            $this->numExecutableLines = 0;

            foreach ($this->children as $child) {
                $this->numExecutableLines += $child->numberOfExecutableLines();
            }
        }

        return $this->numExecutableLines;
    }

    public function numberOfExecutedLines(): int
    {
        if ($this->numExecutedLines === -1) {
            $this->numExecutedLines = 0;

            foreach ($this->children as $child) {
                $this->numExecutedLines += $child->numberOfExecutedLines();
            }
        }

        return $this->numExecutedLines;
    }

    public function numberOfExecutableBranches(): int
    {
        if ($this->numExecutableBranches === -1) {
            $this->numExecutableBranches = 0;

            foreach ($this->children as $child) {
                $this->numExecutableBranches += $child->numberOfExecutableBranches();
            }
        }

        return $this->numExecutableBranches;
    }

    public function numberOfExecutedBranches(): int
    {
        if ($this->numExecutedBranches === -1) {
            $this->numExecutedBranches = 0;

            foreach ($this->children as $child) {
                $this->numExecutedBranches += $child->numberOfExecutedBranches();
            }
        }

        return $this->numExecutedBranches;
    }

    public function numberOfExecutablePaths(): int
    {
        if ($this->numExecutablePaths === -1) {
            $this->numExecutablePaths = 0;

            foreach ($this->children as $child) {
                $this->numExecutablePaths += $child->numberOfExecutablePaths();
            }
        }

        return $this->numExecutablePaths;
    }

    public function numberOfExecutedPaths(): int
    {
        if ($this->numExecutedPaths === -1) {
            $this->numExecutedPaths = 0;

            foreach ($this->children as $child) {
                $this->numExecutedPaths += $child->numberOfExecutedPaths();
            }
        }

        return $this->numExecutedPaths;
    }

    public function numberOfClasses(): int
    {
        if ($this->numClasses === -1) {
            $this->numClasses = 0;

            foreach ($this->children as $child) {
                $this->numClasses += $child->numberOfClasses();
            }
        }

        return $this->numClasses;
    }

    public function numberOfTestedClasses(): int
    {
        if ($this->numTestedClasses === -1) {
            $this->numTestedClasses = 0;

            foreach ($this->children as $child) {
                $this->numTestedClasses += $child->numberOfTestedClasses();
            }
        }

        return $this->numTestedClasses;
    }

    public function numberOfTraits(): int
    {
        if ($this->numTraits === -1) {
            $this->numTraits = 0;

            foreach ($this->children as $child) {
                $this->numTraits += $child->numberOfTraits();
            }
        }

        return $this->numTraits;
    }

    public function numberOfTestedTraits(): int
    {
        if ($this->numTestedTraits === -1) {
            $this->numTestedTraits = 0;

            foreach ($this->children as $child) {
                $this->numTestedTraits += $child->numberOfTestedTraits();
            }
        }

        return $this->numTestedTraits;
    }

    public function numberOfMethods(): int
    {
        if ($this->numMethods === -1) {
            $this->numMethods = 0;

            foreach ($this->children as $child) {
                $this->numMethods += $child->numberOfMethods();
            }
        }

        return $this->numMethods;
    }

    public function numberOfTestedMethods(): int
    {
        if ($this->numTestedMethods === -1) {
            $this->numTestedMethods = 0;

            foreach ($this->children as $child) {
                $this->numTestedMethods += $child->numberOfTestedMethods();
            }
        }

        return $this->numTestedMethods;
    }

    public function numberOfFunctions(): int
    {
        if ($this->numFunctions === -1) {
            $this->numFunctions = 0;

            foreach ($this->children as $child) {
                $this->numFunctions += $child->numberOfFunctions();
            }
        }

        return $this->numFunctions;
    }

    public function numberOfTestedFunctions(): int
    {
        if ($this->numTestedFunctions === -1) {
            $this->numTestedFunctions = 0;

            foreach ($this->children as $child) {
                $this->numTestedFunctions += $child->numberOfTestedFunctions();
            }
        }

        return $this->numTestedFunctions;
    }
}
