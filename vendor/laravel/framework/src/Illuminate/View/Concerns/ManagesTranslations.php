<?php

namespace Illuminate\View\Concerns;

trait ManagesTranslations
{
    /**
     * The translation replacements for the translation being rendered.
     *
     * @var array
     */
    protected $translationReplacements = [];

    /**
     * Start a translation block.
     *
     * @param  array  $replacements
     * @return void
     */
    public function startTranslation($replacements = [])
    {
        ob_start();

        $this->translationReplacements = $replacements;
    }

    /**
     * Render the current translation.
     *
     * @return string
     */
    public function renderTranslation()
    {
        return $this->container->make('translator')->get(
            trim(ob_get_clean()), $this->translationReplacements
        );
    }
}
