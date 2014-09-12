<?php namespace App\Http;

use Illuminate\Contracts\Auth\Authenticator;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Routing\Router;

class FrontendRoutes {

  /**
   * @var Registrar
   */
  private $router;
  /**
   * @var Authenticator
   */
  private $authenticator;

  /**
   * @param Registrar|Router $router
   * @param Authenticator    $authenticator
   */
  function __construct(Registrar $router, Authenticator $authenticator)
  {
    $this->router = $router;
    $this->authenticator = $authenticator;
  }

  /**
   *
   */
  public function map()
  {
    $this->get('/', function()
    {
      return 'It works! :D';
    });
  }

  /**
   * @param $name
   * @param $arguments
   */
  function __call($name, $arguments)
  {
    call_user_func_array([$this->router, $name], $arguments);
  }
} 