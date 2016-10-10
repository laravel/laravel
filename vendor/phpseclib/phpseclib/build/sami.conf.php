<?php

// Prevent e.g. 'Notice: Constant MATH_BIGINTEGER_MONTGOMERY already defined'.
class MyArrayStore extends Sami\Store\ArrayStore
{
    public function removeClass(Sami\Project $project, $name)
    {
        unset($this->classes[$name]);
    }
}

$iterator = Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/../phpseclib/')
;

$versions = Sami\Version\GitVersionCollection::create(__DIR__ . '/../')
    ->add('master')
    ->add('php5')
;

return new Sami\Sami($iterator, array(
    'theme'                => 'enhanced',
    'versions'             => $versions,
    'title'                => 'phpseclib API Documentation',
    'build_dir'            => __DIR__.'/api/output/%version%',
    'cache_dir'            => __DIR__.'/api/cache/%version%',
    'default_opened_level' => 2,
    'store'                => new MyArrayStore,
));
