<?php

namespace Illuminate\Broadcasting;

class EncryptedPrivateChannel extends Channel
{
    /**
     * Create a new channel instance.
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        parent::__construct('private-encrypted-'.$name);
    }
}
