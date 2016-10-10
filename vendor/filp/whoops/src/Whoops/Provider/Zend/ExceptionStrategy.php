<?php
/**
 * ZF2 Integration for Whoops
 * @author Balázs Németh <zsilbi@zsilbi.hu>
 */

namespace Whoops\Provider\Zend;

use Whoops\Run;

use Zend\Mvc\View\Http\ExceptionStrategy as BaseExceptionStrategy;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;

class ExceptionStrategy extends BaseExceptionStrategy {

    protected $run;

    public function __construct(Run $run) {
        $this->run = $run;
        return $this;
    }

    public function prepareExceptionViewModel(MvcEvent $event) {
        // Do nothing if no error in the event
        $error = $event->getError();
        if (empty($error)) {
            return;
        }

        // Do nothing if the result is a response object
        $result = $event->getResult();
        if ($result instanceof Response) {
            return;
        }

        switch ($error) {
            case Application::ERROR_CONTROLLER_NOT_FOUND:
            case Application::ERROR_CONTROLLER_INVALID:
            case Application::ERROR_ROUTER_NO_MATCH:
                // Specifically not handling these
                return;

            case Application::ERROR_EXCEPTION:
            default:
                $exception = $event->getParam('exception');
                if($exception) {
                    $response = $event->getResponse();
                    if (!$response || $response->getStatusCode() === 200) {
                        header('HTTP/1.0 500 Internal Server Error', true, 500);
                    }
                    ob_clean();
                    $this->run->handleException($event->getParam('exception'));
                }
                break;
        }
    }

}
