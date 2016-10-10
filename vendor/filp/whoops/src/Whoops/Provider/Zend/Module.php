<?php
/**
 * ZF2 Integration for Whoops
 * @author Balázs Németh <zsilbi@zsilbi.hu>
 *
 * The Whoops directory should be added as a module to ZF2 (/vendor/Whoops)
 *
 * Whoops must be added as the first module
 * For example:
 *   'modules' => array(
 *       'Whoops',
 *       'Application',
 *   ),
 *
 * This file should be moved next to Whoops/Run.php (/vendor/Whoops/Module.php)
 *
 */

namespace Whoops;

use Whoops\Run;
use Whoops\Provider\Zend\ExceptionStrategy;
use Whoops\Provider\Zend\RouteNotFoundStrategy;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Zend\EventManager\EventInterface;
use Zend\Console\Request as ConsoleRequest;

class Module
{
    protected $run;

    public function onBootstrap(EventInterface $event)
    {
        $prettyPageHandler = new PrettyPageHandler();

        // Set editor
        $config = $event->getApplication()->getServiceManager()->get('Config');
        if (isset($config['view_manager']['editor'])) {
            $prettyPageHandler->setEditor($config['view_manager']['editor']);
        }


        $this->run = new Run();
        $this->run->register();
        $this->run->pushHandler($prettyPageHandler);

        $this->attachListeners($event);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    private function attachListeners(EventInterface $event)
    {
        $request = $event->getRequest();
        $application = $event->getApplication();
        $services = $application->getServiceManager();
        $events = $application->getEventManager();
        $config = $services->get('Config');

        //Display exceptions based on configuration and console mode
        if ($request instanceof ConsoleRequest || empty($config['view_manager']['display_exceptions']))
            return;

        $jsonHandler = new JsonResponseHandler();

        if (!empty($config['view_manager']['json_exceptions']['show_trace'])) {
            //Add trace to the JSON output
            $jsonHandler->addTraceToOutput(true);
        }

        if (!empty($config['view_manager']['json_exceptions']['ajax_only'])) {
            //Only return JSON response for AJAX requests
            $jsonHandler->onlyForAjaxRequests(true);
        }

        if (!empty($config['view_manager']['json_exceptions']['display'])) {
            //Turn on JSON handler
            $this->run->pushHandler($jsonHandler);
        }

        //Attach the Whoops ExceptionStrategy
        $exceptionStrategy = new ExceptionStrategy($this->run);
        $exceptionStrategy->attach($events);

        //Attach the Whoops RouteNotFoundStrategy
        $routeNotFoundStrategy = new RouteNotFoundStrategy($this->run);
        $routeNotFoundStrategy->attach($events);

        //Detach default ExceptionStrategy
        $services->get('Zend\Mvc\View\Http\ExceptionStrategy')->detach($events);

        //Detach default RouteNotFoundStrategy
        $services->get('Zend\Mvc\View\Http\RouteNotFoundStrategy')->detach($events);
    }

}
