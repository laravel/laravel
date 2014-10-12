<?php namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\PasswordBroker;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Middleware("csrf")
 * @Middleware("guest")
 */
class RemindersController {

	/**
	 * The password reminder implementation.
	 *
	 * @var PasswordBroker
	 */
	protected $passwords;

	/**
	 * Create a new password reminder controller instance.
	 *
	 * @param  PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(PasswordBroker $passwords)
	{
		$this->passwords = $passwords;
	}

	/**
	 * Display the password reminder view.
	 *
	 * @Get("password/remind")
	 *
	 * @return Response
	 */
	public function showReminderForm()
	{
		return view('password.remind');
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @Post("password/remind")
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function sendPasswordResetEmail(Request $request)
	{
		switch ($response = $this->passwords->remind($request->only('email')))
		{
			case PasswordBroker::INVALID_USER:
				return redirect()->back()->with('error', trans($response));

			case PasswordBroker::REMINDER_SENT:
				return redirect()->back()->with('status', trans($response));
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @Get("password/reset")
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function showPasswordResetForm($token = null)
	{
		if (is_null($token))
		{
			throw new NotFoundHttpException;
		}

		return view('password.reset')->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @Post("password/reset")
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function resetPassword(Request $request)
	{
		$credentials = $request->only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = $this->passwords->reset($credentials, function($user, $password)
		{
			$user->password = bcrypt($password);

			$user->save();
		});

		switch ($response)
		{
			case PasswordBroker::INVALID_PASSWORD:
			case PasswordBroker::INVALID_TOKEN:
			case PasswordBroker::INVALID_USER:
				return redirect()->back()->with('error', trans($response));

			case PasswordBroker::PASSWORD_RESET:
				return redirect()->to('/');
		}
	}

}
