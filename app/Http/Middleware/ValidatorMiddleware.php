<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Console\AppNamespaceDetectorTrait;

class ValidatorMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Validator Middleware
    |--------------------------------------------------------------------------
    |
    | This middleware can be used to validate requests before entering
    | the controller. By default, this middleware uses a simple trait to
    | get the app namespace.
    |
    */
    use AppNamespaceDetectorTrait;

    /**
     * Handle an incoming request.
     * Model name for which the validator needs to be loaded will be passed as parameter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $model
     * @return mixed
     */
    public function handle($request, Closure $next, $model)
    {
        $namespace = $this->getNamespace();
        $modelWithNamespace = $namespace.$model;
        $validator = $modelWithNamespace::validate($request->all());

        if ($validator->passes())
            return $next($request);

        if ($request->ajax() || $request->wantsJson()) {
            return response($validator->getMessageBag(), 400);
        }

        return redirect()->back()->withInput()->with('error', $validator->getMessageBag());
    }

    public function getNamespace()
    {
        return $this->getAppNamespace();
    }
}
