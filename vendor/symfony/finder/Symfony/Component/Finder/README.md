Finder Component
================

Finder finds files and directories via an intuitive fluent interface.

    use Symfony\Component\Finder\Finder;

    $finder = new Finder();

    $iterator = $finder
      ->files()
      ->name('*.php')
      ->depth(0)
      ->size('>= 1K')
      ->in(__DIR__);

    foreach ($iterator as $file) {
        print $file->getRealpath()."\n";
    }

But you can also use it to find files stored remotely like in this example where
we are looking for files on Amazon S3:

    $s3 = new \Zend_Service_Amazon_S3($key, $secret);
    $s3->registerStreamWrapper("s3");

    $finder = new Finder();
    $finder->name('photos*')->size('< 100K')->date('since 1 hour ago');
    foreach ($finder->in('s3://bucket-name') as $file) {
        print $file->getFilename()."\n";
    }

Resources
---------

You can run the unit tests with the following command:

    $ cd path/to/Symfony/Component/Finder/
    $ composer.phar install
    $ phpunit

