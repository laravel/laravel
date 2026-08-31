<p align="center">
    <img src="https://raw.githubusercontent.com/nunomaduro/collision/v7.x/docs/logo.png" alt="Collision logo" width="480">
    <br>
    <img src="https://raw.githubusercontent.com/nunomaduro/collision/v7.x/docs/example.png" alt="Collision code example" height="300">
</p>

<p align="center">
  <a href="https://github.com/nunomaduro/collision/actions"><img src="https://img.shields.io/github/actions/workflow/status/nunomaduro/collision/tests.yml?branch=v8.x&label=tests&style=round-square" alt="Build Status"></img></a>
  <a href="https://scrutinizer-ci.com/g/nunomaduro/collision"><img src="https://img.shields.io/scrutinizer/g/nunomaduro/collision.svg" alt="Quality Score"></img></a>
  <a href="https://packagist.org/packages/nunomaduro/collision"><img src="https://poser.pugx.org/nunomaduro/collision/d/total.svg" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/nunomaduro/collision"><img src="https://poser.pugx.org/nunomaduro/collision/license.svg" alt="License"></a>
  <a href="https://whyphp.dev"><img src="https://img.shields.io/badge/Why_PHP-in_2026-7A86E8?style=flat-square&labelColor=18181b" alt="Why PHP in 2026"></a>
  <a href="https://youtube.com/@nunomaduro?sub_confirmation=1"><img alt="YouTube Channel Subscribers" src="https://img.shields.io/youtube/channel/subscribers/UCO_hYZF2gb_CyG5sA7ArlGg?style=flat&label=youtube&color=brightgreen"></a>
</p>

---

Collision was created by, and is maintained by **[Nuno Maduro](https://github.com/nunomaduro)**, and is a package designed to give you beautiful error reporting when interacting with your app through the command line.

* It's included on **[Laravel](https://laravel.com)**, the most popular free, open-source PHP framework in the world.
* Built on top of the **[Whoops](https://github.com/filp/whoops)** error handler.
* Supports [Laravel](https://github.com/laravel/laravel), [Symfony](https://symfony.com), [PHPUnit](https://github.com/sebastianbergmann/phpunit), and many other frameworks.

- Follow the creator Nuno Maduro:
    - YouTube: **[youtube.com/@nunomaduro](https://youtube.com/@nunomaduro)** — Videos every week
    - Twitch: **[twitch.tv/nunomaduro](https://twitch.tv/nunomaduro)** — Live coding on Mondays, Wednesdays, and Fridays at 9PM UTC
    - Twitter / X: **[x.com/enunomaduro](https://x.com/enunomaduro)**
    - LinkedIn: **[linkedin.com/in/nunomaduro](https://www.linkedin.com/in/nunomaduro)**
    - Instagram: **[instagram.com/enunomaduro](https://www.instagram.com/enunomaduro)**
    - Tiktok: **[tiktok.com/@enunomaduro](https://www.tiktok.com/@enunomaduro)**

## Installation & Usage

> **Requires [PHP 8.2+](https://php.net/releases/)**

Require Collision using [Composer](https://getcomposer.org):

```bash
composer require nunomaduro/collision --dev
```

## Version Compatibility

 Laravel    | Collision | PHPUnit          | Pest          |
:-----------|:----------|:-----------------|:--------------|
 6.x        | 3.x       |                  |               |
 7.x        | 4.x       |                  |               |
 8.x        | 5.x       |                  |               |
 9.x, 10.x  | 6.x       |                  |               |
 10.x       | 6.x       | 9.x              | 1.x           |
 10.x       | 7.x       | 10.x             | 2.x           |
 11.x, 12.x | 8.x       | 10.x, 11.x, 12.x, 13.x | 2.x, 3.x, 4.x, 5.x |

As an example, here is how to require Collision on Laravel 8.x:

```bash
composer require nunomaduro/collision:^5.0 --dev
```

## No adapter

You need to register the handler in your code:

```php
(new \NunoMaduro\Collision\Provider)->register();
```

## Contributing

Thank you for considering to contribute to Collision. All the contribution guidelines are mentioned [here](CONTRIBUTING.md).

You can also follow the twitter account for latest announcements or just come say hi!: [@enunomaduro](https://twitter.com/enunomaduro)

## License

Collision is an open-sourced software licensed under the [MIT license](LICENSE.md).

Logo by [Caneco](https://twitter.com/caneco).
