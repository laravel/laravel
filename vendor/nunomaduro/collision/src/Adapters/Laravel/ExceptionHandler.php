<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Exceptions\ReportableHandler;
use NunoMaduro\Collision\Provider;
use Symfony\Component\Console\Exception\ExceptionInterface as SymfonyConsoleExceptionInterface;
use Throwable;

/**
 * @internal
 */
final class ExceptionHandler implements ExceptionHandlerContract
{
    /**
     * Holds an instance of the application exception handler.
     *
     * @var ExceptionHandlerContract
     */
    protected $appExceptionHandler;

    /**
     * Holds an instance of the container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Creates a new instance of the ExceptionHandler.
     */
    public function __construct(Container $container, ExceptionHandlerContract $appExceptionHandler)
    {
        $this->container = $container;
        $this->appExceptionHandler = $appExceptionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function report(Throwable $e)
    {
        $this->appExceptionHandler->report($e);
    }

    /**
     * {@inheritdoc}
     */
    public function render($request, Throwable $e)
    {
        return $this->appExceptionHandler->render($request, $e);
    }

    /**
     * {@inheritdoc}
     */
    public function renderForConsole($output, Throwable $e)
    {
        if ($e instanceof SymfonyConsoleExceptionInterface) {
            $this->appExceptionHandler->renderForConsole($output, $e);
        } else {
            /** @var Provider $provider */
            $provider = $this->container->make(Provider::class);

            $handler = $provider->register()
                ->getHandler()
                ->setOutput($output);

            $handler->setInspector((new Inspector($e)));

            $handler->handle();
        }
    }

    /**
     * Determine if the exception should be reported.
     *
     * @return bool
     */
    public function shouldReport(Throwable $e)
    {
        return $this->appExceptionHandler->shouldReport($e);
    }

    /**
     * Register a reportable callback.
     *
     * @return ReportableHandler
     */
    public function reportable(callable $reportUsing)
    {
        return $this->appExceptionHandler->reportable($reportUsing);
    }

    /**
     * Register a renderable callback.
     *
     * @return $this
     */
    public function renderable(callable $renderUsing)
    {
        $this->appExceptionHandler->renderable($renderUsing);

        return $this;
    }

    /**
     * Do not report duplicate exceptions.
     *
     * @return $this
     */
    public function dontReportDuplicates()
    {
        $this->appExceptionHandler->dontReportDuplicates();

        return $this;
    }
}
