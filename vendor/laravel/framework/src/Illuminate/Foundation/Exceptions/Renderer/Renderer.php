<?php

namespace Illuminate\Foundation\Exceptions\Renderer;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Exceptions\Renderer\Mappers\BladeMapper;
use Illuminate\Http\Request;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Throwable;

class Renderer
{
    /**
     * The path to the renderer's distribution files.
     *
     * @var string
     */
    protected const DIST = __DIR__.'/../../resources/exceptions/renderer/dist/';

    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $viewFactory;

    /**
     * The exception listener instance.
     *
     * @var \Illuminate\Foundation\Exceptions\Renderer\Listener
     */
    protected $listener;

    /**
     * The HTML error renderer instance.
     *
     * @var \Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer
     */
    protected $htmlErrorRenderer;

    /**
     * The Blade mapper instance.
     *
     * @var \Illuminate\Foundation\Exceptions\Renderer\Mappers\BladeMapper
     */
    protected $bladeMapper;

    /**
     * The application's base path.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Creates a new exception renderer instance.
     *
     * @param  \Illuminate\Contracts\View\Factory  $viewFactory
     * @param  \Illuminate\Foundation\Exceptions\Renderer\Listener  $listener
     * @param  \Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer  $htmlErrorRenderer
     * @param  \Illuminate\Foundation\Exceptions\Renderer\Mappers\BladeMapper  $bladeMapper
     * @param  string  $basePath
     */
    public function __construct(
        Factory $viewFactory,
        Listener $listener,
        HtmlErrorRenderer $htmlErrorRenderer,
        BladeMapper $bladeMapper,
        string $basePath,
    ) {
        $this->viewFactory = $viewFactory;
        $this->listener = $listener;
        $this->htmlErrorRenderer = $htmlErrorRenderer;
        $this->bladeMapper = $bladeMapper;
        $this->basePath = $basePath;
    }

    /**
     * Render the given exception as an HTML string.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $throwable
     * @return string
     */
    public function render(Request $request, Throwable $throwable)
    {
        $flattenException = $this->bladeMapper->map(
            $this->htmlErrorRenderer->render($throwable),
        );

        $exception = new Exception($flattenException, $request, $this->listener, $this->basePath);

        $exceptionAsMarkdown = $this->viewFactory->make('laravel-exceptions-renderer::markdown', [
            'exception' => $exception,
        ])->render();

        return $this->viewFactory->make('laravel-exceptions-renderer::show', [
            'exception' => $exception,
            'exceptionAsMarkdown' => $exceptionAsMarkdown,
        ])->render();
    }

    /**
     * Get the renderer's CSS content.
     *
     * @return string
     */
    public static function css()
    {
        return '<style>'.file_get_contents(static::DIST.'styles.css').'</style>';
    }

    /**
     * Get the renderer's JavaScript content.
     *
     * @return string
     */
    public static function js()
    {
        $viteJsAutoRefresh = '';

        $vite = app(\Illuminate\Foundation\Vite::class);

        if (is_file($vite->hotFile())) {
            $viteJsAutoRefresh = $vite->__invoke([]);
        }

        return '<script>'
            .file_get_contents(static::DIST.'scripts.js')
            .'</script>'.$viteJsAutoRefresh;
    }
}
