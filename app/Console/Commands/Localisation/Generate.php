<?php

namespace App\Console\Commands\Localisation;

class Generate extends CodeLocalisation
{

    protected $signature   = 'localisation:generate';
    protected $description = "Generates new L5.4 Json translations files from resource/lang/* files. Uses default locale keys as base.";

    public function handle()
    {
        echo "\nWarning: Ignoring " . implode(', ', $this->excludedLangFiles) . " files\n";

        $extraLocalesList = $this->getExtraLocalesList();

        foreach ($extraLocalesList as $locale) {

            $newTranslationsFileContent = $this->getMatchingKeysForLocale($locale);

            $this->createJsonFile($newTranslationsFileContent, $locale);
        }
    }

    private function getMatchingKeysForLocale($locale)
    {
        $matchingKeys = [];

        $defaultLangFilesList = $this->getLangFilesListForLocale($this->defaultLocale);

        foreach ($defaultLangFilesList as $langFile) {

            $matchingKeys = array_merge($matchingKeys, $this->getMatchingKeysForLangFile($locale, $langFile));
        }

        return $matchingKeys;
    }

    private function getMatchingKeysForLangFile($locale, $langFile)
    {
        $defaultLocaleFileContent = $this->getLangFileContent($this->defaultLocale, $langFile);

        $langFileContent = $this->getLangFileContent($locale, $langFile);

        return $this->matchKeys($defaultLocaleFileContent, $langFileContent);
    }

    private function matchKeys($defaultLocaleFileContent, $langFileContent)
    {
        $matchingKeys = [];

        foreach ($defaultLocaleFileContent as $key => $value) {

            $matchingKeys[$value] = isset($langFileContent[$key]) ? $langFileContent[$key] : null;
        }

        return $matchingKeys;
    }

    private function createJsonFile($newTranslationsFileContent, $locale)
    {
        $file = resource_path('lang/' . $locale . '.json');

        \File::put($file, json_encode($newTranslationsFileContent));

        echo $file . " was generated.\n";
    }
}