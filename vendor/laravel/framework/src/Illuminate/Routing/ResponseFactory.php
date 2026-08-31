<?php

namespace Illuminate\Routing;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory as FactoryContract;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\StreamedEvent;
use Illuminate\Routing\Exceptions\StreamedResponseException;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use ReflectionFunction;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ResponseFactory implements FactoryContract
{
    use Macroable;

    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * The redirector instance.
     *
     * @var \Illuminate\Routing\Redirector
     */
    protected $redirector;

    /**
     * Create a new response factory instance.
     *
     * @param  \Illuminate\Contracts\View\Factory  $view
     * @param  \Illuminate\Routing\Redirector  $redirector
     */
    public function __construct(ViewFactory $view, Redirector $redirector)
    {
        $this->view = $view;
        $this->redirector = $redirector;
    }

    /**
     * Create a new response instance.
     *
     * @param  mixed  $content
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response
     */
    public function make($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Create a new "no content" response.
     *
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response
     */
    public function noContent($status = 204, array $headers = [])
    {
        return $this->make('', $status, $headers);
    }

    /**
     * Create a new response for a given view.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response
     */
    public function view($view, $data = [], $status = 200, array $headers = [])
    {
        if (is_array($view)) {
            return $this->make($this->view->first($view, $data), $status, $headers);
        }

        return $this->make($this->view->make($view, $data), $status, $headers);
    }

    /**
     * Create a new JSON response instance.
     *
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }

    /**
     * Create a new JSONP response instance.
     *
     * @param  string  $callback
     * @param  mixed  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $options
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonp($callback, $data = [], $status = 200, array $headers = [], $options = 0)
    {
        return $this->json($data, $status, $headers, $options)->setCallback($callback);
    }

    /**
     * Create a new event stream response.
     *
     * @param  \Closure  $callback
     * @param  array  $headers
     * @param  \Illuminate\Http\StreamedEvent|string|null  $endStreamWith
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function eventStream(Closure $callback, array $headers = [], StreamedEvent|string|null $endStreamWith = '</stream>')
    {
        return $this->stream(function () use ($callback, $endStreamWith) {
            foreach ($callback() as $message) {
                if (connection_aborted()) {
                    break;
                }

                $event = 'update';

                if ($message instanceof StreamedEvent) {
                    $event = $message->event;
                    $message = $message->data;
                }

                if (! is_string($message) && ! is_numeric($message)) {
                    $message = Js::encode($message);
                }

                echo "event: $event\n";
                echo 'data: '.$message;
                echo "\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }

                flush();
            }

            if (filled($endStreamWith)) {
                $endEvent = 'update';

                if ($endStreamWith instanceof StreamedEvent) {
                    $endEvent = $endStreamWith->event;
                    $endStreamWith = $endStreamWith->data;
                }

                echo "event: $endEvent\n";
                echo 'data: '.$endStreamWith;
                echo "\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }

                flush();
            }
        }, 200, array_merge($headers, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]));
    }

    /**
     * Create a new streamed response instance.
     *
     * @param  callable|null  $callback
     * @param  int  $status
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function stream($callback, $status = 200, array $headers = [])
    {
        if (! is_null($callback) && (new ReflectionFunction($callback))->isGenerator()) {
            if (isset($_SERVER['LARAVEL_OCTANE'])) {
                return (new StreamedResponse(
                    null, $status, array_merge($headers, ['X-Accel-Buffering' => 'no'])
                ))->setCallback($callback);
            }

            return new StreamedResponse(function () use ($callback) {
                foreach ($callback() as $chunk) {
                    echo $chunk;
                    when(ob_get_level() > 0, fn () => ob_flush());
                    flush();
                }
            }, $status, array_merge($headers, ['X-Accel-Buffering' => 'no']));
        }

        return new StreamedResponse($callback, $status, $headers);
    }

    /**
     * Create a new streamed JSON response instance.
     *
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @param  int  $encodingOptions
     * @return \Symfony\Component\HttpFoundation\StreamedJsonResponse
     */
    public function streamJson($data, $status = 200, $headers = [], $encodingOptions = JsonResponse::DEFAULT_ENCODING_OPTIONS)
    {
        return new StreamedJsonResponse($data, $status, $headers, $encodingOptions);
    }

    /**
     * Create a new streamed response instance as a file download.
     *
     * @param  callable  $callback
     * @param  string|null  $name
     * @param  array  $headers
     * @param  string|null  $disposition
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * @throws \Illuminate\Routing\Exceptions\StreamedResponseException
     */
    public function streamDownload($callback, $name = null, array $headers = [], $disposition = 'attachment')
    {
        $withWrappedException = function () use ($callback) {
            try {
                $callback();
            } catch (Throwable $e) {
                throw new StreamedResponseException($e);
            }
        };

        $response = new StreamedResponse($withWrappedException, 200, $headers);

        if (! is_null($name)) {
            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                $disposition,
                $name,
                $this->fallbackName($name)
            ));
        }

        return $response;
    }

    /**
     * Create a new file download response.
     *
     * @param  \SplFileInfo|string  $file
     * @param  string|null  $name
     * @param  array  $headers
     * @param  string|null  $disposition
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($file, $name = null, array $headers = [], $disposition = 'attachment')
    {
        $response = new BinaryFileResponse($file, 200, $headers, true, $disposition);

        if (! is_null($name)) {
            return $response->setContentDisposition($disposition, $name, $this->fallbackName($name));
        }

        return $response;
    }

    /**
     * Convert the string to ASCII characters that are equivalent to the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function fallbackName($name)
    {
        return str_replace('%', '', Str::ascii($name));
    }

    /**
     * Return the raw contents of a binary file.
     *
     * @param  \SplFileInfo|string  $file
     * @param  array  $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function file($file, array $headers = [])
    {
        return new BinaryFileResponse($file, 200, $headers);
    }

    /**
     * Create a new redirect response to the given path.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectTo($path, $status = 302, $headers = [], $secure = null)
    {
        return $this->redirector->to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to a named route.
     *
     * @param  \BackedEnum|string  $route
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToRoute($route, $parameters = [], $status = 302, $headers = [])
    {
        return $this->redirector->route($route, $parameters, $status, $headers);
    }

    /**
     * Create a new redirect response to a controller action.
     *
     * @param  array|string  $action
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToAction($action, $parameters = [], $status = 302, $headers = [])
    {
        return $this->redirector->action($action, $parameters, $status, $headers);
    }

    /**
     * Create a new redirect response, while putting the current URL in the session.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectGuest($path, $status = 302, $headers = [], $secure = null)
    {
        return $this->redirector->guest($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the previously intended location.
     *
     * @param  string  $default
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToIntended($default = '/', $status = 302, $headers = [], $secure = null)
    {
        return $this->redirector->intended($default, $status, $headers, $secure);
    }
}
