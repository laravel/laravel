<?php

namespace Illuminate\Log\Events;

class MessageLogged
{
    /**
     * Create a new event instance.
     *
     * @param  "emergency"|"alert"|"critical"|"error"|"warning"|"notice"|"info"|"debug"  $level  The log "level".
     * @param  string  $message  The log message.
     * @param  array  $context  The log context.
     */
    public function __construct(
        public $level,
        public $message,
        public array $context = [],
    ) {
    }
}
