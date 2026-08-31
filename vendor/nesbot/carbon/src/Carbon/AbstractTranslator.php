<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon;

use Carbon\MessageFormatter\MessageFormatterMapper;
use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionProperty;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Throwable;

abstract class AbstractTranslator extends SymfonyTranslator
{
    public const REGION_CODE_LENGTH = 2;

    /**
     * Translator singletons for each language.
     *
     * @var array
     */
    protected static array $singletons = [];

    /**
     * List of custom localized messages.
     *
     * @var array
     */
    protected array $messages = [];

    /**
     * List of custom directories that contain translation files.
     *
     * @var string[]
     */
    protected array $directories = [];

    /**
     * Cache for language files.
     *
     * @var array<string, array>
     */
    protected array $fileCache = [];

    /**
     * Set to true while constructing.
     */
    protected bool $initializing = false;

    /**
     * List of locales aliases.
     *
     * @var array<string, string>
     */
    protected array $aliases = [
        'me' => 'sr_Latn_ME',
        'scr' => 'sh',
    ];

    /**
     * Return a singleton instance of Translator.
     *
     * @param string|null $locale optional initial locale ("en" - english by default)
     *
     * @return static
     */
    public static function get(?string $locale = null): static
    {
        $locale = $locale ?: 'en';
        $key = static::class === Translator::class ? $locale : static::class.'|'.$locale;
        $count = \count(static::$singletons);

        // Remember only the last 10 translators created
        if ($count > 10) {
            foreach (\array_slice(array_keys(static::$singletons), 0, $count - 10) as $index) {
                unset(static::$singletons[$index]);
            }
        }

        static::$singletons[$key] ??= new static($locale);

        return static::$singletons[$key];
    }

    public function __construct($locale, ?MessageFormatterInterface $formatter = null, $cacheDir = null, $debug = false)
    {
        $this->initialize($locale, $formatter, $cacheDir, $debug);
    }

    /**
     * Returns the list of directories translation files are searched in.
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }

    /**
     * Set list of directories translation files are searched in.
     *
     * @param array $directories new directories list
     *
     * @return $this
     */
    public function setDirectories(array $directories): static
    {
        $this->directories = $directories;

        return $this;
    }

    /**
     * Add a directory to the list translation files are searched in.
     *
     * @param string $directory new directory
     *
     * @return $this
     */
    public function addDirectory(string $directory): static
    {
        $this->directories[] = $directory;

        return $this;
    }

    /**
     * Remove a directory from the list translation files are searched in.
     *
     * @param string $directory directory path
     *
     * @return $this
     */
    public function removeDirectory(string $directory): static
    {
        $search = rtrim(strtr($directory, '\\', '/'), '/');

        return $this->setDirectories(array_filter(
            $this->getDirectories(),
            static fn ($item) => rtrim(strtr($item, '\\', '/'), '/') !== $search,
        ));
    }

    /**
     * Reset messages of a locale (all locale if no locale passed).
     * Remove custom messages and reload initial messages from matching
     * file in Lang directory.
     */
    public function resetMessages(?string $locale = null): bool
    {
        if ($locale === null) {
            $this->messages = [];
            $this->catalogues = [];
            $this->modifyResources(static function (array $resources): array {
                foreach ($resources as &$list) {
                    array_splice($list, 1);
                }

                return $resources;
            });

            return true;
        }

        $this->assertValidLocale($locale);

        foreach ($this->getDirectories() as $directory) {
            $file = \sprintf('%s/%s.php', rtrim($directory, '\\/'), $locale);
            $data = ($this->fileCache[$file] ??= self::loadFile($file));

            if ($data !== false) {
                $this->messages[$locale] = $data;
                unset($this->catalogues[$locale]);
                $this->modifyResources(static function (array $resources) use ($locale): array {
                    unset($resources[$locale]);

                    return $resources;
                });
                $this->addResource('array', $this->messages[$locale], $locale);

                return true;
            }
        }

        return false;
    }

