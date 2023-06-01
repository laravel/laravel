<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $profile_photo_url
 * @property integer $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $groups
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * Statuses.
     */
    public const STATUS_ACTIVE = 1;
    public const STATUS_AWAY = 2;
    public const STATUS_DO_NOT_DISTURB = 3;
    public const STATUS_INVISIBLE = 4;

    /**
     * List of statuses.
     *
     * @var array
     */
    public static array $statuses = [
        self::STATUS_ACTIVE,
        self::STATUS_AWAY,
        self::STATUS_DO_NOT_DISTURB,
        self::STATUS_INVISIBLE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The groups that belong to the user.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * Get the messages for the user.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the number of unread messages.
     */
    public function countUnread(): int
    {
        return 0;
    }

    /**
     * Get background color by user status.
     */
    public function getBGColor()
    {
        return [
            self::STATUS_ACTIVE => 'bg-success',
            self::STATUS_AWAY => 'bg-warning',
            self::STATUS_DO_NOT_DISTURB => 'bg-danger',
            self::STATUS_INVISIBLE => 'bg-light',
        ][$this->status];
    }
}
