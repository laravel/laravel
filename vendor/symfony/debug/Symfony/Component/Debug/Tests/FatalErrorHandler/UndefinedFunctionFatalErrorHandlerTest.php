<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Tests\FatalErrorHandler;

use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\FatalErrorHandler\UndefinedFunctionFatalErrorHandler;

class UndefinedFunctionFatalErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideUndefinedFunctionData
     */
    public function testUndefinedFunction($error, $translatedMessage)
    {
        $handler = new UndefinedFunctionFatalErrorHandler();
        $exception = $handler->handleError($error, new FatalErrorException('', 0, $error['type'], $error['file'], $error['line']));

        $this->assertInstanceof('Symfony\Component\Debug\Exception\UndefinedFunctionException', $exception);
        $this->assertSame($translatedMessage, $exception->getMessage());
        $this->assertSame($error['type'], $exception->getSeverity());
        $this->assertSame($error['file'], $exception->getFile());
        $this->assertSame($error['line'], $exception->getLine());
    }

    public function provideUndefinedFunctionData()
    {
        return array(
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Call to undefined function test_namespaced_function()',
                ),
                'Attempted to call function "test_namespaced_function" from the global namespace in foo.php line 12. Did you mean to call: "\\symfony\\component\\debug\\tests\\fatalerrorhandler\\test_namespaced_function"?',
            ),
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Call to undefined function Foo\\Bar\\Baz\\test_namespaced_function()',
                ),
                'Attempted to call function "test_namespaced_function" from namespace "Foo\\Bar\\Baz" in foo.php line 12. Did you mean to call: "\\symfony\\component\\debug\\tests\\fatalerrorhandler\\test_namespaced_function"?',
            ),
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Call to undefined function foo()',
                ),
                'Attempted to call function "foo" from the global namespace in foo.php line 12.',
            ),
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Call to undefined function Foo\\Bar\\Baz\\foo()',
                ),
                'Attempted to call function "foo" from namespace "Foo\Bar\Baz" in foo.php line 12.',
            ),
        );
    }
}

function test_namespaced_function()
{
}
