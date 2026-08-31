<?php

namespace Illuminate\Testing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionProperty;

/**
 * @internal
 *
 * @mixin Assert
 */
class TestResponseAssert
{
    /**
     * Create a new TestResponse assertion helper.
     */
    private function __construct(protected TestResponse $response)
    {
        //
    }

    /**
     * Create a new TestResponse assertion helper.
     */
    public static function withResponse(TestResponse $response): static
    {
        return new static($response);
    }

    /**
     * Pass method calls to the Assert class and decorate the exception message.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function __call($name, $arguments)
    {
        try {
            Assert::$name(...$arguments);
        } catch (ExpectationFailedException $e) {
            throw $this->injectResponseContext($e);
        }
    }

    /**
     * Pass static method calls to the Assert class.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public static function __callStatic($name, $arguments)
    {
        Assert::$name(...$arguments);
    }

    /**
     * Inject additional context from the response into the exception message.
     *
     * @param  \PHPUnit\Framework\ExpectationFailedException  $exception
     * @return \PHPUnit\Framework\ExpectationFailedException
     */
    protected function injectResponseContext($exception)
    {
        if ($lastException = $this->response->exceptions->last()) {
            return $this->appendExceptionToException($lastException, $exception);
        }

        if ($this->response->baseResponse instanceof RedirectResponse) {
            $session = $this->response->baseResponse->getSession();

            if (! is_null($session) && $session->has('errors')) {
                return $this->appendErrorsToException($session->get('errors')->all(), $exception);
            }
        }

        if ($this->response->baseResponse->headers->get('Content-Type') === 'application/json') {
            $testJson = new AssertableJsonString($this->response->getContent());

            if (isset($testJson['errors'])) {
                return $this->appendErrorsToException($testJson->json(), $exception, true);
            }
        }

        return $exception;
    }

    /**
     * Append an exception to the message of another exception.
     *
     * @param  \Throwable  $exceptionToAppend
     * @param  \PHPUnit\Framework\ExpectationFailedException  $exception
     * @return \PHPUnit\Framework\ExpectationFailedException
     */
    protected function appendExceptionToException($exceptionToAppend, $exception)
    {
        $exceptionMessage = is_string($exceptionToAppend) ? $exceptionToAppend : $exceptionToAppend->getMessage();

        $exceptionToAppend = (string) $exceptionToAppend;

        $message = <<<"EOF"
            The following exception occurred during the last request:

            $exceptionToAppend

            ----------------------------------------------------------------------------------

            $exceptionMessage
            EOF;

        return $this->appendMessageToException($message, $exception);
    }

    /**
     * Append errors to an exception message.
     *
     * @param  array  $errors
     * @param  \PHPUnit\Framework\ExpectationFailedException  $exception
     * @param  bool  $json
     * @return \PHPUnit\Framework\ExpectationFailedException
     */
    protected function appendErrorsToException($errors, $exception, $json = false)
    {
        $errors = $json
            ? json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : implode(PHP_EOL, Arr::flatten($errors));

        // JSON error messages may already contain the errors, so we shouldn't duplicate them...
        if (str_contains($exception->getMessage(), $errors)) {
            return $exception;
        }

        $message = <<<"EOF"
            The following errors occurred during the last request:

            $errors
            EOF;

        return $this->appendMessageToException($message, $exception);
    }

    /**
     * Append a message to an exception.
     *
     * @param  string  $message
     * @param  \PHPUnit\Framework\ExpectationFailedException  $exception
     * @return \PHPUnit\Framework\ExpectationFailedException
     */
    protected function appendMessageToException($message, $exception)
    {
        $property = new ReflectionProperty($exception, 'message');

        $property->setValue(
            $exception,
            $exception->getMessage().PHP_EOL.PHP_EOL.$message.PHP_EOL
        );

        return $exception;
    }
}
