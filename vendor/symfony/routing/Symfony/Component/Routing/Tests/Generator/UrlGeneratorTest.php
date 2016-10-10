<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests\Generator;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testAbsoluteUrlWithPort80()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', array(), true);

        $this->assertEquals('http://localhost/app.php/testing', $url);
    }

    public function testAbsoluteSecureUrlWithPort443()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes, array('scheme' => 'https'))->generate('test', array(), true);

        $this->assertEquals('https://localhost/app.php/testing', $url);
    }

    public function testAbsoluteUrlWithNonStandardPort()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes, array('httpPort' => 8080))->generate('test', array(), true);

        $this->assertEquals('http://localhost:8080/app.php/testing', $url);
    }

    public function testAbsoluteSecureUrlWithNonStandardPort()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes, array('httpsPort' => 8080, 'scheme' => 'https'))->generate('test', array(), true);

        $this->assertEquals('https://localhost:8080/app.php/testing', $url);
    }

    public function testRelativeUrlWithoutParameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', array(), false);

        $this->assertEquals('/app.php/testing', $url);
    }

    public function testRelativeUrlWithParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}'));
        $url = $this->getGenerator($routes)->generate('test', array('foo' => 'bar'), false);

        $this->assertEquals('/app.php/testing/bar', $url);
    }

    public function testRelativeUrlWithNullParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing.{format}', array('format' => null)));
        $url = $this->getGenerator($routes)->generate('test', array(), false);

        $this->assertEquals('/app.php/testing', $url);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testRelativeUrlWithNullParameterButNotOptional()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}/bar', array('foo' => null)));
        // This must raise an exception because the default requirement for "foo" is "[^/]+" which is not met with these params.
        // Generating path "/testing//bar" would be wrong as matching this route would fail.
        $this->getGenerator($routes)->generate('test', array(), false);
    }

    public function testRelativeUrlWithOptionalZeroParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{page}'));
        $url = $this->getGenerator($routes)->generate('test', array('page' => 0), false);

        $this->assertEquals('/app.php/testing/0', $url);
    }

    public function testNotPassedOptionalParameterInBetween()
    {
        $routes = $this->getRoutes('test', new Route('/{slug}/{page}', array('slug' => 'index', 'page' => 0)));
        $this->assertSame('/app.php/index/1', $this->getGenerator($routes)->generate('test', array('page' => 1)));
        $this->assertSame('/app.php/', $this->getGenerator($routes)->generate('test'));
    }

    public function testRelativeUrlWithExtraParameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', array('foo' => 'bar'), false);

        $this->assertEquals('/app.php/testing?foo=bar', $url);
    }

    public function testAbsoluteUrlWithExtraParameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', array('foo' => 'bar'), true);

        $this->assertEquals('http://localhost/app.php/testing?foo=bar', $url);
    }

    public function testUrlWithNullExtraParameters()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $url = $this->getGenerator($routes)->generate('test', array('foo' => null), true);

        $this->assertEquals('http://localhost/app.php/testing', $url);
    }

    public function testUrlWithExtraParametersFromGlobals()
    {
        $routes = $this->getRoutes('test', new Route('/testing'));
        $generator = $this->getGenerator($routes);
        $context = new RequestContext('/app.php');
        $context->setParameter('bar', 'bar');
        $generator->setContext($context);
        $url = $generator->generate('test', array('foo' => 'bar'));

        $this->assertEquals('/app.php/testing?foo=bar', $url);
    }

    public function testUrlWithGlobalParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}'));
        $generator = $this->getGenerator($routes);
        $context = new RequestContext('/app.php');
        $context->setParameter('foo', 'bar');
        $generator->setContext($context);
        $url = $generator->generate('test', array());

        $this->assertEquals('/app.php/testing/bar', $url);
    }

    public function testGlobalParameterHasHigherPriorityThanDefault()
    {
        $routes = $this->getRoutes('test', new Route('/{_locale}', array('_locale' => 'en')));
        $generator = $this->getGenerator($routes);
        $context = new RequestContext('/app.php');
        $context->setParameter('_locale', 'de');
        $generator->setContext($context);
        $url = $generator->generate('test', array());

        $this->assertSame('/app.php/de', $url);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function testGenerateWithoutRoutes()
    {
        $routes = $this->getRoutes('foo', new Route('/testing/{foo}'));
        $this->getGenerator($routes)->generate('test', array(), true);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     */
    public function testGenerateForRouteWithoutMandatoryParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}'));
        $this->getGenerator($routes)->generate('test', array(), true);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testGenerateForRouteWithInvalidOptionalParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', array('foo' => '1'), array('foo' => 'd+')));
        $this->getGenerator($routes)->generate('test', array('foo' => 'bar'), true);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testGenerateForRouteWithInvalidParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', array(), array('foo' => '1|2')));
        $this->getGenerator($routes)->generate('test', array('foo' => '0'), true);
    }

    public function testGenerateForRouteWithInvalidOptionalParameterNonStrict()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', array('foo' => '1'), array('foo' => 'd+')));
        $generator = $this->getGenerator($routes);
        $generator->setStrictRequirements(false);
        $this->assertNull($generator->generate('test', array('foo' => 'bar'), true));
    }

    public function testGenerateForRouteWithInvalidOptionalParameterNonStrictWithLogger()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', array('foo' => '1'), array('foo' => 'd+')));
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())
            ->method('error');
        $generator = $this->getGenerator($routes, array(), $logger);
        $generator->setStrictRequirements(false);
        $this->assertNull($generator->generate('test', array('foo' => 'bar'), true));
    }

    public function testGenerateForRouteWithInvalidParameterButDisabledRequirementsCheck()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', array('foo' => '1'), array('foo' => 'd+')));
        $generator = $this->getGenerator($routes);
        $generator->setStrictRequirements(null);
        $this->assertSame('/app.php/testing/bar', $generator->generate('test', array('foo' => 'bar')));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testGenerateForRouteWithInvalidMandatoryParameter()
    {
        $routes = $this->getRoutes('test', new Route('/testing/{foo}', array(), array('foo' => 'd+')));
        $this->getGenerator($routes)->generate('test', array('foo' => 'bar'), true);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testRequiredParamAndEmptyPassed()
    {
        $routes = $this->getRoutes('test', new Route('/{slug}', array(), array('slug' => '.+')));
        $this->getGenerator($routes)->generate('test', array('slug' => ''));
    }

    public function testSchemeRequirementDoesNothingIfSameCurrentScheme()
    {
        $routes = $this->getRoutes('test', new Route('/', array(), array('_scheme' => 'http')));
        $this->assertEquals('/app.php/', $this->getGenerator($routes)->generate('test'));

        $routes = $this->getRoutes('test', new Route('/', array(), array('_scheme' => 'https')));
        $this->assertEquals('/app.php/', $this->getGenerator($routes, array('scheme' => 'https'))->generate('test'));
    }

    public function testSchemeRequirementForcesAbsoluteUrl()
    {
        $routes = $this->getRoutes('test', new Route('/', array(), array('_scheme' => 'https')));
        $this->assertEquals('https://localhost/app.php/', $this->getGenerator($routes)->generate('test'));

        $routes = $this->getRoutes('test', new Route('/', array(), array('_scheme' => 'http')));
        $this->assertEquals('http://localhost/app.php/', $this->getGenerator($routes, array('scheme' => 'https'))->generate('test'));
    }

    public function testPathWithTwoStartingSlashes()
    {
        $routes = $this->getRoutes('test', new Route('//path-and-not-domain'));

        // this must not generate '//path-and-not-domain' because that would be a network path
        $this->assertSame('/path-and-not-domain', $this->getGenerator($routes, array('BaseUrl' => ''))->generate('test'));
    }

    public function testNoTrailingSlashForMultipleOptionalParameters()
    {
        $routes = $this->getRoutes('test', new Route('/category/{slug1}/{slug2}/{slug3}', array('slug2' => null, 'slug3' => null)));

        $this->assertEquals('/app.php/category/foo', $this->getGenerator($routes)->generate('test', array('slug1' => 'foo')));
    }

    public function testWithAnIntegerAsADefaultValue()
    {
        $routes = $this->getRoutes('test', new Route('/{default}', array('default' => 0)));

        $this->assertEquals('/app.php/foo', $this->getGenerator($routes)->generate('test', array('default' => 'foo')));
    }

    public function testNullForOptionalParameterIsIgnored()
    {
        $routes = $this->getRoutes('test', new Route('/test/{default}', array('default' => 0)));

        $this->assertEquals('/app.php/test', $this->getGenerator($routes)->generate('test', array('default' => null)));
    }

    public function testQueryParamSameAsDefault()
    {
        $routes = $this->getRoutes('test', new Route('/test', array('default' => 'value')));

        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test', array('default' => 'foo')));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test', array('default' => 'value')));
        $this->assertSame('/app.php/test', $this->getGenerator($routes)->generate('test'));
    }

    public function testGenerateWithSpecialRouteName()
    {
        $routes = $this->getRoutes('$péß^a|', new Route('/bar'));

        $this->assertSame('/app.php/bar', $this->getGenerator($routes)->generate('$péß^a|'));
    }

    public function testUrlEncoding()
    {
        // This tests the encoding of reserved characters that are used for delimiting of URI components (defined in RFC 3986)
        // and other special ASCII chars. These chars are tested as static text path, variable path and query param.
        $chars = '@:[]/()*\'" +,;-._~&$<>|{}%\\^`!?foo=bar#id';
        $routes = $this->getRoutes('test', new Route("/$chars/{varpath}", array(), array('varpath' => '.+')));
        $this->assertSame('/app.php/@:%5B%5D/%28%29*%27%22%20+,;-._~%26%24%3C%3E|%7B%7D%25%5C%5E%60!%3Ffoo=bar%23id'
           .'/@:%5B%5D/%28%29*%27%22%20+,;-._~%26%24%3C%3E|%7B%7D%25%5C%5E%60!%3Ffoo=bar%23id'
           .'?query=%40%3A%5B%5D%2F%28%29%2A%27%22+%2B%2C%3B-._%7E%26%24%3C%3E%7C%7B%7D%25%5C%5E%60%21%3Ffoo%3Dbar%23id',
            $this->getGenerator($routes)->generate('test', array(
                'varpath' => $chars,
                'query' => $chars,
            ))
        );
    }

    public function testEncodingOfRelativePathSegments()
    {
        $routes = $this->getRoutes('test', new Route('/dir/../dir/..'));
        $this->assertSame('/app.php/dir/%2E%2E/dir/%2E%2E', $this->getGenerator($routes)->generate('test'));
        $routes = $this->getRoutes('test', new Route('/dir/./dir/.'));
        $this->assertSame('/app.php/dir/%2E/dir/%2E', $this->getGenerator($routes)->generate('test'));
        $routes = $this->getRoutes('test', new Route('/a./.a/a../..a/...'));
        $this->assertSame('/app.php/a./.a/a../..a/...', $this->getGenerator($routes)->generate('test'));
    }

    public function testAdjacentVariables()
    {
        $routes = $this->getRoutes('test', new Route('/{x}{y}{z}.{_format}', array('z' => 'default-z', '_format' => 'html'), array('y' => '\d+')));
        $generator = $this->getGenerator($routes);
        $this->assertSame('/app.php/foo123', $generator->generate('test', array('x' => 'foo', 'y' => '123')));
        $this->assertSame('/app.php/foo123bar.xml', $generator->generate('test', array('x' => 'foo', 'y' => '123', 'z' => 'bar', '_format' => 'xml')));

        // The default requirement for 'x' should not allow the separator '.' in this case because it would otherwise match everything
        // and following optional variables like _format could never match.
        $this->setExpectedException('Symfony\Component\Routing\Exception\InvalidParameterException');
        $generator->generate('test', array('x' => 'do.t', 'y' => '123', 'z' => 'bar', '_format' => 'xml'));
    }

    public function testOptionalVariableWithNoRealSeparator()
    {
        $routes = $this->getRoutes('test', new Route('/get{what}', array('what' => 'All')));
        $generator = $this->getGenerator($routes);

        $this->assertSame('/app.php/get', $generator->generate('test'));
        $this->assertSame('/app.php/getSites', $generator->generate('test', array('what' => 'Sites')));
    }

    public function testRequiredVariableWithNoRealSeparator()
    {
        $routes = $this->getRoutes('test', new Route('/get{what}Suffix'));
        $generator = $this->getGenerator($routes);

        $this->assertSame('/app.php/getSitesSuffix', $generator->generate('test', array('what' => 'Sites')));
    }

    public function testDefaultRequirementOfVariable()
    {
        $routes = $this->getRoutes('test', new Route('/{page}.{_format}'));
        $generator = $this->getGenerator($routes);

        $this->assertSame('/app.php/index.mobile.html', $generator->generate('test', array('page' => 'index', '_format' => 'mobile.html')));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testDefaultRequirementOfVariableDisallowsSlash()
    {
        $routes = $this->getRoutes('test', new Route('/{page}.{_format}'));
        $this->getGenerator($routes)->generate('test', array('page' => 'index', '_format' => 'sl/ash'));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testDefaultRequirementOfVariableDisallowsNextSeparator()
    {
        $routes = $this->getRoutes('test', new Route('/{page}.{_format}'));
        $this->getGenerator($routes)->generate('test', array('page' => 'do.t', '_format' => 'html'));
    }

    public function testWithHostDifferentFromContext()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', array(), array(), array(), '{locale}.example.com'));

        $this->assertEquals('//fr.example.com/app.php/Fabien', $this->getGenerator($routes)->generate('test', array('name' => 'Fabien', 'locale' => 'fr')));
    }

    public function testWithHostSameAsContext()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', array(), array(), array(), '{locale}.example.com'));

        $this->assertEquals('/app.php/Fabien', $this->getGenerator($routes, array('host' => 'fr.example.com'))->generate('test', array('name' => 'Fabien', 'locale' => 'fr')));
    }

    public function testWithHostSameAsContextAndAbsolute()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', array(), array(), array(), '{locale}.example.com'));

        $this->assertEquals('http://fr.example.com/app.php/Fabien', $this->getGenerator($routes, array('host' => 'fr.example.com'))->generate('test', array('name' => 'Fabien', 'locale' => 'fr'), true));
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testUrlWithInvalidParameterInHost()
    {
        $routes = $this->getRoutes('test', new Route('/', array(), array('foo' => 'bar'), array(), '{foo}.example.com'));
        $this->getGenerator($routes)->generate('test', array('foo' => 'baz'), false);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testUrlWithInvalidParameterInHostWhenParamHasADefaultValue()
    {
        $routes = $this->getRoutes('test', new Route('/', array('foo' => 'bar'), array('foo' => 'bar'), array(), '{foo}.example.com'));
        $this->getGenerator($routes)->generate('test', array('foo' => 'baz'), false);
    }

    /**
     * @expectedException \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function testUrlWithInvalidParameterEqualsDefaultValueInHost()
    {
        $routes = $this->getRoutes('test', new Route('/', array('foo' => 'baz'), array('foo' => 'bar'), array(), '{foo}.example.com'));
        $this->getGenerator($routes)->generate('test', array('foo' => 'baz'), false);
    }

    public function testUrlWithInvalidParameterInHostInNonStrictMode()
    {
        $routes = $this->getRoutes('test', new Route('/', array(), array('foo' => 'bar'), array(), '{foo}.example.com'));
        $generator = $this->getGenerator($routes);
        $generator->setStrictRequirements(false);
        $this->assertNull($generator->generate('test', array('foo' => 'baz'), false));
    }

    public function testGenerateNetworkPath()
    {
        $routes = $this->getRoutes('test', new Route('/{name}', array(), array('_scheme' => 'http'), array(), '{locale}.example.com'));

        $this->assertSame('//fr.example.com/app.php/Fabien', $this->getGenerator($routes)->generate('test',
            array('name' => 'Fabien', 'locale' => 'fr'), UrlGeneratorInterface::NETWORK_PATH), 'network path with different host'
        );
        $this->assertSame('//fr.example.com/app.php/Fabien?query=string', $this->getGenerator($routes, array('host' => 'fr.example.com'))->generate('test',
            array('name' => 'Fabien', 'locale' => 'fr', 'query' => 'string'), UrlGeneratorInterface::NETWORK_PATH), 'network path although host same as context'
        );
        $this->assertSame('http://fr.example.com/app.php/Fabien', $this->getGenerator($routes, array('scheme' => 'https'))->generate('test',
            array('name' => 'Fabien', 'locale' => 'fr'), UrlGeneratorInterface::NETWORK_PATH), 'absolute URL because scheme requirement does not match context'
        );
        $this->assertSame('http://fr.example.com/app.php/Fabien', $this->getGenerator($routes)->generate('test',
            array('name' => 'Fabien', 'locale' => 'fr'), UrlGeneratorInterface::ABSOLUTE_URL), 'absolute URL with same scheme because it is requested'
        );
    }

    public function testGenerateRelativePath()
    {
        $routes = new RouteCollection();
        $routes->add('article', new Route('/{author}/{article}/'));
        $routes->add('comments', new Route('/{author}/{article}/comments'));
        $routes->add('host', new Route('/{article}', array(), array(), array(), '{author}.example.com'));
        $routes->add('scheme', new Route('/{author}', array(), array('_scheme' => 'https')));
        $routes->add('unrelated', new Route('/about'));

        $generator = $this->getGenerator($routes, array('host' => 'example.com', 'pathInfo' => '/fabien/symfony-is-great/'));

        $this->assertSame('comments', $generator->generate('comments',
            array('author' => 'fabien', 'article' => 'symfony-is-great'), UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('comments?page=2', $generator->generate('comments',
            array('author' => 'fabien', 'article' => 'symfony-is-great', 'page' => 2), UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('../twig-is-great/', $generator->generate('article',
            array('author' => 'fabien', 'article' => 'twig-is-great'), UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('../../bernhard/forms-are-great/', $generator->generate('article',
            array('author' => 'bernhard', 'article' => 'forms-are-great'), UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('//bernhard.example.com/app.php/forms-are-great', $generator->generate('host',
            array('author' => 'bernhard', 'article' => 'forms-are-great'), UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('https://example.com/app.php/bernhard', $generator->generate('scheme',
            array('author' => 'bernhard'), UrlGeneratorInterface::RELATIVE_PATH)
        );
        $this->assertSame('../../about', $generator->generate('unrelated',
            array(), UrlGeneratorInterface::RELATIVE_PATH)
        );
    }

    /**
     * @dataProvider provideRelativePaths
     */
    public function testGetRelativePath($sourcePath, $targetPath, $expectedPath)
    {
        $this->assertSame($expectedPath, UrlGenerator::getRelativePath($sourcePath, $targetPath));
    }

    public function provideRelativePaths()
    {
        return array(
            array(
                '/same/dir/',
                '/same/dir/',
                '',
            ),
            array(
                '/same/file',
                '/same/file',
                '',
            ),
            array(
                '/',
                '/file',
                'file',
            ),
            array(
                '/',
                '/dir/file',
                'dir/file',
            ),
            array(
                '/dir/file.html',
                '/dir/different-file.html',
                'different-file.html',
            ),
            array(
                '/same/dir/extra-file',
                '/same/dir/',
                './',
            ),
            array(
                '/parent/dir/',
                '/parent/',
                '../',
            ),
            array(
                '/parent/dir/extra-file',
                '/parent/',
                '../',
            ),
            array(
                '/a/b/',
                '/x/y/z/',
                '../../x/y/z/',
            ),
            array(
                '/a/b/c/d/e',
                '/a/c/d',
                '../../../c/d',
            ),
            array(
                '/a/b/c//',
                '/a/b/c/',
                '../',
            ),
            array(
                '/a/b/c/',
                '/a/b/c//',
                './/',
            ),
            array(
                '/root/a/b/c/',
                '/root/x/b/c/',
                '../../../x/b/c/',
            ),
            array(
                '/a/b/c/d/',
                '/a',
                '../../../../a',
            ),
            array(
                '/special-chars/sp%20ce/1€/mäh/e=mc²',
                '/special-chars/sp%20ce/1€/<µ>/e=mc²',
                '../<µ>/e=mc²',
            ),
            array(
                'not-rooted',
                'dir/file',
                'dir/file',
            ),
            array(
                '//dir/',
                '',
                '../../',
            ),
            array(
                '/dir/',
                '/dir/file:with-colon',
                './file:with-colon',
            ),
            array(
                '/dir/',
                '/dir/subdir/file:with-colon',
                'subdir/file:with-colon',
            ),
            array(
                '/dir/',
                '/dir/:subdir/',
                './:subdir/',
            ),
        );
    }

    protected function getGenerator(RouteCollection $routes, array $parameters = array(), $logger = null)
    {
        $context = new RequestContext('/app.php');
        foreach ($parameters as $key => $value) {
            $method = 'set'.$key;
            $context->$method($value);
        }
        $generator = new UrlGenerator($routes, $context, $logger);

        return $generator;
    }

    protected function getRoutes($name, Route $route)
    {
        $routes = new RouteCollection();
        $routes->add($name, $route);

        return $routes;
    }
}
