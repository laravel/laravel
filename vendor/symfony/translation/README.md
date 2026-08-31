Translation Component
=====================

The Translation component provides tools to internationalize your application.

Getting Started
---------------

```bash
composer require symfony/translation
```

```php
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

$translator = new Translator('fr_FR');
$translator->addLoader('array', new ArrayLoader());
$translator->addResource('array', [
    'Hello World!' => 'Bonjour !',
], 'fr_FR');

echo $translator->trans('Hello World!'); // outputs « Bonjour ! »
```

Sponsor
-------

Help Symfony by [sponsoring][3] its development!

Resources
---------

 * [Documentation](https://symfony.com/doc/current/translation.html)
 * [Contributing](https://symfony.com/doc/current/contributing/index.html)
 * [Report issues](https://github.com/symfony/symfony/issues) and
   [send Pull Requests](https://github.com/symfony/symfony/pulls)
   in the [main Symfony repository](https://github.com/symfony/symfony)

[3]: https://symfony.com/sponsor
