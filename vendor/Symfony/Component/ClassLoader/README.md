ClassLoader Component
=====================

ClassLoader loads your project classes automatically if they follow some
standard PHP conventions.

The Universal ClassLoader is able to autoload classes that implement the PSR-0
standard or the PEAR naming convention.

First, register the autoloader:

    require_once __DIR__.'/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

    use Symfony\Component\ClassLoader\UniversalClassLoader;

    $loader = new UniversalClassLoader();
    $loader->register();

Then, register some namespaces with the `registerNamespace()` method:

    $loader->registerNamespace('Symfony', __DIR__.'/src');
    $loader->registerNamespace('Monolog', __DIR__.'/vendor/monolog/src');

The `registerNamespace()` method takes a namespace prefix and a path where to
look for the classes as arguments.

You can also register a sub-namespaces:

    $loader->registerNamespace('Doctrine\\Common', __DIR__.'/vendor/doctrine-common/lib');

The order of registration is significant and the first registered namespace
takes precedence over later registered one.

You can also register more than one path for a given namespace:

    $loader->registerNamespace('Symfony', array(__DIR__.'/src', __DIR__.'/symfony/src'));

Alternatively, you can use the `registerNamespaces()` method to register more
than one namespace at once:

    $loader->registerNamespaces(array(
        'Symfony'          => array(__DIR__.'/src', __DIR__.'/symfony/src'),
        'Doctrine\\Common' => __DIR__.'/vendor/doctrine-common/lib',
        'Doctrine'         => __DIR__.'/vendor/doctrine/lib',
        'Monolog'          => __DIR__.'/vendor/monolog/src',
    ));

For better performance, you can use the APC based version of the universal
class loader:

    require_once __DIR__.'/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
    require_once __DIR__.'/src/Symfony/Component/ClassLoader/ApcUniversalClassLoader.php';

    use Symfony\Component\ClassLoader\ApcUniversalClassLoader;

    $loader = new ApcUniversalClassLoader('apc.prefix.');

Furthermore, the component provides tools to aggregate classes into a single
file, which is especially useful to improve performance on servers that do not
provide byte caches.
