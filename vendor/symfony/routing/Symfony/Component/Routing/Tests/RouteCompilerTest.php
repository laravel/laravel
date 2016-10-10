<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests;

use Symfony\Component\Routing\Route;

class RouteCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCompileData
     */
    public function testCompile($name, $arguments, $prefix, $regex, $variables, $tokens)
    {
        $r = new \ReflectionClass('Symfony\\Component\\Routing\\Route');
        $route = $r->newInstanceArgs($arguments);

        $compiled = $route->compile();
        $this->assertEquals($prefix, $compiled->getStaticPrefix(), $name.' (static prefix)');
        $this->assertEquals($regex, $compiled->getRegex(), $name.' (regex)');
        $this->assertEquals($variables, $compiled->getVariables(), $name.' (variables)');
        $this->assertEquals($tokens, $compiled->getTokens(), $name.' (tokens)');
    }

    public function provideCompileData()
    {
        return array(
            array(
                'Static route',
                array('/foo'),
                '/foo', '#^/foo$#s', array(), array(
                    array('text', '/foo'),
                ),),

            array(
                'Route with a variable',
                array('/foo/{bar}'),
                '/foo', '#^/foo/(?P<bar>[^/]++)$#s', array('bar'), array(
                    array('variable', '/', '[^/]++', 'bar'),
                    array('text', '/foo'),
                ),),

            array(
                'Route with a variable that has a default value',
                array('/foo/{bar}', array('bar' => 'bar')),
                '/foo', '#^/foo(?:/(?P<bar>[^/]++))?$#s', array('bar'), array(
                    array('variable', '/', '[^/]++', 'bar'),
                    array('text', '/foo'),
                ),),

            array(
                'Route with several variables',
                array('/foo/{bar}/{foobar}'),
                '/foo', '#^/foo/(?P<bar>[^/]++)/(?P<foobar>[^/]++)$#s', array('bar', 'foobar'), array(
                    array('variable', '/', '[^/]++', 'foobar'),
                    array('variable', '/', '[^/]++', 'bar'),
                    array('text', '/foo'),
                ),),

            array(
                'Route with several variables that have default values',
                array('/foo/{bar}/{foobar}', array('bar' => 'bar', 'foobar' => '')),
                '/foo', '#^/foo(?:/(?P<bar>[^/]++)(?:/(?P<foobar>[^/]++))?)?$#s', array('bar', 'foobar'), array(
                    array('variable', '/', '[^/]++', 'foobar'),
                    array('variable', '/', '[^/]++', 'bar'),
                    array('text', '/foo'),
                ),),

            array(
                'Route with several variables but some of them have no default values',
                array('/foo/{bar}/{foobar}', array('bar' => 'bar')),
                '/foo', '#^/foo/(?P<bar>[^/]++)/(?P<foobar>[^/]++)$#s', array('bar', 'foobar'), array(
                    array('variable', '/', '[^/]++', 'foobar'),
                    array('variable', '/', '[^/]++', 'bar'),
                    array('text', '/foo'),
                ),),

            array(
                'Route with an optional variable as the first segment',
                array('/{bar}', array('bar' => 'bar')),
                '', '#^/(?P<bar>[^/]++)?$#s', array('bar'), array(
                    array('variable', '/', '[^/]++', 'bar'),
                ),),

            array(
                'Route with a requirement of 0',
                array('/{bar}', array('bar' => null), array('bar' => '0')),
                '', '#^/(?P<bar>0)?$#s', array('bar'), array(
                    array('variable', '/', '0', 'bar'),
                ),),

            array(
                'Route with an optional variable as the first segment with requirements',
                array('/{bar}', array('bar' => 'bar'), array('bar' => '(foo|bar)')),
                '', '#^/(?P<bar>(foo|bar))?$#s', array('bar'), array(
                    array('variable', '/', '(foo|bar)', 'bar'),
                ),),

            array(
                'Route with only optional variables',
                array('/{foo}/{bar}', array('foo' => 'foo', 'bar' => 'bar')),
                '', '#^/(?P<foo>[^/]++)?(?:/(?P<bar>[^/]++))?$#s', array('foo', 'bar'), array(
                    array('variable', '/', '[^/]++', 'bar'),
                    array('variable', '/', '[^/]++', 'foo'),
                ),),

            array(
                'Route with a variable in last position',
                array('/foo-{bar}'),
                '/foo', '#^/foo\-(?P<bar>[^/]++)$#s', array('bar'), array(
                array('variable', '-', '[^/]++', 'bar'),
                array('text', '/foo'),
            ),),

            array(
                'Route with nested placeholders',
                array('/{static{var}static}'),
                '/{static', '#^/\{static(?P<var>[^/]+)static\}$#s', array('var'), array(
                array('text', 'static}'),
                array('variable', '', '[^/]+', 'var'),
                array('text', '/{static'),
            ),),

            array(
                'Route without separator between variables',
                array('/{w}{x}{y}{z}.{_format}', array('z' => 'default-z', '_format' => 'html'), array('y' => '(y|Y)')),
                '', '#^/(?P<w>[^/\.]+)(?P<x>[^/\.]+)(?P<y>(y|Y))(?:(?P<z>[^/\.]++)(?:\.(?P<_format>[^/]++))?)?$#s', array('w', 'x', 'y', 'z', '_format'), array(
                array('variable', '.', '[^/]++', '_format'),
                array('variable', '', '[^/\.]++', 'z'),
                array('variable', '', '(y|Y)', 'y'),
                array('variable', '', '[^/\.]+', 'x'),
                array('variable', '/', '[^/\.]+', 'w'),
            ),),

            array(
                'Route with a format',
                array('/foo/{bar}.{_format}'),
                '/foo', '#^/foo/(?P<bar>[^/\.]++)\.(?P<_format>[^/]++)$#s', array('bar', '_format'), array(
                array('variable', '.', '[^/]++', '_format'),
                array('variable', '/', '[^/\.]++', 'bar'),
                array('text', '/foo'),
            ),),
        );
    }

    /**
     * @expectedException \LogicException
     */
    public function testRouteWithSameVariableTwice()
    {
        $route = new Route('/{name}/{name}');

        $compiled = $route->compile();
    }

    /**
     * @dataProvider getNumericVariableNames
     * @expectedException \DomainException
     */
    public function testRouteWithNumericVariableName($name)
    {
        $route = new Route('/{'.$name.'}');
        $route->compile();
    }

    public function getNumericVariableNames()
    {
        return array(
           array('09'),
           array('123'),
           array('1e2'),
        );
    }

    /**
     * @dataProvider provideCompileWithHostData
     */
    public function testCompileWithHost($name, $arguments, $prefix, $regex, $variables, $pathVariables, $tokens, $hostRegex, $hostVariables, $hostTokens)
    {
        $r = new \ReflectionClass('Symfony\\Component\\Routing\\Route');
        $route = $r->newInstanceArgs($arguments);

        $compiled = $route->compile();
        $this->assertEquals($prefix, $compiled->getStaticPrefix(), $name.' (static prefix)');
        $this->assertEquals($regex, str_replace(array("\n", ' '), '', $compiled->getRegex()), $name.' (regex)');
        $this->assertEquals($variables, $compiled->getVariables(), $name.' (variables)');
        $this->assertEquals($pathVariables, $compiled->getPathVariables(), $name.' (path variables)');
        $this->assertEquals($tokens, $compiled->getTokens(), $name.' (tokens)');
        $this->assertEquals($hostRegex, str_replace(array("\n", ' '), '', $compiled->getHostRegex()), $name.' (host regex)');
        $this->assertEquals($hostVariables, $compiled->getHostVariables(), $name.' (host variables)');
        $this->assertEquals($hostTokens, $compiled->getHostTokens(), $name.' (host tokens)');
    }

    public function provideCompileWithHostData()
    {
        return array(
            array(
                'Route with host pattern',
                array('/hello', array(), array(), array(), 'www.example.com'),
                '/hello', '#^/hello$#s', array(), array(), array(
                    array('text', '/hello'),
                ),
                '#^www\.example\.com$#s', array(), array(
                    array('text', 'www.example.com'),
                ),
            ),
            array(
                'Route with host pattern and some variables',
                array('/hello/{name}', array(), array(), array(), 'www.example.{tld}'),
                '/hello', '#^/hello/(?P<name>[^/]++)$#s', array('tld', 'name'), array('name'), array(
                    array('variable', '/', '[^/]++', 'name'),
                    array('text', '/hello'),
                ),
                '#^www\.example\.(?P<tld>[^\.]++)$#s', array('tld'), array(
                    array('variable', '.', '[^\.]++', 'tld'),
                    array('text', 'www.example'),
                ),
            ),
            array(
                'Route with variable at beginning of host',
                array('/hello', array(), array(), array(), '{locale}.example.{tld}'),
                '/hello', '#^/hello$#s', array('locale', 'tld'), array(), array(
                    array('text', '/hello'),
                ),
                '#^(?P<locale>[^\.]++)\.example\.(?P<tld>[^\.]++)$#s', array('locale', 'tld'), array(
                    array('variable', '.', '[^\.]++', 'tld'),
                    array('text', '.example'),
                    array('variable', '', '[^\.]++', 'locale'),
                ),
            ),
            array(
                'Route with host variables that has a default value',
                array('/hello', array('locale' => 'a', 'tld' => 'b'), array(), array(), '{locale}.example.{tld}'),
                '/hello', '#^/hello$#s', array('locale', 'tld'), array(), array(
                    array('text', '/hello'),
                ),
                '#^(?P<locale>[^\.]++)\.example\.(?P<tld>[^\.]++)$#s', array('locale', 'tld'), array(
                    array('variable', '.', '[^\.]++', 'tld'),
                    array('text', '.example'),
                    array('variable', '', '[^\.]++', 'locale'),
                ),
            ),
        );
    }
}
