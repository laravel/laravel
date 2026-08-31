<?php

namespace Laravel\Sanctum;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\Contracts\HasAbilities;

class PersonalAccessToken extends Model implements HasAbilities
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'token',
    ];

    /**
     * Get the tokenable model that the access token belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function tokenable()
    {
        return $this->morphTo('tokenable');
    }

    /**
     * Find the token instance matching the given token.
     *
     * @param  string  $token
     * @return static|null
     */
    public static function findToken($token)
    {
        if (strpos($token, '|') === false) {
            return static::where('token', hash('sha256', $token))->first();
        }

        [$id, $token] = explode('|', $token, 2);

        if ($instance = static::find($id)) {
            return hash_equals($instance->token, hash('sha256', $token)) ? $instance : null;
        }
    }

    /**
     * Determine if the token has a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function can($ability)
    {
        return in_array('*', $this->abilities, true) ||
               in_array($ability, $this->abilities, true);
    }

    /**
     * Determine if the token is missing a given ability.
     *
     * @param  string  $ability
     * @return bool
     */
    public function cant($ability)
    {
        return ! $this->can($ability);
    }
}
