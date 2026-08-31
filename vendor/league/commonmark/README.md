# league/commonmark

[![Latest Version](https://img.shields.io/packagist/v/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Total Downloads](https://img.shields.io/packagist/dt/league/commonmark.svg?style=flat-square)](https://packagist.org/packages/league/commonmark)
[![Software License](https://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/thephpleague/commonmark/tests.yml?branch=main&style=flat-square)](https://github.com/thephpleague/commonmark/actions?query=workflow%3ATests+branch%3Amain)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/commonmark.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/commonmark)
[![Psalm Type Coverage](https://shepherd.dev/github/thephpleague/commonmark/coverage.svg)](https://shepherd.dev/github/thephpleague/commonmark)
[![CII Best Practices](https://bestpractices.coreinfrastructure.org/projects/126/badge)](https://bestpractices.coreinfrastructure.org/projects/126)
[![Sponsor development of this project](https://img.shields.io/badge/sponsor%20this%20package-%E2%9D%A4-ff69b4.svg?style=flat-square)](https://www.colinodell.com/sponsor)

![league/commonmark](commonmark-banner.png)

**league/commonmark** is a highly-extensible PHP Markdown parser created by [Colin O'Dell][@colinodell] which supports the full [CommonMark] spec and [GitHub-Flavored Markdown].  It is based on the [CommonMark JS reference implementation][commonmark.js] by [John MacFarlane] \([@jgm]\).

## 📦 Installation & Basic Usage

This project requires PHP 7.4 or higher with the `mbstring` extension.  To install it via [Composer] simply run:

``` bash
$ composer require league/commonmark
```

The `CommonMarkConverter` class provides a simple wrapper for converting CommonMark to HTML:

```php
use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

echo $converter->convert('# Hello World!');

// <h1>Hello World!</h1>
```

Or if you want GitHub-Flavored Markdown, use the `GithubFlavoredMarkdownConverter` class instead:

```php
use League\CommonMark\GithubFlavoredMarkdownConverter;

$converter = new GithubFlavoredMarkdownConverter([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

echo $converter->convert('# Hello World!');

// <h1>Hello World!</h1>
```

Please note that only UTF-8 and ASCII encodings are supported.  If your Markdown uses a different encoding please convert it to UTF-8 before running it through this library.

> [!CAUTION]
> If you will be parsing untrusted input from users, please consider setting the `html_input` and `allow_unsafe_links` options per the example above. See <https://commonmark.thephpleague.com/security/> for more details. If you also do choose to allow raw HTML input from untrusted users, consider using a library (like [HTML Purifier](https://github.com/ezyang/htmlpurifier)) to provide additional HTML filtering.

## 📓 Documentation

Full documentation on advanced usage, configuration, and customization can be found at [commonmark.thephpleague.com][docs].

## ⏫ Upgrading

Information on how to upgrade to newer versions of this library can be found at <https://commonmark.thephpleague.com/releases>.

## 💻 GitHub-Flavored Markdown

The `GithubFlavoredMarkdownConverter` shown earlier is a drop-in replacement for the `CommonMarkConverter` which adds additional features found in the GFM spec:

 - Autolinks
 - Disallowed raw HTML
 - Strikethrough
 - Tables
 - Task Lists

See the [Extensions documentation](https://commonmark.thephpleague.com/customization/extensions/) for more details on how to include only certain GFM features if you don't want them all.

## 🗃️ Related Packages

### Integrations

- [CakePHP 3](https://github.com/gourmet/common-mark)
- [Drupal](https://www.drupal.org/project/markdown)
- [Laravel 4+](https://github.com/GrahamCampbell/Laravel-Markdown)
- [Sculpin](https://github.com/bcremer/sculpin-commonmark-bundle)
- [Symfony 2 & 3](https://github.com/webuni/commonmark-bundle)
- [Symfony 4](https://github.com/avensome/commonmark-bundle)
- [Twig Markdown extension](https://github.com/twigphp/markdown-extension)
- [Twig filter and tag](https://github.com/aptoma/twig-markdown)
- [Laravel CommonMark Blog](https://github.com/spekulatius/laravel-commonmark-blog)

### Included Extensions

See [our extension documentation](https://commonmark.thephpleague.com/extensions/overview) for a full list of extensions bundled with this library.

### Community Extensions

Custom parsers/renderers can be bundled into extensions which extend CommonMark.  Here are some that you may find interesting:

 - [Emoji extension](https://github.com/ElGigi/CommonMarkEmoji) - UTF-8 emoji extension with Github tag.
 - [Sup Sub extensions](https://github.com/OWS/commonmark-sup-sub-extensions) - Adds support of superscript and subscript (`<sup>` and `<sub>` HTML tags).
 - [YouTube iframe extension](https://github.com/zoonru/commonmark-ext-youtube-iframe) - Replaces youtube link with iframe.
 - [Lazy Image extension](https://github.com/simonvomeyser/commonmark-ext-lazy-image) - Adds various options for lazy loading of images.
 - [Marker Extension](https://github.com/noah1400/commonmark-marker-extension) - Adds support of highlighted text (`<mark>` HTML tag).
 - [Pygments Highlighter extension](https://github.com/DanielEScherzer/commonmark-ext-pygments-highlighter) - Adds support for highlighting code with the Pygments library.
 - [LatexRenderer extension](https://github.com/samwilson/commonmark-latex) - For rendering Markdown to LaTeX.

Others can be found on [Packagist under the `commonmark-extension` package type](https://packagist.org/packages/league/commonmark?type=commonmark-extension).

If you build your own, feel free to submit a PR to add it to this list!

### Others

Check out the other cool things people are doing with `league/commonmark`: <https://packagist.org/packages/league/commonmark/dependents>

## 🏷️ Versioning

[SemVer](http://semver.org/) is followed closely. Minor and patch releases should not introduce breaking changes to the codebase; however, they might change the resulting AST or HTML output of parsed Markdown (due to bug fixes, spec changes, etc.)  As a result, you might get slightly different HTML, but any custom code built onto this library should still function correctly.

Any classes or methods marked `@internal` are not intended for use outside of this library and are subject to breaking changes at any time, so please avoid using them.

## 🛠️ Maintenance & Support

When a new **minor** version (e.g. `2.0` -> `2.1`) is released, the previous one (`2.0`) will continue to receive security and critical bug fixes for *at least* 3 months.

When a new **major** version is released (e.g. `1.6` -> `2.0`), the previous one (`1.6`) will receive critical bug fixes for *at least* 3 months and security updates for 6 months after that new release comes out.

(This policy may change in the future and exceptions may be made on a case-by-case basis.)

**Professional support, including notification of new releases and security updates, is available through a [Tidelift Subscription](https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme).**

## 👷‍♀️ Contributing

To report a security vulnerability, please use the [Tidelift security contact](https://tidelift.com/security). Tidelift will coordinate the fix and disclosure with us.

If you encounter a bug in the spec, please report it to the [CommonMark] project.  Any resulting fix will eventually be implemented in this project as well.

Contributions to this library are **welcome**, especially ones that:

 * Improve usability or flexibility without compromising our ability to adhere to the [CommonMark spec]
 * Mirror fixes made to the [reference implementation][commonmark.js]
 * Optimize performance
 * Fix issues with adhering to the [CommonMark spec]

Major refactoring to core parsing logic should be avoided if possible so that we can easily follow updates made to [the reference implementation][commonmark.js]. That being said, we will absolutely consider changes which don't deviate too far from the reference spec or which are favored by other popular CommonMark implementations.

Please see [CONTRIBUTING](https://github.com/thephpleague/commonmark/blob/main/.github/CONTRIBUTING.md) for additional details.

## 🧪 Testing

``` bash
$ composer test
```

This will also test league/commonmark against the latest supported spec.

## 🚀 Performance Benchmarks

You can compare the performance of **league/commonmark** to other popular parsers by running the included benchmark tool:

``` bash
$ ./tests/benchmark/benchmark.php
```

## 👥 Credits & Acknowledgements

This code was originally based on the [CommonMark JS reference implementation][commonmark.js] which is written, maintained, and copyrighted by [John MacFarlane].  This project simply wouldn't exist without his work.

And a huge thanks to all of our amazing contributors:

<a href="https://github.com/thephpleague/commonmark/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=thephpleague/commonmark" />
</a>

### Sponsors

We'd also like to extend our sincere thanks the following sponsors who support ongoing development of this project:

 - [Tidelift](https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme) for offering support to both the maintainers and end-users through their [professional support](https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme) program
 - [Blackfire](https://www.blackfire.io/) for providing an Open-Source Profiler subscription
 - [JetBrains](https://www.jetbrains.com/) for supporting this project with complimentary [PhpStorm](https://www.jetbrains.com/phpstorm/) licenses
 - [Anthropic](https://www.anthropic.com/) for providing complimentary access to Claude Max through their [Open Source Program](https://claude.com/contact-sales/claude-for-oss)

Are you interested in sponsoring development of this project? See <https://www.colinodell.com/sponsor> for a list of ways to contribute.

## 📄 License

**league/commonmark** is licensed under the BSD-3 license.  See the [`LICENSE`](LICENSE) file for more details.

## 🏛️ Governance

This project is primarily maintained by [Colin O'Dell][@colinodell].  Members of the [PHP League] Leadership Team may occasionally assist with some of these duties.

## 🗺️  Who Uses It?

This project is used by [Drupal](https://www.drupal.org/project/markdown), [Laravel Framework](https://laravel.com/), [Cachet](https://cachethq.io/), [Firefly III](https://firefly-iii.org/), [Neos](https://www.neos.io/), [Daux.io](https://daux.io/), and [more](https://packagist.org/packages/league/commonmark/dependents)!

---

<div align="center">
	<b>
		<a href="https://tidelift.com/subscription/pkg/packagist-league-commonmark?utm_source=packagist-league-commonmark&utm_medium=referral&utm_campaign=readme">Get professional support for league/commonmark with a Tidelift subscription</a>
	</b>
	<br>
	<sub>
		Tidelift helps make open source sustainable for maintainers while giving companies<br>assurances about security, maintenance, and licensing for their dependencies.
	</sub>
</div>

[CommonMark]: http://commonmark.org/
[CommonMark spec]: http://spec.commonmark.org/
[commonmark.js]: https://github.com/jgm/commonmark.js
[GitHub-Flavored Markdown]: https://github.github.com/gfm/
[John MacFarlane]: http://johnmacfarlane.net
[docs]: https://commonmark.thephpleague.com/
[docs-examples]: https://commonmark.thephpleague.com/customization/overview/#examples
[docs-example-twitter]: https://commonmark.thephpleague.com/customization/inline-parsing#example-1---twitter-handles
[docs-example-smilies]: https://commonmark.thephpleague.com/customization/inline-parsing#example-2---emoticons
[All Contributors]: https://github.com/thephpleague/commonmark/contributors
[@colinodell]: https://www.twitter.com/colinodell
[@jgm]: https://github.com/jgm
[jgm/stmd]: https://github.com/jgm/stmd
[Composer]: https://getcomposer.org/
[PHP League]: https://thephpleague.com
