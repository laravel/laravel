# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

**Upgrading from 1.x?** See <https://commonmark.thephpleague.com/2.0/upgrading/> for additional information.

## [Unreleased][unreleased]

## [2.8.3] - 2026-07-12

### Fixed
- Fixed tab-indented fenced code blocks inside list items losing the first character of each line and having their info string mangled (#981, #1130)
- Fixed the unsafe link filter incorrectly blocking safe URLs containing `vbscript:`, `file:`, or `data:` anywhere after the start (#1131)

## [2.8.2] - 2026-03-19

This is a **security release** to address an issue where the `allowed_domains` setting for the `Embed` extension can be bypassed, resulting in a possible SSRF and XSS vulnerabilities.

### Fixed
- Fixed `DomainFilteringAdapter` hostname boundary bypass where domains like `youtube.com.evil` could match an allowlist entry for `youtube.com` (GHSA-hh8v-hgvp-g3f5)

## [2.8.1] - 2026-03-05

This is a **security release** to address an issue where `DisallowedRawHtml` can be bypassed, resulting in a possible cross-site scripting (XSS) vulnerability.

### Fixed
- Fixed `DisallowedRawHtmlRenderer` not blocking raw HTML tags with trailing ASCII whitespace (GHSA-4v6x-c7xx-hw9f)
- Fixed PHP 8.5 deprecation (#1107)

## [2.8.0] - 2025-11-26

### Added
- Added a new `HighlightExtension` for marking important text using `==` syntax (#1100)

### Fixed
- Fixed `AutolinkExtension` incorrectly matching URLs after invalid `www.` prefix (#1095, #1103)

## [2.7.1] - 2025-07-20

### Changed
- Optimized several regular expressions in `RegexHelper` to improve performance (#674, #1086)

### Fixed
- `EmbedProcessor` no longer calls `updateEmbeds()` when there are no embeds to update (#1081)
- Fixed missing `benchmark.php` CSV path validation for non-existent files (#1068, #1085)

## [2.7.0] - 2025-05-05

This is a **security release** to address a potential cross-site scripting (XSS) vulnerability when using the `AttributesExtension` with untrusted user input.

### Added
- Added `attributes/allow` config option to specify which attributes users are allowed to set on elements (default allows virtually all attributes)

### Changed
- The `AttributesExtension` blocks all attributes starting with `on` unless explicitly allowed via the `attributes/allow` config option
- The `allow_unsafe_links` option is now respected by the `AttributesExtension` when users specify `href` and `src` attributes

## [2.6.2] - 2025-04-18

### Fixed

- Fixed Attributes extension parsing regression (#1071)

## [2.6.1] - 2024-12-29

### Fixed

- Rendered list items should only add newlines around block-level children (#1059, #1061)

## [2.6.0] - 2024-12-07

This is a **security release** to address potential denial of service attacks when parsing specially crafted,
malicious input from untrusted sources (like user input).

### Added

- Added `max_delimiters_per_line` config option to prevent denial of service attacks when parsing malicious input
- Added `table/max_autocompleted_cells` config option to prevent denial of service attacks when parsing large tables
- The `AttributesExtension` now supports attributes without values (#985, #986)
- The `AutolinkExtension` exposes two new configuration options to override the default behavior (#969, #987):
    - `autolink/allowed_protocols` - an array of protocols to allow autolinking for
    - `autolink/default_protocol` - the default protocol to use when none is specified
- Added `RegexHelper::isWhitespace()` method to check if a given character is an ASCII whitespace character
- Added `CacheableDelimiterProcessorInterface` to ensure linear complexity for dynamic delimiter processing
- Added `Bracket` delimiter type to optimize bracket parsing

### Changed

- `[` and `]` are no longer added as `Delimiter` objects on the stack; a new `Bracket` type with its own stack is used instead
- `UrlAutolinkParser` no longer parses URLs with more than 127 subdomains
- Expanded reference links can no longer exceed 100kb, or the size of the input document (whichever is greater)
- Delimiters should always provide a non-null value via `DelimiterInterface::getIndex()`
  - We'll attempt to infer the index based on surrounding delimiters where possible
- The `DelimiterStack` now accepts integer positions for any `$stackBottom` argument
- Several small performance optimizations

## [2.5.3] - 2024-08-16

### Changed

- Made compatible with CommonMark spec 0.31.1, including:
  - Remove `source`, add `search` to list of recognized block tags

## [2.5.2] - 2024-08-14

### Changed

- Boolean attributes now require an explicit `true` value (#1040)

### Fixed

- Fixed regression where text could be misinterpreted as an attribute (#1040)

## [2.5.1] - 2024-07-24

### Fixed

- Fixed attribute parsing incorrectly parsing mustache-like syntax (#1035)
- Fixed incorrect `Table` start line numbers (#1037)

## [2.5.0] - 2024-07-22

### Added

- The `AttributesExtension` now supports attributes without values (#985, #986)
- The `AutolinkExtension` exposes two new configuration options to override the default behavior (#969, #987):
    - `autolink/allowed_protocols` - an array of protocols to allow autolinking for
    - `autolink/default_protocol` - the default protocol to use when none is specified

### Changed

- Made compatible with CommonMark spec 0.31.0, including:
    - Allow closing fence to be followed by tabs
    - Remove restrictive limitation on inline comments
    - Unicode symbols now treated like punctuation (for purposes of flankingness)
    - Trailing tabs on the last line of indented code blocks will be excluded
    - Improved HTML comment matching
- `Paragraph`s only containing link reference definitions will be kept in the AST until the `Document` is finalized
    - (These were previously removed immediately after parsing the `Paragraph`)

### Fixed

- Fixed list tightness not being determined properly in some edge cases
- Fixed incorrect ending line numbers for several block types in various scenarios
- Fixed lowercase inline HTML declarations not being accepted

## [2.4.4] - 2024-07-22

### Fixed

- Fixed SmartPunct extension changing already-formatted quotation marks (#1030)

## [2.4.3] - 2024-07-22

### Fixed

- Fixed the Attributes extension not supporting CSS level 3 selectors (#1013)
- Fixed `UrlAutolinkParser` incorrectly parsing text containing `www` anywhere before an autolink (#1025)


## [2.4.2] - 2024-02-02

### Fixed

- Fixed declaration parser being too strict
- `FencedCodeRenderer`: don't add `language-` to class if already prefixed

### Deprecated

- Returning dynamic values from `DelimiterProcessorInterface::getDelimiterUse()` is deprecated
    - You should instead implement `CacheableDelimiterProcessorInterface` to help the engine perform caching to avoid performance issues.
- Failing to set a delimiter's index (or returning `null` from `DelimiterInterface::getIndex()`) is deprecated and will not be supported in 3.0
- Deprecated `DelimiterInterface::isActive()` and `DelimiterInterface::setActive()`, as these are no longer used by the engine
- Deprecated `DelimiterStack::removeEarlierMatches()` and `DelimiterStack::searchByCharacter()`, as these are no longer used by the engine
- Passing a `DelimiterInterface` as the `$stackBottom` argument to `DelimiterStack::processDelimiters()` or `::removeAll()` is deprecated and will not be supported in 3.0; pass the integer position instead.

### Fixed

- Fixed NUL characters not being replaced in the input
- Fixed quadratic complexity parsing unclosed inline links
- Fixed quadratic complexity parsing emphasis and strikethrough delimiters
- Fixed issue where having 500,000+ delimiters could trigger a [known segmentation fault issue in PHP's garbage collection](https://bugs.php.net/bug.php?id=68606)
- Fixed quadratic complexity deactivating link openers
- Fixed quadratic complexity parsing long backtick code spans with no matching closers
- Fixed catastrophic backtracking when parsing link labels/titles

## [2.4.1] - 2023-08-30

### Fixed

- Fixed `ExternalLinkProcessor` not fully disabling the `rel` attribute when configured to do so (#992)

## [2.4.0] - 2023-03-24

### Added

- Added generic `CommonMarkException` marker interface for all exceptions thrown by the library
- Added several new specific exception types implementing that marker interface:
    - `AlreadyInitializedException`
    - `InvalidArgumentException`
    - `IOException`
    - `LogicException`
    - `MissingDependencyException`
    - `NoMatchingRendererException`
    - `ParserLogicException`
- Added more configuration options to the Heading Permalinks extension (#939):
    - `heading_permalink/apply_id_to_heading` - When `true`, the `id` attribute will be applied to the heading element itself instead of the `<a>` tag
    - `heading_permalink/heading_class` - class to apply to the heading element
    - `heading_permalink/insert` - now accepts `none` to prevent the creation of the `<a>` link
- Added new `table/alignment_attributes` configuration option to control how table cell alignment is rendered (#959)

### Changed

- Change several thrown exceptions from `RuntimeException` to `LogicException` (or something extending it), including:
    - `CallbackGenerator`s that fail to set a URL or return an expected value
    - `MarkdownParser` when deactivating the last block parser or attempting to get an active block parser when they've all been closed
    - Adding items to an already-initialized `Environment`
    - Rendering a `Node` when no renderer has been registered for it
- `HeadingPermalinkProcessor` now throws `InvalidConfigurationException` instead of `RuntimeException` when invalid config values are given.
- `HtmlElement::setAttribute()` no longer requires the second parameter for boolean attributes
- Several small micro-optimizations
- Changed Strikethrough to only allow 1 or 2 tildes per the updated GFM spec

### Fixed

- Fixed inaccurate `@throws` docblocks throughout the codebase, including `ConverterInterface`, `MarkdownConverter`, and `MarkdownConverterInterface`.
    - These previously suggested that only `\RuntimeException`s were thrown, which was inaccurate as `\LogicException`s were also possible.

## [2.3.9] - 2023-02-15

### Fixed

- Fixed autolink extension not detecting some URIs with underscores (#956)

## [2.3.8] - 2022-12-10

### Fixed

- Fixed parsing issues when `mb_internal_encoding()` is set to something other than `UTF-8` (#951)

## [2.3.7] - 2022-11-03

### Fixed

- Fixed `TaskListItemMarkerRenderer` not including HTML attributes set on the node by other extensions (#947)

## [2.3.6] - 2022-10-30

### Fixed

- Fixed unquoted attribute parsing when closing curly brace is followed by certain characters (like a `.`) (#943)

## [2.3.5] - 2022-07-29

### Fixed

- Fixed error using `InlineParserEngine` when no inline parsers are registered in the `Environment` (#908)

## [2.3.4] - 2022-07-17

### Changed

- Made a number of small tweaks to the embed extension's parsing behavior to fix #898:
    - Changed `EmbedStartParser` to always capture embed-like lines in container blocks, regardless of parent block type
    - Changed `EmbedProcessor` to also remove `Embed` blocks that aren't direct children of the `Document`
    - Increased the priority of `EmbedProcessor` to `1010`

### Fixed

- Fixed `EmbedExtension` not parsing embeds following a list block (#898)

## [2.3.3] - 2022-06-07

### Fixed

- Fixed `DomainFilteringAdapter` not reindexing the embed list (#884, #885)

## [2.3.2] - 2022-06-03

### Fixed

- Fixed FootnoteExtension stripping extra characters from tab-indented footnotes (#881)

## [2.2.5] - 2022-06-03

### Fixed

- Fixed FootnoteExtension stripping extra characters from tab-indented footnotes (#881)

## [2.3.1] - 2022-05-14

### Fixed

- Fixed AutolinkExtension not ignoring trailing strikethrough syntax (#867)

## [2.2.4] - 2022-05-14

### Fixed

- Fixed AutolinkExtension not ignoring trailing strikethrough syntax (#867)

## [2.3.0] - 2022-04-07

### Added

- Added new `EmbedExtension` (#805)
- Added `DocumentRendererInterface` as a replacement for the now-deprecated `MarkdownRendererInterface`

### Deprecated

- Deprecated `MarkdownRendererInterface`; use `DocumentRendererInterface` instead

## [2.2.3] - 2022-02-26

### Fixed

- Fixed front matter parsing with Windows line endings (#821)

## [2.1.3] - 2022-02-26

### Fixed

- Fixed front matter parsing with Windows line endings (#821)

## [2.0.4] - 2022-02-26

### Fixed

- Fixed front matter parsing with Windows line endings (#821)

## [2.2.2] - 2022-02-13

### Fixed

- Fixed double-escaping of image alt text (#806, #810)
- Fixed Psalm typehints for event class names

## [2.2.1] - 2022-01-25

### Fixed

 - Fixed `symfony/deprecation-contracts` constraint

### Removed

 - Removed deprecation trigger from `MarkdownConverterInterface` to reduce noise

## [2.2.0] - 2022-01-22

### Added

 - Added new `ConverterInterface`
 - Added new `MarkdownToXmlConverter` class
 - Added new `HtmlDecorator` class which can wrap existing renderers with additional HTML tags
 - Added new `table/wrap` config to apply an optional wrapping/container element around a table (#780)

### Changed

 - `HtmlElement` contents can now consist of any `Stringable`, not just `HtmlElement` and `string`

### Deprecated

 - Deprecated `MarkdownConverterInterface` and its `convertToHtml()` method; use `ConverterInterface` and `convert()` instead

## [2.1.2] - 2022-02-13

### Fixed

- Fixed double-escaping of image alt text (#806, #810)
- Fixed Psalm typehints for event class names

## [2.1.1] - 2022-01-02

### Added

 - Added missing return type to `Environment::dispatch()` to fix deprecation warning (#778)

## [2.1.0] - 2021-12-05

### Added

- Added support for ext-yaml in FrontMatterExtension (#715)
- Added support for symfony/yaml v6.0 in FrontMatterExtension (#739)
- Added new `heading_permalink/aria_hidden` config option (#741)

### Fixed

 - Fixed PHP 8.1 deprecation warning (#759, #762)

## [2.0.3] - 2022-02-13

### Fixed

- Fixed double-escaping of image alt text (#806, #810)
- Fixed Psalm typehints for event class names

## [2.0.2] - 2021-08-14

### Changed

- Bumped minimum version of league/config to support PHP 8.1

### Fixed

- Fixed ability to register block parsers that identify lines starting with letters (#706)

## [2.0.1] - 2021-07-31

### Fixed

- Fixed nested autolinks (#689)
- Fixed description lists being parsed incorrectly (#692)
- Fixed Table of Contents not respecting Heading Permalink prefixes (#690)

## [2.0.0] - 2021-07-24

No changes were introduced since the previous RC2 release.
See all entries below for a list of changes between 1.x and 2.0.

## [2.0.0-rc2] - 2021-07-17

### Fixed

- Fixed Mentions inside of links creating nested links against the spec's rules (#688)

## [2.0.0-rc1] - 2021-07-10

No changes were introduced since the previous release.

## [2.0.0-beta3] - 2021-07-03

### Changed

 - Any leading UTF-8 BOM will be stripped from the input
 - The `getEnvironment()` method of `CommonMarkConverter` and `GithubFlavoredMarkdownConverter` will always return the concrete, configurable `Environment` for upgrading convenience
 - Optimized AST iteration
 - Lots of small micro-optimizations

## [2.0.0-beta2] - 2021-06-27

### Added

- Added new `Node::iterator()` method and `NodeIterator` class for faster AST iteration (#683, #684)

### Changed

- Made compatible with CommonMark spec 0.30.0
- Optimized link label parsing
- Optimized AST iteration for a 50% performance boost in some event listeners (#683, #684)

### Fixed

- Fixed processing instructions with EOLs
- Fixed case-insensitive matching for HTML tag types
- Fixed type 7 HTML blocks incorrectly interrupting lazy paragraphs
- Fixed newlines in reference labels not collapsing into spaces
- Fixed link label normalization with escaped newlines
- Fixed unnecessary AST iteration when no default attributes are configured

## [2.0.0-beta1] - 2021-06-20

### Added

 - **Added three new extensions:**
   - `FrontMatterExtension` ([see documentation](https://commonmark.thephpleague.com/extensions/front-matter/))
   - `DescriptionListExtension` ([see documentation](https://commonmark.thephpleague.com/extensions/description-lists/))
   - `DefaultAttributesExtension` ([see documentation](https://commonmark.thephpleague.com/extensions/default-attributes/))
 - **Added new `XmlRenderer` to simplify AST debugging** ([see documentation](https://commonmark.thephpleague.com/xml/)) (#431)
 - **Added the ability to configure disallowed raw HTML tags** (#507)
 - **Added the ability for Mentions to use multiple characters for their symbol** (#514, #550)
 - **Added the ability to delegate event dispatching to PSR-14 compliant event dispatcher libraries**
 - **Added new configuration options:**
   - Added `heading_permalink/min_heading_level` and `heading_permalink/max_heading_level` options to control which headings get permalinks (#519)
   - Added `heading_permalink/fragment_prefix` to allow customizing the URL fragment prefix (#602)
   - Added `footnote/backref_symbol` option for customizing backreference link appearance (#522)
   - Added `slug_normalizer/max_length` option to control the maximum length of generated URL slugs
   - Added `slug_normalizer/unique` option to control whether unique slugs should be generated per-document or per-environment
 - **Added purity markers throughout the codebase** (verified with Psalm)
 - Added `Query` class to simplify Node traversal when looking to take action on certain Nodes
 - Added new `HtmlFilter` and `StringContainerHelper` utility classes
 - Added new `AbstractBlockContinueParser` class to simplify the creation of custom block parsers
 - Added several new classes and interfaces:
   - `BlockContinue`
   - `BlockContinueParserInterface`
   - `BlockContinueParserWithInlinesInterface`
   - `BlockStart`
   - `BlockStartParserInterface`
   - `ChildNodeRendererInterface`
   - `ConfigurableExtensionInterface`
   - `CursorState`
   - `DashParser` (extracted from `PunctuationParser`)
   - `DelimiterParser`
   - `DocumentBlockParser`
   - `DocumentPreRenderEvent`
   - `DocumentRenderedEvent`
   - `EllipsesParser` (extracted from `PunctuationParser`)
   - `ExpressionInterface`
   - `FallbackNodeXmlRenderer`
   - `InlineParserEngineInterface`
   - `InlineParserMatch`
   - `MarkdownParserState`
   - `MarkdownParserStateInterface`
   - `MarkdownRendererInterface`
   - `Query`
   - `RawMarkupContainerInterface`
   - `ReferenceableInterface`
   - `RenderedContent`
   - `RenderedContentInterface`
   - `ReplaceUnpairedQuotesListener`
   - `SpecReader`
   - `TableOfContentsRenderer`
   - `UniqueSlugNormalizer`
   - `UniqueSlugNormalizerInterface`
   - `XmlRenderer`
   - `XmlNodeRendererInterface`
 - Added several new methods:
   - `Cursor::getCurrentCharacter()`
   - `Environment::createDefaultConfiguration()`
   - `Environment::setEventDispatcher()`
   - `EnvironmentInterface::getExtensions()`
   - `EnvironmentInterface::getInlineParsers()`
   - `EnvironmentInterface::getSlugNormalizer()`
   - `FencedCode::setInfo()`
   - `Heading::setLevel()`
   - `HtmlRenderer::renderDocument()`
   - `InlineParserContext::getFullMatch()`
   - `InlineParserContext::getFullMatchLength()`
   - `InlineParserContext::getMatches()`
   - `InlineParserContext::getSubMatches()`
   - `LinkParserHelper::parsePartialLinkLabel()`
   - `LinkParserHelper::parsePartialLinkTitle()`
   - `Node::assertInstanceOf()`
   - `RegexHelper::isLetter()`
   - `StringContainerInterface::setLiteral()`
   - `TableCell::getType()`
   - `TableCell::setType()`
   - `TableCell::getAlign()`
   - `TableCell::setAlign()`

### Changed

 - **Changed the converter return type**
   - `CommonMarkConverter::convertToHtml()` now returns an instance of `RenderedContentInterface`. This can be cast to a string for backward compatibility with 1.x.
 - **Table of Contents items are no longer wrapped with `<p>` tags** (#613)
 - **Heading Permalinks now link to element IDs instead of using `name` attributes** (#602)
 - **Heading Permalink IDs and URL fragments now have a `content` prefix by default** (#602)
 - **Changes to configuration options:**
     - `enable_em` has been renamed to `commonmark/enable_em`
     - `enable_strong` has been renamed to `commonmark/enable_strong`
     - `use_asterisk` has been renamed to `commonmark/use_asterisk`
     - `use_underscore` has been renamed to `commonmark/use_underscore`
     - `unordered_list_markers` has been renamed to `commonmark/unordered_list_markers`
     - `mentions/*/symbol` has been renamed to `mentions/*/prefix`
     - `mentions/*/regex` has been renamed to `mentions/*/pattern` and requires partial regular expressions (without delimiters or flags)
     - `max_nesting_level` now defaults to `PHP_INT_MAX` and no longer supports floats
     - `heading_permalink/slug_normalizer` has been renamed to `slug_normalizer/instance`
 - **Event dispatching is now fully PSR-14 compliant**
 - **Moved and renamed several classes** - [see the full list here](https://commonmark.thephpleague.com/2.0/upgrading/#classesnamespaces-renamed)
 - The `HeadingPermalinkExtension` and `FootnoteExtension` were modified to ensure they never produce a slug which conflicts with slugs created by the other extension
 - `SlugNormalizer::normalizer()` now supports optional prefixes and max length options passed in via the `$context` argument
 - The `AbstractBlock::$data` and `AbstractInline::$data` arrays were replaced with a `Data` array-like object on the base `Node` class
 - **Implemented a new approach to block parsing.** This was a massive change, so here are the highlights:
   - Functionality previously found in block parsers and node elements has moved to block parser factories and block parsers, respectively ([more details](https://commonmark.thephpleague.com/2.0/upgrading/#new-block-parsing-approach))
   - `ConfigurableEnvironmentInterface::addBlockParser()` is now `EnvironmentBuilderInterface::addBlockParserFactory()`
   - `ReferenceParser` was re-implemented and works completely different than before
   - The paragraph parser no longer needs to be added manually to the environment
 - **Implemented a new approach to inline parsing** where parsers can now specify longer strings or regular expressions they want to parse (instead of just single characters):
   - `InlineParserInterface::getCharacters()` is now `getMatchDefinition()` and returns an instance of `InlineParserMatch`
   - `InlineParserContext::__construct()` now requires the contents to be provided as a `Cursor` instead of a `string`
 - **Implemented delimiter parsing as a special type of inline parser** (via the new `DelimiterParser` class)
 - **Changed block and inline rendering to use common methods and interfaces**
   - `BlockRendererInterface` and `InlineRendererInterface` were replaced by `NodeRendererInterface` with slightly different parameters. All core renderers now implement this interface.
   - `ConfigurableEnvironmentInterface::addBlockRenderer()` and `addInlineRenderer()` were combined into `EnvironmentBuilderInterface::addRenderer()`
   - `EnvironmentInterface::getBlockRenderersForClass()` and `getInlineRenderersForClass()` are now just `getRenderersForClass()`
 - **Completely refactored the Configuration implementation**
   - All configuration-specific classes have been moved into a new `league/config` package with a new namespace
   - `Configuration` objects must now be configured with a schema and all options must match that schema - arbitrary keys are no longer permitted
   - `Configuration::__construct()` no longer accepts the default configuration values - use `Configuration::merge()` instead
   - `ConfigurationInterface` now only contains a `get(string $key)`; this method no longer allows arbitrary default values to be returned if the option is missing
   - `ConfigurableEnvironmentInterface` was renamed to `EnvironmentBuilderInterface`
   - `ExtensionInterface::register()` now requires an `EnvironmentBuilderInterface` param instead of `ConfigurableEnvironmentInterface`
 - **Added missing return types to virtually every class and interface method**
 - Re-implemented the GFM Autolink extension using the new inline parser approach instead of document processors
   - `EmailAutolinkProcessor` is now `EmailAutolinkParser`
   - `UrlAutolinkProcessor` is now `UrlAutolinkParser`
 - `HtmlElement` can now properly handle array (i.e. `class`) and boolean (i.e. `checked`) attribute values
 - `HtmlElement` automatically flattens any attributes with array values into space-separated strings, removing duplicate entries
 - Combined separate classes/interfaces into one:
   - `DisallowedRawHtmlRenderer` replaces `DisallowedRawHtmlBlockRenderer` and `DisallowedRawHtmlInlineRenderer`
   - `NodeRendererInterface` replaces `BlockRendererInterface` and `InlineRendererInterface`
 - Renamed the following methods:
   - `Environment` and `ConfigurableEnvironmentInterface`:
     - `addBlockParser()` is now `addBlockStartParser()`
   - `ReferenceMap` and `ReferenceMapInterface`:
     - `addReference()` is now `add()`
     - `getReference()` is now `get()`
     - `listReferences()` is now `getIterator()`
   - Various node (block/inline) classes:
     - `getContent()` is now `getLiteral()`
     - `setContent()` is now `setLiteral()`
 - Moved and renamed the following constants:
   - `EnvironmentInterface::HTML_INPUT_ALLOW` is now `HtmlFilter::ALLOW`
   - `EnvironmentInterface::HTML_INPUT_ESCAPE` is now `HtmlFilter::ESCAPE`
   - `EnvironmentInterface::HTML_INPUT_STRIP` is now `HtmlFilter::STRIP`
   - `TableCell::TYPE_HEAD` is now `TableCell::TYPE_HEADER`
   - `TableCell::TYPE_BODY` is now `TableCell::TYPE_DATA`
 - Changed the visibility of the following properties:
   - `AttributesInline::$attributes` is now `private`
   - `AttributesInline::$block` is now `private`
   - `TableCell::$align` is now `private`
   - `TableCell::$type` is now `private`
   - `TableSection::$type` is now `private`
 - Several methods which previously returned `$this` now return `void`
   - `Delimiter::setPrevious()`
   - `Node::replaceChildren()`
   - `Context::setTip()`
   - `Context::setContainer()`
   - `Context::setBlocksParsed()`
   - `AbstractStringContainer::setContent()`
   - `AbstractWebResource::setUrl()`
 - Several classes are now marked `final`:
   - `ArrayCollection`
   - `Emphasis`
   - `FencedCode`
   - `Heading`
   - `HtmlBlock`
   - `HtmlElement`
   - `HtmlInline`
   - `IndentedCode`
   - `Newline`
   - `Strikethrough`
   - `Strong`
   - `Text`
 - `Heading` nodes no longer directly contain a copy of their inner text
 - `StringContainerInterface` can now be used for inlines, not just blocks
 - `ArrayCollection` only supports integer keys
 - `HtmlElement` now implements `Stringable`
 - `Cursor::saveState()` and `Cursor::restoreState()` now use `CursorState` objects instead of arrays
 - `NodeWalker::next()` now enters, traverses any children, and leaves all elements which may have children (basically all blocks plus any inlines with children). Previously, it only did this for elements explicitly marked as "containers".
 - `InvalidOptionException` was removed
 - Anything with a `getReference(): ReferenceInterface` method now implements `ReferencableInterface`
 - The `SmartPunct` extension now replaces all unpaired `Quote` elements with `Text` elements towards the end of parsing, making the `QuoteRenderer` unnecessary
 - Several changes made to the Footnote extension:
   - Footnote identifiers can no longer contain spaces
   - Anonymous footnotes can now span subsequent lines
   - Footnotes can now contain multiple lines of content, including sub-blocks, by indenting them
   - Footnote event listeners now have numbered priorities (but still execute in the same order)
   - Footnotes must now be separated from previous content by a blank line
 - The line numbers (keys) returned via `MarkdownInput::getLines()` now start at 1 instead of 0
 - `DelimiterProcessorCollectionInterface` now extends `Countable`
 - `RegexHelper::PARTIAL_` constants must always be used in case-insensitive contexts
 - `HeadingPermalinkProcessor` no longer accepts text normalizers via the constructor - these must be provided via configuration instead
 - Blocks which can't contain inlines will no longer be asked to render inlines
 - `AnonymousFootnoteRefParser` and `HeadingPermalinkProcessor` now implement `EnvironmentAwareInterface` instead of `ConfigurationAwareInterface`
 - The second argument to `TextNormalizerInterface::normalize()` must now be an array
 - The `title` attribute for `Link` and `Image` nodes is now stored using a dedicated property instead of stashing it in `$data`
 - `ListData::$delimiter` now returns either `ListBlock::DELIM_PERIOD` or `ListBlock::DELIM_PAREN` instead of the literal delimiter

### Fixed

 - **Fixed parsing of footnotes without content**
 - **Fixed rendering of orphaned footnotes and footnote refs**
 - **Fixed some URL autolinks breaking too early** (#492)
 - Fixed `AbstractStringContainer` not actually being `abstract`

### Removed

 - **Removed support for PHP 7.1, 7.2, and 7.3** (#625, #671)
 - **Removed all previously-deprecated functionality:**
   - Removed the ability to pass custom `Environment` instances into the `CommonMarkConverter` and `GithubFlavoredMarkdownConverter` constructors
   - Removed the `Converter` class and `ConverterInterface`
   - Removed the `bin/commonmark` script
   - Removed the `Html5Entities` utility class
   - Removed the `InlineMentionParser` (use `MentionParser` instead)
   - Removed `DefaultSlugGenerator` and `SlugGeneratorInterface` from the `Extension/HeadingPermalink/Slug` sub-namespace (use the new ones under `./SlugGenerator` instead)
   - Removed the following `ArrayCollection` methods:
     - `add()`
     - `set()`
     - `get()`
     - `remove()`
     - `isEmpty()`
     - `contains()`
     - `indexOf()`
     - `containsKey()`
     - `replaceWith()`
     - `removeGaps()`
   - Removed the `ConfigurableEnvironmentInterface::setConfig()` method
   - Removed the `ListBlock::TYPE_UNORDERED` constant
   - Removed the `CommonMarkConverter::VERSION` constant
   - Removed the `HeadingPermalinkRenderer::DEFAULT_INNER_CONTENTS` constant
   - Removed the `heading_permalink/inner_contents` configuration option
 - **Removed now-unused classes:**
   - `AbstractStringContainerBlock`
   - `BlockRendererInterface`
   - `Context`
   - `ContextInterface`
   - `Converter`
   - `ConverterInterface`
   - `InlineRendererInterface`
   - `PunctuationParser` (was split into two classes: `DashParser` and `EllipsesParser`)
   - `QuoteRenderer`
   - `UnmatchedBlockCloser`
 - Removed the following methods, properties, and constants:
   - `AbstractBlock::$open`
   - `AbstractBlock::$lastLineBlank`
   - `AbstractBlock::isContainer()`
   - `AbstractBlock::canContain()`
   - `AbstractBlock::isCode()`
   - `AbstractBlock::matchesNextLine()`
   - `AbstractBlock::endsWithBlankLine()`
   - `AbstractBlock::setLastLineBlank()`
   - `AbstractBlock::shouldLastLineBeBlank()`
   - `AbstractBlock::isOpen()`
   - `AbstractBlock::finalize()`
   - `AbstractBlock::getData()`
   - `AbstractInline::getData()`
   - `ConfigurableEnvironmentInterface::addBlockParser()`
   - `ConfigurableEnvironmentInterface::mergeConfig()`
   - `Delimiter::setCanClose()`
   - `EnvironmentInterface::getConfig()`
   - `EnvironmentInterface::getInlineParsersForCharacter()`
   - `EnvironmentInterface::getInlineParserCharacterRegex()`
   - `HtmlRenderer::renderBlock()`
   - `HtmlRenderer::renderBlocks()`
   - `HtmlRenderer::renderInline()`
   - `HtmlRenderer::renderInlines()`
   - `Node::isContainer()`
   - `RegexHelper::matchAll()` (use the new `matchFirst()` method instead)
   - `RegexHelper::REGEX_WHITESPACE`
 - Removed the second `$contents` argument from the `Heading` constructor

### Deprecated

**The following things have been deprecated and will not be supported in v3.0:**

 - `Environment::mergeConfig()` (set configuration before instantiation instead)
 - `Environment::createCommonMarkEnvironment()` and `Environment::createGFMEnvironment()`
    - Alternative 1: Use `CommonMarkConverter` or `GithubFlavoredMarkdownConverter` if you don't need to customize the environment
    - Alternative 2: Instantiate a new `Environment` and add the necessary extensions yourself

[unreleased]: https://github.com/thephpleague/commonmark/compare/2.8.3...HEAD
[2.8.3]: https://github.com/thephpleague/commonmark/compare/2.8.2...2.8.3
[2.8.2]: https://github.com/thephpleague/commonmark/compare/2.8.1...2.8.2
[2.8.1]: https://github.com/thephpleague/commonmark/compare/2.8.0...2.8.1
[2.8.0]: https://github.com/thephpleague/commonmark/compare/2.7.1...2.8.0
[2.7.1]: https://github.com/thephpleague/commonmark/compare/2.7.0...2.7.1
[2.7.0]: https://github.com/thephpleague/commonmark/compare/2.6.2...2.7.0
[2.6.2]: https://github.com/thephpleague/commonmark/compare/2.6.1...2.6.2
[2.6.1]: https://github.com/thephpleague/commonmark/compare/2.6.0...2.6.1
[2.6.0]: https://github.com/thephpleague/commonmark/compare/2.5.3...2.6.0
[2.5.3]: https://github.com/thephpleague/commonmark/compare/2.5.2...2.5.3
[2.5.2]: https://github.com/thephpleague/commonmark/compare/2.5.1...2.5.2
[2.5.1]: https://github.com/thephpleague/commonmark/compare/2.5.0...2.5.1
[2.5.0]: https://github.com/thephpleague/commonmark/compare/2.4.4...2.5.0
[2.4.4]: https://github.com/thephpleague/commonmark/compare/2.4.3...2.4.4
[2.4.3]: https://github.com/thephpleague/commonmark/compare/2.4.2...2.4.3
[2.4.2]: https://github.com/thephpleague/commonmark/compare/2.4.1...2.4.2
[2.4.1]: https://github.com/thephpleague/commonmark/compare/2.4.0...2.4.1
[2.4.0]: https://github.com/thephpleague/commonmark/compare/2.3.9...2.4.0
[2.3.9]: https://github.com/thephpleague/commonmark/compare/2.3.8...2.3.9
[2.3.8]: https://github.com/thephpleague/commonmark/compare/2.3.7...2.3.8
[2.3.7]: https://github.com/thephpleague/commonmark/compare/2.3.6...2.3.7
[2.3.6]: https://github.com/thephpleague/commonmark/compare/2.3.5...2.3.6
[2.3.5]: https://github.com/thephpleague/commonmark/compare/2.3.4...2.3.5
[2.3.4]: https://github.com/thephpleague/commonmark/compare/2.3.3...2.3.4
[2.3.3]: https://github.com/thephpleague/commonmark/compare/2.3.2...2.3.3
[2.3.2]: https://github.com/thephpleague/commonmark/compare/2.3.2...main
[2.3.1]: https://github.com/thephpleague/commonmark/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/thephpleague/commonmark/compare/2.2.3...2.3.0
[2.2.5]: https://github.com/thephpleague/commonmark/compare/2.2.4...2.2.5
[2.2.4]: https://github.com/thephpleague/commonmark/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/thephpleague/commonmark/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/thephpleague/commonmark/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/thephpleague/commonmark/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/thephpleague/commonmark/compare/2.1.1...2.2.0
[2.1.3]: https://github.com/thephpleague/commonmark/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/thephpleague/commonmark/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/thephpleague/commonmark/compare/2.0.2...2.1.1
[2.1.0]: https://github.com/thephpleague/commonmark/compare/2.0.2...2.1.0
[2.0.4]: https://github.com/thephpleague/commonmark/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/thephpleague/commonmark/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/thephpleague/commonmark/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/thephpleague/commonmark/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/thephpleague/commonmark/compare/2.0.0-rc2...2.0.0
[2.0.0-rc2]: https://github.com/thephpleague/commonmark/compare/2.0.0-rc1...2.0.0-rc2
[2.0.0-rc1]: https://github.com/thephpleague/commonmark/compare/2.0.0-beta3...2.0.0-rc1
[2.0.0-beta3]: https://github.com/thephpleague/commonmark/compare/2.0.0-beta2...2.0.0-beta3
[2.0.0-beta2]: https://github.com/thephpleague/commonmark/compare/2.0.0-beta1...2.0.0-beta2
[2.0.0-beta1]: https://github.com/thephpleague/commonmark/compare/1.6...2.0.0-beta1