    /**
     * Returns the list of files matching a given locale prefix (or all if empty).
     *
     * @param string $prefix prefix required to filter result
     *
     * @return array
     */
    public function getLocalesFiles(string $prefix = ''): array
    {
        $files = [];

        foreach ($this->getDirectories() as $directory) {
            foreach (self::getPhpFilesInDirectory(rtrim($directory, '\\/'), $prefix) as $file) {
                $files[] = $file;
            }
        }

        return array_unique($files);
    }

    /**
     * Returns the list of internally available locales and already loaded custom locales.
     * (It will ignore custom translator dynamic loading.)
     *
     * @param string $prefix prefix required to filter result
     *
     * @return array
     */
    public function getAvailableLocales(string $prefix = ''): array
    {
        return array_unique(array_merge(
            array_map(
                static fn (string $file) => substr($file, strrpos($file, '/') + 1, -4),
                $this->getLocalesFiles($prefix),
            ),
            array_keys($this->messages),
        ));
    }

    protected function translate(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        $domain ??= 'messages';
        $catalogue = $this->getCatalogue($locale);
        $format = $this instanceof TranslatorStrongTypeInterface
            ? $this->getFromCatalogue($catalogue, (string) $id, $domain)
            : $this->getCatalogue($locale)->get((string) $id, $domain); // @codeCoverageIgnore

        if ($format instanceof Closure) {
            // @codeCoverageIgnoreStart
            try {
                $count = (new ReflectionFunction($format))->getNumberOfRequiredParameters();
            } catch (ReflectionException) {
                $count = 0;
            }
            // @codeCoverageIgnoreEnd

            return $format(
                ...array_values($parameters),
                ...array_fill(0, max(0, $count - \count($parameters)), null)
            );
        }

        return parent::trans($id, $parameters, $domain, $locale);
    }

    /**
     * Init messages language from matching file in Lang directory.
     *
     * @param string $locale
     *
     * @return bool
     */
    protected function loadMessagesFromFile(string $locale): bool
    {
        return isset($this->messages[$locale]) || $this->resetMessages($locale);
    }

    /**
     * Set messages of a locale and take file first if present.
     *
     * @param string $locale
     * @param array  $messages
     *
     * @return $this
     */
    public function setMessages(string $locale, array $messages): static
    {
        $this->loadMessagesFromFile($locale);
        $this->addResource('array', $messages, $locale);
        $this->messages[$locale] = array_merge(
            $this->messages[$locale] ?? [],
            $messages
        );

        return $this;
    }

    /**
     * Set messages of the current locale and take file first if present.
     *
     * @param array $messages
     *
     * @return $this
     */
    public function setTranslations(array $messages): static
    {
        return $this->setMessages($this->getLocale(), $messages);
    }

    /**
     * Get messages of a locale, if none given, return all the
     * languages.
     */
    public function getMessages(?string $locale = null): array
    {
        return $locale === null ? $this->messages : ($this->messages[$locale] ?? []);
    }

    /**
     * Set the current translator locale and indicate if the source locale file exists
     *
     * @param string $locale locale ex. en
     */
    public function setLocale($locale): void
    {
        $previousLocale = $this->getLocale();

        if ($previousLocale === $locale && isset($this->messages[$locale])) {
            return;
        }

        $locale = preg_replace_callback('/[-_]([a-z]{2,}|\d{2,})/', function ($matches) {
            // _2-letters or YUE is a region, _3+-letters is a variant
            $upper = strtoupper($matches[1]);

            if ($upper === 'YUE' || $upper === 'ISO' || \strlen($upper) <= static::REGION_CODE_LENGTH) {
                return "_$upper";
            }

            return '_'.ucfirst($matches[1]);
        }, strtolower($locale));

        if ($previousLocale === $locale && isset($this->messages[$locale])) {
            return;
        }

        unset(static::$singletons[$previousLocale]);

        if ($locale === 'auto') {
            $completeLocale = setlocale(LC_TIME, '0');
            $locale = preg_replace('/^([^_.-]+).*$/', '$1', $completeLocale);
            $locales = $this->getAvailableLocales($locale);

            $completeLocaleChunks = preg_split('/[_.-]+/', $completeLocale);

            $getScore = static fn ($language) => self::compareChunkLists(
                $completeLocaleChunks,
                preg_split('/[_.-]+/', $language),
            );

            usort($locales, static fn ($first, $second) => $getScore($second) <=> $getScore($first));

            $locale = $locales[0] ?? 'en';
        }

        if (isset($this->aliases[$locale])) {
            $locale = $this->aliases[$locale];
        }

        // If the language is not provided by a Carbon file
        // and the tag contains a region (ex: en_CA), then
        // first load the macro (ex: en) to have a fallback
        if (
            str_contains($locale, '_')
            && !\in_array($locale, self::getInternallySupportedLocales(), true)
            && $this->loadMessagesFromFile($macroLocale = preg_replace('/^([^_]+).*$/', '$1', $locale))
        ) {
            parent::setLocale($macroLocale);
        }

        if (!$this->loadMessagesFromFile($locale) && !$this->initializing) {
            return;
        }

        parent::setLocale($locale);
    }

