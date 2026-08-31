<?php

namespace Illuminate\Auth;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Support\Arrayable;

class EloquentUserProvider implements UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The Eloquent user model.
     *
     * @var class-string<\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model>
     */
    protected $model;

    /**
     * The callback that may modify the user retrieval queries.
     *
     * @var (\Closure(\Illuminate\Database\Eloquent\Builder<*>):mixed)|null
     */
    protected $queryCallback;

    /**
     * Create a new database user provider.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $model
     */
    public function __construct(HasherContract $hasher, $model)
    {
        $this->model = $model;
        $this->hasher = $hasher;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return (\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model)|null
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return (\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model)|null
     */
    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)->where(
            $model->getAuthIdentifierName(), $identifier
        )->first();

        if (! $retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $retrievedModel : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, #[\SensitiveParameter] $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return (\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model)|null
     */
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        $credentials = array_filter(
            $credentials,
            fn ($key) => ! str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($credentials)) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } elseif ($value instanceof Closure) {
                $value($query);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, #[\SensitiveParameter] array $credentials)
    {
        if (is_null($plain = $credentials['password'])) {
            return false;
        }

        if (is_null($hashed = $user->getAuthPassword())) {
            return false;
        }

        return $this->hasher->check($plain, $hashed);
    }

    /**
     * Rehash the user's password if required and supported.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model  $user
     * @param  array  $credentials
     * @param  bool  $force
     * @return void
     */
    public function rehashPasswordIfRequired(UserContract $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        if (! $this->hasher->needsRehash($user->getAuthPassword()) && ! $force) {
            return;
        }

        $user->forceFill([
            $user->getAuthPasswordName() => $this->hasher->make($credentials['password']),
        ])->save();
    }

    /**
     * Get a new query builder for the model instance.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  TModel|null  $model
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    protected function newModelQuery($model = null)
    {
        $query = is_null($model)
            ? $this->createModel()->newQuery()
            : $model->newQuery();

        with($query, $this->queryCallback);

        return $query;
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Gets the hasher implementation.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }

    /**
     * Sets the hasher implementation.
     *
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @return $this
     */
    public function setHasher(HasherContract $hasher)
    {
        $this->hasher = $hasher;

        return $this;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return class-string<\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model>
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  class-string<\Illuminate\Contracts\Auth\Authenticatable&\Illuminate\Database\Eloquent\Model>  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the callback that modifies the query before retrieving users.
     *
     * @return (\Closure(\Illuminate\Database\Eloquent\Builder<*>):mixed)|null
     */
    public function getQueryCallback()
    {
        return $this->queryCallback;
    }

    /**
     * Sets the callback to modify the query before retrieving users.
     *
     * @param  (\Closure(\Illuminate\Database\Eloquent\Builder<*>):mixed)|null  $queryCallback
     * @return $this
     */
    public function withQuery($queryCallback = null)
    {
        $this->queryCallback = $queryCallback;

        return $this;
    }
}
