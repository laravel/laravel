<?php

namespace App\Console\Commands\Localisation;

use Illuminate\Console\Command;

class CodeLocalisation extends Command
{

    public $defaultLocale;
    public $codeFiles = [];
    public $logOutput = '';
    public $excludedLangFiles = ['auth.php', 'pagination.php', 'passwords.php', 'validation.php'];

    public function __construct()
    {
        parent::__construct();

        $this->defaultLocale = config('app.locale');
    }

    public function getExtraLocalesList()
    {
        $localesList = $this->getLocalesList();

        return $this->excludeDefaultLocaleFrom($localesList);
    }

    private function excludeDefaultLocaleFrom($localesList)
    {
        $index = array_search($this->defaultLocale, $localesList);

        unset($localesList[$index]);

        return $localesList;
    }

    public function getLocalesList()
    {
        $fullPathLocalesDirectoriesList = \File::directories(resource_path('lang'));

        return array_map([$this, 'getLastChildDirectoryName'], $fullPathLocalesDirectoriesList);
    }

    private function getLastChildDirectoryName($fullPathDirectory)
    {
        $fullPathArray = explode('/', $fullPathDirectory);

        return end($fullPathArray);
    }

    public function getCodeFiles()
    {
        foreach ($this->argument('folders') as $folder) {

            $folderFiles = \File::allFiles($folder);

            $this->codeFiles = array_merge($this->codeFiles, $folderFiles);
        }

        echo "\nScanning " . count($this->codeFiles) . " files for translations in " . count($this->arguments()["folders"]) . " folder(s)...\n";
    }

    public function getLangFilesListForLocale($locale)
    {
        $files = \File::files(resource_path('lang/' . $locale));

        $files = array_map(function ($file) {

            return last(explode('/', $file));
        }, $files);

        return $this->removeExcludedLangFilesFrom($files);
    }

    private function removeExcludedLangFilesFrom($files)
    {
        $keysToUnset = array_intersect($files, $this->excludedLangFiles);

        foreach ($keysToUnset as $key => $value) {

            unset($files[$key]);
        }

        return $files;
    }

    public function getLangFileContent($locale, $langFile)
    {
        $langFileArray = include resource_path('lang/' . $locale . '/' . $langFile);

        return $langFileArray;
    }
}
