CssSelector Component
=====================

CssSelector converts CSS selectors to XPath expressions.

The component only goal is to convert CSS selectors to their XPath
equivalents:

    use Symfony\Component\CssSelector\CssSelector;

    print CssSelector::toXPath('div.item > h4 > a');

HTML and XML are different
--------------------------

The `CssSelector` component comes with an `HTML` extension which is enabled by
default. If you need to use this component with `XML` documents, you have to
disable this `HTML` extension. That's because, `HTML` tag & attribute names
are always lower-cased, but case-sensitive in `XML`:

    // disable `HTML` extension:
    CssSelector::disableHtmlExtension();

    // re-enable `HTML` extension:
    CssSelector::enableHtmlExtension();

When the `HTML` extension is enabled, tag names are lower-cased, attribute
names are lower-cased, the following extra pseudo-classes are supported:
`checked`, `link`, `disabled`, `enabled`, `selected`, `invalid`, `hover`,
`visited`, and the `lang()` function is also added.

Resources
---------

This component is a port of the Python lxml library, which is copyright Infrae
and distributed under the BSD license.

Current code is a port of https://github.com/SimonSapin/cssselect/releases/tag/v0.7.1

You can run the unit tests with the following command:

    $ cd path/to/Symfony/Component/CssSelector/
    $ composer.phar install
    $ phpunit
