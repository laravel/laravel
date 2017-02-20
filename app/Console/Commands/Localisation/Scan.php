<?php

namespace App\Console\Commands\Localisation;

class Scan extends CodeLocalisation
{

    protected $signature   = 'localisation:scan {folders*} {--M|migrate}';
    protected $description = "Collect localisation labels used with 'trans' in given directories and migrate to the L5.4 localisation format. Will output verbose result in laravel.log";

    private $notProcessableCount = 0;
    private $legacyTranslationsFromCodeFiles = [];
    private $legacyTranslationsFromCodeFilesCount = 0;
    private $legacyTranslationsFromLangFiles = [];

    public function handle()
    {
        $this->getCodeFiles();
        $this->getLegacyTranslationsFromLangFiles();
        $this->getLegacyTranslationsFromCodeFiles();
        $this->scanLegacyTranslationsFromCodeFiles();
        $this->renameOldLangFiles();

        echo $this->legacyTranslationsFromCodeFilesCount . " translations found\n" . ($this->option('migrate') ? ($this->legacyTranslationsFromCodeFilesCount - $this->notProcessableCount) . " translations were processed\n" . $this->notProcessableCount . " were ignored\nCheck laravel.log for details\n" :
            $this->notProcessableCount . " are not processable (will be ignored)\nCheck laravel.log for details\n");

        \Log::info($this->logOutput);
    }

    private function getLegacyTranslationsFromLangFiles()
    {
        $legacyFiles = $this->getLangFilesListForLocale($this->defaultLocale);

        foreach ($legacyFiles as $file) {

            $fileArray = $this->getLegacyTranslationsFromLangFile($file);

            $this->legacyTranslationsFromLangFiles = array_merge($this->legacyTranslationsFromLangFiles, $fileArray);
        }
    }

    private function getLegacyTranslationsFromLangFile($file)
    {
        $fileArray = $this->getLangFileContent($this->defaultLocale, $file);
        $keyPrefix = str_replace('.php', '', $file);
        $fileArray = $this->addKeyPrefixToArray($fileArray, $keyPrefix);

        return $fileArray;
    }

    private function addKeyPrefixToArray($fileArray, $keyPrefix)
    {
        $newKeysArray = array_map(function ($key) use ($keyPrefix) {

            return $keyPrefix . '.' . $key;
        }, array_keys($fileArray));

        $fileArray = array_combine($newKeysArray, array_values($fileArray));

        return $fileArray;
    }

    private function getLegacyTranslationsFromCodeFiles()
    {
        foreach ($this->codeFiles as $file) {

            $translations = $this->getLegacyTranslationsFromFile($file);

            if (count($translations)) {

                $this->legacyTranslationsFromCodeFiles[] = ['file' => $file, 'translations' => $translations];
            }
        }
    }

    private function getLegacyTranslationsFromFile($file)
    {
        $index        = 0;
        $translations = [];
        $fileContent  = \File::get($file);

        while ($startingPos = strpos($fileContent, 'trans(', $index)) {

            $endingPos = strpos($fileContent, ')', $startingPos);
            $label     = substr($fileContent, $startingPos, $endingPos - $startingPos + 1);

            $translations[] = $label;

            $index = $startingPos + 1;
            $this->legacyTranslationsFromCodeFilesCount++;
        }

        return $translations;
    }

    private function scanLegacyTranslationsFromCodeFiles()
    {
        foreach ($this->legacyTranslationsFromCodeFiles as $legacyTranslationsArray) {

            $this->logFileName($legacyTranslationsArray['file']);

            $fileContent = \File::get($legacyTranslationsArray['file']);
            $fileContent = $this->migrateLegacyTranslations($fileContent, $legacyTranslationsArray['translations']);

            if ($this->option('migrate')) {

                \File::put($legacyTranslationsArray['file'], $fileContent);
            }
        }
    }

