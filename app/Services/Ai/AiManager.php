<?php

namespace App\Services\Ai;

use App\Services\Ai\Providers\AiProvider;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\GroqProvider;
use App\Services\Ai\Providers\OpenAiProvider;
use InvalidArgumentException;

class AiManager
{
    protected ?string $provider = null;

    public function __construct(?string $provider = null)
    {
        $this->provider = $provider ?? config('ai.default');
    }

    /**
     * Select the provider to use.
     */
    public function provider(?string $provider = null): self
    {
        $clone = clone $this;
        $clone->provider = $provider ?? config('ai.default');

        return $clone;
    }

    /**
     * Alias for provider().
     */
    public function using(string $provider): self
    {
        return $this->provider($provider);
    }

    /**
     * Allow ai() style usage when resolved from the container.
     */
    public function __invoke(?string $provider = null): self
    {
        return $this->provider($provider);
    }

    /**
     * Ask the current provider a question.
     *
     * @param  array<string, mixed>  $options
     */
    public function ask(string $prompt, array $options = []): string
    {
        return $this->resolveProvider()->ask($prompt, $options);
    }

    protected function resolveProvider(): AiProvider
    {
        $name = $this->provider ?? config('ai.default');

        $config = config("ai.providers.{$name}");

        if ($config === null) {
            throw new InvalidArgumentException("AI provider [{$name}] is not configured.");
        }

        return match ($name) {
            'openai' => new OpenAiProvider($config),
            'gemini' => new GeminiProvider($config),
            'groq' => new GroqProvider($config),
            default => throw new InvalidArgumentException("AI provider [{$name}] is not supported."),
        };
    }
}
