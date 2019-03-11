<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {
  /**
   * A list of the exception types that are not reported.
   *
   * @var array
   */
  protected $dontReport = [
    //
  ];

  /**
   * A list of the inputs that are never flashed for validation exceptions.
   *
   * @var array
   */
  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  /**
   * Report or log an exception.
   *
   * @param  \Exception  $exception
   * @return void
   */
  public function report(Exception $exception) {
    parent::report($exception);
  }
  /**
   * different responses to exceptions in JSON APIs.
   * every unit in $jsonResponseTypes is an exception catch.
   * keys:
   *  instance: exceptions class
   *  message: message have to be sent as response
   *  status-code: HTTP Status Code in response
   */
  private $jsonResponseTypes = [
    [
      "instance" => \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
      "message" => "requested endpoint not found",
      "status-code" => 404,
    ],
    [
      "instance" => \Illuminate\Auth\AuthenticationException::class,
      "message" => "you are unauthorized. make sure to headrs are right and token is not expired",
      "status-code" => 401,
    ],
    [
      "instance" => \Illuminate\Database\Eloquent\ModelNotFoundException::class,
      "message" => "requested entity not found",
      "status-code" => 404,
    ],
    [
      "instance" => \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
      "message" => "requested verb for this endpoint is not allowed",
      "status-code" => 405,
    ],
  ];
  /**
   * customized abort responses in JSON APIs. when using abort() function in APIs. to return errors with message
   * add another status code as project needs
   */
  private $jsonResponseAbortTypes = [
    404 => "requested entity not found",
    402 => "you don't have enough credit to complete this payment",
    403 => "you don't have permission to this entit",
  ];
  /**
   * to enable json responses set this value = true
   */
  private $jsonResponseEnabled = false;
  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Exception  $exception
   * @return \Illuminate\Http\Response
   */
  public function render($request, Exception $exception) {
    if ($request->wantsJson() && $this->jsonResponseEnabled) {
      foreach ($this->jsonResponseTypes as $responseType) {
        if ($exception instanceof $responseType['instance']) {
          return response()->json([
            'message' => $responseType['message'],
          ], $responseType['status-code']);
        }
      }
      if (method_exists($exception, "getStatusCode")) {
        foreach ($this->jsonResponseAbortTypes as $statusCode => $responseAbortMessage) {
          if ($exception->getStatusCode() == $statusCode) {
            return response()->json([
              "message" => $responseAbortMessage,
            ], $statusCode);
          }
        }
      }
    }
    return parent::render($request, $exception);
  }
}
