<?php

namespace Illuminate\Auth;

trait Authenticatable
{
    /**
     * The column name of the password field using during authentication.
     *
     * @var string
     */
    protected $authPasswordName = 'password';

    /**
     * The column name of the "remember me" token.
     *
     * @var string
     */
    protected $rememberTokenName = 'remember_token';

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the unique broadcast identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifierForBroadcasting()
    {
        return $this->getAuthIdentifier();
    }

    /**
     * Get the name of the password attribute for the user.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return $this->authPasswordName;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->{$this->getAuthPasswordName()};
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        if (! empty($this->getRememberTokenName())) {
            return (string) $this->{$this->getRememberTokenName()};
        }
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        if (! empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->rememberTokenName;
    }
}