    /**
     * Show locale on var_dump().
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'locale' => $this->getLocale(),
        ];
    }

    public function __serialize(): array
    {
        return [
            'locale' => $this->getLocale(),
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->initialize($data['locale'] ?? 'en');
    }

    private function initialize($locale, ?MessageFormatterInterface $formatter = null, $cacheDir = null, $debug = false): void
    {
        parent::setLocale($locale);
        $this->initializing = true;
        $this->directories = [self::getDefaultLangDirectory()];
        $this->addLoader('array', new ArrayLoader());
        parent::__construct($locale, new MessageFormatterMapper($formatter), $cacheDir, $debug);
        $this->initializing = false;
    }

    private function loadFile(string $file): array|false
    {
        return file_exists($file) ? (include $file) : false;
    }

    private static function compareChunkLists(array $referenceChunks, array $chunks): int
    {
        $score = 0;

        foreach ($referenceChunks as $index => $chunk) {
            if (!isset($chunks[$index])) {
                $score++;

                continue;
            }

            if (strtolower($chunks[$index]) === strtolower($chunk)) {
                $score += 10;
            }
        }

        return $score;
    }

    /** @codeCoverageIgnore */
    private function modifyResources(callable $callback): void
    {
        try {
            $resourcesProperty = new ReflectionProperty(SymfonyTranslator::class, 'resources');
            $resources = $resourcesProperty->getValue($this);
            $resourcesProperty->setValue($this, $callback($resources));
        } catch (Throwable) {
            // Clear resources if available, if not, then nothing to clean
        }
    }

    private static function getPhpFilesInDirectory(string $directory, string $prefix): array
    {
        if ($directory !== self::getDefaultLangDirectory()) {
            return glob("$directory/$prefix*.php") ?: [];
        }

        // If it's the internal Carbon directory we use a static list
        // which is faster than scanning the folder with glob()
        $locales = self::getInternallySupportedLocales();

        if ($prefix !== '') {
            $locales = array_values(array_filter(
                self::getInternallySupportedLocales(),
                static fn (string $locale) => str_starts_with($locale, $prefix),
            ));
        }

        return array_map(
            static fn (string $locale) => "$directory/$locale.php",
            $locales,
        );
    }

    private static function getDefaultLangDirectory(): string
    {
        return __DIR__.'/Lang';
    }

