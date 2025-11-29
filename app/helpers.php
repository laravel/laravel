<?php

use App\Services\Ai\AiManager;

if (! function_exists('ai')) {
    /**
     * Resolve the AI manager and optionally select a provider.
     */
    function ai(?string $provider = null): AiManager
    {
        /** @var \App\Services\Ai\AiManager $manager */
        $manager = app(AiManager::class);

        if ($provider !== null) {
            return $manager->provider($provider);
        }

        return $manager;
    }
}
