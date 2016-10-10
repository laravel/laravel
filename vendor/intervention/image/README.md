# Intervention Image

Intervention Image is a **PHP image handling and manipulation** library based on the PHP GD library. The package includes ServiceProviders and Facades for easy **Laravel 4** integration.

[![Build Status](https://travis-ci.org/Intervention/image.png?branch=master)](https://travis-ci.org/Intervention/image)

## Requirements

- PHP >=5.3
- GD >=2.0

## Getting started

- [Installation Guide](http://image.intervention.io/getting_started/installation)
- [Laravel Framework Integration](http://image.intervention.io/getting_started/laravel)
- [Official Documentation](http://image.intervention.io/)

## Code Examples

```php
// open an image file
$img = Image::make('public/foo.jpg');

// resize image instance
$img->resize(320, 240);

// insert a watermark
$img->insert('public/watermark.png');

// save image in desired format
$img->save('public/bar.jpg');
```

Refer to the [documentation](http://image.intervention.io/) to learn more about Intervention Image.

## License

Intervention Image is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2013 [Oliver Vogel](http://olivervogel.net/)
