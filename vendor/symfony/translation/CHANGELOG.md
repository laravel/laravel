CHANGELOG
=========

7.4
---

 * Make the extractor alias optional
 * Deprecate `TranslatableMessage::__toString`
 * Add `Symfony\Component\Translation\StaticMessage`

7.3
---

 * Add `Translator::addGlobalParameter()` to allow defining global translation parameters

7.2
---

 * Deprecate `ProviderFactoryTestCase`, extend `AbstractProviderFactoryTestCase` instead

   The `testIncompleteDsnException()` test is no longer provided by default. If you make use of it by implementing the `incompleteDsnProvider()` data providers,
   you now need to use the `IncompleteDsnTestTrait`.

 * Make `ProviderFactoryTestCase` and `ProviderTestCase` compatible with PHPUnit 10+
 * Add `lint:translations` command
 * Deprecate passing an escape character to `CsvFileLoader::setCsvControl()`
 * Make Xliff 2.0 attributes in segment element available as `segment-attributes`
   metadata returned by `XliffFileLoader` and make `XliffFileDumper` write them to the file

7.1
---

 * Mark class `DataCollectorTranslator` as `final`

7.0
---

 * Remove `PhpStringTokenParser`
 * Remove `PhpExtractor` in favor of `PhpAstExtractor`

6.4
---

 * Give current locale to `LocaleSwitcher::runWithLocale()`'s callback
 * Add `--as-tree` option to `translation:pull` command to write YAML messages as a tree-like structure
 * [BC BREAK] Add argument `$buildDir` to `DataCollectorTranslator::warmUp()`
 * Add `DataCollectorTranslatorPass` and `LoggingTranslatorPass`  (moved from `FrameworkBundle`)
 * Add `PhraseTranslationProvider`

6.2.7
-----

 * [BC BREAK] The following data providers for `ProviderFactoryTestCase` are now static:
   `supportsProvider()`, `createProvider()`, `unsupportedSchemeProvider()`and `incompleteDsnProvider()`
 * [BC BREAK] `ProviderTestCase::toStringProvider()` is now static

