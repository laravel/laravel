<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Testing\Fakes\ExceptionHandlerFake;
use Illuminate\Support\Traits\ReflectsClosures;
use Illuminate\Testing\Assert;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait InteractsWithExceptionHandling
{
    use ReflectsClosures;

    /**
     * The original exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler|null
     */
    protected $originalExceptionHandler;

    /**
     * Restore exception handling.
     *
     * @return $this
     */
    protected function withExceptionHandling()
    {
        if ($this->originalExceptionHandler) {
            $currentExceptionHandler = app(ExceptionHandler::class);

            $currentExceptionHandler instanceof ExceptionHandlerFake
                ? $currentExceptionHandler->setHandler($this->originalExceptionHandler)
                : $this->app->instance(ExceptionHandler::class, $this->originalExceptionHandler);
        }

        return $this;
    }

    /**
     * Only handle the given exceptions via the exception handler.
     *
     * @param  list<class-string<\Throwable>>  $exceptions
     * @return $this
     */
    protected function handleExceptions(array $exceptions)
    {
        return $this->withoutExceptionHandling($exceptions);
    }

    /**
     * Only handle validation exceptions via the exception handler.
     *
     * @return $this
     */
    protected function handleValidationExceptions()
    {
        return $this->handleExceptions([ValidationException::class]);
    }

    /**
     * Disable exception handling for the test.
     *
     * @param  list<class-string<\Throwable>>  $except
     * @return $this
     */
    protected function withoutExceptionHandling(array $except = [])
    {
        if ($this->originalExceptionHandler == null) {
            $currentExceptionHandler = app(ExceptionHandler::class);

            $this->originalExceptionHandler = $currentExceptionHandler instanceof ExceptionHandlerFake
                ? $currentExceptionHandler->handler()
                : $currentExceptionHandler;
        }

        $exceptionHandler = new class($this->originalExceptionHandler, $except) implements ExceptionHandler, WithoutExceptionHandlingHandler
        {
            protected $except;
            protected $originalHandler;

            /**
             * Create a new class instance.
             *
             * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $originalHandler
             * @param  list<class-string<\Throwable>>  $except
             * @return void
             */
            public function __construct($originalHandler, $except = [])
            {
                $this->except = $except;
                $this->originalHandler = $originalHandler;
            }

            /**
             * Report or log an exception.
             *
             * @param  \Throwable  $e
             * @return void
             *
             * @throws \Exception
             */
            public function report(Throwable $e)
            {
                //
            }

            /**
             * Determine if the exception should be reported.
             *
             * @param  \Throwable  $e
             * @return false
             */
            public function shouldReport(Throwable $e)
            {
                return false;
            }

            /**
             * Render an exception into an HTTP response.
             *
             * @param  \Illuminate\Http\Request  $request
             * @param  \Throwable  $e
             * @return \Symfony\Component\HttpFoundation\Response
             *
             * @throws \Throwable
             */
            public function render($request, Throwable $e)
            {
                foreach ($this->except as $class) {
                    if ($e instanceof $class) {
                        return $this->originalHandler->render($request, $e);
                    }
                }

                if ($e instanceof NotFoundHttpException) {
                    throw new NotFoundHttpException(
                        "{$request->method()} {$request->url()}", $e, is_int($e->getCode()) ? $e->getCode() : 0
                    );
                }

                throw $e;
            }

            /**
             * Render an exception to the console.
             *
             * @param  \Symfony\Component\Console\Output\OutputInterface  $output
             * @param  \Throwable  $e
             * @return void
             */
            public function renderForConsole($output, Throwable $e)
            {
                (new ConsoleApplication)->renderThrowable($e, $output);
            }
        };

        $currentExceptionHandler = app(ExceptionHandler::class);

        $currentExceptionHandler instanceof ExceptionHandlerFake
            ? $currentExceptionHandler->setHandler($exceptionHandler)
            : $this->app->instance(ExceptionHandler::class, $exceptionHandler);

        return $this;
    }

    /**
     * Assert that the given callback throws an exception with the given message when invoked.
     *
     * @param  \Closure  $test
     * @param  (\Closure(\Throwable): bool)|class-string<\Throwable>  $expectedClass
     * @param  string|null  $expectedMessage
     * @return $this
     */
    protected function assertThrows(Closure $test, string|Closure $expectedClass = Throwable::class, ?string $expectedMessage = null)
    {
        [$expectedClass, $expectedClassCallback] = $expectedClass instanceof Closure
            ? [$this->firstClosureParameterType($expectedClass), $expectedClass]
            : [$expectedClass, null];

        try {
            $test();

            $thrown = false;
        } catch (Throwable $exception) {
            $thrown = $exception instanceof $expectedClass && ($expectedClassCallback === null || $expectedClassCallback($exception));

            $actualMessage = $exception->getMessage();
        }

        Assert::assertTrue(
            $thrown,
            sprintf('Failed asserting that exception of type "%s" was thrown.', $expectedClass)
        );

        if (isset($expectedMessage)) {
            if (! isset($actualMessage)) {
                Assert::fail(
                    sprintf(
                        'Failed asserting that exception of type "%s" with message "%s" was thrown.',
                        $expectedClass,
                        $expectedMessage
                    )
                );
            } else {
                Assert::assertStringContainsString($expectedMessage, $actualMessage);
            }
        }

        return $this;
    }

    /**
     * Assert that the given callback does not throw an exception.
     *
     * @param  \Closure  $test
     * @return $this
     */
    protected function assertDoesntThrow(Closure $test)
    {
        try {
            $test();

            $thrown = false;
        } catch (Throwable $exception) {
            $thrown = true;

            $exceptionClass = get_class($exception);
            $exceptionMessage = $exception->getMessage();
        }

        Assert::assertTrue(
            ! $thrown,
            sprintf('Unexpected exception of type %s with message %s was thrown.', $exceptionClass ?? null, $exceptionMessage ?? null)
        );

        return $this;
    }
}
