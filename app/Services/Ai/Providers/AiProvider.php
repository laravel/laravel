<?php

namespace App\Services\Ai\Providers;

interface AiProvider
{
    /**
     * Send a prompt to the AI provider and return the response text.
     *
     * @param  string  $prompt
     * @param  array<string, mixed>  $options
     */
    public function ask(string $prompt, array $options = []): string;
}