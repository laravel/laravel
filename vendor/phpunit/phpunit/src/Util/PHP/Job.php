<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\PHP;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Job
{
    /**
     * @var non-empty-string
     */
    private string $code;

    /**
     * @var list<string>
     */
    private array $phpSettings;

    /**
     * @var array<string, string>
     */
    private array $environmentVariables;

    /**
     * @var list<non-empty-string>
     */
    private array $arguments;

    /**
     * @var ?non-empty-string
     */
    private ?string $input;
    private bool $redirectErrors;
    private bool $requiresXdebug;

    /**
     * @param non-empty-string       $code
     * @param list<string>           $phpSettings
     * @param array<string, string>  $environmentVariables
     * @param list<non-empty-string> $arguments
     * @param ?non-empty-string      $input
     */
    public function __construct(string $code, array $phpSettings = [], array $environmentVariables = [], array $arguments = [], ?string $input = null, bool $redirectErrors = false, bool $requiresXdebug = false)
    {
        $this->code                 = $code;
        $this->phpSettings          = $phpSettings;
        $this->environmentVariables = $environmentVariables;
        $this->arguments            = $arguments;
        $this->input                = $input;
        $this->redirectErrors       = $redirectErrors;
        $this->requiresXdebug       = $requiresXdebug;
    }

    /**
     * @return non-empty-string
     */
    public function code(): string
    {
        return $this->code;
    }

    /**
     * @return list<string>
     */
    public function phpSettings(): array
    {
        return $this->phpSettings;
    }

    /**
     * @phpstan-assert-if-true !empty $this->environmentVariables
     */
    public function hasEnvironmentVariables(): bool
    {
        return $this->environmentVariables !== [];
    }

    /**
     * @return array<string, string>
     */
    public function environmentVariables(): array
    {
        return $this->environmentVariables;
    }

    /**
     * @phpstan-assert-if-true !empty $this->arguments
     */
    public function hasArguments(): bool
    {
        return $this->arguments !== [];
    }

    /**
     * @return list<non-empty-string>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * @phpstan-assert-if-true !empty $this->input
     */
    public function hasInput(): bool
    {
        return $this->input !== null;
    }

    /**
     * @throws PhpProcessException
     *
     * @return non-empty-string
     */
    public function input(): string
    {
        if ($this->input === null) {
            throw new PhpProcessException('No input specified');
        }

        return $this->input;
    }

    public function redirectErrors(): bool
    {
        return $this->redirectErrors;
    }

    public function requiresXdebug(): bool
    {
        return $this->requiresXdebug;
    }
}
