<?php



namespace App\Http\Middleware;



use Closure;

use Illuminate\Auth\Middleware\EnsureEmailIsVerified as Middleware;

use Illuminate\Support\Facades\Auth;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

// use Illuminate\Support\Facades\Redirect;



class EnsureEmailIsVerified extends Middleware

{

    protected $guard;

    /**

     * Handle an incoming request.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \Closure  $next

     * @return mixed

     */

    public function handle($request, Closure $next, $guard = null)

    {

        switch ($guard) {

            // case 'admin':

            //     $this->guard = $guard;

            //     $link = "admin.verification.notice";

            //     break;



            // case 'writer':

            //     $this->guard = $guard;

            //     $link = "writer.verification.notice";

            //     break;    

            

            default:

                $this->guard = Auth::getDefaultDriver();

                $link = "verification.notice";

                break;

        }



        if (! $request->user($this->guard) ||

            ($request->user($this->guard) instanceof MustVerifyEmail &&

            ! $request->user($this->guard)->hasVerifiedEmail())) {



            return $request->expectsJson()

                    ? abort(403, 'Your email address is not verified.')

                    : Redirect::route($link);

        }



        return $next($request);

    }

}