6.2
---

 * Deprecate `PhpStringTokenParser`
 * Deprecate `PhpExtractor` in favor of `PhpAstExtractor`
 * Add `PhpAstExtractor` (requires [nikic/php-parser](https://github.com/nikic/php-parser) to be installed)

6.1
---

 * Parameters implementing `TranslatableInterface` are processed
 * Add the file extension to the `XliffFileDumper` constructor

5.4
---

 * Add `github` format & autodetection to render errors as annotations when
   running the XLIFF linter command in a Github Actions environment.
 * Translation providers are not experimental anymore

5.3
---

 * Add `translation:pull` and `translation:push` commands to manage translations with third-party providers
 * Add `TranslatorBagInterface::getCatalogues` method
 * Add support to load XLIFF string in `XliffFileLoader`

5.2.0
-----

 * added support for calling `trans` with ICU formatted messages
 * added `PseudoLocalizationTranslator`
 * added `TranslatableMessage` objects that represent a message that can be translated
 * added the `t()` function to easily create `TranslatableMessage` objects
 * Added support for extracting messages from `TranslatableMessage` objects

5.1.0
-----

 * added support for `name` attribute on `unit` element from xliff2 to be used as a translation key instead of always the `source` element

5.0.0
-----

 * removed support for using `null` as the locale in `Translator`
 * removed `TranslatorInterface`
 * removed `MessageSelector`
 * removed `ChoiceMessageFormatterInterface`
 * removed `PluralizationRule`
 * removed `Interval`
 * removed `transChoice()` methods, use the trans() method instead with a %count% parameter
 * removed `FileDumper::setBackup()` and `TranslationWriter::disableBackup()`
 * removed `MessageFormatter::choiceFormat()`
 * added argument `$filename` to `PhpExtractor::parseTokens()`
 * removed support for implicit STDIN usage in the `lint:xliff` command, use `lint:xliff -` (append a dash) instead to make it explicit.

4.4.0
-----

 * deprecated support for using `null` as the locale in `Translator`
 * deprecated accepting STDIN implicitly when using the `lint:xliff` command, use `lint:xliff -` (append a dash) instead to make it explicit.
 * Marked the `TranslationDataCollector` class as `@final`.

4.3.0
-----

 * Improved Xliff 1.2 loader to load the original file's metadata
 * Added `TranslatorPathsPass`

4.2.0
-----

 * Started using ICU parent locales as fallback locales.
 * allow using the ICU message format using domains with the "+intl-icu" suffix
 * deprecated `Translator::transChoice()` in favor of using `Translator::trans()` with a `%count%` parameter
 * deprecated `TranslatorInterface` in favor of `Symfony\Contracts\Translation\TranslatorInterface`
 * deprecated `MessageSelector`, `Interval` and `PluralizationRules`; use `IdentityTranslator` instead
 * Added `IntlFormatter` and `IntlFormatterInterface`
 * added support for multiple files and directories in `XliffLintCommand`
 * Marked `Translator::getFallbackLocales()` and `TranslationDataCollector::getFallbackLocales()` as internal

4.1.0
-----

 * The `FileDumper::setBackup()` method is deprecated.
 * The `TranslationWriter::disableBackup()` method is deprecated.
 * The `XliffFileDumper` will write "name" on the "unit" node when dumping XLIFF 2.0.

4.0.0
-----

 * removed the backup feature of the `FileDumper` class
 * removed `TranslationWriter::writeTranslations()` method
 * removed support for passing `MessageSelector` instances to the constructor of the `Translator` class

3.4.0
-----

 * Added `TranslationDumperPass`
 * Added `TranslationExtractorPass`
 * Added `TranslatorPass`
 * Added `TranslationReader` and `TranslationReaderInterface`
 * Added `<notes>` section to the Xliff 2.0 dumper.
 * Improved Xliff 2.0 loader to load `<notes>` section.
 * Added `TranslationWriterInterface`
 * Deprecated `TranslationWriter::writeTranslations` in favor of `TranslationWriter::write`
 * added support for adding custom message formatter and decoupling the default one.
 * Added `PhpExtractor`
 * Added `PhpStringTokenParser`

3.2.0
-----

 * Added support for escaping `|` in plural translations with double pipe.

3.1.0
-----

 * Deprecated the backup feature of the file dumper classes.

3.0.0
-----

 * removed `FileDumper::format()` method.
 * Changed the visibility of the locale property in `Translator` from protected to private.

2.8.0
-----

 * deprecated FileDumper::format(), overwrite FileDumper::formatCatalogue() instead.
 * deprecated Translator::getMessages(), rely on TranslatorBagInterface::getCatalogue() instead.
 * added `FileDumper::formatCatalogue` which allows format the catalogue without dumping it into file.
 * added option `json_encoding` to JsonFileDumper
 * added options `as_tree`, `inline` to YamlFileDumper
 * added support for XLIFF 2.0.
 * added support for XLIFF target and tool attributes.
 * added message parameters to DataCollectorTranslator.
 * [DEPRECATION] The `DiffOperation` class has been deprecated and
   will be removed in Symfony 3.0, since its operation has nothing to do with 'diff',
   so the class name is misleading. The `TargetOperation` class should be used for
   this use-case instead.

2.7.0
-----

 * added DataCollectorTranslator for collecting the translated messages.

2.6.0
-----

 * added possibility to cache catalogues
 * added TranslatorBagInterface
 * added LoggingTranslator
 * added Translator::getMessages() for retrieving the message catalogue as an array

2.5.0
-----

 * added relative file path template to the file dumpers
 * added optional backup to the file dumpers
 * changed IcuResFileDumper to extend FileDumper

2.3.0
-----

 * added classes to make operations on catalogues (like making a diff or a merge on 2 catalogues)
 * added Translator::getFallbackLocales()
 * deprecated Translator::setFallbackLocale() in favor of the new Translator::setFallbackLocales() method

2.2.0
-----

 * QtTranslationsLoader class renamed to QtFileLoader. QtTranslationsLoader is deprecated and will be removed in 2.3.
 * [BC BREAK] uniformized the exception thrown by the load() method when an error occurs. The load() method now
   throws Symfony\Component\Translation\Exception\NotFoundResourceException when a resource cannot be found
   and Symfony\Component\Translation\Exception\InvalidResourceException when a resource is invalid.
 * changed the exception class thrown by some load() methods from \RuntimeException to \InvalidArgumentException
   (IcuDatFileLoader, IcuResFileLoader and QtFileLoader)

2.1.0
-----

 * added support for more than one fallback locale
 * added support for extracting translation messages from templates (Twig and PHP)
 * added dumpers for translation catalogs
 * added support for QT, gettext, and ResourceBundles
