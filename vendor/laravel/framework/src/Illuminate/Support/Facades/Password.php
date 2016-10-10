<?php namespace Illuminate\Support\Facades;

/**
 * @see \Illuminate\Auth\Reminders\PasswordBroker
 */
class Password extends Facade {

	/**
	 * Constant representing a successfully sent reminder.
	 *
	 * @var int
	 */
	const REMINDER_SENT = 'reminders.sent';

	/**
	 * Constant representing a successfully reset password.
	 *
	 * @var int
	 */
	const PASSWORD_RESET = 'reminders.reset';

	/**
	 * Constant representing the user not found response.
	 *
	 * @var int
	 */
	const INVALID_USER = 'reminders.user';

	/**
	 * Constant representing an invalid password.
	 *
	 * @var int
	 */
	const INVALID_PASSWORD = 'reminders.password';

	/**
	 * Constant representing an invalid token.
	 *
	 * @var int
	 */
	const INVALID_TOKEN = 'reminders.token';

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth.reminder'; }

}
