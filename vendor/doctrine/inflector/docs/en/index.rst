Introduction
============

The Doctrine Inflector has methods for inflecting text. The features include pluralization,
singularization, converting between camelCase and under_score and capitalizing
words.

Installation
============

You can install the Inflector with composer:

.. code-block:: console

    $ composer require doctrine/inflector

Usage
=====

Using the inflector is easy, you can create a new ``Doctrine\Inflector\Inflector`` instance by using
the ``Doctrine\Inflector\InflectorFactory`` class:

.. code-block:: php

    use Doctrine\Inflector\InflectorFactory;

    $inflector = InflectorFactory::create()->build();

By default it will create an English inflector. If you want to use another language, just pass the language
you want to create an inflector for to the ``createForLanguage()`` method:

.. code-block:: php

    use Doctrine\Inflector\InflectorFactory;
    use Doctrine\Inflector\Language;

    $inflector = InflectorFactory::createForLanguage(Language::SPANISH)->build();

The supported languages are as follows:

- ``Language::ENGLISH``
- ``Language::ESPERANTO``
- ``Language::FRENCH``
- ``Language::NORWEGIAN_BOKMAL``
- ``Language::PORTUGUESE``
- ``Language::SPANISH``
- ``Language::TURKISH``

If you want to manually construct the inflector instead of using a factory, you can do so like this:

.. code-block:: php

    use Doctrine\Inflector\CachedWordInflector;
    use Doctrine\Inflector\RulesetInflector;
    use Doctrine\Inflector\Rules\English;

    $inflector = new Inflector(
        new CachedWordInflector(new RulesetInflector(
            English\Rules::getSingularRuleset()
        )),
        new CachedWordInflector(new RulesetInflector(
            English\Rules::getPluralRuleset()
        ))
    );

Adding Languages
----------------

If you are interested in adding support for your language, take a look at the other languages defined in the
``Doctrine\Inflector\Rules`` namespace and the tests located in ``Doctrine\Tests\Inflector\Rules``. You can copy
one of the languages and update the rules for your language.

Once you have done this, send a pull request to the ``doctrine/inflector`` repository with the additions.

Custom Setup
============

If you want to setup custom singular and plural rules, you can configure these in the factory:

.. code-block:: php

    use Doctrine\Inflector\InflectorFactory;
    use Doctrine\Inflector\Rules\Pattern;
    use Doctrine\Inflector\Rules\Patterns;
    use Doctrine\Inflector\Rules\Ruleset;
    use Doctrine\Inflector\Rules\Substitution;
    use Doctrine\Inflector\Rules\Substitutions;
    use Doctrine\Inflector\Rules\Transformation;
    use Doctrine\Inflector\Rules\Transformations;
    use Doctrine\Inflector\Rules\Word;

    $inflector = InflectorFactory::create()
        ->withSingularRules(
            new Ruleset(
                new Transformations(
                    new Transformation(new Pattern('/^(bil)er$/i'), '\1'),
                    new Transformation(new Pattern('/^(inflec|contribu)tors$/i'), '\1ta')
                ),
                new Patterns(new Pattern('singulars')),
                new Substitutions(new Substitution(new Word('spins'), new Word('spinor')))
            )
        )
        ->withPluralRules(
            new Ruleset(
                new Transformations(
                    new Transformation(new Pattern('^(bil)er$'), '\1'),
                    new Transformation(new Pattern('^(inflec|contribu)tors$'), '\1ta')
                ),
                new Patterns(new Pattern('noflect'), new Pattern('abtuse')),
                new Substitutions(
                    new Substitution(new Word('amaze'), new Word('amazable')),
                    new Substitution(new Word('phone'), new Word('phonezes'))
                )
            )
        )
        ->build();

No operation inflector
----------------------

The ``Doctrine\Inflector\NoopWordInflector`` may be used to configure an inflector that doesn't perform any operation for
pluralization and/or singularization. If will simply return the input as output.

This is an implementation of the `Null Object design pattern <https://sourcemaking.com/design_patterns/null_object>`_.

.. code-block:: php

    use Doctrine\Inflector\Inflector;
    use Doctrine\Inflector\NoopWordInflector;

    $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());

Tableize
========

Converts ``ModelName`` to ``model_name``:

.. code-block:: php

    echo $inflector->tableize('ModelName'); // model_name

Classify
========

Converts ``model_name`` to ``ModelName``:

.. code-block:: php

    echo $inflector->classify('model_name'); // ModelName

Camelize
========

This method uses `Classify`_ and then converts the first character to lowercase:

.. code-block:: php

    echo $inflector->camelize('model_name'); // modelName

Capitalize
==========

Takes a string and capitalizes all of the words, like PHP's built-in
``ucwords`` function. This extends that behavior, however, by allowing the
word delimiters to be configured, rather than only separating on
whitespace.

Here is an example:

.. code-block:: php

    $string = 'top-o-the-morning to all_of_you!';

    echo $inflector->capitalize($string); // Top-O-The-Morning To All_of_you!

    echo $inflector->capitalize($string, '-_ '); // Top-O-The-Morning To All_Of_You!

Pluralize
=========

Returns a word in plural form.

.. code-block:: php

    echo $inflector->pluralize('browser'); // browsers

Singularize
===========

Returns a word in singular form.

.. code-block:: php

    echo $inflector->singularize('browsers'); // browser

Urlize
======

Generate a URL friendly string from a string of text:

.. code-block:: php

    echo $inflector->urlize('My first blog post'); // my-first-blog-post

Unaccent
========

You can unaccent a string of text using the ``unaccent()`` method:

.. code-block:: php

    echo $inflector->unaccent('año'); // ano

Legacy API
==========

The API present in Inflector 1.x is still available, but will be deprecated in a future release and dropped for 3.0.
Support for languages other than English is available in the 2.0 API only.

Acknowledgements
================

The language rules in this library have been adapted from several different sources, including but not limited to:

- `Ruby On Rails Inflector <http://api.rubyonrails.org/classes/ActiveSupport/Inflector.html>`_
- `ICanBoogie Inflector <https://github.com/ICanBoogie/Inflector>`_
- `CakePHP Inflector <https://book.cakephp.org/3.0/en/core-libraries/inflector.html>`_
