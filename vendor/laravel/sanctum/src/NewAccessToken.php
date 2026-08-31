<?php

namespace Laravel\Sanctum;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class NewAccessToken implements Arrayable, Jsonable
{
    /**
     * Create a new access token result.
     *
     * @param  \Laravel\Sanctum\PersonalAccessToken  $accessToken  The access token instance.
     * @param  string  $plainTextToken  The plain text version of the token.
     */
    public function __construct(public PersonalAccessToken $accessToken, public string $plainTextToken)
    {
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, string>
     */
    public function toArray()
    {
        return [
            'accessToken' => $this->accessToken,
            'plainTextToken' => $this->plainTextToken,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
