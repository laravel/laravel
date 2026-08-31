<?php

namespace Illuminate\Auth\Events;

use Illuminate\Queue\SerializesModels;

class OtherDeviceLogout
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $guard  The authentication guard name.
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user  \Illuminate\Contracts\Auth\Authenticatable
     */
    public function __construct(
        public $guard,
        public $user,
    ) {
    }
}
