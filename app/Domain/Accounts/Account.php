<?php

namespace App\Domain\Accounts;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

use App\Domain\Accounts\Verification\VerifyCode;

class Account extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use Notifiable;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'verified_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * All, non-trashed, verify codes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allVerifyCodes()
    {
        return $this->hasMany(VerifyCode::class);
    }

    /**
     * Latest, non-trashed, verify code that has not expired.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function verifyCode()
    {
        return $this->hasOne(VerifyCode::class)
            ->where('expired_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Illuminate\Auth\Passwords\CanResetPasswords
    |--------------------------------------------------------------------------
    */

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
