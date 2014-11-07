<?php namespace App;

use Illuminate\Auth\Authenticates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\ResetsPassword;
use Illuminate\Contracts\Auth\User as UserContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements UserContract, CanResetPasswordContract {

	use Authenticates, ResetsPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

}
