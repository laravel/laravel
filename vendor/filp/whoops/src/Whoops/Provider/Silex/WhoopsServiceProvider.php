<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Provider\Silex;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Silex\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use RuntimeException;

class WhoopsServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        // There's only ever going to be one error page...right?
        $app['whoops.error_page_handler'] = $app->share(function() {
            return new PrettyPageHandler;
        });

        // Retrieves info on the Silex environment and ships it off
        // to the PrettyPageHandler's data tables:
        // This works by adding a new handler to the stack that runs
        // before the error page, retrieving the shared page handler
        // instance, and working with it to add new data tables
        $app['whoops.silex_info_handler'] = $app->protect(function() use($app) {
            try {
                /** @var Request $request */
                $request = $app['request'];
            } catch (RuntimeException $e) {
                // This error occurred too early in the application's life
                // and the request instance is not yet available.
                return;
            }

            /** @var PrettyPageHandler $errorPageHandler */
            $errorPageHandler = $app["whoops.error_page_handler"];

            // General application info:
            $errorPageHandler->addDataTable('Silex Application', array(
                'Charset'          => $app['charset'],
                'Locale'           => $app['locale'],
                'Route Class'      => $app['route_class'],
                'Dispatcher Class' => $app['dispatcher_class'],
                'Application Class'=> get_class($app)
            ));

            // Request info:
            $errorPageHandler->addDataTable('Silex Application (Request)', array(
                'URI'         => $request->getUri(),
                'Request URI' => $request->getRequestUri(),
                'Path Info'   => $request->getPathInfo(),
                'Query String'=> $request->getQueryString() ?: '<none>',
                'HTTP Method' => $request->getMethod(),
                'Script Name' => $request->getScriptName(),
                'Base Path'   => $request->getBasePath(),
                'Base URL'    => $request->getBaseUrl(),
                'Scheme'      => $request->getScheme(),
                'Port'        => $request->getPort(),
                'Host'        => $request->getHost(),
            ));
        });

        $app['whoops'] = $app->share(function() use($app) {
            $run = new Run;
            $run->pushHandler($app['whoops.error_page_handler']);
            $run->pushHandler($app['whoops.silex_info_handler']);
            return $run;
        });

        $app->error(array($app['whoops'], Run::EXCEPTION_HANDLER));
        $app['whoops']->register();
    }

    /**
     * @see Silex\ServiceProviderInterface::boot
     */
    public function boot(Application $app) {}
}
