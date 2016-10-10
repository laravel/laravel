<?php
/**
 * ZF2 Integration for Whoops
 * @author Balázs Németh <zsilbi@zsilbi.hu>
 */

namespace Whoops\Provider\Zend;

use Whoops\Run;

use Zend\Mvc\View\Http\RouteNotFoundStrategy as BaseRouteNotFoundStrategy;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ViewModel;

class RouteNotFoundStrategy extends BaseRouteNotFoundStrategy {

    protected $run;
    
    public function __construct(Run $run) {
        $this->run = $run;
    }
    
    public function prepareNotFoundViewModel(MvcEvent $e) {
        $vars = $e->getResult();
        if ($vars instanceof Response) {
            // Already have a response as the result
            return;
        }

        $response = $e->getResponse();
        if ($response->getStatusCode() != 404) {
            // Only handle 404 responses
            return;
        }

        if (!$vars instanceof ViewModel) {
            $model = new ViewModel();
            if (is_string($vars)) {
                $model->setVariable('message', $vars);
            } else {
                $model->setVariable('message', 'Page not found.');
            }
        } else {
            $model = $vars;
            if ($model->getVariable('message') === null) {
                $model->setVariable('message', 'Page not found.');
            }
        }
        // If displaying reasons, inject the reason
        $this->injectNotFoundReason($model, $e);

        // If displaying exceptions, inject
        $this->injectException($model, $e);

        // Inject controller if we're displaying either the reason or the exception
        $this->injectController($model, $e);
        
        ob_clean();
        
        throw new \Exception($model->getVariable('message') . ' ' . $model->getVariable('reason'));
    }

}
