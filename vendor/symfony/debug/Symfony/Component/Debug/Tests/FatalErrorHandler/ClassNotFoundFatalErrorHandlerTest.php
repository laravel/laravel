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
use Symfony\Component\Debug\FatalErrorHandler\ClassNotFoundFatalErrorHandler;

class ClassNotFoundFatalErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideClassNotFoundData
     */
    public function testClassNotFound($error, $translatedMessage)
    {
        $handler = new ClassNotFoundFatalErrorHandler();
        $exception = $handler->handleError($error, new FatalErrorException('', 0, $error['type'], $error['file'], $error['line']));

        $this->assertInstanceof('Symfony\Component\Debug\Exception\ClassNotFoundException', $exception);
        $this->assertSame($translatedMessage, $exception->getMessage());
        $this->assertSame($error['type'], $exception->getSeverity());
        $this->assertSame($error['file'], $exception->getFile());
        $this->assertSame($error['line'], $exception->getLine());
    }

    public function provideClassNotFoundData()
    {
        return array(
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Class \'WhizBangFactory\' not found',
                ),
                'Attempted to load class "WhizBangFactory" from the global namespace in foo.php line 12. Did you forget a use statement for this class?',
            ),
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Class \'Foo\\Bar\\WhizBangFactory\' not found',
                ),
                'Attempted to load class "WhizBangFactory" from namespace "Foo\\Bar" in foo.php line 12. Do you need to "use" it from another namespace?',
            ),
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Class \'UndefinedFunctionException\' not found',
                ),
                'Attempted to load class "UndefinedFunctionException" from the global namespace in foo.php line 12. Did you forget a use statement for this class? Perhaps you need to add a use statement for one of the following: Symfony\Component\Debug\Exception\UndefinedFunctionException.',
            ),
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Class \'PEARClass\' not found',
                ),
                'Attempted to load class "PEARClass" from the global namespace in foo.php line 12. Did you forget a use statement for this class? Perhaps you need to add a use statement for one of the following: Symfony_Component_Debug_Tests_Fixtures_PEARClass.',
            ),
            array(
                array(
                    'type' => 1,
                    'line' => 12,
                    'file' => 'foo.php',
                    'message' => 'Class \'Foo\\Bar\\UndefinedFunctionException\' not found',
                ),
                'Attempted to load class "UndefinedFunctionException" from namespace "Foo\Bar" in foo.php line 12. Do you need to "use" it from another namespace? Perhaps you need to add a use statement for one of the following: Symfony\Component\Debug\Exception\UndefinedFunctionException.',
            ),
        );
    }

    public function testCannotRedeclareClass()
    {
        if (!file_exists(__DIR__.'/../FIXTURES/REQUIREDTWICE.PHP')) {
            $this->markTestSkipped('Can only be run on case insensitive filesystems');
        }

        require_once __DIR__.'/../FIXTURES/REQUIREDTWICE.PHP';

        $error = array(
            'type' => 1,
            'line' => 12,
            'file' => 'foo.php',
            'message' => 'Class \'Foo\\Bar\\RequiredTwice\' not found',
        );

        $handler = new ClassNotFoundFatalErrorHandler();
        $exception = $handler->handleError($error, new FatalErrorException('', 0, $error['type'], $error['file'], $error['line']));

        $this->assertInstanceof('Symfony\Component\Debug\Exception\ClassNotFoundException', $exception);
    }
}
