<?php namespace App\Http\Controllers;

use App, Exception, Request, Route;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is a RESTful style controller.
 *
 * This controller is similar to the resource controller provided by 
 * Laravel framework, which supports 7 actions: 
 *
 *   index/create/store/show/edit/update/destroy
 *
 * However, this controller is more flexible in defining/extending 
 * actions. Some business-specific actions can be defined easily,
 * especially for PUT requests, such as:
 *
 *   PUT /books/352/rename
 *   PUT /books/352/star
 * 
 * Usage:
 * 1. Create a controller class extending RESTfulController
 * 2. Define methods for actions, for example:
 *
 *   protected function putIDRename($id) { ... }
 *   protected function putIDStar($id) { ... }
 *   protected function deleteID($id) { ... }
 *
 * The `ID` part in the method name works as a placeholder, and can be
 * defined to your own pattern, see `getMethodNameIDPlaceHolder` method
 * for more details.
 *
 * The method name looks similar to the request uri and is thus easy to
 * remember and find.
 * 
 * You can also define custom pattern of the resource ID, see
 * `getIDPattern` and `decodeID` methods for more details.
 *
 * There are some limitions of this controller.
 * 1. It can work with the route filters but only the "only" type of
 * before/after filters are supported at the moment.
 * 2. The middleware introduced in Laravel 5 is not supported yet.
 * 3. The implementation depends on some implementation details of the
 * route subsystem, which may change in future Laravel versions.
 *
 * References: 
 * 1. http://codeplanet.io/principles-good-restful-api-design/
 * 2. https://bourgeois.me/rest/
 * 3. http://www.ruanyifeng.com/blog/2014/05/restful_api.html
 *
 * @author Neil <secnelis@gmail.com>
 *
 */
class RESTfulController extends Controller {

  /**
   * Define the placeholder of the ID part within a method name.
   *
   * For example, if this method returns `ABC`, and the request 
   * is `PUT /books/326/star`, then the following method should be
   * defined to handle the request:
   *
   * protected function putABCStar($id) {
   *   // $id === 326
   *   ......
   * }
   *
   */
  protected function getMethodNameIDPlaceHolder() {
    return 'ID';
  }

  /**
   * Define the regex pattern of the resource ID. The default pattern
   * is defined as number.
   *
   * @see decodeID
   */
  protected function getIDPattern() {
    return '\d+';
  }

  /**
   * The ID requested from the client may be encoded(like Hashids),
   * and should be decoded for further process.
   *
   * @return the decoded ID, or `null` if failed(i.e. not an ID)
   * @see getIDPattern
   *
   */
  protected function decodeID($value) {
    return (int)$value;
  }

  /**
   * Entrance for dispatch.
   */
  public function missingMethod($parameters = array()) {
    if (is_string($parameters)) {
      $parameters = explode('/', $parameters);
    }
    $request = Request::instance();
    $httpMethod = $request->method();
    if (preg_match('/'.$this->getIDPattern().'/', head($parameters))) {
      $id = $this->decodeID(head($parameters));
      if (is_null($id)) {
        throw new NotFoundHttpException("Controller method not found.");
      }
      $action = count($parameters) > 1 ? $parameters[1] : '';      
      $methodName = $this->findMethod($httpMethod, $action);
      if ( ! is_null($methodName)) {
        $response = $this->before($methodName, $request);
        if ( ! is_null($response)) {
          return $response;
        }
        $args = array_merge([$id], array_slice($parameters, 2));
        $response = call_user_func_array([$this, $methodName], $args);
        if ( ! $response instanceof SymfonyResponse) {
          $response = new HttpResponse($response);
          $response->prepare($request);
        }
        $this->after($methodName, Request::instance(), $response);
        return $response;
      }
    }
    throw new NotFoundHttpException("Controller method not found.");
  }

  protected function findMethod($httpMethod, $action) {
    foreach ([$httpMethod, 'any'] as $m) {     
      $methodName = strtolower($m).$this->getMethodNameIDPlaceHolder().ucfirst($action);
      if (method_exists($this, $methodName)) {
        return $methodName;
      }
    }
  }

  protected function before($method, $request) {
    foreach ($this->getBeforeFilters() as $filter) {
      if ($this->filterApplies($filter, $method)) {
        $response = $this->callFilter($filter, $request);
        if ( ! is_null($response)) {
          return $response;
        }
      }
    }
  }

  protected function after($method, $request, $response) {
    foreach ($this->getAfterFilters() as $filter) {
      if ($this->filterApplies($filter, $method)) {
        $this->callFilter($filter, $request, $response);
      }
    }
  }

  protected function filterApplies($filter, $method) {
    foreach (array('Only', 'Except', 'On') as $type) {
      if ($this->{"filterFails{$type}"}($filter, $method)) {
        return false;
      }
    }
    return true;
  }

  protected function filterFailsOnly($filter, $method) {
    if ( ! isset($filter['options']['only'])) return false;

    return ! in_array($method, (array) $filter['options']['only']);
  }

  protected function filterFailsExcept($filter, $method) {
    if ( ! isset($filter['options']['except'])) return false;

    throw new Exception('Only "only" filters are supported by RESTfulController, but "except" filter is found');
  }

  protected function filterFailsOn($filter, $method) {
    $on = array_get($filter, 'options.on', null);

    if (is_null($on)) return false;

    throw new Exception('Only "only" filters are supported by RESTfulController, but "on" filter is found');
  }

  protected function callFilter($filter, $request, $response = null) {
    extract($filter);
    return Route::callRouteFilter($filter, $parameters, App::make('router'), $request, $response);
  }

}
