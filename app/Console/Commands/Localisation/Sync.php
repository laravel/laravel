<?php

namespace App\Console\Commands\Localisation;

class Sync extends CodeLocalisation
{

    protected $signature   = 'localisation:sync {folders*} {--U|update}';
    protected $description = "Finds missing and unused translations. Allows the synchronization of the app's legacy translations with the json files. Will output verbose result in laravel.log";

    private $missingTranslationsKeys = [];
    private $extraTranslationsKeys = [];
    private $translationsFromJson = [];
    private $newTranslationsFromCodeFiles = [];

    public function handle()
    {
        $this->getCodeFiles();
        $this->getNewTranslationsFromCodeFiles();
        $extraLocalesList = $this->getExtraLocalesList();

        foreach ($extraLocalesList as $locale) {

            $this->getTranslationsFromJson($locale);
            $this->getDifferences();

            echo "Sync report for locale: " . $locale . "\n"
                . count($this->missingTranslationsKeys)
                . " missing translations were found\n"
                . count($this->extraTranslationsKeys)
                . " extra translations were found\nCheck laravel.log for details\n";

            $this->logOutput = PHP_EOL . "Sync report for locale: " . $locale . PHP_EOL . str_repeat('=', 26) . PHP_EOL;
            $this->logMissingTranslationKeys();
            $this->logExtraTranslationKeys();

            \Log::info($this->logOutput);
        }
    }

    private function getTranslationsFromJson($locale)
    {
        $this->translationsFromJson = (array) json_decode(\File::get(resource_path('lang/' . $locale . '.json')));
    }

    private function getNewTranslationsFromCodeFiles()
    {
        foreach ($this->codeFiles as $file) {

            $translations = $this->getNewTranslationsFromFile($file);

            $this->newTranslationsFromCodeFiles = array_merge($this->newTranslationsFromCodeFiles, $translations);
        }

        $this->newTranslationsFromCodeFiles = array_unique($this->newTranslationsFromCodeFiles);
    }

    private function getNewTranslationsFromFile($file)
    {
        $index        = 0;
        $translations = [];
        $fileContent  = \File::get($file);

        while ($startingPos = strpos($fileContent, '__(', $index)) {

            $endingPos = strpos($fileContent, ')', $startingPos);

            if ($endingPos - $startingPos < 5) {

                $index = $startingPos + 1;
                continue;
            }

            $label = substr($fileContent, $startingPos + 3, $endingPos - $startingPos - 3);

            if ($this->isNewLabelProcessable($label)) {

                $translations[] = substr($label, 1, count($label) - 2);
            }

            $index = $startingPos + 1;
        }

        return $translations;
    }

    private function isNewLabelProcessable($label)
    {
        return strpos($label, '$') === false;
    }

    private function getDifferences()
    {
        $jsonTranslationsKeys          = array_keys($this->translationsFromJson);
        $this->missingTranslationsKeys = array_diff($this->newTranslationsFromCodeFiles, $jsonTranslationsKeys);
        $this->extraTranslationsKeys   = array_diff($jsonTranslationsKeys, $this->newTranslationsFromCodeFiles);
    }

    private function logMissingTranslationKeys()
    {
        $this->logOutput .= "\n" . count($this->missingTranslationsKeys) . " missing translations:\n";

        foreach ($this->missingTranslationsKeys as $key) {

            $this->logOutput .= $key . "\n";
        }
    }

    private function logExtraTranslationKeys()
    {
        $this->logOutput .= "\n" . count($this->extraTranslationsKeys) . " extra translations:\n";

        foreach ($this->extraTranslationsKeys as $key) {

            $this->logOutput .= $key . "\n";
        }
    }
}