    private function migrateLegacyTranslations($fileContent, $translations)
    {
        foreach ($translations as $translation) {

            if (!$this->isLegacyLabelProcessable($translation)) {

                $this->notProcessableCount++;

                continue;
            }

            $newTranslation = $this->migrateLegacyTranslation($translation);
            $fileContent    = str_replace($translation, $newTranslation, $fileContent);

            $this->logMigration($translation, $newTranslation);
        }

        return $fileContent;
    }

    private function migrateLegacyTranslation($translation)
    {
        $legacyKey      = substr($translation, 7, strlen($translation) - 9);
        $newTranslation = str_replace('trans(', '__(', $translation);
        $newTranslation = $this->forceDoubleQuotes($newTranslation);
        $newKey         = $this->legacyTranslationsFromLangFiles[$legacyKey];
        $newTranslation = str_replace($legacyKey, $newKey, $newTranslation);

        return $newTranslation;
    }

    private function forceDoubleQuotes($translation)
    {
        if ($translation[3] === "'" && $translation[-2] === "'") {

            $translation[3]  = '"';
            $translation[-2] = '"';
        }

        return $translation;
    }

    private function isLegacyLabelProcessable($translation)
    {
        $processable = $this->checkTranslationQuotes($translation)
        && $this->checkTranslationLength($translation)
        && $this->checkTranslationForVariables($translation)
        && $this->checkTranslationForConcat($translation)
        && $this->checkLegacyLabelMatching($translation);

        return $processable;
    }

    private function checkTranslationQuotes($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 1, strlen($label) - 2);

        $processable = $label[0] === $label[strlen($label) - 1] && ($label[0] === '"' || $label[0] === "'");

        if (!$processable) {

            $this->logOutput .= $translation . ' -> Error: ' . 'The label has unknown format' . PHP_EOL;
        }

        return $processable;
    }

    private function checkTranslationLength($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 1, strlen($label) - 2);

        $processable = strlen($label) > 3;

        if (!$processable) {

            $this->logOutput .= $translation . ' -> Error: ' . 'The label is too short' . PHP_EOL;
        }

        return $processable;
    }

    private function checkTranslationForVariables($translation)
    {
        $label = str_replace('trans', '', $translation);
        $label = substr($label, 1, strlen($label) - 2);

        $processable = $label[0] === '"' ? (strpos($label, '$') === false ? true : false) : true;

        if (!$processable) {

            $this->logOutput .= $translation . ' -> Error: ' . 'The label may contain variables' . PHP_EOL;
        }

        return $processable;
    }

    private function checkTranslationForConcat($translation)
    {
        $label       = str_replace('trans', '', $translation);
        $label       = substr($label, 2, strlen($label) - 4);
        $processable = strpos($label, "'") === false && strpos($label, '"') === false;

        if (!$processable) {

            $this->logOutput .= $translation . ' -> Error: ' . 'The label has an unknown format' . PHP_EOL;
        }

        return $processable;
    }

    private function checkLegacyLabelMatching($translation)
    {
        $label       = str_replace('trans', '', $translation);
        $label       = substr($label, 2, strlen($label) - 4);
        $processable = isset($this->legacyTranslationsFromLangFiles[$label]);

        if (!$processable) {

            $this->logOutput .= $translation . ' -> Error: ' . 'The label is missing from Lang Files' . PHP_EOL;
        }

        return $processable;
    }

    private function renameOldLangFiles()
    {
        if ($this->option('migrate')) {

            $localesList = $this->getLocalesList();

            foreach ($localesList as $locale) {

                $filesList = $this->getLangFilesListForLocale($locale);

                foreach ($filesList as $file) {

                    \File::move(resource_path('lang/' . $locale . '/' . $file), resource_path('lang/' . $locale . '/' . $file . '.old'));
                }

                echo count($filesList) . " files renamed to *.old in lang/" . $locale . "/" . "directory \n";
            }
        }
    }

    private function logFileName($file)
    {
        $this->logOutput .= PHP_EOL . $file . PHP_EOL . str_repeat('=', strlen($file)) . PHP_EOL;
    }

    private function logMigration($translation, $newTranslation)
    {
        $this->logOutput .= $translation . ' -> Success! The new label is: ' . $newTranslation . PHP_EOL;
    }
}
