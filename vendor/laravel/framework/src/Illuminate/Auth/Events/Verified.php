<?php

namespace Illuminate\Auth\Events;

use Illuminate\Queue\SerializesModels;

class Verified
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Contracts\Auth\MustVerifyEmail  $user  The verified user.
     */
    public function __construct(
        public $user,
    ) {
    }
}