    /** @return list<string> */
    private static function getInternallySupportedLocales(): array
    {
        return [
            'aa',
            'aa_DJ',
            'aa_ER',
            'aa_ER@saaho',
            'aa_ET',
            'af',
            'af_NA',
            'af_ZA',
            'agq',
            'agr',
            'agr_PE',
            'ak',
            'ak_GH',
            'am',
            'am_ET',
            'an',
            'an_ES',
            'anp',
            'anp_IN',
            'ar',
            'ar_AE',
            'ar_BH',
            'ar_DJ',
            'ar_DZ',
            'ar_EG',
            'ar_EH',
            'ar_ER',
            'ar_IL',
            'ar_IN',
            'ar_IQ',
            'ar_JO',
            'ar_KM',
            'ar_KW',
            'ar_LB',
            'ar_LY',
            'ar_MA',
            'ar_MR',
            'ar_OM',
            'ar_PS',
            'ar_QA',
            'ar_SA',
            'ar_SD',
            'ar_SO',
            'ar_SS',
            'ar_SY',
            'ar_Shakl',
            'ar_TD',
            'ar_TN',
            'ar_YE',
            'as',
            'as_IN',
            'asa',
            'ast',
            'ast_ES',
            'ayc',
            'ayc_PE',
            'az',
            'az_AZ',
            'az_Arab',
            'az_Cyrl',
            'az_IR',
            'az_Latn',
            'bas',
            'be',
            'be_BY',
            'be_BY@latin',
            'bem',
            'bem_ZM',
            'ber',
            'ber_DZ',
            'ber_MA',
            'bez',
            'bg',
            'bg_BG',
            'bhb',
            'bhb_IN',
            'bho',
            'bho_IN',
            'bi',
            'bi_VU',
            'bm',
            'bn',
            'bn_BD',
            'bn_IN',
            'bo',
            'bo_CN',
            'bo_IN',
            'br',
            'br_FR',
            'brx',
            'brx_IN',
            'bs',
            'bs_BA',
            'bs_Cyrl',
            'bs_Latn',
            'byn',
            'byn_ER',
            'ca',
            'ca_AD',
            'ca_ES',
            'ca_ES_Valencia',
            'ca_FR',
            'ca_IT',
            'ccp',
            'ccp_IN',
            'ce',
            'ce_RU',
            'cgg',
            'chr',
            'chr_US',
            'ckb',
            'cmn',
            'cmn_TW',
            'crh',
            'crh_UA',
            'cs',
            'cs_CZ',
            'csb',
            'csb_PL',
            'cu',
            'cv',
            'cv_RU',
            'cy',
            'cy_GB',
            'da',
            'da_DK',
            'da_GL',
            'dav',
            'de',
            'de_AT',
            'de_BE',
            'de_CH',
            'de_DE',
            'de_IT',
            'de_LI',
            'de_LU',
            'dje',
            'doi',
            'doi_IN',
            'dsb',
            'dsb_DE',
            'dua',
            'dv',
            'dv_MV',
            'dyo',
            'dz',
            'dz_BT',
            'ebu',
            'ee',
            'ee_TG',
            'el',
            'el_CY',
            'el_GR',
            'en',
            'en_001',
            'en_150',
            'en_AG',
            'en_AI',
            'en_AS',
            'en_AT',
            'en_AU',
            'en_BB',
            'en_BE',
            'en_BI',
            'en_BM',
            'en_BS',
            'en_BW',
            'en_BZ',
            'en_CA',
            'en_CC',
            'en_CH',
            'en_CK',
            'en_CM',
            'en_CX',
            'en_CY',
            'en_DE',
            'en_DG',
            'en_DK',
            'en_DM',
            'en_ER',
            'en_FI',
            'en_FJ',
            'en_FK',
            'en_FM',
            'en_GB',
            'en_GD',
            'en_GG',
            'en_GH',
            'en_GI',
            'en_GM',
            'en_GU',
            'en_GY',
            'en_HK',
            'en_IE',
            'en_IL',
            'en_IM',
            'en_IN',
            'en_IO',
            'en_ISO',
            'en_JE',
            'en_JM',
            'en_KE',
            'en_KI',
            'en_KN',
            'en_KY',
            'en_LC',
            'en_LR',
            'en_LS',
            'en_MG',
            'en_MH',
            'en_MO',
            'en_MP',
            'en_MS',
            'en_MT',
            'en_MU',
            'en_MW',
            'en_MY',
            'en_NA',
            'en_NF',
            'en_NG',
            'en_NL',
            'en_NR',
            'en_NU',
            'en_NZ',
            'en_PG',
            'en_PH',
            'en_PK',
            'en_PN',
            'en_PR',
            'en_PW',
            'en_RW',
            'en_SB',
            'en_SC',
            'en_SD',
            'en_SE',
            'en_SG',
            'en_SH',
            'en_SI',
            'en_SL',
            'en_SS',
            'en_SX',
            'en_SZ',
            'en_TC',
            'en_TK',
            'en_TO',
            'en_TT',
            'en_TV',
            'en_TZ',
            'en_UG',
            'en_UM',
            'en_US',
            'en_US_Posix',
            'en_VC',
            'en_VG',
            'en_VI',
            'en_VU',
            'en_WS',
            'en_ZA',
            'en_ZM',
            'en_ZW',
            'eo',
            'es',
            'es_419',
            'es_AR',
            'es_BO',
            'es_BR',
            'es_BZ',
            'es_CL',
            'es_CO',
            'es_CR',
            'es_CU',
            'es_DO',
            'es_EA',
            'es_EC',
            'es_ES',
            'es_GQ',
            'es_GT',
            'es_HN',
            'es_IC',
            'es_MX',
            'es_NI',
            'es_PA',
            'es_PE',
            'es_PH',
            'es_PR',
            'es_PY',
            'es_SV',
            'es_US',
            'es_UY',
            'es_VE',
            'et',
            'et_EE',
            'eu',
            'eu_ES',
            'ewo',
            'fa',
            'fa_AF',
            'fa_IR',
            'ff',
            'ff_CM',
            'ff_GN',
            'ff_MR',
            'ff_SN',
            'fi',
            'fi_FI',
            'fil',
            'fil_PH',
            'fo',
            'fo_DK',
            'fo_FO',
            'fr',
            'fr_BE',
            'fr_BF',
            'fr_BI',
            'fr_BJ',
            'fr_BL',
            'fr_CA',
            'fr_CD',
            'fr_CF',
            'fr_CG',
            'fr_CH',
            'fr_CI',
            'fr_CM',
            'fr_DJ',
            'fr_DZ',
            'fr_FR',
            'fr_GA',
            'fr_GF',
            'fr_GN',
            'fr_GP',
            'fr_GQ',
            'fr_HT',
            'fr_KM',
            'fr_LU',
            'fr_MA',
            'fr_MC',
            'fr_MF',
            'fr_MG',
            'fr_ML',
            'fr_MQ',
            'fr_MR',
            'fr_MU',
            'fr_NC',
            'fr_NE',
            'fr_PF',
            'fr_PM',
            'fr_RE',
            'fr_RW',
            'fr_SC',
            'fr_SN',
            'fr_SY',
            'fr_TD',
            'fr_TG',
            'fr_TN',
            'fr_VU',
            'fr_WF',
            'fr_YT',
            'fur',
            'fur_IT',
            'fy',
            'fy_DE',
            'fy_NL',
            'ga',
            'ga_IE',
            'gd',
            'gd_GB',
            'gez',
            'gez_ER',
            'gez_ET',
            'gl',
            'gl_ES',
            'gom',
            'gom_Latn',
            'gsw',
            'gsw_CH',
            'gsw_FR',
            'gsw_LI',
            'gu',
            'gu_IN',
            'guz',
            'gv',
            'gv_GB',
            'ha',
            'ha_GH',
            'ha_NE',
            'ha_NG',
            'hak',
            'hak_TW',
            'haw',
            'he',
            'he_IL',
            'hi',
            'hi_IN',
            'hif',
            'hif_FJ',
            'hne',
            'hne_IN',
            'hr',
            'hr_BA',
            'hr_HR',
            'hsb',
            'hsb_DE',
            'ht',
            'ht_HT',
            'hu',
            'hu_HU',
            'hy',
            'hy_AM',
            'i18n',
            'ia',
            'ia_FR',
            'id',
            'id_ID',
            'ig',
            'ig_NG',
            'ii',
            'ik',
            'ik_CA',
            'in',
            'is',
            'is_IS',
            'it',
            'it_CH',
            'it_IT',
            'it_SM',
            'it_VA',
            'iu',
            'iu_CA',
            'iw',
            'ja',
            'ja_JP',
            'jgo',
            'jmc',
            'jv',
            'ka',
            'ka_GE',
            'kab',
            'kab_DZ',
            'kam',
            'kde',
            'kea',
            'khq',
            'ki',
            'kk',
            'kk_KZ',
            'kkj',
            'kl',
            'kl_GL',
            'kln',
            'km',
            'km_KH',
            'kn',
            'kn_IN',
            'ko',
            'ko_KP',
            'ko_KR',
            'kok',
            'kok_IN',
            'ks',
            'ks_IN',
            'ks_IN@devanagari',
            'ksb',
            'ksf',
            'ksh',
            'ku',
            'ku_TR',
            'kw',
            'kw_GB',
            'ky',
            'ky_KG',
            'lag',
            'lb',
            'lb_LU',
            'lg',
            'lg_UG',
            'li',
            'li_NL',
            'lij',
            'lij_IT',
            'lkt',
            'ln',
            'ln_AO',
            'ln_CD',
            'ln_CF',
            'ln_CG',
            'lo',
            'lo_LA',
            'lrc',
            'lrc_IQ',
            'lt',
            'lt_LT',
            'lu',
            'luo',
            'luy',
            'lv',
            'lv_LV',
            'lzh',
            'lzh_TW',
            'mag',
            'mag_IN',
            'mai',
            'mai_IN',
            'mas',
            'mas_TZ',
            'mer',
            'mfe',
            'mfe_MU',
            'mg',
            'mg_MG',
            'mgh',
            'mgo',
            'mhr',
            'mhr_RU',
            'mi',
            'mi_NZ',
            'miq',
            'miq_NI',
            'mjw',
            'mjw_IN',
            'mk',
            'mk_MK',
            'ml',
            'ml_IN',
            'mn',
            'mn_MN',
            'mni',
            'mni_IN',
            'mo',
            'mr',
            'mr_IN',
            'ms',
            'ms_BN',
            'ms_MY',
            'ms_SG',
            'mt',
            'mt_MT',
            'mua',
            'my',
            'my_MM',
            'mzn',
            'nan',
            'nan_TW',
            'nan_TW@latin',
            'naq',
            'nb',
            'nb_NO',
            'nb_SJ',
            'nd',
            'nds',
            'nds_DE',
            'nds_NL',
            'ne',
            'ne_IN',
            'ne_NP',
            'nhn',
            'nhn_MX',
            'niu',
            'niu_NU',
            'nl',
            'nl_AW',
            'nl_BE',
            'nl_BQ',
            'nl_CW',
            'nl_NL',
            'nl_SR',
            'nl_SX',
            'nmg',
            'nn',
            'nn_NO',
            'nnh',
            'no',
            'nr',
            'nr_ZA',
            'nso',
            'nso_ZA',
            'nus',
            'nyn',
            'oc',
            'oc_FR',
            'om',
            'om_ET',
            'om_KE',
            'or',
            'or_IN',
            'os',
            'os_RU',
            'pa',
            'pa_Arab',
            'pa_Guru',
            'pa_IN',
            'pa_PK',
            'pap',
            'pap_AW',
            'pap_CW',
            'pl',
            'pl_PL',
            'prg',
            'ps',
            'ps_AF',
            'pt',
            'pt_AO',
            'pt_BR',
            'pt_CH',
            'pt_CV',
            'pt_GQ',
            'pt_GW',
            'pt_LU',
            'pt_MO',
            'pt_MZ',
            'pt_PT',
            'pt_ST',
            'pt_TL',
            'qu',
            'qu_BO',
            'qu_EC',
            'quz',
            'quz_PE',
            'raj',
            'raj_IN',
            'rm',
            'rn',
            'ro',
            'ro_MD',
            'ro_RO',
            'rof',
            'ru',
            'ru_BY',
            'ru_KG',
            'ru_KZ',
            'ru_MD',
            'ru_RU',
            'ru_UA',
            'rw',
            'rw_RW',
            'rwk',
            'sa',
            'sa_IN',
            'sah',
            'sah_RU',
            'saq',
            'sat',
            'sat_IN',
            'sbp',
            'sc',
            'sc_IT',
            'sd',
            'sd_IN',
            'sd_IN@devanagari',
            'se',
            'se_FI',
            'se_NO',
            'se_SE',
            'seh',
            'ses',
            'sg',
            'sgs',
            'sgs_LT',
            'sh',
            'shi',
            'shi_Latn',
            'shi_Tfng',
            'shn',
            'shn_MM',
            'shs',
            'shs_CA',
            'si',
            'si_LK',
            'sid',
            'sid_ET',
            'sk',
            'sk_SK',
            'sl',
            'sl_SI',
            'sm',
            'sm_WS',
            'smn',
            'sn',
            'so',
            'so_DJ',
            'so_ET',
            'so_KE',
            'so_SO',
            'sq',
            'sq_AL',
            'sq_MK',
            'sq_XK',
            'sr',
            'sr_Cyrl',
            'sr_Cyrl_BA',
            'sr_Cyrl_ME',
            'sr_Cyrl_XK',
            'sr_Latn',
            'sr_Latn_BA',
            'sr_Latn_ME',
            'sr_Latn_XK',
            'sr_ME',
            'sr_RS',
            'sr_RS@latin',
            'ss',
            'ss_ZA',
            'st',
            'st_ZA',
            'sv',
            'sv_AX',
            'sv_FI',
            'sv_SE',
            'sw',
            'sw_CD',
            'sw_KE',
            'sw_TZ',
            'sw_UG',
            'szl',
            'szl_PL',
            'ta',
            'ta_IN',
            'ta_LK',
            'ta_MY',
            'ta_SG',
            'tcy',
            'tcy_IN',
            'te',
            'te_IN',
            'teo',
            'teo_KE',
            'tet',
            'tg',
            'tg_TJ',
            'th',
            'th_TH',
            'the',
            'the_NP',
            'ti',
            'ti_ER',
            'ti_ET',
            'tig',
            'tig_ER',
            'tk',
            'tk_TM',
            'tl',
            'tl_PH',
            'tlh',
            'tn',
            'tn_ZA',
            'to',
            'to_TO',
            'tpi',
            'tpi_PG',
            'tr',
            'tr_CY',
            'tr_TR',
            'ts',
            'ts_ZA',
            'tt',
            'tt_RU',
            'tt_RU@iqtelif',
            'twq',
            'tzl',
            'tzm',
            'tzm_Latn',
            'ug',
            'ug_CN',
            'uk',
            'uk_UA',
            'unm',
            'unm_US',
            'ur',
            'ur_IN',
            'ur_PK',
            'uz',
            'uz_Arab',
            'uz_Cyrl',
            'uz_Latn',
            'uz_UZ',
            'uz_UZ@cyrillic',
            'vai',
            'vai_Latn',
            'vai_Vaii',
            've',
            've_ZA',
            'vi',
            'vi_VN',
            'vo',
            'vun',
            'wa',
            'wa_BE',
            'wae',
            'wae_CH',
            'wal',
            'wal_ET',
            'wo',
            'wo_SN',
            'xh',
            'xh_ZA',
            'xog',
            'yav',
            'yi',
            'yi_US',
            'yo',
            'yo_BJ',
            'yo_NG',
            'yue',
            'yue_HK',
            'yue_Hans',
            'yue_Hant',
            'yuw',
            'yuw_PG',
            'zgh',
            'zh',
            'zh_CN',
            'zh_HK',
            'zh_Hans',
            'zh_Hans_HK',
            'zh_Hans_MO',
            'zh_Hans_SG',
            'zh_Hant',
            'zh_Hant_HK',
            'zh_Hant_MO',
            'zh_Hant_TW',
            'zh_MO',
            'zh_SG',
            'zh_TW',
            'zh_YUE',
            'zu',
            'zu_ZA',
        ];
    }
}
