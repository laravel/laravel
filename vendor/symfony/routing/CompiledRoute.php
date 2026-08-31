<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing;

/**
 * CompiledRoutes are returned by the RouteCompiler class.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CompiledRoute
{
    /**
     * @param string      $staticPrefix  The static prefix of the compiled route
     * @param string      $regex         The regular expression to use to match this route
     * @param array       $tokens        An array of tokens to use to generate URL for this route
     * @param array       $pathVariables An array of path variables
     * @param string|null $hostRegex     Host regex
     * @param array       $hostTokens    Host tokens
     * @param array       $hostVariables An array of host variables
     * @param array       $variables     An array of variables (variables defined in the path and in the host patterns)
     */
    public function __construct(
        private string $staticPrefix,
        private string $regex,
        private array $tokens,
        private array $pathVariables,
        private ?string $hostRegex = null,
        private array $hostTokens = [],
        private array $hostVariables = [],
        private array $variables = [],
    ) {
    }

    public function __serialize(): array
    {
        return [
            'vars' => $this->variables,
            'path_prefix' => $this->staticPrefix,
            'path_regex' => $this->regex,
            'path_tokens' => $this->tokens,
            'path_vars' => $this->pathVariables,
            'host_regex' => $this->hostRegex,
            'host_tokens' => $this->hostTokens,
            'host_vars' => $this->hostVariables,
        ];
    }

    public function __unserialize(array $data): void
    {
        if (($data['path_prefix'] ?? null) instanceof \Stringable
            || ($data['path_regex'] ?? null) instanceof \Stringable
            || ($data['host_regex'] ?? null) instanceof \Stringable
        ) {
            throw new \BadMethodCallException('Cannot unserialize '.self::class);
        }

        $this->variables = $data['vars'];
        $this->staticPrefix = $data['path_prefix'];
        $this->regex = $data['path_regex'];
        $this->tokens = $data['path_tokens'];
        $this->pathVariables = $data['path_vars'];
        $this->hostRegex = $data['host_regex'];
        $this->hostTokens = $data['host_tokens'];
        $this->hostVariables = $data['host_vars'];
    }

    /**
     * Returns the static prefix.
     */
    public function getStaticPrefix(): string
    {
        return $this->staticPrefix;
    }

    /**
     * Returns the regex.
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * Returns the host regex.
     */
    public function getHostRegex(): ?string
    {
        return $this->hostRegex;
    }

    /**
     * Returns the tokens.
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Returns the host tokens.
     */
    public function getHostTokens(): array
    {
        return $this->hostTokens;
    }

    /**
     * Returns the variables.
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Returns the path variables.
     */
    public function getPathVariables(): array
    {
        return $this->pathVariables;
    }

    /**
     * Returns the host variables.
     */
    public function getHostVariables(): array
    {
        return $this->hostVariables;
    }
}
