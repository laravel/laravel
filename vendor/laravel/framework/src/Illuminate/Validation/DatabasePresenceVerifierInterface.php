<?php

namespace Illuminate\Validation;

interface DatabasePresenceVerifierInterface extends PresenceVerifierInterface
{
    /**
     * Set the connection to be used.
     *
     * @param  string  $connection
     * @return void
     */
    public function setConnection($connection);
}
